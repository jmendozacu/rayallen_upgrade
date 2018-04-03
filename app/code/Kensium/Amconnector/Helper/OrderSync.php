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
use Kensium\Amconnector\Helper\AmconnectorSoap;
use Kensium\Amconnector\Helper\Data;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface as Logger;
use Kensium\Lib;

class OrderSync extends \Magento\Framework\App\Helper\AbstractHelper
{
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

    protected $syncDirection;

    protected $clientHelper;

    /**
     * @var Magento\Sales\Model\Order\Payment\TransactionFactory
     */
    protected $transactionFactory;

    const IS_LICENSE_INVALID = "Invalid";
    const IS_LICENSE_VALID = "Valid";
    const IS_TIME_VALID = "Valid";

    /**
     * @param Context $context
     * @param DateTime $date
     * @param Timezone $timeZone
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
        \Kensium\Amconnector\Helper\Data $amconnectorHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Model\TimeFactory $timeFactory,
        \Kensium\Synclog\Helper\Order $orderHelper,
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
        \Magento\Sales\Model\Order\Payment\TransactionFactory $transactionFactory,
        Lib\Common $common
    )
    {
        ini_set('default_socket_timeout', 1000);
        parent::__construct($context);
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->date = $date;
        $this->timeZone = $timeZone;
        $this->urlHelper = $urlHelper;
        $this->amconnectorHelper = $amconnectorHelper;
        $this->timeHelper = $timeHelper;
        $this->xmlHelper = $xmlHelper;
        $this->timeFactory = $timeFactory;
        $this->clientHelper = $clientHelper;
        $this->logger = $context->getLogger();
        $this->orderHelper = $orderHelper;
        $this->messageManager = $messageManager;
        $this->orderFactory = $orderFactory;
        $this->resourceModelKemsOrder = $resourceModelKemsOrder;
        $this->resourceModelSync = $resourceModelSync;
        $this->customerResourceModel = $customerResourceModel;
        $this->helperCustomer = $helperCustomer;
        $this->customerFactory = $customerFactory;
        $this->productFactory = $productFactory;
        $this->objectManagerInterface = $objectManagerInterface;
        $this->licenseResourceModel = $licenseResourceModel;
        $this->cardTypePaymentMethods = array("authorizenet_directpost");
        $this->branchCodes = array("1" => "RA", "2" => "JJ", "3" => "RA", "4" => "GN", "5" => "RA" );
        $this->orderType = array("1" => "WC", "2" => "WC", "3" => "WC", "4" => "WC", "5" => "WB" );
        //$this->branchCodes = array("0" => "MAIN", "1" => "MAIN", "2" => "EAST", "3" => "NORTH", "4" => "SOUTH", "5" => "WEST");
        $this->shippingTerm = 'WEBORDER';
        $this->invoiceSender = $invoiceSender;
        $this->transactionFactory = $transactionFactory;
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
    /**
     * @param $autoSync
     * @param $syncType
     * @param $entitySyncId
     * @param $storeId
     * @param $orderId
     * @param $failedOrderFlag
     */
    public function getOrderSync($autoSync, $syncType, $entitySyncId, $storeId, $orderId, $failedOrderFlag)
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
        $orderLogArr['sync_action'] = "syncToAcumatica";
        $orderLogArr['message_type'] = "";
        $orderLogArr['long_message'] = "";
        if (!empty($failedOrderFlag))
            $entity = 'failedOrder';
        else
            $entity = 'order';
        $this->entity = $entity;

        if (empty($storeId)) {
            $this->scopeMode = 'default';
        } else {
            $this->scopeMode = 'stores';
        }
        /*$this->orderType = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/ordertype', $this->scopeMode, $storeId);
        
        if (empty($this->orderType))
            $this->orderType = 'SO';
        $this->orderType = strtoupper($this->orderType);*/
        
        if (empty($entitySyncId)) {
            if (empty($storeId))
                $storeId = 1;
            $this->syncId = $this->resourceModelSync->getSyncId($entity, $storeId);
        } else {
            $this->syncId = $entitySyncId;
        }


        $envelopeData = $this->common->getEnvelopeData('CREATEORDER');
        $endpointData = $this->common->getEnvelopeData('GETSHIPMENTBYNUMBER');
        $defaultendPointData = $this->common->getEnvelopeData('GETORDERS');

        $lastSyncDate = $this->resourceModelSync->getLastSyncDate($this->syncId, $storeId);
        $timestamp = strtotime($lastSyncDate) - 25200; // reducing time for 2 hr
        $lastSyncShipmentDate = date('Y-m-d H:i:s', $timestamp);
        $lastSyncShipmentDate = str_replace(" ", "T", $lastSyncShipmentDate);
        $lastSyncDate = date('Y-m-d', strtotime($lastSyncDate));


        $url = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
        $this->syncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/syncdirection');
        $flag = "webservice";
	
        $webserviceName = $envelopeData['envName'];
        $loginUrl = $url . "Soap/" . $webserviceName . ".asmx?wsdl";
        $this->webServiceUrl = $loginUrl;
        $this->endpointUrl = $url ."entity/".$endpointData['envName'] . "/" . $endpointData['envVersion'] . "?wsdl";

