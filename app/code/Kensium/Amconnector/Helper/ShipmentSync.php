<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Webapi\Soap;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use SoapClient;
use SoapFault;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Kensium\Amconnector\Helper\Sync;
use Kensium\Amconnector\Helper\AmconnectorSoap;
use Kensium\Amconnector\Helper\Data;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Config\Definition\Exception\Exception;
use Kensium\Lib;

class ShipmentSync extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    public $errorCheckInMagento = array();
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var array
     */
    protected $branchCodes;

    /**
     * @var Timezone
     */
    protected $timeZone;

    /**
     * @var Sync
     */
    protected $helper;

    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $amconnectorHelper;


    /**
     * @var \Kensium\Amconnector\Helper\Time
     */
    protected $timeHelper;

    /**
     * @var \Kensium\Amconnector\Model\TimeFactory
     */
    protected $timeFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Customer
     */
    protected $customerResourceModel;

    /**
     * @var \Kensium\Amconnector\Helper\Customer
     */
    protected $helperCustomer;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $resourceModelSync;


    protected $clientHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Kensium\Amconnector\Helper\Customer
     */
    protected $modelCustomer;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Order
     */
    protected $resourceModelKemsOrder;

    protected $productFactory;
    /**
     * @var paymentMethods array
     */
    protected $cardTypePaymentMethods;

    public $stopSyncFlg;

    protected $orderType;

    /**
     * @var Xml
     */
    protected $xmlHelper;

    protected $urlHelper;

    protected $entity;

    protected $shippingTerm;

    protected $logViewFileName;

    protected $syncId;

    protected $scopeMode;

    protected $webserviceCookies;

    protected $endpointCookies;

    /**
     * @var
     */
    protected $webServiceUrl;

    protected $endpointUrl;

    protected $defaultUrl;

    protected $scheduledId;

    protected $defaultEndpointUrl;

    protected $invoiceSender;

    protected $marketplaceAcumaticaCustomerId;

    protected $giftCardPaymentMethod;

    protected $giftCardCashAccount;
    /**
     * @var
     */
    protected $licenseResourceModel;

    /**
     * @var string
     */
    protected $acumaticaOrderPrefix;
    /**
     * @var Magento\Framework\ObjectManagerInterface
     */
    protected $objectManagerInterface;

    const IS_LICENSE_INVALID = "Invalid";
    const IS_LICENSE_VALID = "Valid";
    const IS_TIME_VALID = "Valid";

    /**
     * @param Context $context
     * @param DateTime $date
     * @param Timezone $timeZone
     * @param Sync $helper
     * @param Data $amconnectorHelper
     * @param Time $timeHelper
     * @param Xml $xmlHelper
     * @param Url $urlHelper
     * @param \Kensium\Amconnector\Model\TimeFactory $timeFactory
     * @param \Kensium\Synclog\Helper\Order $orderHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Order $resourceModelKemsOrder
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync
     * @param Client $clientHelper
     * @param Customer $helperCustomer
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Customer $customerResourceModel
     * @param \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel
     * @param ObjectManagerInterface $objectManagerInterface
     * @param InvoiceSender $invoiceSender
     */
    public function __construct(
        Context $context,
        DateTime $date,
        TimeZone $timeZone,
        Sync $helper,
        \Kensium\Amconnector\Helper\Data $amconnectorHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Model\TimeFactory $timeFactory,
        \Kensium\Synclog\Helper\Shipment $orderShipment,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Kensium\Amconnector\Model\ResourceModel\Order $resourceModelKemsOrder,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Customer $helperCustomer,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Kensium\Amconnector\Model\ResourceModel\Customer $customerResourceModel,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        Lib\Common $common
    )
    {
        ini_set('default_socket_timeout', 1000);
        $this->context = $context;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->date = $date;
        $this->timeZone = $timeZone;
        $this->helper = $helper;
        $this->urlHelper = $urlHelper;
        $this->amconnectorHelper = $amconnectorHelper;
        $this->timeHelper = $timeHelper;
        $this->xmlHelper = $xmlHelper;
        $this->timeFactory = $timeFactory;
        $this->clientHelper = $clientHelper;
        $this->logger = $context->getLogger();
        $this->orderShipment = $orderShipment;
        $this->messageManager = $messageManager;
        $this->orderFactory = $orderFactory;
        $this->resourceModelKemsOrder = $resourceModelKemsOrder;
        $this->resourceModelSync = $resourceModelSync;
        $this->customerResourceModel = $customerResourceModel;
        $this->helperCustomer = $helperCustomer;
        $this->customerFactory = $customerFactory;
        $this->productFactory =  $productFactory;
        $this->objectManagerInterface = $objectManagerInterface;
        $this->licenseResourceModel = $licenseResourceModel;
        $this->cardTypePaymentMethods = array("authorizenet_directpost");
        $this->branchCodes = array("0" => "MAIN", "1" => "MAIN", "2" => "EAST", "3" => "NORTH", "4" => "SOUTH", "5" => "WEST");
        $this->shippingTerm = 'FREESHIP';
        $this->invoiceSender = $invoiceSender;
        // $this->marketplaceAcumaticaCustomerId = '7000000';
        $this->giftCardPaymentMethod = 'GIFTCARD';
        $this->giftCardCashAccount = 1010;
        $this->acumaticaOrderPrefix = "SB";
        $this->common = $common;
    }

    /**
     * license status verification
     * Acumatica login
     * get Customers From Acumatica
     * Insert customers data into temporary location in magento
     * Create Customer into Magento
     *
     */
    public function getShipmentSync($autoSync, $syncType, $entitySyncId, $storeId, $orderId, $failedOrderFlag)
    {
        $scheduleId = NULL;
        $orderLogArray = array();
        $insertedId = 0;

        /* Values that will be stored by default */
        $orderLogArr['store_id'] = $storeId;
        $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());;;
        $orderLogArr['order_id'] = "";
        $orderLogArr['acumatica_order_id'] = "";
        $orderLogArr['description'] = "";
        $orderLogArr['action'] = $syncType;
        $orderLogArr['customer_email'] = "";
        $orderLogArr['sync_action'] = "syncToMagento";
        $orderLogArr['message_type'] = "";
        $orderLogArr['long_message'] = "";
        $entity = 'orderShipment';

        if (empty($storeId)) {
            $this->scopeMode = 'default';
        } else {
            $this->scopeMode = 'stores';
        }
        if (empty($entitySyncId)) {
            if (empty($storeId))
                $storeId = 1;
            $this->syncId = $this->resourceModelSync->getSyncId($entity, $storeId);
        } else {
            $this->syncId = $entitySyncId;
        }
        $envelopeData = $this->helper->getEnvelopeData('GETSHIPMENTS');
        $lastSyncDate = $this->resourceModelSync->getLastSyncDate($this->syncId, $storeId);
        $timestamp = strtotime($lastSyncDate) - 25200; // reducing time for 2 hr
        $lastSyncShipmentDate = date('Y-m-d H:i:s', $timestamp);
        $lastSyncShipmentDate = str_replace(" ","T",$lastSyncShipmentDate);
        $lastSyncDate = date('Y-m-d', strtotime($lastSyncDate));

        $url = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
        $this->endpointUrl = $url . "entity/" . $envelopeData['envName'] . "/" . $envelopeData['envVersion'] . "?wsdl";
        $this->logViewFileName = $this->amconnectorHelper->syncLogFile($this->syncId, $entity, '');

        $txt = "Info : Sync process started!";
        $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
        /* End point Cookies*/

        /**
         * License status check
         */
        $txt = "Info : License verification is in progress";
        $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
        $licenseStatus = $this->licenseResourceModel->getLicenseStatus($storeId);
        $cimRequest = '';

        if ($licenseStatus != self::IS_LICENSE_VALID) {
            $txt = "Error: Invalid License Key";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            $this->resourceModelSync->updateSyncAttribute($this->syncId, 'ERROR', $storeId);
            $orderLogArr['description'] = "Invalid License Key";
            $orderLogArr['messages'] = "Invalid License Key";
            $orderLogArr['customer_email'] = "";
            $orderLogArr['job_code'] = $entity;
            $orderLogArr['run_mode'] = 'Manual';
            $orderLogArr['sync_action'] = "syncToMagento";
            $orderLogArr['message_type'] = "error";
            $orderLogArr['status'] = 'error';
            $this->orderShipment->shipmentManualSync($orderLogArr);
        } else {
            $txt = "Info : License verified successfully!";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);

            $txt = "Info : Server time verification is in progress";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);

            /*Time sync Check*/
            $timeSyncCheck = $this->timeHelper->timeSyncCheck($storeId);
            if ($timeSyncCheck != self::IS_TIME_VALID) { //Check Time is synced or not
                /**
                 * logs here for Time Not Synced
                 */
                $txt = "Error: Server time is not in sync.";
                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                $this->resourceModelSync->updateSyncAttribute($this->syncId, 'ERROR', $storeId);
                $orderLogArr['description'] = "Server time is not in sync.";
                $orderLogArr['messages'] = "Server time is not in sync.";
                $orderLogArr['customer_email'] = "";
                $orderLogArr['job_code'] = $entity;
                $orderLogArr['run_mode'] = 'Manual';
                $orderLogArr['status'] = 'error';
                $orderLogArr['sync_action'] = "syncToMagento";
                $orderLogArr['message_type'] = "error";
                $this->orderShipment->shipmentManualSync($orderLogArr);
            } else {
                $txt = "Info : Server time is in sync.";
                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                $orderLogArr['description'] = "Server time is in sync";
                $orderLogArr['customer_email'] = "";
                $orderLogArr['sync_action'] = "syncToMagento";
                $orderLogArr['message_type'] = "success";


                /*End Soap Request parmeters*/
                $this->resourceModelSync->updateSyncAttribute($this->syncId, 'STARTED', $storeId);
                $txt = "Info : Shipment manual sync initiated.";
                $orderLogArr['job_code'] = $entity;
                $orderLogArr['run_mode'] = 'Manual';
                $orderLogArr['messages'] = 'Shipment manual sync initiated';
                $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                $orderLogArr['scheduled_at'] = $this->date->date('Y-m-d H:i:s', time());
                $orderLogArr['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                $orderLogArr['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                $orderLogArr['status'] = 'success';
                $orderLogArr['auto_sync'] = 'Complete';
                $orderLogArr['store_id'] = $storeId;
                $this->scheduledId = $this->orderShipment->shipmentManualSync($orderLogArr);
                $orderLogArr['schedule_id'] = $this->scheduledId;
                $this->orderShipment->shipmentManualSync($orderLogArr); //to enter the log details inside the cron_schedule table
                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                $insertedId = $this->resourceModelSync->checkConnectionFlag($this->syncId, $entity, $storeId);


                $this->resourceModelSync->updateConnection($insertedId, 'PROCESS',$storeId);
                $this->resourceModelSync->updateSyncAttribute($this->syncId, 'PROCESSING', $storeId);

                $errorCount = 0;
                $orderCnt = 0;

                $this->getDataFromAcumatica($storeId, $lastSyncShipmentDate, $orderLogArr, NULL);
                //$this->getPOShipments($storeId, $lastSyncDate,$orderLogArr, NULL);

            }
        }
        $txt = "Info : Sync process completed!";
        $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
        $this->resourceModelSync->updateConnection($insertedId, 'SUCCESS',$storeId);
        if(count($this->errorCheckInMagento) > 0)
        {
            $this->resourceModelSync->updateSyncAttribute($this->syncId, 'ERROR', $storeId);
        }else{
            $this->resourceModelSync->updateSyncAttribute($this->syncId, 'SUCCESS', $storeId);
        }
    }

    /**
     * @param $this ->syncId
     * @param $this ->logViewFileName
     * @param $storeId
     * @param $lastSyncDate
     */
    public function getDataFromAcumatica($storeId, $lastSyncDate, $orderLogArr, $orderId = NULL)
    {
        $envelopeData = $this->helper->getEnvelopeData('GETSHIPMENTS');

        $XMLRequest = '';
        $XMLRequest = $envelopeData['envelope'];
	//$lastSyncDate = "2018-03-09T00:00:00";
	$XMLRequest = str_replace('{{FROMDATE}}', trim($lastSyncDate), $XMLRequest);
	$dateToTime = strtotime(str_replace("T"," ",$lastSyncDate))+30000;
	//$dateToSync = str_replace(" ","T",date('Y-m-d H:i:s',$dateToTime));
	$dateToSync = str_replace(" ","T",$this->date->date('Y-m-d H:i:s', strtotime("+1 day")));
	$XMLRequest = str_replace('{{TODATE}}', trim($dateToSync), $XMLRequest);
	//Send Order request to Acumatica
	$requestString = $envelopeData['envName'] . '/' . $envelopeData['envVersion'] . '/' . $envelopeData['methodName'];

	
	echo $XMLRequest;

        $configParameters = $this->amconnectorHelper->getConfigParameters($storeId);
        $response = $this->common->getAcumaticaResponse($configParameters, $XMLRequest, $this->endpointUrl, $requestString);

        $xml = $response;
        $data = $xml->Body->GetListResponse->GetListResult;
        if (isset($xml->Body->Fault->faultstring) && is_object($xml->Body->Fault->faultstring))
        {
            /**
             * Error logs Here
             */
            $error = json_encode($xml);
            $txt = "Error :.".$error;
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());;;
            $orderLogArr['order_id'] = '';
            $orderLogArr['acumatica_order_id'] = '';
            $orderLogArr['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
            $orderLogArr['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
            $orderLogArr['customer_email'] = "";
            $orderLogArr['sync_action'] = "syncToMagento";
            $orderLogArr['message_type'] = "error";
            $orderLogArr['description'] = $error;
            $this->orderShipment->shipmentManualSync($orderLogArr);
            $this->errorCheckInMagento[] = 1;

        }else if (is_object($xml->Body->GetListResponse->GetListResult))
        {
            $totalData = $this->xmlHelper->xml2array($data);
            $i = 0;
            $j = 0;
            if (isset($totalData['Entity']))
            {
                $oneRecordFlag = false;

                foreach ($totalData['Entity'] as $key => $res) {
                    if (!is_numeric($key)) {
                        $oneRecordFlag = true;
                        break;
                    }
                    $orderResults = $this->xmlHelper->xml2array($res);
                    if (isset($orderResults)) {
                        if (isset($orderResults['ShipmentNbr']['Value']) && isset($orderResults['ShipVia']['Value']))
                        {
                            $shipmentNumber = $orderResults['ShipmentNbr']['Value'];
                            $shipViaCode = $orderResults['ShipVia']['Value'];

                            /**
                             * getting shipped line items and tracking numbers
                             */
                            if (isset($orderResults['Details']['ShipmentDetail'])) {
                                $k = 0;
                                $oneShipmentRecordFlag = false;
                                $shipments = array();
                                foreach ($orderResults['Details']['ShipmentDetail'] as $shipKey => $inventoryRes) {
                                    if (!is_numeric($shipKey)) {
                                        $oneShipmentRecordFlag = true;
                                        break;
                                    }
                                    $inventoryRes = $this->xmlHelper->xml2array($inventoryRes);
                                    if (isset($inventoryRes['InventoryID']['Value']))
                                    {
                                        $shipments[$k]['InventoryId'] = $inventoryRes['InventoryID']['Value'];
                                        $shipments[$k]['ShippedQty'] = $inventoryRes['ShippedQty']['Value'];
                                        $shipments[$k]['OrderNbr'] = $inventoryRes['OrderNbr']['Value'];

                                        $oneTrackingNbrFlg = false;
                                        if(isset($orderResults['Packages']['ShipmentPackage'])) {
                                            foreach ($orderResults['Packages']['ShipmentPackage'] as $trackKey => $track) {
                                                if (!is_numeric($trackKey)) {
                                                    $oneTrackingNbrFlg = true;
                                                    break;
                                                }
                                                $track = $this->xmlHelper->xml2array($track);
                                                $shipments[$k]['ShippedQty'] = $inventoryRes['ShippedQty']['Value'];
                                                $shipments[$k]['OrderNbr'] = $inventoryRes['OrderNbr']['Value'];
                                                $shipments[$k]['InventoryId'] = $inventoryRes['InventoryID']['Value'];
                                                if (isset($track['TrackingNumber']['Value']) && $track['TrackingNumber']['Value'] != '') {
                                                    $shipments[$k]['TrackingNumber'] = $track['TrackingNumber']['Value'];
                                                } else {
                                                    $shipments[$k]['TrackingNumber'] = '';
                                                }
                                                $k++;
                                            }
                                            if ($oneTrackingNbrFlg) {
                                                if (isset($orderResults['Packages']['ShipmentPackage']['TrackingNumber']['Value'])) {
                                                    $shipments[$k]['TrackingNumber'] = $orderResults['Packages']['ShipmentPackage']['TrackingNumber']['Value'];
                                                } else {
                                                    $shipments[$k]['TrackingNumber'] = '';
                                                }
                                                $k++;
                                            }
                                        }
                                    }
                                }
                                if ($oneShipmentRecordFlag) {
                                    /**
                                     * single lineItem
                                     */
                                    if (isset($orderResults['Details']['ShipmentDetail']['InventoryID']['Value'])) {
                                        $shipments[$k]['InventoryId'] = $orderResults['Details']['ShipmentDetail']['InventoryID']['Value'];
                                        $shipments[$k]['ShippedQty'] = $orderResults['Details']['ShipmentDetail']['ShippedQty']['Value'];
                                        $shipments[$k]['OrderNbr'] = $orderResults['Details']['ShipmentDetail']['OrderNbr']['Value'];

                                        $oneTrackingNbrFlg = false;
                                        if(isset($orderResults['Packages']['ShipmentPackage']))
                                        {
                                            foreach ($orderResults['Packages']['ShipmentPackage'] as $trackKey => $track) {
                                                if (!is_numeric($trackKey)) {
                                                    $oneTrackingNbrFlg = true;
                                                    break;
                                                }
                                                $track = $this->xmlHelper->xml2array($track);
                                                $shipments[$k]['ShippedQty'] = $orderResults['Details']['ShipmentDetail']['ShippedQty']['Value'];
                                                $shipments[$k]['OrderNbr'] = $orderResults['Details']['ShipmentDetail']['OrderNbr']['Value'];
                                                $shipments[$k]['InventoryId'] = $orderResults['Details']['ShipmentDetail']['InventoryID']['Value'];
                                                if (isset($track['TrackingNumber']['Value']) && $track['TrackingNumber']['Value'] != '') {
                                                    $shipments[$k]['TrackingNumber'] = $track['TrackingNumber']['Value'];
                                                } else {
                                                    $shipments[$k]['TrackingNumber'] = '';
                                                }
                                                $k++;
                                            }
                                            if ($oneTrackingNbrFlg) {
                                                if (isset($orderResults['Packages']['ShipmentPackage']['TrackingNumber']['Value'])) {
                                                    $shipments[$k]['TrackingNumber'] = $orderResults['Packages']['ShipmentPackage']['TrackingNumber']['Value'];
                                                } else {
                                                    $shipments[$k]['TrackingNumber'] = '';
                                                }
                                            }
                                        }
                                    }
                                }
                                if(count($shipments) > 0)
                                {
                                    $this->createShipmentInMagento($shipments,$shipViaCode,$storeId,$orderLogArr);
                                }
                            }
                        }
                    }
                }
                if ($oneRecordFlag) {
                    if (isset($totalData['Entity']['ShipmentNbr']['Value']) && isset($totalData['Entity']['ShipVia']['Value']))
                    {
                        $shipmentNumber = $totalData['Entity']['ShipmentNbr']['Value'];
                        $shipViaCode = $totalData['Entity']['ShipVia']['Value'];
                        /**
                         * getting shipped line items and tracking numbers
                         */
                        if (isset($totalData['Entity']['Details']['ShipmentDetail'])) {
                            $k = 0;
                            $oneShipmentRecordFlag = false;
                            $shipments = array();
                            foreach ($totalData['Entity']['Details']['ShipmentDetail'] as $shipKey => $inventoryRes) {
                                if (!is_numeric($shipKey)) {
                                    $oneShipmentRecordFlag = true;
                                    break;
                                }
                                $inventoryRes = $this->xmlHelper->xml2array($inventoryRes);
                                if (isset($inventoryRes['InventoryID']['Value'])) {
                                    $shipments[$k]['InventoryId'] = $inventoryRes['InventoryID']['Value'];
                                    $shipments[$k]['ShippedQty'] = $inventoryRes['ShippedQty']['Value'];
                                    $shipments[$k]['OrderNbr'] = $inventoryRes['OrderNbr']['Value'];

                                    $oneTrackingNbrFlg = false;
                                    if(isset($totalData['Entity']['Packages']['ShipmentPackage']))
                                    {
                                        foreach ($totalData['Entity']['Packages']['ShipmentPackage'] as $trackKey => $track) {
                                            if (!is_numeric($trackKey)) {
                                                $oneTrackingNbrFlg = true;
                                                break;
                                            }
                                            $track = $this->xmlHelper->xml2array($track);
                                            $shipments[$k]['ShippedQty'] = $inventoryRes['ShippedQty']['Value'];
                                            $shipments[$k]['OrderNbr'] = $inventoryRes['OrderNbr']['Value'];
                                            $shipments[$k]['InventoryId'] = $inventoryRes['InventoryID']['Value'];
                                            if (isset($track['TrackingNumber']['Value']) && $track['TrackingNumber']['Value'] != '') {
                                                $shipments[$k]['TrackingNumber'] = $track['TrackingNumber']['Value'];
                                            } else {
                                                $shipments[$k]['TrackingNumber'] = '';
                                            }
                                            $k++;
                                        }
                                        if ($oneTrackingNbrFlg) {
                                            if (isset($totalData['Entity']['Packages']['ShipmentPackage']['TrackingNumber']['Value'])) {
                                                $shipments[$k]['TrackingNumber'] = $totalData['Entity']['Packages']['ShipmentPackage']['TrackingNumber']['Value'];
                                            } else {
                                                $shipments[$k]['TrackingNumber'] = '';
                                            }
                                            $k++;
                                        }
                                    }
                                }
                            }
                            if ($oneShipmentRecordFlag) {
                                /**
                                 * single lineItem
                                 */
                                if (isset($totalData['Entity']['Details']['ShipmentDetail']['InventoryID']['Value'])) {
                                    $shipments[$k]['InventoryId'] = $totalData['Entity']['Details']['ShipmentDetail']['InventoryID']['Value'];
                                    $shipments[$k]['ShippedQty'] = $totalData['Entity']['Details']['ShipmentDetail']['ShippedQty']['Value'];
                                    $shipments[$k]['OrderNbr'] = $totalData['Entity']['Details']['ShipmentDetail']['OrderNbr']['Value'];

                                    $oneTrackingNbrFlg = false;
                                    if(isset($totalData['Entity']['Packages']['ShipmentPackage']))
                                    {
                                        foreach ($totalData['Entity']['Packages']['ShipmentPackage'] as $trackKey => $track) {
                                            if (!is_numeric($trackKey)) {
                                                $oneTrackingNbrFlg = true;
                                                break;
                                            }
                                            $track = $this->xmlHelper->xml2array($track);
                                            $shipments[$k]['ShippedQty'] = $totalData['Entity']['Details']['ShipmentDetail']['ShippedQty']['Value'];
                                            $shipments[$k]['OrderNbr'] = $totalData['Entity']['Details']['ShipmentDetail']['OrderNbr']['Value'];
                                            $shipments[$k]['InventoryId'] = $totalData['Entity']['Details']['ShipmentDetail']['InventoryID']['Value'];
                                            if (isset($track['TrackingNumber']['Value']) && $track['TrackingNumber']['Value'] != '') {
                                                $shipments[$k]['TrackingNumber'] = $track['TrackingNumber']['Value'];
                                            } else {
                                                $shipments[$k]['TrackingNumber'] = '';
                                            }
                                            $k++;
                                        }
                                        if ($oneTrackingNbrFlg) {
                                            if (isset($totalData['Entity']['Packages']['ShipmentPackage']['TrackingNumber']['Value'])) {
                                                $shipments[$k]['TrackingNumber'] = $totalData['Entity']['Packages']['ShipmentPackage']['TrackingNumber']['Value'];
                                            } else {
                                                $shipments[$k]['TrackingNumber'] = '';
                                            }
                                        }
                                    }
                                }
                            }
                            if(count($shipments) > 0)
                            {
                                $this->createShipmentInMagento($shipments,$shipViaCode,$storeId,$orderLogArr,$orderLogArr);
                            }
                        }
                    }
                }
            }else {
                $txt = "Info : No order for Shipment";
                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                $orderLogArr['description'] = "No order for Shipment.";
                $orderLogArr['messages'] = "No order for Shipment.";
                $orderLogArr['customer_email'] = "";
                $orderLogArr['run_mode'] = 'Manual';
                $orderLogArr['status'] = 'success';
                $orderLogArr['sync_action'] = "syncToMagento";
                $orderLogArr['message_type'] = "success";
                $this->orderShipment->shipmentManualSync($orderLogArr);
            }
        }
        $txt = "Info : Shipment sync completed";
        $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
        $orderLogArr['description'] = "Shipment sync completed.";
        $orderLogArr['messages'] = "Shipment sync completed.";
        $orderLogArr['customer_email'] = "";
        $orderLogArr['run_mode'] = 'Manual';
        if(count($this->errorCheckInMagento) > 0)
        {
            $orderLogArr['status'] = 'error';
        }else{
            $orderLogArr['status'] = 'success';
        }
        $orderLogArr['sync_action'] = "syncToMagento";
        $orderLogArr['message_type'] = "success";
        $this->orderShipment->shipmentManualSync($orderLogArr);
    }

    /**
     * @param $shipments
     * @param $shipViaCode
     * @param $storeId
     * @param $orderLogArr
     * @throws LocalizedException
     */
    public function createShipmentInMagento($shipments,$shipViaCode,$storeId,$orderLogArr)
    {
        if(count($shipments) > 0)
        {
            try {
                $s = 0;
                foreach ($shipments as $shipData) {
                    if (isset($shipData['TrackingNumber']) && $shipData['TrackingNumber'] != '') {
                        echo $mgOrdrNbr = $this->resourceModelKemsOrder->getMagentoOrderIdAcuOrderId($shipData['OrderNbr'], $storeId);
			echo ", ";
                        $curentOrder = '';
                        $curentOrder = $this->orderFactory->create()->loadByIncrementId($mgOrdrNbr);
                        $skipOrderStatus = array("complete", "closed");
                        if (in_array(strtolower($curentOrder->getStatus()), $skipOrderStatus))
                            continue;

                        if ($curentOrder->canShip()) {
                            $shipProducts = array();
                            foreach ($curentOrder->getAllItems() as $_eachItem) {
                                foreach ($shipments as $shippedProduct) {
                                    if ($_eachItem->getSku() == str_replace(" ", "_", $shippedProduct['InventoryId'])) {
                                        if ($_eachItem->getParentItemId()) {
                                            $shipProducts[$_eachItem->getParentItemId()] = $shippedProduct['ShippedQty'];
                                        } else {
                                            $shipProducts[$_eachItem->getId()] = $shippedProduct['ShippedQty'];
                                        }
                                    }

                                }

                            }
                            $shipVia = $this->resourceModelKemsOrder->getMagentoMappingShipmentMethod($shipViaCode, $storeId);
                            if (isset($shipVia['magento_attr_code'])) {
                                $shipmentCarrierTitle = $shipVia['magento_attr_code'];
                                $shipmentCarrierCode = $shipVia['carrier'];
                            }
                            if (isset($shipmentCarrierCode) && !empty($shipmentCarrierCode)) {
                                $shipmentCarrierCode = 'custom';
                                $trackNumbers = '';
                                $shipmentTracking = array();
                                $tNum = array();
                                foreach ($shipments as $shipDatas) {

                                    $duplicateChk = $this->resourceModelKemsOrder->checkShipmentTrackNumber($shipDatas['TrackingNumber']);
                                    if ($duplicateChk == '' && !in_array($shipDatas['TrackingNumber'],$tNum))
                                    {

                                        $shipmentTracking[$s]['carrier_code'] = $shipmentCarrierCode;
                                        $shipmentTracking[$s]['title'] = $shipmentCarrierTitle;
                                        $shipmentTracking[$s]['number'] = $shipDatas['TrackingNumber'];
                                        $tNum[] = $shipDatas['TrackingNumber'];
                                        $trackNumbers .= $shipmentTracking[$s]['number'] . " -";
                                    }
                                    $s++;
                                }
                                if(count($shipmentTracking) > 0)
                                {
                                    $shipmentFactory = $this->objectManagerInterface->create('\Magento\Sales\Model\Order\ShipmentFactory');
                                    $shipFactory = $shipmentFactory->create($curentOrder, $shipProducts, $shipmentTracking);
                                    $shipFactory->save();
                                

                                foreach ($curentOrder->getAllItems() as $_eachItem) {

                                    foreach ($shipments as $shippedProduct) {
                                        if ($_eachItem->getSku() == str_replace(" ", "_", $shippedProduct['InventoryId'])) {
                                            if ($_eachItem->getParentItemId()) {
                                                $shipProducts[$_eachItem->getParentItemId()] = $shippedProduct['ShippedQty'];
                                            } else {
                                                $shipProducts[$_eachItem->getId()] = $shippedProduct['ShippedQty'];
                                            }
                                            if ($_eachItem->canShip()) {
                                                $tobeShipped = $_eachItem->getQtyShipped() + $shippedProduct['ShippedQty'];
                                                $_eachItem->setQtyShipped($tobeShipped);
                                                $_eachItem->save();
                                            }
                                        }

                                    }
                                }
                                $curentOrder->save();
                                if (!empty($trackNumbers)) {
                                    $txt = "Info : " . $mgOrdrNbr . " shipment created with tracking number " . $trackNumbers;
                                    $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                                    $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());;;
                                    $orderLogArr['order_id'] = $mgOrdrNbr;
                                    $orderLogArr['acumatica_order_id'] = $shipData['OrderNbr'];
                                    $orderLogArr['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    $orderLogArr['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    $orderLogArr['customer_email'] = "";
                                    $orderLogArr['sync_action'] = "syncToMagento";
                                    $orderLogArr['message_type'] = "success";
                                    $orderLogArr['description'] = $mgOrdrNbr . " shipment created with tracking number " . $trackNumbers;
                                    $this->invoiceCreation($mgOrdrNbr, $shipData['OrderNbr'], $orderLogArr, $storeId);
                                    $this->orderShipment->shipmentSyncSuccessLogs($orderLogArr);
                                }
			      }
                            }
                        } 
                        /*else if($curentOrder->canInvoice()){ 
                            $this->invoiceCreation($mgOrdrNbr, $shipData['OrderNbr'], $orderLogArr, $storeId);
                        }*/else {
                            if (!empty($orderId)) {
                                echo " Shipment already completed";
                                exit;
                            }
                        }
                    }
                }
            }catch (Exception $e)
            {
                $txt = "Error : " . $e->getMessage();
                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());;;
                $orderLogArr['order_id'] = $mgOrdrNbr;
                $orderLogArr['acumatica_order_id'] = $shipData['OrderNbr'];
                $orderLogArr['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                $orderLogArr['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                $orderLogArr['customer_email'] = "";
                $orderLogArr['sync_action'] = "syncToMagento";
                $orderLogArr['message_type'] = "error";
                $orderLogArr['long_message'] = $e->getMessage();
                $orderLogArr['description'] = $e->getMessage();
                $this->orderShipment->shipmentSyncSuccessLogs($orderLogArr);
                $this->errorCheckInMagento[] = 1;
            }
        }
    }
    /**
     * @param $storeId
     * @param $lastSyncDate
     * @param $orderLogArr
     * @param null $orderId
     */
    public function getPOShipments($storeId, $lastSyncDate, $orderLogArr, $orderId = NULL)
    {
        $getLastSyncDateByTimezone = $this->timeZone->date($lastSyncDate,null,true);
        $lastSyncDate = $getLastSyncDateByTimezone->format('m/d/Y H:i:s');
        if($storeId == 0){
            $scopeType = 'default';
        }else{
            $scopeType = 'stores';
        }

        $cookies = $this->endpointCookies;
        /*Soap Request parmeters*/
        $client = new AmconnectorSoap($this->endpointUrl, array(
            'cache_wsdl' => WSDL_CACHE_NONE,
            'cache_ttl' => 86400,
            'trace' => true,
            'exceptions' => true,
        ));

        $client->__setCookie('ASP.NET_SessionId', $cookies['asp_session_id']);
        $client->__setCookie('UserBranch', $cookies['userBranch']);
        $client->__setCookie('Locale', $cookies['locale']);
        $client->__setCookie('.ASPXAUTH', $cookies['aspx_auth']);

        /**
         * Here we need to write Individual Sync Code
         */
        $envelopeData = $this->helper->getEnvelopeData('GETPOSHIPMENTS');
        $txt = "Info : DropShip Shipment sync started";
        $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
        $XMLRequest = $envelopeData['envelope'];
        $XMLRequest = str_replace('{{LASTSYNCDATE}}', trim($lastSyncDate), $XMLRequest);

        //Send Order request to Acumatica
        $flag = '';
        $location = str_replace('?wsdl', '', $loginUrl);
        $requestString = "GISBMS30/Submit";
        $response = $client->__mySoapRequest($XMLRequest, $requestString, $location, $flag,NULL,1);

        $soapArray = array('SOAP-ENV:', 'SOAP:');
        $cleanXml = str_ireplace($soapArray, '', $response);
        $xml = simplexml_load_string($cleanXml);
        if (isset($xml->Body->Fault->faultstring) && is_object($xml->Body->Fault->faultstring))
        {
            /**
             * logs here for failure
             */
            $txt = "Error : " .json_encode($xml);
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);

        }else{
            $shipmentNumbers = array();
            $data = $xml->Body->GISBMS30SubmitResponse->SubmitResult;
            $totalData = $this->xmlHelper->xml2array($data);
            if(isset($totalData['Content']['Result']))
            {
                $shipmentNumbers[] = $totalData['Content']['Result']['ShipmentNbr']['Value'];

            }else if(isset($totalData['Content']))
            {
                foreach ($totalData['Content'] as $PoShipmentNumber)
                {
                    $PoShipmentNumber = $this->xmlHelper->xml2array($PoShipmentNumber);
                    if(isset($PoShipmentNumber['Result']['ShipmentNbr']['Value']) && $PoShipmentNumber['Result']['ShipmentNbr']['Value'] != '')
                    {
                        $shipmentNumbers[] = $PoShipmentNumber['Result']['ShipmentNbr']['Value'];
                    }
                }
            }
            // $shipmentNumbers = array();
            // $shipmentNumbers = array('SB063517','SB063550','SB063548');
            $shipmentsImport = 0;
            if(isset($shipmentNumbers) && !empty($shipmentNumbers))
            {

                foreach($shipmentNumbers as $singleShipmentNumber) {
                    try {
                        if (isset($singleShipmentNumber) && $singleShipmentNumber != '') {
                            $shipmentDetailsEenvelopeData = $this->helper->getEnvelopeData('GETPOSHIPMENTDETAILS');
                            $XMLRequestPoShipmentDetails = $shipmentDetailsEenvelopeData['envelope'];
                            $XMLRequestPoShipmentDetails = str_replace('{{SHIPNUMBER}}', trim($singleShipmentNumber), $XMLRequestPoShipmentDetails);

                            //Send Order request to Acumatica
                            $requestString = "GISBMS31/Submit";
                            $poShipmentResponse = $client->__mySoapRequest($XMLRequestPoShipmentDetails, $requestString, $location, $flag, NULL, 1);
                            $poCleanXml = str_ireplace($soapArray, '', $poShipmentResponse);
                            $poXml = simplexml_load_string($poCleanXml);
                            if (isset($poXml->Body->Fault->faultstring) && is_object($poXml->Body->Fault->faultstring)) {
                                /**
                                 * logs here for failure
                                 */
                                $txt = "Error : " . json_encode($poXml);
                                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                            } else {

                                $poShipmentData = $poXml->Body->GISBMS31SubmitResponse->SubmitResult;
                                $PoTotalData = $this->xmlHelper->xml2array($poShipmentData);
                                $shipments = array();
                                $i = 0;
                                if (isset($PoTotalData['Content']['Result'])) {
                                    if (isset($PoTotalData['Content']['Result']['InventoryID']['Value']) && $PoTotalData['Content']['Result']['InventoryID']['Value'] != '') {
                                        $shipments[$i]['InventoryId'] = trim($PoTotalData['Content']['Result']['InventoryID']['Value']);
                                        $shipments[$i]['ShippedQty'] = $PoTotalData['Content']['Result']['ReceiptQty']['Value'];
                                        $shipments[$i]['OrderNbr'] = $PoTotalData['Content']['Result']['SalesOrderNbr']['Value'];
                                        $shipments[$i]['TrackingNumber'] = $PoTotalData['Content']['Result']['TrackingNumber']['Value'];
                                        if(isset($PoTotalData['Content']['Result']['ShipVia']['Value']) && $PoTotalData['Content']['Result']['ShipVia']['Value'] != '')
                                        {
                                            $shipments[$i]['ShipVia'] = $PoTotalData['Content']['Result']['ShipVia']['Value'];
                                        }else{
                                            $shipments[$i]['ShipVia'] = '';
                                        }
                                    }
                                } else if (isset($PoTotalData['Content'])) {
                                    foreach ($PoTotalData['Content'] as $poRes)
                                    {
                                        if(trim($poRes->Result->InventoryID->Value) == "DROPSHIP FEE")
                                            continue;

                                        $shipments[$i]['InventoryId'] = trim($poRes->Result->InventoryID->Value);
                                        $shipments[$i]['ShippedQty'] = $poRes->Result->ReceiptQty->Value;
                                        $shipments[$i]['OrderNbr'] = $poRes->Result->SalesOrderNbr->Value;
                                        $shipments[$i]['TrackingNumber'] = $poRes->Result->TrackingNumber->Value;
                                        $shipments[$i]['ShipVia'] = $poRes->Result->ShipVia->Value;
                                        $i++;
                                    }
                                }
                                if (isset($shipments) && !empty($shipments)) {
                                    $s = 0;
                                    foreach ($shipments as $shipData) {
                                        if (isset($shipData['TrackingNumber']) && $shipData['TrackingNumber'] != '') {
                                            $mgOrdrNbr = str_replace("SB", "", $shipData['OrderNbr']);
                                            $curentOrder = '';
                                            $curentOrder = $this->orderFactory->create()->loadByIncrementId($mgOrdrNbr);
                                            if ($curentOrder->canShip()) {
                                                $shipProducts = array();
                                                foreach ($curentOrder->getAllItems() as $_eachItem)
                                                {
                                                    foreach ($shipments as $shippedProduct) {
                                                        if ($_eachItem->getSku() == str_replace(" ", "_", $shippedProduct['InventoryId'])) {
                                                            if ($_eachItem->getParentItemId()) {
                                                                $shipProducts[$_eachItem->getParentItemId()] = $shippedProduct['ShippedQty'];
                                                            } else {
                                                                $shipProducts[$_eachItem->getId()] = $shippedProduct['ShippedQty'];
                                                            }
                                                        }
                                                    }
                                                }
                                                $shipVia = $this->resourceModelKemsOrder->getMagentoMappingShipmentMethod($shipData['ShipVia'], $storeId);
                                                if (isset($shipVia['magento_attr_code'])) {
                                                    $shipmentCarrierTitle = $shipVia['magento_attr_code'];
                                                    $shipmentCarrierCode = $shipVia['carrier'];
                                                }
                                                if (isset($shipmentCarrierCode) && !empty($shipmentCarrierCode)) {
                                                    $shipmentCarrierCode = 'custom';
                                                    $trackNumbers = '';
                                                    foreach ($shipments as $shipDatas)
                                                    {
                                                        $duplicateChk = $this->resourceModelKemsOrder->checkShipmentTrackNumber($shipDatas['TrackingNumber']);
                                                        if ($duplicateChk == '') {

                                                            $shipmentTracking[$s]['carrier_code'] = $shipmentCarrierCode;
                                                            $shipmentTracking[$s]['title'] = $shipmentCarrierTitle;
                                                            $shipmentTracking[$s]['number'] = $shipDatas['TrackingNumber'];
                                                            $trackNumbers .= $shipmentTracking[$s]['number'] . " -";

                                                            $shipmentFactory = $this->objectManagerInterface->create('\Magento\Sales\Model\Order\ShipmentFactory');
                                                            $shipFactory = $shipmentFactory->create($curentOrder, $shipProducts, $shipmentTracking);
                                                            $shipFactory->save();
                                                        }
                                                        $s++;
                                                    }
                                                    foreach ($curentOrder->getAllItems() as $_eachItem) {

                                                        foreach ($shipments as $shippedProduct) {
                                                            if ($_eachItem->getSku() == str_replace(" ", "_", $shippedProduct['InventoryId'])) {
                                                                if ($_eachItem->getParentItemId()) {
                                                                    $shipProducts[$_eachItem->getParentItemId()] = $shippedProduct['ShippedQty'];
                                                                } else {
                                                                    $shipProducts[$_eachItem->getId()] = $shippedProduct['ShippedQty'];
                                                                }
                                                                if ($_eachItem->canShip()) {
                                                                    $tobeShipped = $_eachItem->getQtyShipped() + $shippedProduct['ShippedQty'];
                                                                    $_eachItem->setQtyShipped($tobeShipped);
                                                                    $_eachItem->save();
                                                                }
                                                            }

                                                        }
                                                    }
                                                    $curentOrder->save();
                                                    if (!empty($trackNumbers)) {
                                                        $txt = "Info : " . $mgOrdrNbr . " drop shipment created with tracking number " . $trackNumbers;
                                                        $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                                                        $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());;;
                                                        $orderLogArr['order_id'] = $mgOrdrNbr;
                                                        $orderLogArr['acumatica_order_id'] = $shipData['OrderNbr'];
                                                        $orderLogArr['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                                        $orderLogArr['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                                        $orderLogArr['customer_email'] = "";
                                                        $orderLogArr['sync_action'] = "syncToMagento";
                                                        $orderLogArr['message_type'] = "success";
                                                        $orderLogArr['description'] = $mgOrdrNbr . " drop shipment created with tracking number " . $shipData['TrackingNumber'];
                                                        $this->invoiceCreation($mgOrdrNbr, $shipData['OrderNbr'], $orderLogArr, $storeId);
                                                        $this->orderShipment->shipmentSyncSuccessLogs($orderLogArr);
                                                    }
                                                    $shipmentsImport++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }catch (Exception $e) {
                        $txt = "Error : " . $e->getMessage();
                        $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                        $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());;;
                        $orderLogArr['order_id'] = $mgOrdrNbr;
                        $orderLogArr['acumatica_order_id'] = $shipData['OrderNbr'];
                        $orderLogArr['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                        $orderLogArr['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                        $orderLogArr['customer_email'] = "";
                        $orderLogArr['sync_action'] = "syncToMagento";
                        $orderLogArr['message_type'] = "error";
                        $orderLogArr['description'] = $e->getMessage();
                        $this->orderShipment->shipmentSyncSuccessLogs($orderLogArr);
                    }
                }
            }
            if ($shipmentsImport == 0)
            {
                $txt = "Info : No order for Shipment";
                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            }
        }
        $this->acumaticaSessionLogout($client);
    }

    /**
     * @param $shipmentNbr
     * @param $this ->syncId
     * @param $this ->logViewFileName
     * @param $storeId
     * @return array
     */
    public function getShipmentDetails($shipmentNbr, $storeId)
    {
        $cookies = $this->endpointCookies;

        /*Soap Request parmeters*/
        $client = new AmconnectorSoap($this->endpointUrl, array(
            'cache_wsdl' => WSDL_CACHE_NONE,
            'cache_ttl' => 86400,
            'trace' => true,
            'exceptions' => true,
        ));
        if (empty($cookies))
        {
            $cookies = $this->clientHelper->login(array(), $this->endpointUrl);
        }
        $client->__setCookie('ASP.NET_SessionId', $cookies['asp_session_id']);
        $client->__setCookie('UserBranch', $cookies['userBranch']);
        $client->__setCookie('Locale', $cookies['locale']);
        $client->__setCookie('.ASPXAUTH', $cookies['aspx_auth']);


        $XMLRequest = '';
        $envelopeData = $this->helper->getEnvelopeData('GETSHIPMENTBYNUMBER');
        $XMLRequest = $envelopeData['envelope'];
        $XMLRequest = str_replace('{{SHIPMENTNUMBER}}', $shipmentNbr, $XMLRequest);
        /**
         * End Soap Request parmeters
         */
        $location = str_replace('?wsdl', '', $this->endpointUrl);
        $requestString = $envelopeData['envVersion'] . '/' . $envelopeData['envName'] . '/' . $envelopeData['methodName'];

        //Send  request to Acumatica
        $flag = '';
        $response = $client->__mySoapRequest($XMLRequest, $requestString, $location, $flag);
        $soapArray = array('SOAP-ENV:', 'SOAP:');
        $cleanXml = str_ireplace($soapArray, '', $response);
        $xml = simplexml_load_string($cleanXml);
        $data = $xml->Body->GetListResponse->GetListResult;
        $totalData = $this->xmlHelper->xml2array($data);

        $shipmentResults = array();
        if(isset($totalData['Entity']))
            $shipmentResults = $totalData['Entity'];

        $i = 0;
        $shipments = array();
        foreach ($shipmentResults['DocumentDetails'] as $shipResult) {
            if (isset($shipResult['InventoryID']['Value'])
                && $shipResult['InventoryID']['Value'] != '') {

                $shipments[$i]['InventoryId'] = $shipResult['InventoryID']['Value'];
                if (isset($shipResult['ShippedQty']['Value']))
                    $shipments[$i]['ShippedQty'] = $shipResult['ShippedQty']['Value'];

                $shipments[$i]['OrderNbr'] = $shipResult['OrderNbr']['Value'];
                foreach ($shipmentResults['TrackingDetails'] as $track) {
                    if (isset($track['TrackingNumber']) ) {
                        if(isset($track['TrackingNumber']['Value']) && $track['TrackingNumber']['Value'] != '') {
                            $shipments[$i]['TrackingNumber'] = $track['TrackingNumber']['Value'];
                        }else {
                            $shipments[$i]['TrackingNumber'] = '';
                        }
                    } else {
                        foreach ($track as $tracks) {
                            $trackNbr = (array)$tracks->TrackingNumber->Value;
                            $trackNbrVal = implode(",", $trackNbr);
                            $shipments[$i]['TrackingNumber'] = $trackNbrVal;
                            $shipments[$i]['ShippedQty'] = $shipResult['ShippedQty']['Value'];
                            $shipments[$i]['OrderNbr'] = $shipResult['OrderNbr']['Value'];
                            $shipments[$i]['InventoryId'] = $shipResult['InventoryID']['Value'];
                            $i++;
                        }
                    }
                }
            } else {
                foreach ($shipResult as $res) {

                    $inventoryData = (array)$res->InventoryID->Value;
                    $shippedData = (array)$res->ShippedQty->Value;
                    $orderData = (array)$res->OrderNbr->Value;
                    $inventoryId = implode(",", $inventoryData);
                    $shipQty = implode(",", $shippedData);
                    $orderNbr = implode(",", $orderData);
                    if ($inventoryId) {
                        $shipments[$i]['InventoryId'] = $inventoryId;
                        $shipments[$i]['ShippedQty'] = $shipQty;
                        $shipments[$i]['OrderNbr'] = $orderNbr;
                        foreach ($shipmentResults['TrackingDetails'] as $track) {
                            if (isset($track['TrackingNumber']) ) {
                                if(isset($track['TrackingNumber']['Value']) && $track['TrackingNumber']['Value'] != '') {
                                    $shipments[$i]['TrackingNumber'] = $track['TrackingNumber']['Value'];
                                }else {
                                    $shipments[$i]['TrackingNumber'] = '';
                                }
                            } else {
                                foreach ($track as $tracks) {
                                    $trackNbr = (array)$tracks->TrackingNumber->Value;
                                    $trackNbrVal = implode(",", $trackNbr);
                                    $shipments[$i]['TrackingNumber'] = $trackNbrVal;
                                }
                            }
                        }
                        $i++;
                    }

                }

            }
        }
        return $shipments;
    }

    public function invoiceCreation($magentoOrderId, $acumaticaOrderId, $orderLogArr, $storeId)
    {
        $XMLRequest = '';
        $envelopeData = $this->helper->getEnvelopeData('GETORDERBYID');

        $XMLRequest = $envelopeData['envelope'];
        $XMLRequest = str_replace('{{ORDERNUMBER}}', $acumaticaOrderId, $XMLRequest);

        $requestString = $envelopeData['envName'] . '/' . $envelopeData['envVersion'] . '/' . $envelopeData['methodName'];
        $configParameters = $this->amconnectorHelper->getConfigParameters($storeId);
        $response = $this->common->getAcumaticaResponse($configParameters, $XMLRequest, $this->endpointUrl, $requestString);
        $xml = $response;
        $data = $xml->Body->GetResponse->GetResult;
        $totalData = $this->xmlHelper->xml2array($data);

        $order = $this->objectManagerInterface->create('Magento\Sales\Model\Order')
            ->loadByAttribute('increment_id', $magentoOrderId);

        if (isset($totalData['Status']['Value'])) {
            $acumaticaOrderStatus = $totalData['Status']['Value'];
            if ($acumaticaOrderStatus == 'Completed') {

                if ($order->canInvoice()) {
                    // Create invoice for this order
                    $invoice = $this->objectManagerInterface->create('Magento\Sales\Model\Service\InvoiceService')->prepareInvoice($order);

                    // Make sure there is a qty on the invoice
                    if (!$invoice->getTotalQty()) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('You can\'t create an invoice without products.')
                        );
                    }

                    // Register as invoice item
                    $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
                    $invoice->register();

                    // Save the invoice to the order
                    $transaction = $this->objectManagerInterface->create('Magento\Framework\DB\Transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());

                    $transaction->save();

                    // Magento\Sales\Model\Order\Email\Sender\InvoiceSender
                    //$this->invoiceSender->send($invoice);

                    $order->addStatusHistoryComment(
                        __('Notified customer about invoice #%1.', $invoice->getId())
                    )
                        ->setIsCustomerNotified(false)
                        ->save();
                }
            } else {
                $magentoOrderStatusCode = $this->resourceModelKemsOrder->getMagentoOrderStatusMapping($acumaticaOrderStatus, $storeId);

                $magentoOrderStateCode = $this->resourceModelKemsOrder->getMagentoOrderStateCode($magentoOrderStatusCode);

                if ((isset($magentoOrderStatusCode) && isset($magentoOrderStateCode)) && ($magentoOrderStatusCode != '' && $magentoOrderStateCode != '')) {
                    $this->resourceModelKemsOrder->updateOrderStatusMapping($magentoOrderId, $magentoOrderStatusCode, $magentoOrderStateCode);
                }

            }
        }
    }

    public function getSyncedOrdersFromMagento($storeId)
    {
        $status = array('completed');
        $orders = array();
        $collection = $this->orderFactory->create()->getCollection()
            ->addFieldToFilter('status', array('nin' => $status))
            ->addFieldToFilter('store_id', array('eq' => $storeId))
            //->addFieldToFilter('entity_id', array('eq' => 1));
            ->addFieldToFilter('acumatica_order_id', array('neq' => ''));
        foreach ($collection as $order) {
            $orders[$order->getId()] = $order;
        }
        return $orders;
    }

    public function orderStatusMapping($storeId, $orderLogArr)
    {
        $ordersCollection = $this->getSyncedOrdersFromMagento($storeId);

        foreach ($ordersCollection as $mgOrder) {
            $magentoOrderNumer = $mgOrder->getIncrementId();
            $acumaticaOrderNbr = $mgOrder->getAcumaticaOrderId();
            $this->invoiceCreation($magentoOrderNumer, $acumaticaOrderNbr, $orderLogArr, $storeId);

        }
    }

    public function acumaticaSessionLogout($client)
    {
        try
        {
            $logout = $client->__soapCall('Logout', array());
        } catch(Exception $e)
        {
            echo $e->getMessage();
        }
    }
}