        $this->logViewFileName = $this->amconnectorHelper->syncLogFile($this->syncId, $entity, '');
        $this->stopSyncFlg = 0;

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
            $orderLogArr['sync_action'] = "syncToAcumatica";
            $orderLogArr['message_type'] = "error";
            $orderLogArr['status'] = 'error';
            $this->orderHelper->orderManualSync($orderLogArr);
        } else {
            $txt = "Info : License verified successfully!";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);

            $txt = "Info : Server time verification is in progress";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);

            /*Time sync Check*/
            if ($this->resourceModelKemsOrder->StopSyncValue() == 1) {

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
                    $orderLogArr['sync_action'] = "syncToAcumatica";
                    $orderLogArr['message_type'] = "error";
                    $this->orderHelper->orderManualSync($orderLogArr);
                } else {
                    $txt = "Info : Server time is in sync.";
                    $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                    $orderLogArr['description'] = "Synced Order To Acumatica";
                    $orderLogArr['customer_email'] = "";
                    $orderLogArr['sync_action'] = "syncToAcumatica";
                    $orderLogArr['message_type'] = "success";


                    /*End Soap Request parmeters*/
                    if (!$orderId) {
                        $this->resourceModelSync->updateSyncAttribute($this->syncId, 'STARTED', $storeId);
                    }
                    $txt = "Info : Order manual sync initiated.";
                    $orderLogArr['job_code'] = $entity;
                    $orderLogArr['run_mode'] = 'Manual';
                    $orderLogArr['messages'] = 'Order manual sync initiated';
                    $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                    $orderLogArr['scheduled_at'] = $this->date->date('Y-m-d H:i:s', time());
                    $orderLogArr['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                    $orderLogArr['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                    $orderLogArr['status'] = 'success';
                    $orderLogArr['auto_sync'] = '';
                    $orderLogArr['store_id'] = $storeId;
                    $this->scheduledId = $this->orderHelper->orderManualSync($orderLogArr);
                    $orderLogArr['schedule_id'] = $this->scheduledId;
                    $this->orderHelper->orderManualSync($orderLogArr); //to enter the log details inside the cron_schedule table
                    $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                    if (!$orderId) {
                        $insertedId = $this->resourceModelSync->checkConnectionFlag($this->syncId, $entity, $storeId);
                    }

                    if ($this->resourceModelKemsOrder->StopSyncValue() == 1) {

                        $orderCollection = $this->getDataFromMagento($storeId, $lastSyncDate, $orderId, $failedOrderFlag);
                        if (!$orderId) {
                            $this->resourceModelSync->updateConnection($insertedId, 'PROCESS', $storeId);
                            $this->resourceModelSync->updateSyncAttribute($this->syncId, 'PROCESSING', $storeId);
                        }
                        $errorCount = 0;
                        $orderCnt = 0;
                        foreach ($orderCollection as $mgOrder) {
                            $XMLRequest = '';
                            $preAuthNumberData = '';
                            $acumaticaPaymentMethod = '';
                            $XMLRequest = $envelopeData['envelope'];
                            if ($this->resourceModelKemsOrder->StopSyncValue() == 1) {
                                $orderCnt++;
                                $orderDetails = $mgOrder->getData();
                                $orderLogArr['acumatica_order_id'] = $orderDetails['acumatica_order_id'];
                                $orderLogArr['order_id'] = $mgOrder->getIncrementId();
                                $orderLogArr['customer_email'] = $orderDetails['customer_email'];

                                $billingAddress = $mgOrder->getBillingAddress()->getData();
                                $shippingData = $mgOrder->getShippingAddress();
                                if (!$shippingData) {
                                    $shippingAddress = $mgOrder->getBillingAddress()->getData();
                                } else {
                                    $shippingAddress = $mgOrder->getShippingAddress()->getData();
                                }
                                $paymentDetails = $mgOrder->getPayment()->getData();
                                $paymentMethod = $paymentDetails['method'];
                                if ($paymentMethod === 'payflow_express') {
                                    $transactionInfo = $this->transactionFactory->create()->getCollection()->addFieldToFilter('order_id', $paymentDetails['parent_id'])->addFieldToFilter('txn_type', array('eq' => 'authorization'));
                                    $txnCollection = $transactionInfo->getData();
                                    $transactionInfo = $this->transactionFactory->create()->load($txnCollection[0]['transaction_id']);
                                    $txnInfo = $transactionInfo->getData();
                                    $payflowExpressId = $txnInfo['additional_information']['payflow_trxid'];
                                }
                                $productDetails = $mgOrder->getAllItems();
                                $magentoOrderShipVia = $orderDetails['shipping_description'];
                                $paypalMethod = 0;
                                if (strstr($paymentMethod, 'paypal')) {
                                    $paypalMethod = 1;
                                }
                                $billAddress1 = $billAddress2 = $shipAddress1 = $shipAddress2 = '';
                                $billingState = $shippingState = $billingStateCode = $shippingStateCode = '';
                                $billingStateCode = $this->resourceModelKemsOrder->getStateCodeById($billingAddress['region_id']);
                                $shippingStateCode = $this->resourceModelKemsOrder->getStateCodeById($shippingAddress['region_id']);
                                
                                if (!empty($billingStateCode)) {
                                    $billingState = '<Command xsi:type="Value"><Value>' . $billingStateCode . '</Value><LinkedCommand xsi:type="Field"><FieldName>State</FieldName><ObjectName>Billing_Address</ObjectName><Value>State</Value></LinkedCommand></Command>';
                                } else {
                                    $billingState = '<Command xsi:type="Value"><Value /><LinkedCommand xsi:type="Field"><FieldName>State</FieldName><ObjectName>Billing_Address</ObjectName><Value>State</Value></LinkedCommand></Command>';
                                }
                                if (!empty($shippingStateCode)) {
                                    $shippingState = '<Command xsi:type="Value"><Value>' . $shippingStateCode . '</Value><LinkedCommand xsi:type="Field"><FieldName>State</FieldName><ObjectName>Shipping_Address</ObjectName><Value>State</Value></LinkedCommand></Command>';
                                } else {
                                    $shippingState = '<Command xsi:type="Value"><Value /><LinkedCommand xsi:type="Field"><FieldName>State</FieldName><ObjectName>Shipping_Address</ObjectName><Value>State</Value></LinkedCommand></Command>';
                                }

                                $billAddress = explode("\n", $billingAddress['street']);
                                $shipAddress = explode("\n", $shippingAddress['street']);
                                $billAddress2 = $shipAddress2 = '';
                                if (isset($billAddress[0]))
                                    $billAddress1 = $billAddress[0];
                                if (isset($billAddress[1]))
                                    $billAddress2 = $billAddress[1];
                                if (isset($shipAddress[0]))
                                    $shipAddress1 = $shipAddress[0];
                                if (isset($shipAddress[1]))
                                    $shipAddress2 = $shipAddress[1];
                                $branchName = $this->branchCodes[$orderDetails['store_id']];
                                $storeNameData = explode("\n", $orderDetails['store_name']);
                                
                                if (isset($storeNameData[0])) {
                                    $storeName = $storeNameData[0];
                                    $external = $storeNameData[0];
                                }

                                $acumaticaShipVia = $this->resourceModelKemsOrder->getMappingShipmentMethod($magentoOrderShipVia, $storeId);

                                $customerRecord = '';
                                $mgCustomerId = $this->resourceModelKemsOrder->getCustomerIdByEmail($orderDetails['customer_email']);
                                if (isset($mgCustomerId) && !empty($mgCustomerId))
                                    $orderDetails['customer_id'] = $mgCustomerId;

                                if (empty($orderDetails['customer_id'])) {
                                    //Here need to mappiing condition
                                    $acumaticaCustomerId = $this->resourceModelKemsOrder->getGuestCustomerId($orderDetails['customer_email'], $storeId);

                                    if (empty($acumaticaCustomerId)) {
                                        $customerRecord = 'NEW';
                                        $result = $this->helperCustomer->getCustomerSync('INDIVIDUAL', 'MANUAL', $this->syncId, $scheduleId = NULL, $storeId, 'ORDER', $orderDetails);
                                        $acumaticaCustomerId = $this->resourceModelKemsOrder->getGuestCustomerId($orderDetails['customer_email'], $storeId);
                                        $orderLogArr['run_mode'] = 'Manual';
                                    }
                                } else {
                                    $acumaticaCustomerId = $this->resourceModelKemsOrder->getGuestCustomerId($orderDetails['customer_email'], $storeId);

                                    if (empty($acumaticaCustomerId)) {
                                        $acumaticaCustomerId = $this->resourceModelKemsOrder->getCustomerId($orderDetails['customer_id']);
                                        if (empty($acumaticaCustomerId)) {
                                            $customerRecord = 'NEW';
                                            $result = $this->helperCustomer->getCustomerSync('INDIVIDUAL', 'MANUAL', $this->syncId, $scheduleId =
                                                NULL, $storeId, 'ORDER', $orderDetails);
                                            $acumaticaCustomerId = $this->resourceModelKemsOrder->getCustomerId($orderDetails['customer_id']);
                                            $orderLogArr['run_mode'] = 'Manual';

                                        }
                                    }
                                }
                                $acumaticaCustomerId = trim($acumaticaCustomerId);
                                $paymentMethod = $paymentDetails['method'];
                                $paymentMethodName = $mgOrder->getPayment()->getMethodInstance()->getTitle();
                                if (isset($paymentDetails['additional_information'])) {
                                    if (isset($paymentDetails['additional_information']['auth_code'])) {
                                        $cardType = $paymentDetails['cc_type'];
                                        $paymentMethodName = $paymentMethod . "_" . $cardType;
                                    }
                                    if (isset($paymentDetails['additional_information']['profile_id'])) {
                                        $paymentMethodName = $paymentMethod . "_" . $paymentDetails['cc_type'];
                                    }
                                }

                                $paymentMethodResult = $this->resourceModelKemsOrder->getMappingPaymentMethod($paymentMethodName, $storeId);
                                $defaultAcumaticaPaymentMethod = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/defaultpaymentmethod');
                                $paypalEnable = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/paypalstatus');
                                //Payment Conditions here
                                $cardTypeTrans = 0;
                                if ((isset($paymentMethod) && !empty($paymentMethod)) && ((isset($paymentMethodResult['paymentmethod']) && !empty($paymentMethodResult['paymentmethod'])) && ($paymentMethodResult['paymentmethod'] != $defaultAcumaticaPaymentMethod))) {
                                    if (isset($paymentDetails['additional_information']['auth_code'])) {
                                        $cardType = $paymentDetails['cc_type'];
                                        $paymentCardType = $paymentMethod . "_" . $cardType;
                                        $cardNumber = '000000' . $paymentDetails['cc_last_4'];
                                    } else {
                                        $cardNumber = '000000' . '1111';
                                    }
                                    $acumaticaPaymentMethod = $paymentMethodResult['paymentmethod'];
                                    $cashAccount = $paymentMethodResult['cashaccount'];
                                    if (isset($paymentDetails['additional_information']['profile_id'])) {
                                        $paymentId = $paymentDetails['additional_information']['payment_id'];
                                        $customerProfileId = $paymentDetails['additional_information']['profile_id'];
                                        $paymentResultCreation = $this->orderPaymentCreationWithCIM($acumaticaCustomerId, $cashAccount, $customerProfileId, $paymentId, $acumaticaPaymentMethod, $storeId, $mgOrder->getIncrementId());
                                        $paymentIdentifier = $acumaticaPaymentMethod . ':****-****-****-' . $paymentDetails['cc_last_4'];
                                        $cimRequest = '<Command xsi:type="Value"><Value>' . $paymentIdentifier . '</Value><Commit>true</Commit><LinkedCommand xsi:type="Field"><FieldName>PMInstanceID!Descr</FieldName><ObjectName>CurrentDocument: 2</ObjectName><Value>CardAccountNo</Value><Commit>true</Commit></LinkedCommand></Command>';
                                        $cardTypeTrans = 1;
                                    } elseif (($paypalMethod == 1 && $paypalEnable == 1) || ($paypalMethod == 0 && strstr($paymentMethod, 'auth'))) {
                                        $paymentResultCreation = $this->orderPaymentCreationWithCard($acumaticaCustomerId, $cashAccount, $cardNumber, $acumaticaPaymentMethod, $storeId, $mgOrder->getIncrementId());
                                        $cardTypeTrans = 1;
                                    }
                                }

				//Akash
                                if($paymentMethod == 'onaccount') {
				$paymentResultCreation1 = $this->orderPaymentCreationWithCardOnAccount($acumaticaCustomerId, $cashAccount, "ON ACCOUNT", $acumaticaPaymentMethod, $storeId, $mgOrder->getIncrementId());
				}
				//Akash

                                if ($customerRecord == 'NEW') {
                                    $customerCreatedMessage = "Customer " . $orderDetails['customer_email'] . "  created in Acumatica with Id " . $acumaticaCustomerId;
                                    $txt = "Info : " . $customerCreatedMessage;
                                    $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);

                                }


                                //Product Details
                                $loop = 0;
                                $oldParentSku = '';
                                $condition = 0;
                                $productItems = '';
                                $parentItemId = 0;
                                /*Discount Code*/
                                $lastLineDiscountAmount = 0;
                                $lastItemId = 0;

                                if ($mgOrder->getDiscountAmount() != 0) {

                                    $lastLineDiscountAmount = $this->resourceModelKemsOrder->getOrderDiscountAmount($mgOrder->getId(), $mgOrder->getDiscountAmount());
                                    if ($lastLineDiscountAmount != 0) {
                                        $lastItemId = $this->resourceModelKemsOrder->getLastItemId($mgOrder->getId());
                                    }

                                }
                                /*Discount Code*/

                                /**
                                 * checking whether discount is applied on shipping amount
                                 */
                                $orderDiscountAmount = $orderTotalDiscountAmount = -1 * $mgOrder->getBaseDiscountAmount();
                                $orderItemDiscountAmt = 0;
                                foreach ($productDetails as $item) {
                                    $orderItemDiscountAmt = $orderItemDiscountAmt + $item->getBaseDiscountAmount();
                                }

                                $orderDiscountDifference = 0;
                                if ($orderItemDiscountAmt != $orderDiscountAmount) {
                                    $orderDiscountDifference = $orderDiscountAmount - $orderItemDiscountAmt;
                                }

                                $eachLineDiscountAmountValue = 0;
                                $sieItemEnabled = $this->scopeConfigInterface->getValue('amconnectorsync/configuratorsync/configuratorsync', $this->scopeMode, $storeId);
                                $productItems = '';
                                $totalItems = count($productDetails);
                                $lineItem = 0;                               
				                $defaultWarehouse = $this->scopeConfigInterface->getValue('amconnectorsync/defaultwarehouses/defaultwarehouse', $this->scopeMode, $storeId);
                                foreach ($productDetails as $product) {
                                    $sieItems = 0;
                                    $lineItem++;
                                    $qtyOrdered = 0;
                                    $productPrice = 0;
                                    $parentItemId = 0;
                                    $parentItemPrice = 0;
                                    $parentQtyOrdered = 0;
                                    if ($product->getParentItemId() != '') {
                                        $sieItems = 1;
                                    } elseif ($product->getProductType() == 'grouped') {
                                        $sieItems = 1;
                                    }
                                    if ($sieItemEnabled && $sieItems != 0) {
                                        $productOptions = $product->getProductOptions();

                                        $productItems .= '<Command xsi:type="NewRow"><ObjectName>Transactions</ObjectName></Command>';
                                        if (isset($productOptions['info_buyRequest']['product'])) {
                                            $parentItemId = $productOptions['info_buyRequest']['product'];
                                        }

                                        if (isset($productOptions['info_buyRequest']['super_product_config']['product_id'])) {
                                            $parentItemId = $productOptions['info_buyRequest']['super_product_config']['product_id'];
                                        }
                                        $bundleOptions = array();
                                        if (isset($productOptions['bundle_selection_attributes'])) {
                                            $bundleOptions = unserialize($productOptions['bundle_selection_attributes']);
                                        }
                                        if ($product->getParentItemId()) {
                                            $parentId = $product->getParentItemId();

                                            $itemDetails = $this->resourceModelKemsOrder->getParentProductId($orderDetails['entity_id'], $parentId);

                                            $parentItemId = $itemDetails[0]['product_id'];
                                            $parentItemPrice = $itemDetails[0]['price'];
                                        }

                                        if ($oldParentSku != $parentItemId) {
                                            $oldParentSku = $parentItemId;
                                            $condition = 1;
                                        } else {
                                            $condition = 0;
                                        }

                                        $parentItem = $this->productFactory->create()->load($parentItemId);
                                        if ($parentItem) {
                                            $parentSku = $parentItem->getSku();
                                            if ($condition == 1) {

                                                $masterSku = $parentSku . $condition . $orderDetails['entity_id'];
                                                $productItems .= '<Command xsi:type="Value"><Value>' . $product->getQtyOrdered() . '</Value><Commit>true</Commit><LinkedCommand xsi:type="Field"><FieldName>CompositeItemOrderQty</FieldName><ObjectName>CompositeFilter</ObjectName><Value>CompositeItemOrderQty</Value></LinkedCommand></Command>';
                                                $productItems .= '<Command xsi:type="Value"><Value>' . $parentSku . '</Value><LinkedCommand xsi:type="Field"><FieldName>InventoryID</FieldName><ObjectName>Transactions</ObjectName><Value>InventoryID</Value><Commit>true</Commit><LinkedCommand xsi:type="NewRow"><ObjectName>Transactions</ObjectName></LinkedCommand></LinkedCommand></Command>';
                                                $productItems .= '<Command xsi:type="Value"><Value>' . $masterSku . '</Value><LinkedCommand xsi:type="Field"><FieldName>UsrKNMasterID</FieldName><ObjectName>Transactions</ObjectName><Value>MasterID</Value></LinkedCommand></Command>';
                                                $productItems .= '<Command xsi:type="Value"><Value>' . $defaultWarehouse . '</Value><LinkedCommand xsi:type="Field"><FieldName>SiteID</FieldName><ObjectName>Transactions</ObjectName><Value>Warehouse</Value><Commit>true</Commit></LinkedCommand></Command>';



                                                /**
                                                 * For Bundle parent Item we need to send price as 0
                                                 */
                                                $productItems .= '<Command xsi:type="NewRow"><ObjectName>Transactions</ObjectName></Command>';
                                            }
                                            //Unit Price

                                            $productItems .= '<Command xsi:type="Value"><Value>' . $product->getSku() . '</Value><LinkedCommand xsi:type="Field"><FieldName>InventoryID</FieldName><ObjectName>Transactions</ObjectName><Value>InventoryID</Value><Commit>true</Commit><LinkedCommand xsi:type="NewRow"><ObjectName>Transactions</ObjectName></LinkedCommand></LinkedCommand></Command>';
                                            $productItems .= '<Command xsi:type="Value"><Value>' . $parentSku . '</Value><LinkedCommand xsi:type="Field"><FieldName>UsrKNCompositeInventory</FieldName><ObjectName>Transactions</ObjectName><Value>CompositeInventory</Value></LinkedCommand></Command>';
                                            $productItems .= '<Command xsi:type="Value"><Value>' . $masterSku . '</Value><LinkedCommand xsi:type="Field"><FieldName>UsrKNParentID</FieldName><ObjectName>Transactions</ObjectName><Value>ParentID</Value></LinkedCommand></Command>';
                                            $productItems .= '<Command xsi:type="Value"><Value>' . $defaultWarehouse . '</Value><LinkedCommand xsi:type="Field"><FieldName>SiteID</FieldName><ObjectName>Transactions</ObjectName><Value>Warehouse</Value><Commit>true</Commit></LinkedCommand></Command>';

                                            if ($product->getPrice() == 0) {
                                                $productPrice = $itemDetails [0]['price'];
                                            } else {
                                                $productPrice = $product->getPrice();
                                            }
                                            if ($parentItem->getTypeId() == "bundle") {

                                                if (isset($bundleOptions['price'])) {
                                                    $productsPrice = $bundleOptions['price'] / $bundleOptions['qty'];
                                                    $productPrice = number_format($productsPrice, 2, null, '');

                                                }
                                            } else {
                                                $qtyOrdered = $product->getQtyOrdered();
                                            }

                                            $qtyOrdered = $product->getQtyOrdered();
                                            $productItems .= '<Command xsi:type="Value"><Value>' . $qtyOrdered . '</Value><LinkedCommand xsi:type="Field"><FieldName>OrderQty</FieldName>';
                                            $productItems .= '<ObjectName>Transactions</ObjectName><Value>Quantity</Value><Commit>true</Commit></LinkedCommand></Command>';
                                            $productItems .= '<Command xsi:type="Value"><Value>' . $productPrice . '</Value><LinkedCommand xsi:type="Field"><FieldName>CuryUnitPrice</FieldName>';
                                            $productItems .= '<ObjectName>Transactions</ObjectName><Value>UnitPrice</Value></LinkedCommand></Command>';
					                        if(isset($parentId) && !empty($parentId))
                                            $parentQtyOrdered = $this->resourceModelKemsOrder->getParentItemQunatity($orderDetails['entity_id'], $parentId);

                                            $productItems .= '<Command xsi:type="Value"><Value>' . $parentQtyOrdered . '</Value><LinkedCommand xsi:type="Field"><FieldName>UsrKNMasterQty</FieldName><ObjectName>Transactions</ObjectName><Value>MasterQty</Value></LinkedCommand></Command>';
                                            
                                            //Customization - Disabled discount code feature as we are using Discount Details tab//
                                            /*Discount Code*/
                                            /*if ($mgOrder->getDiscountAmount() != 0) {
                                                $productItems .= '<Command xsi:type="Value"><Value>True</Value><LinkedCommand xsi:type="Field"><FieldName>ManualDisc</FieldName>';
                                                $productItems .= '<ObjectName>Transactions</ObjectName><Value>ManualDiscount</Value><Commit>true</Commit></LinkedCommand></Command>';
                                                if ($parentItem->getTypeId() == "bundle") {
                                                    $productPriceDiscount = $this->resourceModelKemsOrder->getParentProductDiscount($parentId);
                                                    //Discount Calculation//
                                                    if (isset($bundleOptions['price']) && $bundleOptions['price'] > 0) {
                                                        if (!empty($productPriceDiscount)) {
                                                            $bundleItemDiscount = ($productPriceDiscount * $bundleOptions['price']) / $parentItemPrice;
                                                            $productPriceDiscount = number_format($bundleItemDiscount, 2, null, '');
                                                        }
                                                    }

                                                }elseif($parentItem->getTypeId() == "configurable"){
                                                    $productPriceDiscount = $this->resourceModelKemsOrder->getParentProductDiscount($parentId);
                                                } else {
                                                    $productPriceDiscount = $product->getBaseDiscountAmount();
                                                }

                                                if ($orderDiscountDifference > 0) {
                                                    $orderSubtotal = $mgOrder->getSubtotal();
                                                    $lineSubtotal = $productPrice * $product->getQtyOrdered();

                                                    $percent = 100 - ((($orderSubtotal - $lineSubtotal) / $orderSubtotal) * 100);

                                                    $shareAmount = ($percent * $orderDiscountDifference) / 100;
                                                    $shareAmount = number_format($shareAmount, 4, null, '');

                                                    if ($lineItem == $totalItems) {
                                                        $productPriceDiscount = $productPriceDiscount + $orderDiscountDifference;
                                                    } else {
                                                        $orderDiscountDifference = $orderDiscountDifference - $shareAmount;
                                                        $productPriceDiscount = $productPriceDiscount + $shareAmount;
                                                    }

                                                }
                                                $eachLineDiscountAmountValue = $productPriceDiscount;
                                                $productItems .= '<Command xsi:type="Value"><Value>' . $eachLineDiscountAmountValue . '</Value><LinkedCommand xsi:type="Field"><FieldName>CuryDiscAmt</FieldName>';

                                                $productItems .= '<ObjectName>Transactions</ObjectName><Value>DiscountAmount</Value><Commit>true</Commit></LinkedCommand></Command>';
                                               
                                            }*/
                                            //Customization - Disabled discount code feature as we are using Discount Details tab//
                                            $loop++;
                                        }
                                    } elseif ($product->getProductType() != 'configurable') {

                                        if ($product->getProductType() == 'bundle') {
                                            continue;
                                        }
                                        $productItems .= '<Command xsi:type="NewRow"><ObjectName>Transactions</ObjectName></Command>';
                                        //Sku
                                        $sku = trim($product->getSku());
					if(substr($sku, -2) == '-Z') {
					   $sku = substr($sku, 0, -2);
				    	}
                                        $productItems .= '<Command xsi:type="Value"><Value>' . $sku . '</Value><Commit>true</Commit>';
                                        $productItems .= '<LinkedCommand xsi:type="Field"><FieldName>InventoryID</FieldName><ObjectName>Transactions</ObjectName>';
                                        $productItems .= '<Value>InventoryID</Value><Commit>true</Commit><LinkedCommand xsi:type="NewRow"><ObjectName>Transactions</ObjectName></LinkedCommand></LinkedCommand></Command>';

                                        //Unit Price
                                        $unitPrice = $product->getPrice();
                                        if ($unitPrice == 0) {
                                            $mgOrderId = $mgOrder->getId();
                                            $parentItemId = $product->getParentItemId();
                                            if ($parentItemId)
                                                $unitPrice = $this->resourceModelKemsOrder->getParentItemPrice($mgOrderId, $parentItemId);

                                        }
                                        //Free Item
                                        if ($unitPrice == 0) {
                                            $productItems .= '<Command xsi:type="Value"><Value>true</Value><LinkedCommand xsi:type="Field"><FieldName>IsFree</FieldName>';
                                            $productItems .= '<ObjectName>Transactions</ObjectName><Value>FreeItem</Value></LinkedCommand></Command>';
                                        }
                                       // $productItems .= '<Command xsi:type="Value"><FieldName>ManualPrice</FieldName><ObjectName>Transactions</ObjectName><Value>1</Value><Commit>true</Commit></Command>';

                                        $productItems .= '<Command xsi:type="Value"><Value>' . $unitPrice . '</Value><LinkedCommand xsi:type="Field"><FieldName>CuryUnitPrice</FieldName>';
                                        $productItems .= '<ObjectName>Transactions</ObjectName><Value>UnitPrice</Value></LinkedCommand></Command>';

                                        //Product Qunatity
                                        $productItems .= '<Command xsi:type="Value"><Value>' . $product->getQtyOrdered() . '</Value><LinkedCommand xsi:type="Field"><FieldName>OrderQty</FieldName>';
                                        $productItems .= '<ObjectName>Transactions</ObjectName><Value>Quantity</Value><Commit>true</Commit></LinkedCommand></Command>';

										//Customization - Disabled discount code feature as we are using Discount Details tab//
                                        /*Discount Code*/
                                        /*if ($mgOrder->getDiscountAmount() != 0) {

                                            $productItems .= '<Command xsi:type="Value"><Value>True</Value><LinkedCommand xsi:type="Field"><FieldName>ManualDisc</FieldName>';
                                            $productItems .= '<ObjectName>Transactions</ObjectName><Value>ManualDiscount</Value><Commit>true</Commit></LinkedCommand></Command>';

                                            $productPriceDiscount = $product->getBaseDiscountAmount();
                                            if ($orderDiscountDifference > 0) {
                                                $orderSubtotal = $mgOrder->getSubtotal();
                                                $lineSubtotal = $product->getBaseOriginalPrice() * $product->getQtyOrdered();

                                                $percent = 100 - ((($orderSubtotal - $lineSubtotal) / $orderSubtotal) * 100);

                                                $shareAmount = ($percent * $orderDiscountDifference) / 100;
                                                $shareAmount = number_format($shareAmount, 4, null, '');

                                                if ($lineItem == $totalItems) {
                                                    $productPriceDiscount = $productPriceDiscount + $orderDiscountDifference;
                                                } else {
                                                    $orderDiscountDifference = $orderDiscountDifference - $shareAmount;
                                                    $productPriceDiscount = $productPriceDiscount + $shareAmount;
                                                }

                                            }
                                            $eachLineDiscountAmountValue = $productPriceDiscount;
                                            $productItems .= '<Command xsi:type="Value"><Value>' . $eachLineDiscountAmountValue . '</Value><LinkedCommand xsi:type="Field"><FieldName>CuryDiscAmt</FieldName>';

                                            $productItems .= '<ObjectName>Transactions</ObjectName><Value>DiscountAmount</Value><Commit>true</Commit></LinkedCommand></Command>';
                                            
                                        }
                                        */
                                        //Customization - Disabled discount code feature as we are using Discount Details tab//
                                    }
                                }
                                // Discounted value

                                //Customization//
								if($orderDetails['store_id'] == 5 && $orderDetails['pr_number']) {
									$external = $orderDetails['pr_number'];
								} elseif(isset($orderDetails['ig_order_number'])) {
									$external = $orderDetails['ig_order_number'];
								} 
								//Customization//
								//Customization//
								//if (empty($this->orderType))
								//    $this->orderType = 'SO';
								//else
								   //$this->orderType = $this->orderType[$orderDetails['store_id']]; 
								   if($orderDetails['store_id'] == "1" || $orderDetails['store_id'] == "2" || $orderDetails['store_id'] == "3" || $orderDetails['store_id'] == "4")
									   $this->orderType = "WC";
								   else
									   $this->orderType = "WB";
								   //$this->orderType = $this->orderType[$orderDetails['store_id']]; 
								//Customization//
								
								
								
								$this->orderType = strtoupper($this->orderType);
								
                                $XMLRequest = str_replace("{{ORDERTYPE}}", $this->orderType, $XMLRequest);
                                $XMLRequest = str_replace("{{CUSTOMERID}}", $acumaticaCustomerId, $XMLRequest);
                                $XMLRequest = str_replace("{{MAGENTORDERNUMBER}}", '&lt;NEW&gt;', $XMLRequest);
                                $XMLRequest = str_replace("{{CUSTORDERNUMBER}}", $mgOrder->getIncrementId(), $XMLRequest);
                                $XMLRequest = str_replace("{{STORENAME}}", $storeName, $XMLRequest);
                                $XMLRequest = str_replace("{{EXTERNAL}}", $external, $XMLRequest);
                                //Customization//
                                $XMLRequest = str_replace("{{BRANCHCODE}}", $branchName, $XMLRequest);
                                
                                $XMLRequest = str_replace("{{LINEITEMS}}", $productItems, $XMLRequest);

								$discountDetails = '';
                                //Customization//
								if (strtolower($mgOrder->getCouponCode()) != 'jjshp' && $mgOrder->getDiscountAmount() != 0 && !empty($mgOrder->getCouponCode())) {
									$discountDetails .= '<Command xsi:type="NewRow"><ObjectName>DiscountDetails</ObjectName></Command><Command xsi:type="Value"><Value>'.strtoupper($mgOrder->getCouponCode()).'</Value><Commit>true</Commit><LinkedCommand xsi:type="Field"><FieldName>DiscountID</FieldName><ObjectName>DiscountDetails</ObjectName><Value>DiscountCode</Value><Commit>true</Commit><LinkedCommand xsi:type="NewRow"><ObjectName>DiscountDetails</ObjectName></LinkedCommand></LinkedCommand></Command>';
								}
								$XMLRequest = str_replace("{{DISCOUNTDETAILS}}", $discountDetails, $XMLRequest);
								//Customization//
								//$this->amconnectorHelper->writeLogToFile($this->logViewFileName, "XMLREQUEST: ".$XMLRequest);
								

                                //Name
                                $billingName = $billingAddress['firstname'] . " " . str_replace("<missing>", "", $billingAddress['lastname']);

                                $billingCompany = $billingAddress['company'];
                                if (empty($billingCompany))
                                    $billingCompany = $billingName;
                                //Name
                                $shippingName = $shippingAddress['firstname'] . ' ' . str_replace("<missing>", "", $shippingAddress['lastname']);

                                $shippingCompany = $shippingAddress['company'];
                                if (empty($shippingCompany))
                                    $shippingCompany = $shippingName;
                                /*Card type payment method*/
                                if (1 == $cardTypeTrans) {

                                    if (isset($paymentDetails['additional_information']['transaction_id'])) {
                                        $preAuthNumber = $paymentDetails['additional_information']['transaction_id'];
                                    } elseif (isset($paymentDetails['transaction_id'])) {
                                        $preAuthNumber = $paymentDetails['transaction_id'];
                                    }
                                    $preAuthorizedAmount = $orderDetails['grand_total'];
                                    //PaymentMethod
                                    if ($paymentMethod === 'payflow_express') {
                                        $preAuthNumber = $payflowExpressId;
                                    }

                                    if (isset($preAuthNumber) && $paymentDetails['base_amount_paid'] == 0) {
                                        $preAuthNumberData .= '<Command xsi:type="Value"><Value>' . $preAuthNumber . '</Value><Commit>true</Commit><LinkedCommand xsi:type="Field"><FieldName>PreAuthTranNumber</FieldName>';
                                        $preAuthNumberData .= '<ObjectName>CurrentDocument: 2</ObjectName><Value>PreAuthNbr</Value><Commit>true</Commit></LinkedCommand></Command>';
                                    }
                                    //PreAuthAmount
                                    $preAuthNumberData .= '<Command xsi:type="Value"><Value>' . $orderDetails['grand_total'] . '</Value><LinkedCommand xsi:type="Field"><FieldName>CuryCCPreAuthAmount</FieldName>';
                                    $preAuthNumberData .= '<ObjectName>CurrentDocument: 2</ObjectName><Value>PreAuthorizedAmount</Value></LinkedCommand></Command>';
                                    /*End Card type payment method*/
                                } else {

                                    $paymentMethodName = $mgOrder->getPayment()->getMethodInstance()->getTitle();
                                    $paymentMethodResult = $this->resourceModelKemsOrder->getMappingPaymentMethod($paymentMethodName, $storeId);
                                    if (isset($paymentMethodResult['paymentmethod'])) {
                                        $acumaticaPaymentMethod = $paymentMethodResult['paymentmethod'];
                                        $cashAccount = $paymentMethodResult['cashaccount'];
                                    }
                                }
                                
                                //Customization//
                                if($paymentMethod == 'onaccount') {
                                    $acumaticaPaymentMethod = 'ON_ACCOUNT';
                                }
								//Customization//
                                $billAddress1 = '<![CDATA['.htmlspecialchars(substr(strtoupper($billAddress1),0,50)).']]>';
                                $billAddress2 = '<![CDATA['.htmlspecialchars(substr(strtoupper($billAddress2),0,50)).']]>';
                                $shipAddress1 = '<![CDATA['.htmlspecialchars(substr(strtoupper($shipAddress1),0,50)).']]>';
                                $shipAddress2 = '<![CDATA['.htmlspecialchars(substr(strtoupper($shipAddress2),0,50)).']]>';
                                $billingCity = '<![CDATA['.htmlspecialchars(strtoupper($billingAddress['city'])).']]>';
                                $shippingCity = '<![CDATA['.htmlspecialchars(strtoupper($shippingAddress['city'])).']]>';
                                $billingTelephone = '<![CDATA['.htmlspecialchars($billingAddress['telephone']).']]>';
                                $shippingTelephone = '<![CDATA['.htmlspecialchars($shippingAddress['telephone']).']]>';
                                $billingPostcode =  '<![CDATA['.htmlspecialchars($billingAddress['postcode']).']]>';
                                $shippingPostcode =  '<![CDATA['.htmlspecialchars($shippingAddress['postcode']).']]>';
                                $shippingCompany =  '<![CDATA['.htmlspecialchars(strtoupper($shippingCompany)).']]>';
                                $billingCompany =  '<![CDATA['.htmlspecialchars(strtoupper($billingCompany)).']]>';
                                $shippingName =  '<![CDATA['.htmlspecialchars(strtoupper($shippingName)).']]>';
                                $billingName =  '<![CDATA['.htmlspecialchars(strtoupper($billingName)).']]>';

                                //Billing Details
                                $XMLRequest = str_replace("{{BILLINGCOMPANY}}", $billingCompany, $XMLRequest);
                                $XMLRequest = str_replace("{{BILLINGNAME}}", $billingName, $XMLRequest);
                                $XMLRequest = str_replace("{{BILLINGEMAIL}}", $billingAddress['email'], $XMLRequest);
                                $XMLRequest = str_replace("{{BILLINGPHONE}}", $billingTelephone, $XMLRequest);
                                $XMLRequest = str_replace("{{BILLINGADDRESS1}}", $billAddress1, $XMLRequest);
                                $XMLRequest = str_replace("{{BILLINGADDRESS2}}", $billAddress2 , $XMLRequest);
                                $XMLRequest = str_replace("{{BILLINGCITY}}", $billingCity, $XMLRequest);
                                $XMLRequest = str_replace("{{BILLINGCOUNTRY}}", $billingAddress['country_id'], $XMLRequest);
                                $XMLRequest = str_replace("{{BILLINGSTATE}}", $billingState, $XMLRequest);
                                $XMLRequest = str_replace("{{BILLINGPOSTALCODE}}",$billingPostcode, $XMLRequest);
                                //ShippingDetails
                                $XMLRequest = str_replace("{{SHIPPINGCOMPANY}}", $shippingCompany, $XMLRequest);
                                $XMLRequest = str_replace("{{SHIPPINGNAME}}", $shippingName, $XMLRequest);
                                $XMLRequest = str_replace("{{SHIPPINGEMAIL}}", $shippingAddress['email'], $XMLRequest);
                                $XMLRequest = str_replace("{{SHIPPINGPHONE}}", $shippingTelephone, $XMLRequest);
                                $XMLRequest = str_replace("{{SHIPPINGADDR1}}", $shipAddress1, $XMLRequest);
                                $XMLRequest = str_replace("{{SHIPPINGADDR2}}", $shipAddress2, $XMLRequest);
                                $XMLRequest = str_replace("{{SHIPPINGCITY}}", $shippingCity, $XMLRequest);
                                $XMLRequest = str_replace("{{SHIPPINGCOUNTRY}}", $shippingAddress['country_id'], $XMLRequest);
                                $XMLRequest = str_replace("{{SHIPPINGSTATE}}", $shippingState, $XMLRequest);
                                $XMLRequest = str_replace("{{SHIPPINGPOSTALCODE}}", $shippingPostcode, $XMLRequest);

                                //Payment Details
                                $XMLRequest = str_replace("{{PAYMENTMETHOD}}", $acumaticaPaymentMethod, $XMLRequest);
                                $XMLRequest = str_replace("{{CIMREQUEST}}", $cimRequest, $XMLRequest);
                                $XMLRequest = str_replace("{{PREAUTHNUMBER}}", $preAuthNumberData, $XMLRequest);
								
								//Customization//
								$shipAmount = '';
								$iGlobalTaxXMLRequest = '';
								if($orderDetails['ig_order_number']) {
									$shipTermsID = 'iGLOBAL';
									$shipAmount = $mgOrder->getShippingAmount() + $mgOrder->getTaxAmount();
									if($mgOrder->getTaxAmount() > 0) {
										$iGlobalTax = 'Y';
									} else {
										$iGlobalTax = 'N';
									}
									$iGlobalTaxXMLRequest .= '<Command xsi:type="Value"><Value>'.$iGlobalTax.'</Value><Commit>true</Commit><LinkedCommand xsi:type="Field"><FieldName>UsriGlobalTax</FieldName>';
									$iGlobalTaxXMLRequest .= '<ObjectName>Document</ObjectName><Value>IGlobalTax</Value></LinkedCommand></Command>';

								} else {
									$shipTermsID = 'WEB_SHIP';
									$shipAmount = $mgOrder->getShippingAmount();
								}
								$XMLRequest = str_replace("{{GLOBALTAX}}", $iGlobalTaxXMLRequest, $XMLRequest);
								//Customization//
								
								$shipViaXML = '';
								if($magentoOrderShipVia != '') {
									$shipViaXML .= '<Command xsi:type="Value"><Value>'.$acumaticaShipVia.'</Value><Commit>true</Commit><LinkedCommand xsi:type="Field"><FieldName>ShipVia</FieldName><ObjectName>CurrentDocument: 3</ObjectName><Value>ShipVia</Value><Commit>true</Commit></LinkedCommand></Command>';
								}
								$XMLRequest = str_replace("{{SHIPVIA}}", $shipViaXML, $XMLRequest);
                                $XMLRequest = str_replace("{{SHIPPINGTERMS}}", $shipTermsID, $XMLRequest);
                                $XMLRequest = str_replace("{{SHIPPINGAMOUNT}}", $shipAmount, $XMLRequest);
                                
                                $action = $envelopeData['methodName'];
                                $requestString = $envelopeData['envVersion'] . '/' . $envelopeData['envName'] . '/' . $envelopeData['methodName'];
								//$this->amconnectorHelper->writeLogToFile($this->logViewFileName, "CUSTID111: ".$acumaticaCustomerId);

                                //Send Order request to Acumatica
                                $flag = 'webservice';
                                try {
                                    $XMLRequest = str_replace("&", "&amp;", $XMLRequest);
                                    $configParameters = $this->amconnectorHelper->getConfigParametersOrder($storeId);
                                    $response = $this->common->getAcumaticaResponse($configParameters, $XMLRequest, $loginUrl, $action);
                                    $xml = $response;
                                    $msg = '';
                                    $curentOrder = $this->orderFactory->create()->loadByIncrementId($mgOrder->getIncrementId());
                                    if (is_object($xml->Body->SO301000SubmitResponse->SubmitResult)) {
                                        $acumaticaOrderNumber = $xml->Body->SO301000SubmitResponse->SubmitResult->Content->OrderSummary->OrderNbr->Value;
                                        $curentOrder->setAcumaticaOrderId($acumaticaOrderNumber);
                                        $curentOrder->setSyncOrderFailed(0);
                                        $curentOrder->save();
                                        $txt = "Info : " . "Magento Order " . $mgOrder->getIncrementId() . " synced to Acumatica with OrderId " . $acumaticaOrderNumber;
                                        $msg = "Magento Order " . $mgOrder->getIncrementId() . " synced to Acumatica with OrderId " . $acumaticaOrderNumber;
                                        $orderLogArr['auto_sync'] = "Complete";
                                        $orderLogArr['messages'] = "Synced";
                                        $orderLogArr['description'] = "Magento Order " . $mgOrder->getIncrementId() . " synced to Acumatica with OrderId " . $acumaticaOrderNumber;
                                        $orderLogArr['long_message'] = "";
                                        $orderLogArr['message_type'] = "success";
                                        $orderLogArr['order_id'] = $mgOrder->getIncrementId();
                                        $this->orderHelper->orderManualSync($orderLogArr);
                                        $orderLogArr['acumatica_order_id'] = $acumaticaOrderNumber;

                                        $this->orderHelper->orderSyncSuccessLogs($orderLogArr);
                                        $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                                            if (!empty($paymentDetails['last_trans_id'])) {
                                                $paymentReferenceId = $paymentDetails['last_trans_id'];
                                            } else {
                                                $paymentReferenceId = $mgOrder->getIncrementId();
                                            }
                                            if ($mgOrder->getGiftCardsAmount()) {
                                                $giftCardAmount = $mgOrder->getGiftCardsAmount();
                                                if ($giftCardAmount > 0) {
                                                    $cashAccount = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/giftcardcashaccount', $this->scopeMode, $storeId);
                                                    if ($cashAccount == '') {
                                                        $cashAccount = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/giftcardcashaccount', NULL, NULL);
                                                    }
                                                    $acumaticaPaymentMethod = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/giftcardpaymentmethod', $this->scopeMode, $storeId);
                                                    if ($acumaticaPaymentMethod == '') {
                                                        $acumaticaPaymentMethod = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/giftcardpaymentmethod', NULL, NULL);
                                                    }

                                                    if ($cashAccount != '' && $acumaticaPaymentMethod != '') {
                                                        $gift = 1;
							$giftCode =  unserialize($mgOrder->getGiftCards());
                                                        $this->orderPaymentCreation($acumaticaCustomerId, $acumaticaOrderNumber, $mgOrder->getGiftCardsAmount(), $giftCode[0]['c'], $acumaticaPaymentMethod, $cashAccount, $storeId, $mgOrder->getIncrementId(), $syncType, $gift);
                                                    }
                                                }
                                              }
                                                if ($paymentDetails['base_amount_paid'] != 0) {
                                                    $cardTypeTrans = 0;
                                                }
                                                if ($cardTypeTrans == 0) {
                                                   if (!empty($paymentDetails['last_trans_id'])) {
                                                        $paymentReferenceId = $paymentDetails['last_trans_id'];
                                                    } else {
                                                       $paymentReferenceId = $mgOrder->getIncrementId();
                                                    }
                                                    if ($orderDetails['grand_total'] > 0) {
                                                        $gift = 0;
                                                        $this->orderPaymentCreation($acumaticaCustomerId, $acumaticaOrderNumber, $orderDetails['grand_total'], $paymentReferenceId, $acumaticaPaymentMethod, $cashAccount, $storeId, $mgOrder->getIncrementId(), $syncType, $gift);
                                                    }
                                                }

                                    } else {
                                        $errorCount++;
                                        $acumaticaOrderNumber = $xml->Body->Fault->faultstring;
                                        $curentOrder->setSyncOrderFailed(1);
                                        $failedOrderCount = 0;
                                        $failedOrderCount = $curentOrder->getFailedRetryCount();
                                        $failedOrderCount = $failedOrderCount + 1;
                                        $curentOrder->setFailedRetryCount($failedOrderCount);
                                        $txt = "Error : " . $mgOrder->getIncrementId() . " is sync failed ";
                                        $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                                        $txt = "Error : " . $acumaticaOrderNumber;
                                        $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                                        $orderLogArr['store_id'] = $storeId;
                                        $orderLogArr['auto_sync'] = 'error';
                                        $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                                        $orderLogArr['description'] = "Order Sync Failed";
                                        $orderLogArr['long_message'] = "Magento Order " . $mgOrder->getIncrementId() . " sync to Acumatica with OrderId " . $acumaticaOrderNumber . "failed";
                                        $txt = $orderLogArr['long_message'];
                                        $orderLogArr['action'] = $syncType;
                                        $orderLogArr['sync_action'] = "syncToAcumatica";
                                        $orderLogArr['message_type'] = "error";
                                        $this->orderHelper->orderSyncSuccessLogs($orderLogArr);
                                        $orderLogArr['auto_sync'] = "Complete";
                                        $orderLogArr['messages'] = "Order Sync Failed";
                                        $orderLogArr['status'] = 'error';
                                        $this->orderHelper->orderManualSync($orderLogArr);
                                        $curentOrder->save();
				    	/** Failed order email function **/	
				    	$to = "joakley@rayallen.com, rchapman@rayallen.com, krobinson@rayallen.com, tspalding@rayallen.com, wgray@rayallen.com, sudhakark@kensium.com, sathishs@kensium.com";
                		    	$subject = 'RayAllen Failed Order: '.$mgOrder->getIncrementId();
				    	$emailMessage = "Magento Order " . $mgOrder->getIncrementId() . " failed to sync due to the following error \n " . $acumaticaOrderNumber . "failed";
                		    	$headers = "From: sales@rayallen.com";
                		    	//mail($to,$subject,$emailMessage,$headers, '-fwebmaster@jjdog.com');

                                        $msg = "Magento Order " . $mgOrder->getIncrementId() . " sync to Acumatica with OrderId " . $acumaticaOrderNumber . "failed";
                                    }
                                    if ($orderId != 'NULL' && $orderId != 0) {
                                        if ($syncType != 'Realtime') {
                                            echo $msg;
                                            exit;
                                        }
                                    }
                                } catch (Exception $ex) {
                                    $txt = "Error: " . $ex->getMessage();
                                    $orderLogArr['message_type'] = 'error';
                                    $orderLogArr['description'] = $ex->getMessage();
                                    $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                                }
                            } else {
                                $this->stopSyncFlg = 1;
                                break;
                            }
                        }
                        if ($this->resourceModelKemsOrder->StopSyncValue() == 1) {

                            if (empty($errorCount) && !$orderId) {
                                $this->resourceModelSync->updateConnection($insertedId, 'SUCCESS', $storeId);
                                $this->resourceModelSync->updateSyncAttribute($this->syncId, 'SUCCESS', $storeId);
                                $orderLogArr['status'] = 'success';
                            } else if (!$orderId) {
                                $this->resourceModelSync->updateSyncAttribute($this->syncId, 'ERROR', $storeId);
                                $this->resourceModelSync->updateConnection($insertedId, 'ERROR', $storeId);
                                $orderLogArr['status'] = 'error';
                                $this->orderHelper->orderManualSync($orderLogArr);
                            }
                            if (empty($orderCnt) && !$orderId) {
                                $txt = "Info : No new orders present in Magento to sync to Acumatica.";
                                $orderLogArr['description'] = 'No new orders present in Magento to sync to Acumatica';
                                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                                $orderLogArr['status'] = 'success';
                                $orderLogArr['message'] = "No new orders present in Magento to sync to Acumatica";
                                $orderLogArr['messages'] = "Order Sync completed successfully";
                                $orderLogArr['auto_sync'] = "Complete";
                                $this->orderHelper->orderManualSync($orderLogArr);
                                $this->orderHelper->orderSyncSuccessLogs($orderLogArr);
                            }
                        } else {
                            $this->stopSyncFlg = 1;
                        }

                    } else {
                        $this->stopSyncFlg = 1;
                    }
                }
            } else {
                $this->stopSyncFlg = 1;
            }
        }
        if ($this->stopSyncFlg == 1) {
            $txt = "Notice: Order sync stopped";
            $orderLogArr['status'] = 'notice';
            $orderLogArr['executed_at'] = '';
            $orderLogArr['finished_at'] = '';
            $orderLogArr['auto_sync'] = "Complete";
            $orderLogArr['messages'] = 'Order sync stopped';
            $orderLogArr['description'] = 'Order sync stopped';
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            $this->orderHelper->orderManualSync($orderLogArr);
            $this->resourceModelSync->updateSyncAttribute($this->syncId, 'NOTICE', $storeId);
            $this->resourceModelSync->updateConnection($insertedId, 'NOTICE', $storeId);
        }
        if ($licenseStatus == self::IS_LICENSE_VALID) {
            if ($this->stopSyncFlg != 1) {
                $acumaticaOrderId = '';
                if (!empty($orderId)) {
                    $curentOrder = $this->orderFactory->create()->loadByIncrementId($orderId);
                    $acumaticaOrderId = $curentOrder->getAcumaticaOrderId();
                    $orderId = $acumaticaOrderId;
                }
                if (empty($failedOrderFlag)) {
                    if (!empty($orderId)) {
                        $this->getDataFromAcumatica($storeId, $lastSyncShipmentDate, $orderLogArr, $orderId);
                    }
                    if (in_array($this->syncDirection, [1, 3])) {
                        $this->getOrdersFromAcumatica($storeId, $lastSyncDate, $orderLogArr, $orderId = NULL);
                    }
                    //$this->getPOShipments($storeId, $lastSyncDate,$orderLogArr, $orderId);
                    //$this->orderStatusMapping($storeId, $orderLogArr);
                }
            }
            $orderLogArr['description'] = 'Order Sync completed successfully';

            $txt = "Info : Sync process completed!";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            $this->resourceModelKemsOrder->enableSync();
            $orderLogArr['messages'] = 'Order Sync completed successfully';
        }
    }

    public function orderPaymentCreationWithCIM($customerId, $cashAccount, $customerProfileId, $paymentId, $orderPaymentMethod, $storeId, $magentoOrderNumber)
    {
        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl', $this->scopeMode, $storeId);

        $cookies = $this->webserviceCookies;
        $XMLRequest = '';
        $envelopeData = $this->common->getEnvelopeData('AUTHCIM');
        $XMLRequest = $envelopeData['envelope'];
        $url = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
        $webserviceName = $envelopeData['envName'];
        $loginUrl = $url . "Soap/" . $webserviceName . ".asmx?wsdl";
        $configParameters = $this->amconnectorHelper->getConfigParametersOrder($storeId);
        $action = $envelopeData['methodName'];

        /*End Soap Request parmeters*/

        $XMLRequest = str_replace('{{CASHACCOUNT}}', trim($cashAccount), $XMLRequest);
        $XMLRequest = str_replace('{{CUSTOMERPROFILEID}}', trim($customerProfileId), $XMLRequest);
        $XMLRequest = str_replace('{{CUSTOMERID}}', trim($customerId), $XMLRequest);
        $XMLRequest = str_replace('{{PAYMENTID}}', trim($paymentId), $XMLRequest);
        $XMLRequest = str_replace('{{PAYMENTMETHOD}}', trim($orderPaymentMethod), $XMLRequest);

		//$this->amconnectorHelper->writeLogToFile($this->logViewFileName, "CIM CUSTOMERID: ".trim($customerId));
		$xml = $this->common->getAcumaticaResponse($configParameters, $XMLRequest, $loginUrl, $action);
		//$this->amconnectorHelper->writeLogToFile($this->logViewFileName,"RESPONSE::::: ". $xml);
		$orderArray['store_id'] = $storeId;
        $orderArray['schedule_id'] = $this->syncId;
        $orderArray['created_at'] = $this->date->date('Y-m-d H:i:s', time());
        $orderArray['order_id'] = $magentoOrderNumber;
        $orderArray['acumatica_order_id'] = '';

        $orderArray['action'] = '';
        $orderArray['customer_email'] = '';
        $orderArray['sync_action'] = 'syncToMagento';

        if (is_object($xml->Body->Fault->faultstring)) {
            $errorResponse = $xml->Body->Fault->faultstring;
            $orderArray['messageType'] = "Error";
            $orderArray['long_message'] = $xml;
            if (strpos($errorResponse, 'is already used in another customer payment') !== false) {
                return true;
            } else {
                $txt = $this->date->date('Y-m-d H:i:s', time()) . " : Error : " . $errorResponse;
                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                return false;
            }
        } else {
            $orderArray['description'] = ' Payment Created';
            $txt = $this->date->date('Y-m-d H:i:s', time()) . " : Info : Customer Payment method with payment method " . $orderPaymentMethod . " created for order#" . $magentoOrderNumber;
            $orderArray['messageType'] = "Success";
            return true;
        }

    }

    public function orderPaymentCreationWithCardOnAccount($customerId, $cashAccount, $cardNumber, $orderPaymentMethod, $storeId, $magentoOrderNumber)
    {
        $XMLRequest = '';
        
        $XMLRequest .= '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><Put xmlns="http://www.acumatica.com/entity/KemsConfig/6.00.001/"><entity xsi:type="CustomerPaymentMethod"><ID xsi:nil="true" /><Delete>false</Delete><ReturnBehavior>All</ReturnBehavior><Active xsi:nil="true" /><CardAccountNo><Value>' . $cardNumber . '</Value></CardAccountNo><CashAccount><Value>' . $cashAccount . '</Value></CashAccount><CustomerID><Value>'.$customerId.'</Value></CustomerID><CustomerProfileID xsi:nil="true" /><Details xsi:nil="true" /><InstanceID xsi:nil="true" /><PaymentMethod><Value>' . $orderPaymentMethod . '</Value></PaymentMethod><ProcCenterID xsi:nil="true" /></entity></Put></soap:Body></soap:Envelope>';

        //Send Customer Payment  request to Acumatica
	//mail("akashc@kensium.com","Request - ".$cardNumber,$XMLRequest);
        $requestType = 'Put';
       $url = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
       $loginUrl = $this->common->getBasicConfigUrl($url);
       $action = "KemsConfig/6.00.001/Put";
       $configParameters = $this->amconnectorHelper->getConfigParameters($storeId);
       $xml = $this->common->getAcumaticaResponse($configParameters, $XMLRequest, $loginUrl, $action);        
       //print_r($xml);
        $data = $xml->Body->PutResponse->PutResult;
        $totalData = $this->xmlHelper->xml2array($data);

        if (isset($totalData['CardAccountNo']['Value']) && $totalData['CardAccountNo']['Value'] != '') {
            $txt = "Info : Payment Created";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            return true;
        } else {
            $txt = "Error : Error in payment creation";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            return false;
        }

    }

    public function orderPaymentCreationWithCard($customerId, $cashAccount, $cardNumber, $orderPaymentMethod, $storeId, $magentoOrderNumber)
    {
        $XMLRequest = '';
        $XMLRequest .= '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">';
        $XMLRequest .= '<soap:Body><Put xmlns="http://www.acumatica.com/entity/KemsConfig/6.00.001/">';
        $XMLRequest .= '<entity xsi:type="CustomerPaymentMethod"><ID xsi:nil="true" /><Delete>false</Delete>';
        $XMLRequest .= '<Active xsi:nil="true" /><CardAccountNo xsi:nil="true" />';
        //Cash Account
        $XMLRequest .= '<CashAccount><Value>' . $cashAccount . '</Value><HasError>false</HasError></CashAccount>';
        //CustomerId
        $XMLRequest .= '<CustomerID><Value>' . $customerId . '</Value><HasError>false</HasError></CustomerID>';
        $XMLRequest .= '<Details><CustomerPaymentMethodDetail><ID xsi:nil="true" /><Delete>false</Delete>';
        //CardNumber
        $XMLRequest .= '<Description><Value>CCDNUM</Value><HasError>false</HasError></Description>';
        $XMLRequest .= '<Name xsi:nil="true" /><Value><Value>' . $cardNumber . '</Value><HasError>false</HasError></Value>';
        $XMLRequest .= '</CustomerPaymentMethodDetail>';


        $XMLRequest .= '</Details><InstanceID xsi:nil="true" />';
        //PaymentMethod
        $XMLRequest .= '<PaymentMethod><Value>' . $orderPaymentMethod . '</Value><HasError>false</HasError></PaymentMethod>';
        /* //Processcenter
        $XMLRequest .= '<ProcCenterID><Value>AUTHDOTNET</Value><HasError>false</HasError></ProcCenterID>';*/
        $XMLRequest .= '</entity></Put>';
        $XMLRequest .= '</soap:Body></soap:Envelope>';


        //Send Customer Payment  request to Acumatica

        $requestType = 'Put';
	//echo $XMLRequest;
        $xml = $this->clientHelper->getAcumaticaResponseDefault($XMLRequest, $requestType, $storeId, NULL);
	//print_r($xml);
        $data = $xml->Body->PutResponse->PutResult;
        $totalData = $this->xmlHelper->xml2array($data);

        if (isset($totalData['CardAccountNo']['Value']) && $totalData['CardAccountNo']['Value'] != '') {
            $txt = "Info : Payment Created";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            return true;
        } else {
            $txt = "Error : Error in payment creation";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            return false;
        }

    }

    /**
     * @param null $lastSyncDate
     * @param null $orderId ,
     *
     * @return array
     */
    public function getDataFromMagento($storeId, $lastSyncDate, $orderId, $failedOrderFlag)
    {
        $numberOfRetrails = 0;
        $failedOrdersEnabled = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/retryfailedorder', $this->scopeMode, $storeId);
        if ($failedOrdersEnabled) {
            $numberOfRetrails = 0;
            $numberOfRetrailsVal = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/retryfailedorder', $this->scopeMode, $storeId);
            if ($numberOfRetrailsVal && is_int($numberOfRetrailsVal)) {
                $numberOfRetrails = $numberOfRetrailsVal;
            }
            if ($numberOfRetrails == 0)
                $numberOfRetrails = 3;
        }
        $orderStaus = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/orderstatuses', $this->scopeMode, $storeId);

        if (!empty($orderStaus)) {
            $status = explode(",", $orderStaus);
        } else {
            $status = array('processing');
        }

        if ($lastSyncDate != NULL && ($failedOrderFlag == 'NULL' || $failedOrderFlag == 0) && ($orderId == 'NULL' || $orderId == 0)) {
            $collection = $this->orderFactory->create()->getCollection()
                ->addFieldToFilter('status', array('in' => $status))
                ->addFieldToFilter('store_id', array('eq' => $storeId))
                //->addFieldToFilter('entity_id', array('gteq' => 37))
                ->addFieldToFilter('acumatica_order_id', array('eq' => ''));
            if ($numberOfRetrails != 0)
                $collection->addFieldToFilter('failed_retry_count', array('lteq' => $numberOfRetrails));
            else {
                $collection->addFieldToFilter('updated_at', array('gteq' => $lastSyncDate));
                $collection->addFieldToFilter('sync_order_failed', array('eq' => 0));
            }
        }
        if ($orderId != 'NULL' && $orderId != 0) {
            $collection = $this->orderFactory->create()->getCollection()->addFieldToFilter('increment_id', $orderId)
                ->addFieldToFilter('acumatica_order_id', array('eq' => ''));
        }

        if ($failedOrderFlag != 'NULL' && $failedOrderFlag != 0) {
            $failedOrderId = $this->scopeConfigInterface->getValue('kemssync/ordersync/orderid', $this->scopeMode, $storeId);
            $numberofDays = $this->scopeConfigInterface->getValue('amconnectorsync/failedordersync/failedorderdays', $this->scopeMode, $storeId);
            if (!empty($numberofDays))
                $lastSyncDate = $this->date->date("Y-m-d", strtotime("-" . $numberofDays . " day"));
            $collection = $this->orderFactory->create()->getCollection()
                //->addFieldToFilter('updated_at', array('gteq' => $lastSyncDate))
                ->addFieldToFilter('status', array('in' => $status))
                ->addFieldToFilter('store_id', array('eq' => $storeId))
                ->addFieldToFilter('acumatica_order_id', array('eq' => ''))
                ->addFieldToFilter('sync_order_failed', array('eq' => 1));
            if ($failedOrderId)
                $collection->addFieldToFilter('entity_id', array('gteq' => $failedOrderId));
        }

        $orders = array();
        foreach ($collection as $order) {
            $orders[$order->getId()] = $order;
        }
        return $orders;
    }

    public function orderPaymentCreation($customerId, $orderNumber, $orderTotal, $magentoOrderNumber, $paymentMethod, $cashAccount, $storeId, $magentoNumber, $syncType)
    {
	//mail("akashc@kensium.com","Payment Method - ".$magentoOrderNumber,$paymentMethod);	
	if($paymentMethod == "IGLOBAL" || $paymentMethod == "ON_ACCOUNT") { return 1;}
        /*End Soap Request parmeters*/
        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl', $this->scopeMode, $storeId);

        $cookies = $this->endpointCookies;
        $XMLRequest = '';
        $envelopeData = $this->common->getEnvelopeData('CREATEPAYMENT');

        $XMLRequest = $envelopeData['envelope'];
        $url = $this->endpointUrl;

        $orderLogArr['schedule_id'] = $this->scheduledId;
        $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());
        $orderLogArr['order_id'] = $magentoNumber;
        $orderLogArr['acumatica_order_id'] = $orderNumber;
        $orderLogArr['sync_action'] = 'syncToAcumatica';
        $orderLogArr['run_mode'] = $syncType;
        $orderLogArr['store_id'] = $storeId;


        //Send Customer Payment  request to Acumatica
        $XMLRequest = str_replace('{{CASHACCOUNT}}', trim($cashAccount), $XMLRequest);
        $XMLRequest = str_replace('{{ORDERTYPE}}', $this->orderType, $XMLRequest);
        $XMLRequest = str_replace('{{CUSTOMERID}}', trim($customerId), $XMLRequest);
        $XMLRequest = str_replace('{{ORDERTOTAL}}', $orderTotal, $XMLRequest);
        $XMLRequest = str_replace('{{ORDERNUMBER}}', $orderNumber, $XMLRequest);
        $XMLRequest = str_replace('{{PAYMENTMETHOD}}', trim($paymentMethod), $XMLRequest);
        $XMLRequest = str_replace('{{PAYMENTREFERENCE}}', trim($magentoOrderNumber), $XMLRequest);
        $flag = '';
        $requestString = $envelopeData['envName'] . '/' . $envelopeData['envVersion'] . '/' . $envelopeData['methodName'];
        $XMLRequest = str_replace("&", "&amp;", $XMLRequest);
	//mail("akashc@kensium.com","Payment Creation Request - ".$magentoNumber,$XMLRequest);
        $configParameters = $this->amconnectorHelper->getConfigParameters($storeId);
        $response = $this->common->getAcumaticaResponse($configParameters, $XMLRequest, $url, $requestString);
        $xml = $response;
        if (isset($xml->Body->Fault->faultstring) && is_object($xml->Body->Fault->faultstring)) {

            $txt = "Info : " . 'Payment reference not created  for Acumatica Order ' . $orderNumber;
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            $orderLogArr['message_type'] = 'error';
            $orderLogArr['description'] = 'Payment reference not created  for Acumatica Order ' . $orderNumber;
            $orderLogArr['long_message'] = $xml->Body->Fault->faultstring;
            $this->orderHelper->orderSyncSuccessLogs($orderLogArr);
            return 0;
        } else if (isset($xml->Body->PutResponse->PutResult) && is_object($xml->Body->PutResponse->PutResult)) {
            $paymentId = $xml->Body->PutResponse->PutResult->ReferenceNbr->Value;
            $acumaticaPamentData = $paymentId;
            //Save payment reference to db-sales_order tbl
            $curentOrder = $this->orderFactory->create()->loadByIncrementId($magentoNumber);
            if (empty($gift)) {
                $curentOrder->setAcumaticaPaymentId($acumaticaPamentData);
            } else {
                $curentOrder->setAcumaticaGiftcardPaymentId($acumaticaPamentData);
            }
            $curentOrder->save();
            $txt = "Info : " . 'Payment reference  ' . $acumaticaPamentData . ' created  for Acumatica Order ' . $orderNumber;

            $orderLogArr['message_type'] = 'success';
            $orderLogArr['description'] = 'Payment reference  ' . $acumaticaPamentData . ' created  for Acumatica Order ' . $orderNumber;

            $this->orderHelper->orderSyncSuccessLogs($orderLogArr);
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            return 1;
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
        $XMLRequest = '';
        if (!empty($orderId)) {
            $envelopeData = $this->common->getEnvelopeData('GETORDERBYID');
            $txt = "Info : Shipment sync started";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
            $XMLRequest = $envelopeData['envelope'];
            $XMLRequest = str_replace('{{ORDERNUMBER}}', $orderId, $XMLRequest);
        }
        if (!empty($orderId)) {
            $flag = '';
            $requestString = $envelopeData['envName'] . '/' . $envelopeData['envVersion'] . '/' . $envelopeData['methodName'];
            $configParameters = $this->amconnectorHelper->getConfigParameters($storeId);
            $response = $this->common->getAcumaticaResponse($configParameters, $XMLRequest, $this->endpointUrl, $requestString);
        }
        $xml = $response;
        if (!empty($orderId)) {
            $data = $xml->Body->GetResponse->GetResult;
        }
        $totalData = $this->xmlHelper->xml2array($data);


        $shipmentResult = array();
        $shipmentsResData = array();
        $shipmentsData = array();
        $shipVia = array();
        $i = 0;
        $j = 0;

        if (!empty($orderId)) {
            try {
                if (isset($totalData['Shipments']['SalesOrderShipment'])) {
                    if (isset($totalData['Shipments']['SalesOrderShipment']['ShipmentNbr']['Value'])) {
                        /* if(isset($totalData['Shipments']['SalesOrderShipment']['ShipmentType']['Value']) && $totalData['Shipments']['SalesOrderShipment']['ShipmentType']['Value'] != "Drop-Shipment")
                         {*/
                        $shipmentNbr = $totalData['Shipments']['SalesOrderShipment']['ShipmentNbr']['Value'];
                        if (isset($totalData['ShipVia']['Value'])) {
                            $shipVia = $totalData['ShipVia']['Value'];
                        }
                        if (isset($shipmentNbr) && $shipVia) {
                            $shipmentsResData[$j]['ShipmentNbr'] = $shipmentNbr;
                            $shipmentsResData[$j]['ShipVia'] = $shipVia;
                        }
                        /*}*/
                    } else {
                        if (isset($totalData['Shipments']['SalesOrderShipment'])) {
                            foreach ($totalData['Shipments']['SalesOrderShipment'] as $multiShips) {
                                /* if(isset($multiShips->ShipmentType->Value) && $multiShips->ShipmentType->Value != "Drop-Shipment")
                                 {*/
                                $shipmentNbr = $multiShips->ShipmentNbr->Value;
                                if (isset($totalData['ShipVia']['Value'])) {
                                    $shipVia = $totalData['ShipVia']['Value'];
                                }
                                if (isset($shipmentNbr) && $shipVia) {
                                    $shipmentsResData[$j]['ShipmentNbr'] = $shipmentNbr;
                                    $shipmentsResData[$j]['ShipVia'] = $shipVia;
                                }
                                /* }*/
                            }
                        }
                    }
                } else {
                    if (isset($totalData['Shipments']['SalesOrderShipment']))
                        foreach ($totalData['Shipments']['SalesOrderShipment'] as $shipResult) {
                            $shipmentsData = (array)$shipResult->ShipmentNbr->Value;

                            $shipViaData = (array)$shipResult->ShipVia->Value;

                            $shipmentNbr = implode(",", $shipmentsData);
                            $shipVia = implode(",", $shipViaData);
                            $shipmentsResData[$j]['ShipmentNbr'] = $shipmentNbr;
                            $shipmentsResData[$j]['ShipVia'] = $shipVia;
                            $j++;
                        }
                }
	
                if (empty($shipmentsResData) && !empty($orderId)  /*($orderId != 'NULL' && $orderId != 0)*/) {
                    echo " No updates";
                    exit;
                }
            } catch (Exception $e) {
                $txt = "Error : " . $e->getMessage();
                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);

            }
        }
        //$sFlg = false;
        $shipmentsImport = 0;
        foreach ($shipmentsResData as $shipResult) {
            try {
                if (!empty($shipResult['ShipmentNbr'])) {
                    $configParameters = $this->amconnectorHelper->getConfigParameters($storeId);
                    $url = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
                    $shipmentsInfo = $this->common->getShipmentDetails($shipResult['ShipmentNbr'], $storeId,$configParameters,$url );
                    $s = 0;
                    foreach ($shipmentsInfo as $shipData) {
                        if (isset($shipData['TrackingNumber']) && $shipData['TrackingNumber'] != '') {
                            $mgOrdrNbr = $this->resourceModelKemsOrder->getMagentoOrderIdAcuOrderId($shipData['OrderNbr'], $storeId);
                            $curentOrder = '';
                            $curentOrder = $this->orderFactory->create()->loadByIncrementId($mgOrdrNbr);
                            if ($curentOrder->canShip()) {
                                $shipProducts = array();
                                foreach ($curentOrder->getAllItems() as $_eachItem) {
                                    foreach ($shipmentsInfo as $shippedProduct) {
                                        if ($_eachItem->getSku() == str_replace(" ", "_", $shippedProduct['InventoryId'])) {
                                            if ($_eachItem->getParentItemId()) {
                                                $shipProducts[$_eachItem->getParentItemId()] = $shippedProduct['ShippedQty'];
                                            } else {
                                                $shipProducts[$_eachItem->getId()] = $shippedProduct['ShippedQty'];
                                            }
                                        }

                                    }
                                }
                                $shipVia = $this->resourceModelKemsOrder->getMagentoMappingShipmentMethod($shipResult['ShipVia'], $storeId);
                                if (isset($shipVia['magento_attr_code'])) {
                                    $shipmentCarrierTitle = $shipVia['magento_attr_code'];
                                    $shipmentCarrierCode = $shipVia['carrier'];
                                }
                                if (isset($shipmentCarrierCode) && !empty($shipmentCarrierCode)) {
                                    $shipmentCarrierCode = 'custom';
                                    $trackNumbers = '';
                                    $shipmentTracking = array();
                                    $tNum = array();
                                    foreach ($shipmentsInfo as $shipDatas) {

                                        $duplicateChk = $this->resourceModelKemsOrder->checkShipmentTrackNumber($shipDatas['TrackingNumber']);
                                        if ($duplicateChk == '' && !in_array($shipDatas['TrackingNumber'], $tNum)) {
                                            $shipmentTracking[$s]['carrier_code'] = $shipmentCarrierCode;
                                            $shipmentTracking[$s]['title'] = $shipmentCarrierTitle;
                                            $shipmentTracking[$s]['number'] = $shipDatas['TrackingNumber'];
                                            $tNum[] = $shipDatas['TrackingNumber'];
                                            $trackNumbers .= $shipmentTracking[$s]['number'] . " -";
                                        }
                                        $s++;
                                    }
                                    if (count($shipmentTracking) > 0) {
                                        $shipmentFactory = $this->objectManagerInterface->create('\Magento\Sales\Model\Order\ShipmentFactory');
                                        $shipFactory = $shipmentFactory->create($curentOrder, $shipProducts, $shipmentTracking);
                                        $shipFactory->save();
                                    
                                    /*if (!is_numeric($duplicateChk) && $duplicateChk == '') {*/

                                    foreach ($curentOrder->getAllItems() as $_eachItem) {

                                        foreach ($shipmentsInfo as $shippedProduct) {
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
                                                $i++;
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
                                        $orderLogArr['auto_sync'] = "Individual";
                                        $orderLogArr['messages'] = "Synced";
                                        $orderLogArr['description'] = $mgOrdrNbr . " shipment created with tracking number " . $shipData['TrackingNumber'];
                                        $this->invoiceCreation($mgOrdrNbr, $shipData['OrderNbr'], $orderLogArr, $storeId);
                                        $this->orderHelper->orderSyncSuccessLogs($orderLogArr);
                                        $orderLogArr['schedule_id'] = $this->scheduledId;
                                        $this->orderHelper->orderManualSync($orderLogArr);
                                        if (!empty($mgOrdrNbr)) {
                                            echo $mgOrdrNbr . " shipment created with tracking number " . $trackNumbers;
                                            exit;
                                        }
                                    }

                                    //$orderLogArr['schedule_id'] = $this->orderHelper->orderManualSync($orderLogArr);

                                    /*} else {
                                        $txt = "Info : Tracking number" . $shipData['TrackingNumber'] . " already assigned";
                                        //$this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                                    }*/

                                    $shipmentsImport++;
                                 }
				}
                            } else {
                                if (!empty($orderId)) {
                                    echo " Shipment already completed";
                                    exit;
                                }
                            }
                        }
                        else
                        {
				$mgOrdrNbr = $this->resourceModelKemsOrder->getMagentoOrderIdAcuOrderId($shipData['OrderNbr'], $storeId);
				$this->invoiceCreation($mgOrdrNbr, $shipData['OrderNbr'], $orderLogArr, $storeId);
			}

                    }
                }

            } catch (Exception $e) {
                $txt = "Error : " . $e->getMessage();
                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);

            }
        }

        if ($shipmentsImport == 0) {
            $txt = "Info : No order for Shipment";
            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
        }
    }


    /**
     * @param $shipmentNbr
     * @param $this ->syncId
     * @param $this ->logViewFileName
     * @param $storeId
     * @return array
     */

    public function invoiceCreation($magentoOrderId, $acumaticaOrderId, $orderLogArr, $storeId)
    {
        $XMLRequest = '';
        $envelopeData = $this->common->getEnvelopeData('GETORDERBYID');

        $XMLRequest = $envelopeData['envelope'];
        $XMLRequest = str_replace('{{ORDERNUMBER}}', $acumaticaOrderId, $XMLRequest);

        $flag = '';
        $requestString = $envelopeData['envName'] . '/' . $envelopeData['envVersion'] . '/' . $envelopeData['methodName'];
        $configParameters = $this->amconnectorHelper->getConfigParameters($storeId);
        $response = $this->common->getAcumaticaResponse($configParameters, $XMLRequest, $this->endpointUrl, $requestString);
        $xml = $response;
        $data = $xml->Body->GetResponse->GetResult;
        $totalData = $this->xmlHelper->xml2array($data);

        // $txt = "Info : Sync process started!";
        //  $this->amconnectorHelper->writeLogToFile($this->logViewFileName, var_dump($totalData));
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
                    $this->invoiceSender->send($invoice);

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
        try {
            $logout = $client->__soapCall('Logout', array());
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * @param $this ->syncId
     * @param $this ->logViewFileName
     * @param $storeId
     * @param $lastSyncDate
     */
    public function getOrdersFromAcumatica($storeId, $lastSyncDate, $orderLogArr, $orderId = NULL)
    {

        if (!empty($orderId)) {
            return array();
        }
        $cookies = $this->endpointCookies;
        /*Soap Request parmeters*/
        $client = new AmconnectorSoap($this->endpointUrl, array(
            'cache_wsdl' => WSDL_CACHE_NONE,
            'cache_ttl' => 86400,
            'trace' => true,
            'exceptions' => true,
        ));
        if (empty($cookies)) {
            $cookies = $this->clientHelper->login(array(), $this->endpointUrl);
        }
        $client->__setCookie('ASP.NET_SessionId', $cookies['asp_session_id']);
        $client->__setCookie('UserBranch', $cookies['userBranch']);
        $client->__setCookie('Locale', $cookies['locale']);
        $client->__setCookie('.ASPXAUTH', $cookies['aspx_auth']);
        $XMLRequest = '';
        $envelopeData = $this->common->getEnvelopeData('GETORDERS');
        $XMLRequest = $envelopeData['envelope'];
        $XMLRequest = str_replace('{{LASTSYNCDATE}}', trim($lastSyncDate), $XMLRequest);
        $XMLRequest = str_replace('{{LASTSYNCDATEPLUSHONE}}', trim(date('Y-m-d', strtotime($lastSyncDate . "+1 days"))), $XMLRequest);
        $XMLRequet = str_replace('{{ORDERTYPE}}', trim($this->orderType), $XMLRequest);
        $flag = '';
        $location = str_replace('wsdl&', '', $this->endpointUrl);
        $requestString = $envelopeData['envName'] . '/' . $envelopeData['envVersion'] . '/' . $envelopeData['methodName'];
        $response = $client->__mySoapRequest($XMLRequest, $requestString, $location, $flag);
        $this->acumaticaSessionLogout($client);

        $soapArray = array('SOAP-ENV:', 'SOAP:');
        $cleanXml = str_ireplace($soapArray, '', $response);
        $xml = simplexml_load_string($cleanXml);
        $data = $xml->Body->GetListResponse->GetListResult;
        $totalData = $this->xmlHelper->xml2array($data);

        $testData = array();
        $ordersCnt = 0;
        try {
            foreach ($totalData as $tData) {

                if (isset($tData['OrderNbr']['Value']) && !is_array($tData['OrderNbr']['Value'])) {
                    $testData [] = $tData;

                } else {
                    $testData = $this->xmlHelper->xml2array($tData);
                }
                foreach ($testData as $temp) {
                    $customerEmail = (isset($temp['BillingContact']['Email']['Value'])) ? $temp['BillingContact']['Email']['Value'] : null;
                    if (is_null($customerEmail)) {
                        //unset($temp);
                        continue; // skip or ignore because this order does not exist customer email id
                    }
                    $mgOrdrNbr = '';
                    if (isset($temp['OrderNbr']['Value']) && !is_array($temp['OrderNbr']['Value'])) {
                        $mgOrdrNbr = $this->resourceModelKemsOrder->getMagentoOrderIdAcuOrderId($temp['OrderNbr']['Value'], $storeId);
                    }
                    if (!$mgOrdrNbr) {
                        $ordersCnt++;
                        $orderRequiredMsg = array();
                        $failureMsg = '';
                        $storeManager = $this->objectManagerInterface->get('Magento\Store\Model\StoreManagerInterface');
                        $store = $storeManager->getStore();
                        $cartRepositoryInterface = $this->objectManagerInterface->get('\Magento\Quote\Api\CartRepositoryInterface');
                        $cartManagementInterface = $this->objectManagerInterface->get('\Magento\Quote\Api\CartManagementInterface');
                        $shippingRate = $this->objectManagerInterface->get('\Magento\Quote\Model\Quote\Address\Rate');

                        $cart_id = $cartManagementInterface->createEmptyCart();
                        $cart = $cartRepositoryInterface->get($cart_id);
                        $cart->setStore($store);
                        $cart->setCurrency();

                        $mgCustomerId = $this->resourceModelKemsOrder->getCustomerIdByEmail($customerEmail);


                        //Billing
                        if (isset($temp['BillingContact']['Address'])) {
                            $billingAddressLine1 = '.';
                            if (isset($temp['BillingContact']['Address']['AddressLine1']['Value']) && !is_array($temp['BillingContact']['Address']['AddressLine1']['Value'])) {
                                $billingAddressLine1 = $temp['BillingContact']['Address']['AddressLine1']['Value'];
                            } else {
                                array_push($orderRequiredMsg, 'Billing Address line1');
                            }
                            $billingAddressLine2 = '';

                            if (isset($temp['BillingContact']['Address']['AddressLine2']['Value']) && !is_array($temp['BillingContact']['Address']['AddressLine2']['Value']))
                                $billingAddressLine2 = $temp['BillingContact']['Address']['AddressLine2']['Value'];

                            $billingStreetAddress = $billingAddressLine1 . "\n" . $billingAddressLine2;
                            if (isset($temp['BillingContact']['Attention']['Value']) && !is_array($temp['BillingContact']['Attention']['Value']))
                                $billingFirstName = $temp['BillingContact']['Attention']['Value'];
                            if (isset($temp['BillingContact']['DisplayName']['Value']) && !is_array($temp['BillingContact']['DisplayName']['Value']))
                                $billingFirstName = [];
                            $billingFirstName = (isset($temp['BillingContact']['DisplayName']['Value']) ? $temp['BillingContact']['DisplayName']['Value'] : '');
                            $billingFirstNameTemp = explode(" ", $billingFirstName);
                            $billFirstName = $billingFirstNameTemp[0];
                            $billLastName = str_replace($billFirstName, "", $billingFirstName);
                            if (empty($billLastName))
                                $billLastName = '.';
                            $billingAddress['firstname'] = $billFirstName;
                            $billingAddress['lastname'] = $billLastName;
                            $billingAddress['street'] = $billingStreetAddress;

                            if (isset($temp['BillingContact']['Address']['City']['Value']) && !is_array($temp['BillingContact']['Address']['City']['Value'])) {
                                $billingAddress['city'] = $temp['BillingContact']['Address']['City']['Value'];
                            } else {

                                array_push($orderRequiredMsg, 'Billing city');
                            }
                            if (isset($temp['BillingContact']['Address']['State']['Value']) && !is_array($temp['BillingContact']['Address']['State']['Value'])) {
                                $billingAddress['region'] = $temp['BillingContact']['Address']['State']['Value'];
                                $billingRegionId = $this->resourceModelKemsOrder->getStateIdByCode($billingAddress['region']);
                                if (empty($billingRegionId)) {
                                    array_push($orderRequiredMsg, 'Billing State not exists in Magento');
                                } else {
                                    $billingAddress['region_id'] = $billingRegionId;
                                }


                            } else {
                                array_push($orderRequiredMsg, 'Billing State');
                            }
                            $billingAddress['country_id'] = $temp['BillingContact']['Address']['Country']['Value'];
                            if (isset($temp['BillingContact']['Address']['PostalCode']['Value']) && !is_array($temp['BillingContact']['Address']['PostalCode']['Value'])) {
                                $billingAddress['postcode'] = $temp['BillingContact']['Address']['PostalCode']['Value'];
                            } else {
                                array_push($orderRequiredMsg, 'Billing Postalcode');
                            }
                            if (isset($temp['BillingContact']['Phone1']['Value']) && !is_array($temp['BillingContact']['Phone1']['Value'])) {
                                $billingAddress['telephone'] = $temp['BillingContact']['Phone1']['Value'];
                            } else {
                                array_push($orderRequiredMsg, 'Billing phonenumber');
                            }

                        } else {
                            array_push($orderRequiredMsg, 'Billing information');
                        }
                        //Shipping
                        if (isset($temp['ShippingContact']['Address'])) {
                            $shippingAddressLine1 = '.';
                            if (isset($temp['ShippingContact']['Address']['AddressLine1']['Value']) && !is_array($temp['ShippingContact']['Address']['AddressLine1']['Value'])) {
                                $shippingAddressLine1 = $temp['ShippingContact']['Address']['AddressLine1']['Value'];
                            } else {
                                array_push($orderRequiredMsg, 'ShippingAddress AddressLine1');
                            }
                            $shippingAddressLine2 = '';
                            if (isset($temp['ShippingContact']['Address']['AddressLine2']['Value']) && !is_array($temp['ShippingContact']['Address']['AddressLine2']['Value']))
                                $shippingAddressLine2 = $temp['ShippingContact']['Address']['AddressLine2']['Value'];
                            $shippingStreetAddress = $shippingAddressLine1 . "\n" . $shippingAddressLine2;
                            if (isset($temp['ShippingContact']['Attention']['Value']) && !is_array($temp['ShippingContact']['Attention']['Value']))
                                $shippingFirstName = $temp['ShippingContact']['Attention']['Value'];
                            if (isset($temp['ShippingContact']['DisplayName']['Value']) && !is_array($temp['ShippingContact']['DisplayName']['Value']))
                                $shippingFirstName = $temp['ShippingContact']['DisplayName']['Value'];
                            $shippingFirstNameTemp = explode(" ", $shippingFirstName);
                            $shipFirstName = $shippingFirstNameTemp[0];
                            $shipLastName = str_replace($shipFirstName, "", $shippingFirstName);
                            if (empty($shipLastName))
                                $shipLastName = '.';
                            $shippingAddress['firstname'] = $shipFirstName;
                            $shippingAddress['lastname'] = $shipLastName;
                            $shippingAddress['street'] = $shippingStreetAddress;
                            if (isset($temp['ShippingContact']['Address']['City']['Value']) && !is_array($temp['ShippingContact']['Address']['City']['Value'])) {
                                $shippingAddress['city'] = $temp['ShippingContact']['Address']['City']['Value'];
                            } else {
                                array_push($orderRequiredMsg, 'ShippingAddress City');
                            }
                            $shippingAddress['country_id'] = $temp['ShippingContact']['Address']['Country']['Value'];
                            if (isset($temp['ShippingContact']['Address']['State']['Value']) && !is_array($temp['ShippingContact']['Address']['State']['Value'])) {
                                $shippingAddress['region'] = $temp['ShippingContact']['Address']['State']['Value'];
                                $shippingRegionId = $this->resourceModelKemsOrder->getStateIdByCode($shippingAddress['region']);
                                if (empty($shippingRegionId)) {
                                    array_push($orderRequiredMsg, 'Shipping State not exists in Magento');
                                } else {
                                    $shippingAddress['region_id'] = $shippingRegionId;
                                }
                            } else {
                                array_push($orderRequiredMsg, 'ShippingAddress State');
                            }
                            if (isset($temp['ShippingContact']['Address']['PostalCode']['Value']) && !is_array($temp['ShippingContact']['Address']['PostalCode']['Value'])) {
                                $shippingAddress['postcode'] = $temp['ShippingContact']['Address']['PostalCode']['Value'];
                            } else {
                                array_push($orderRequiredMsg, 'ShippingAddress Postalcode');
                            }
                            if (isset($temp['ShippingContact']['Phone1']['Value']) && !is_array($temp['ShippingContact']['Phone1']['Value'])) {
                                $shippingAddress['telephone'] = $temp['ShippingContact']['Phone1']['Value'];
                            } else {
                                array_push($orderRequiredMsg, 'ShippingAddress Phone');
                            }
                        }
                        //Line Items
                        /* $product = $this->objectManagerInterface->create('\Magento\Catalog\Model\Product')->load(76);
                         $product->setPrice(3);
                         $cart->addProduct(
                             $product,intval(2)
                         );*/
                        try {
                            $skuMsg = '';
                            if (isset($temp['Details']['SalesOrderDetail']['InventoryID']['Value'])) {
                                $sku = $temp['Details']['SalesOrderDetail']['InventoryID']['Value'];
                                $sku = str_replace(" ", "_", $sku);
                                $productId = '';
                                $productId = $this->resourceModelKemsOrder->getIdBySku($sku);
                                if ($productId) {
                                    $product = $this->objectManagerInterface->create('\Magento\Catalog\Model\Product')->load($productId);
                                    if (isset($temp['Details']['SalesOrderDetail']['UnitPrice']['Value']) && $temp['Details']['SalesOrderDetail']['UnitPrice']['Value'] > 0) {
                                        $product->setPrice($temp['Details']['SalesOrderDetail']['UnitPrice']['Value']);
                                    } else {
                                        array_push($orderRequiredMsg, $sku . ' price not assgined in Acumatica');
                                    }
                                    if (isset($temp['Details']['SalesOrderDetail']['Quantity']['Value']) && $temp['Details']['SalesOrderDetail']['Quantity']['Value'] > 0) {
                                        $cart->addProduct(
                                            $product,
                                            intval($temp['Details']['SalesOrderDetail']['Quantity']['Value'])
                                        );
                                    } else {
                                        array_push($orderRequiredMsg, $sku . 'product quantity is not defined');

                                    }
                                } else {
                                    array_push($orderRequiredMsg, $sku . ' not exists');
                                }
                            } else {
                                $linData = array();
                                if (isset($temp['Details']['SalesOrderDetail'])) {
                                    foreach ($temp['Details']['SalesOrderDetail'] as $lineItems) {
                                        $linData = $this->xmlHelper->xml2array($lineItems);
                                        $sku = $linData['InventoryID']['Value'];
                                        $sku = str_replace(" ", "_", $sku);
                                        $productId = '';
                                        $productId = $this->resourceModelKemsOrder->getIdBySku($sku);
                                        if ($productId) {
                                            $product = $this->objectManagerInterface->create('\Magento\Catalog\Model\Product')->load($productId);
                                            if (isset($linData['UnitPrice']['Value']) && $linData['UnitPrice']['Value'] > 0) {
                                                $product->setPrice($linData['UnitPrice']['Value']);
                                            } else {
                                                array_push($orderRequiredMsg, $sku . ' price not assgined in Acumatica');
                                            }
                                            if (isset($linData['Quantity']['Value']) && $linData['Quantity']['Value'] > 0) {
                                                $cart->addProduct(
                                                    $product,
                                                    intval($linData['Quantity']['Value'])
                                                );
                                            } else {
                                                array_push($orderRequiredMsg, $sku . 'product quantity is not defined');
                                            }
                                        } else {
                                            array_push($orderRequiredMsg, $sku . ' not exists');
                                        }
                                    }
                                }

                            }
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                            echo $skuMsg .= $sku;
                        }
                        //Customer
                        $orderData['email'] = $customerEmail;
                        $orderData['currency_id'] = 'USD';
                        try {
                            $cart->setCustomerEmail($customerEmail);
                            if ($mgCustomerId) {

                                $customer = $this->objectManagerInterface->create('\Magento\Customer\Api\CustomerRepositoryInterface')->getById($mgCustomerId);
                                $cart->assignCustomer($customer);
                            } else {
                                $cart->setCustomerIsGuest(1);
                            }
                            // Collect Rates and Set Shipping & Payment Method

                            if (isset($temp['ShipVia']['Value']) && !is_array($temp['ShipVia']['Value'])) {
                                $shipVia = $this->resourceModelKemsOrder->getMagentoMappingShipmentMethod($temp['ShipVia']['Value'], $storeId);
                                if (isset($shipVia['magento_attr_code'])) {
                                    $shipmentCarrierTitle = $shipVia['magento_attr_code'];
                                    $shipmentCarrierCode = $shipVia['carrier'];
                                    /*if (strstr($shipmentCarrierCode, 'custom')) {
                                        $shipmentCarrierCode = 'flatrate_flatrate';
                                    }
                                    if (strstr($shipmentCarrierCode, 'tablerate')) {
                                        $shipmentCarrierCode = 'flatrate_flatrate';
                                    }*/

                                }
                            } else {
                                $shipmentCarrierCode = 'customship_customship';

                            }
                            $shipmentCarrierCode = 'flatrate_flatrate';

                            if (isset($shipmentCarrierCode) && !empty($shipmentCarrierCode)) {
                                if (isset($shippingAddress['region'])) {
                                    if ($shippingAddress['region'] == 'AK' || $shippingAddress['region'] == 'HI') {
                                        $shipmentCarrierTitle = 'Shipping Charges - Flat rate: $4 per pound';
                                    } else {
                                        $shipmentCarrierTitle = 'Shipping Charges - Free expedited shipping: $0.00';
                                    }
                                }


                            } else {

                                array_push($orderRequiredMsg, 'Shipping method information');
                            }

                            $paymentMethodResult = $this->objectManagerInterface->create('\Magento\Payment\Model\Config')->getActiveMethods();
                            $paymentMethodStatus = 0;
                            foreach ($paymentMethodResult as $key => $value) {
                                if ($key == 'cashondelivery') {
                                    $paymentMethodStatus = 1;
                                }
                            }
                            if ($paymentMethodStatus == 1) {
                                $cart->setPaymentMethod('cashondelivery'); //payment method

                                $cart->setInventoryProcessed(false); //not effetc inventory

                                // Set Sales Order Payment
                                $cart->getPayment()->importData(['method' => 'cashondelivery']);

                            } else {
                                array_push($orderRequiredMsg, 'Check/MoneyOrder payment method not enabled ');
                            }


                            //Set Address to cart interface
                            if (empty($orderRequiredMsg)) {
                                $cart->getBillingAddress()->addData($billingAddress);
                                $cart->getShippingAddress()->addData($shippingAddress);
                                $shippingAddressObj = $cart->getShippingAddress();

                                $shippingAddressObj->setCollectShippingRates(true)->collectShippingRates()
                                    ->setShippingMethod($shipmentCarrierCode);
                                $cart->collectTotals();
                                $cart->save();
                                $cart = $cartRepositoryInterface->get($cart->getId());
                                try {

                                    $orderId = $cartManagementInterface->placeOrder($cart->getId());

                                    $orderObj = $this->orderFactory->create()->load($orderId);
                                    $orderObj->setAcumaticaOrderId($temp['OrderNbr']['Value']);
                                    $old = $orderObj->getBaseShippingAmount();
                                    $shippingPrice = 0;
                                    if (isset($temp['PremiumFreight']['Value']) && !is_array($temp['PremiumFreight']['Value']))
                                        $shippingPrice = $temp['PremiumFreight']['Value'];
                                    if (isset($shipmentCarrierTitle) && !empty($shipmentCarrierTitle)) {
                                        $shipmentCarrierTitle = str_replace(": $0.00", '', $shipmentCarrierTitle);
                                        $orderObj->setShippingDescription($shipmentCarrierTitle);
                                    }

                                    $orderObj->setShippingAmount($shippingPrice);
                                    $orderObj->setBaseShippingAmount($shippingPrice);
                                    $orderObj->setBaseGrandTotal($orderObj->getGrandTotal() - $old + $shippingPrice);
                                    $orderObj->setGrandTotal($orderObj->getGrandTotal() - $old + $shippingPrice); //adding shipping price to grand total
                                    $orderObj->save();

                                    $XMLRequest = '';
                                    $XMLRequest .= '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><SO301000Submit xmlns="http://www.acumatica.com/generic/">';
                                    $XMLRequest .= '<commands>';
                                    $XMLRequest .= '<Command xsi:type="Value"><Value>' . $this->orderType . '</Value>';
                                    $XMLRequest .= '<LinkedCommand xsi:type="Field"><FieldName>OrderType</FieldName><ObjectName>Document</ObjectName>';
                                    $XMLRequest .= '<Value>OrderType</Value><Commit>true</Commit><LinkedCommand xsi:type="Action"><FieldName>Cancel</FieldName>';
                                    $XMLRequest .= '<ObjectName>Document</ObjectName><LinkedCommand xsi:type="Key"><FieldName>OrderNbr</FieldName><ObjectName>Document</ObjectName>';
                                    $XMLRequest .= '<Value>=[Document.OrderNbr]</Value><LinkedCommand xsi:type="Key"><FieldName>OrderType</FieldName>';
                                    $XMLRequest .= '<ObjectName>Document</ObjectName><Value>=[Document.OrderType]</Value></LinkedCommand></LinkedCommand></LinkedCommand></LinkedCommand></Command>';
                                    $XMLRequest .= '<Command xsi:type="Value">';
                                    $XMLRequest .= '<Value>' . $temp['OrderNbr']['Value'] . '</Value><LinkedCommand xsi:type="Field"><FieldName>OrderNbr</FieldName>';
                                    $XMLRequest .= '<ObjectName>Document</ObjectName><Value>OrderNbr</Value><Commit>true</Commit>';
                                    $XMLRequest .= '<LinkedCommand xsi:type="Action"><FieldName>Cancel</FieldName><ObjectName>Document</ObjectName>';
                                    $XMLRequest .= '<LinkedCommand xsi:type="Key"><FieldName>OrderNbr</FieldName><ObjectName>Document</ObjectName>';
                                    $XMLRequest .= '<Value>=[Document.OrderNbr]</Value><LinkedCommand xsi:type="Key"><FieldName>OrderType</FieldName>';
                                    $XMLRequest .= '<ObjectName>Document</ObjectName><Value>=[Document.OrderType]</Value></LinkedCommand></LinkedCommand>';
                                    $XMLRequest .= '</LinkedCommand></LinkedCommand></Command>';

                                    // Magento Order Number to CustomerOrderNbr
                                    $XMLRequest .= '<Command xsi:type="Value"><Value>' . $orderObj->getIncrementId() . '</Value><LinkedCommand xsi:type="Field"><FieldName>CustomerOrderNbr</FieldName>';
                                    $XMLRequest .= '<ObjectName>Document</ObjectName><Value>CustomerOrder</Value></LinkedCommand></Command>';
                                    //Save
                                    $XMLRequest .= '<Command xsi:type="Action"><FieldName>Save</FieldName><ObjectName>Document</ObjectName></Command>';
                                    $XMLRequest .= '</commands></SO301000Submit></soap:Body></soap:Envelope>';

                                    $cookies = array();
                                    $cookies = $this->clientHelper->login(array(), $this->webServiceUrl);
                                    $client = new AmconnectorSoap($this->webServiceUrl, array(
                                        'cache_wsdl' => WSDL_CACHE_NONE,
                                        'cache_ttl' => 86400,
                                        'trace' => true,
                                        'exceptions' => true,
                                    ));
                                    $client->__setCookie('ASP.NET_SessionId', $cookies['asp_session_id']);
                                    $client->__setCookie('UserBranch', $cookies['userBranch']);
                                    if (isset($cookies['locale']))
                                        $client->__setCookie('Locale', $cookies['locale']);
                                    $client->__setCookie('.ASPXAUTH', $cookies['aspx_auth']);
                                    $orderEnvelopeData = $this->common->getEnvelopeData('CREATEORDER');
                                    $url = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
                                    $location = $url . "Soap/" . $orderEnvelopeData['envName'] . ".asmx";
                                    $requestString = $orderEnvelopeData['envVersion'] . '/' . $orderEnvelopeData['envName'] . '/' . $orderEnvelopeData['methodName'];

                                    $flag = 'webservice';
                                    //echo $XMLRequest;
                                    $response = $client->__mySoapRequest($XMLRequest, $requestString, $location, $flag);
                                    $this->acumaticaSessionLogout($client);
                                    $xml = $this->xmlHelper->getXmlResponse($response);


                                    $txt = "Info : " . "Acumatica Order " . $temp['OrderNbr']['Value'] . " synced to Magento with OrderNumber " . $orderObj->getIncrementId();
                                    echo $msg = "Acumatica Order " . $temp['OrderNbr']['Value'] . " synced to Magento with OrderNumber " . $orderObj->getIncrementId() . PHP_EOL;
                                    $orderLogArr['store_id'] = $storeId;
                                    $orderLogArr['customer_email'] = $customerEmail;
                                    $orderLogArr['schedule_id'] = $this->scheduledId;
                                    $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    $orderLogArr['auto_sync'] = "Complete";
                                    $orderLogArr['messages'] = "Synced";
                                    $orderLogArr['description'] = $msg;
                                    $orderLogArr['long_message'] = "";
                                    $orderLogArr['message_type'] = "success";
                                    $orderLogArr['order_id'] = $orderObj->getIncrementId();
                                    $orderLogArr['acumatica_order_id'] = $temp['OrderNbr']['Value'];
                                    $this->orderHelper->orderManualSync($orderLogArr);
                                    $this->orderHelper->orderSyncSuccessLogs($orderLogArr);
                                    $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                                }

                            } else {
                                $failureMsg = implode(" ", $orderRequiredMsg);
                                $txt = "Info : " . "Acumatica Order " . $temp['OrderNbr']['Value'] . " not synced to Magento. ";
                                $msg = "Acumatica Order " . $temp['OrderNbr']['Value'] . " not synced to Magento " . $failureMsg . " details required";
                                $orderLogArr['store_id'] = $storeId;
                                $orderLogArr['schedule_id'] = $this->scheduledId;
                                $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                                $orderLogArr['auto_sync'] = "Complete";
                                $orderLogArr['messages'] = "Synced";
                                $orderLogArr['description'] = $txt;
                                $orderLogArr['long_message'] = $msg;
                                $orderLogArr['message_type'] = "error";
                                $orderLogArr['order_id'] = '';
                                $orderLogArr['acumatica_order_id'] = $temp['OrderNbr']['Value'];
                                $this->orderHelper->orderManualSync($orderLogArr);
                                $this->orderHelper->orderSyncSuccessLogs($orderLogArr);
                                $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                            }
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {

                            $txt = "Info : " . "Acumatica Order " . $temp['OrderNbr']['Value'] . " not synced to Magento ";
                            $msg = "Acumatica Order " . $temp['OrderNbr']['Value'] . " not synced to Magento ";
                            $orderLogArr['store_id'] = $storeId;
                            $orderLogArr['schedule_id'] = $this->scheduledId;
                            $orderLogArr['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                            $orderLogArr['auto_sync'] = "Complete";
                            $orderLogArr['messages'] = "Synced";
                            $orderLogArr['description'] = $msg;
                            $orderLogArr['long_message'] = $e->getMessage();
                            $orderLogArr['message_type'] = "error";
                            $orderLogArr['order_id'] = '';
                            $orderLogArr['acumatica_order_id'] = $temp['OrderNbr']['Value'];
                            $this->orderHelper->orderManualSync($orderLogArr);
                            $this->orderHelper->orderSyncSuccessLogs($orderLogArr);
                            $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                        }

                        //To check Order status
                        if (isset($temp['Status']['Value']) && !is_array($temp['Status']['Value']) && strtolower($temp['Status']['Value']) == strtolower('complete')) {
                            $this->getDataFromAcumatica($storeId, $lastSyncDate, $orderLogArr, $temp['OrderNbr']['Value']);
                        }
                    }
                }

                if ($ordersCnt == 0) {
                    $txt = "Info : No new orders present in Acumatica  to sync to Magento.";
                    $orderLogArr['description'] = 'No new orders present in Acumatica to sync to Magento';
                    $this->amconnectorHelper->writeLogToFile($this->logViewFileName, $txt);
                    $orderLogArr['status'] = 'success';
                    $orderLogArr['message'] = "No new orders present in Acumatica  to sync to Magento";
                    $orderLogArr['messages'] = "Order Sync completed successfully";
                    $orderLogArr['auto_sync'] = "Complete";
                    $this->orderHelper->orderManualSync($orderLogArr);
                    $this->orderHelper->orderSyncSuccessLogs($orderLogArr);
                }
            }
        } catch (Exception $e) {
            echo $temp['OrderNbr']['Value'] . "Not created" . PHP_EOL;
        }

    }
}

