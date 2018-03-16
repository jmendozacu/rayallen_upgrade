<?php
/**
 *
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Webapi\Soap;
use SoapClient;
use SoapFault;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\Locale\ListsInterface;
use Magento\Config\Model\ResourceModel\Config;
use Kensium\Lib;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
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
     * @var TimeZone
     */
    protected $timezone;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Customer
     */
    protected $resourceModelAmconnectorCustomer;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $syncResourceModel;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Kensium\Amconnector\Model\Customer
     */
    protected $amconnectorCustomerFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Data
     */
    protected $timeHelper;

    /**
     * @var Xml
     */
    protected $xmlHelper;

    /**
     * @var \Kensium\Synclog\Helper\Customer
     */
    protected $customerLogHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Licensecheck
     *
     */
    protected $licenseResourceModel;

    /**
     * @var
     */
    protected $licensecheck;
    protected $common;

    const IS_LICENSE_INVALID = "Invalid";
    const IS_LICENSE_VALID = "Valid";
    const IS_TIME_VALID = "Valid";

    /**
     * @param Context $context
     * @param DateTime $date
     * @param Timezone $timezone
     * @param Data $dataHelper
     * @param \Kensium\Synclog\Helper\Customer $customerLogHelper
     * @param Time $timeHelper
     * @param Xml $xmlHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Customer $resourceModelAmconnectorCustomer
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Kensium\Amconnector\Model\CustomerFactory $amconnectorCustomerFactory
     * @param \Magento\Customer\Model\CustomerFactory $customer
     */
    public function __construct(
        Context $context,
        DateTime $date,
        Timezone $timezone,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Synclog\Helper\Customer $customerLogHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Customer $resourceModelAmconnectorCustomer,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Kensium\Amconnector\Model\CustomerFactory $amconnectorCustomerFactory,
        \Magento\Customer\Model\CustomerFactory $customer,
        Lib\Common $common

    )
    {

        ini_set('default_socket_timeout', 1000);
        parent::__construct($context);
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->date = $date;
        $this->timezone = $timezone;
        $this->dataHelper = $dataHelper;
        $this->customerLogHelper=$customerLogHelper;
        $this->timeHelper = $timeHelper;
        $this->resourceModelAmconnectorCustomer = $resourceModelAmconnectorCustomer;
        //$this->resourceModelAmconnectorCustomer = '';
        $this->syncResourceModel = $syncResourceModel;
        $this->logger = $context->getLogger();
        $this->messageManager = $messageManager;
        $this->amconnectorCustomerFactory = $amconnectorCustomerFactory;
        $this->licenseResourceModel = $licenseResourceModel;
        $this->customerFactory = $customer;
        $this->xmlHelper = $xmlHelper;
        $this->common = $common;
    }

    /**
     * @param $url
     * @param $XMLRequest
     */
    public function getCustomerSchema($url,$storeId)
    {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvCustomerSchemaData = $this->common->getEnvelopeData('GETCUSTOMERSCHEMA');
            $customerSchemaXMLGetRequest = $csvCustomerSchemaData['envelope'];
            $action = $csvCustomerSchemaData['methodName'];
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $customerSchemaXMLGetRequest, $url, $action);
            $arrayData = array();
            if($xmlResponse->Body->GetSchemaResponse->GetSchemaResult){
                $data = $xmlResponse->Body->GetSchemaResponse->GetSchemaResult;
                $arrayData = $this->xmlHelper->XMLtoArray($data);
            }
            return $arrayData;
        } catch (SoapFault $e) {
            echo "Last request:<pre>" . htmlentities($e->getMessage()) . "</pre>";
        }
    }


    /**
     * Acumatica login
     * get Customers From Acumatica
     * Insert customers data into temporary location in magento
     * Create Customer into Magento
     *
     */
    /**
     * @param $syncType
     * @param $autoSync
     * @param $syncId
     * @param null $scheduleId
     * @param null $storeId
     * @param string $flag
     * @param null $orderData
     * @param null $individualCustomerId
     */
    public function getCustomerSync($syncType, $autoSync, $syncId, $scheduleId = NULL, $storeId=null, $flag = 'CUSTOMER', $orderData=null, $individualCustomerId = NULL)
    {
        $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'customer', NULL);
        try {
            if ($storeId == 0) {
                $storeId = 1;
            }
            $this->licenseType = $this->licenseResourceModel->checkLicenseTypes($storeId);
            if ($this->resourceModelAmconnectorCustomer->StopSyncValue() == 1) {

                /**
                 * License status check
                 */
                $txt = "Info : License verification is in progress";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $licenseStatus = $this->licenseResourceModel->getLicenseStatus($storeId);

                if ($licenseStatus != self::IS_LICENSE_VALID) {
                    /**
                     * logs here for Invalid License
                     */
                    if ($scheduleId != '') {
                        $customerLog['schedule_id'] = $scheduleId;
                    } else {
                        $customerLog['schedule_id'] = "";
                    }
                    $customerLog['store_id'] = $storeId;
                    $customerLog['job_code'] = "customer"; //job code
                    $customerLog['messages'] = "Invalid License Key"; //messages
                    $customerLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                    $customerLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                    $customerLog['status'] = "error"; //status
                    if ($autoSync == 'MANUAL') {
                        $customerLog['runMode'] = 'Manual';
                    } elseif ($autoSync == 'AUTO') {
                        $customerLog['runMode'] = 'Automatic';
                    }
                    if ($syncType == 'COMPLETE') {
                        $customerLog['autoSync'] = 'Complete';
                        $customerLog['action'] = "Batch Process";
                    } elseif ($syncType == 'INDIVIDUAL') {
                        $customerLog['autoSync'] = 'Individual';
                        $customerLog['action'] = "Individual";
                    }
                    $this->customerLogHelper->customerManualSync($customerLog);
                    $txt = "Error: " . $customerLog['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                } else {

                    $txt = "Info : License verified successfully!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    $txt = "Info : Server time verification is in progress";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);


                    $timeSyncCheck = $this->timeHelper->timeSyncCheck($storeId);
                    if ($timeSyncCheck != self::IS_TIME_VALID) { //Check Time is synced or not
                        /**
                         * logs here for Time Not Synced
                         */
                        if ($scheduleId != '') {
                            $customerLog['schedule_id'] = $scheduleId;
                        } else {
                            $customerLog['schedule_id'] = "";
                        }
                        $customerLog['store_id'] = $storeId;
                        $customerLog['job_code'] = "customer"; //job code
                        $customerLog['messages'] = "Server time is not in sync"; //messages
                        $customerLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                        $customerLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                        $customerLog['status'] = "error"; //status
                        if ($autoSync == 'MANUAL') {
                            $customerLog['runMode'] = 'Manual';
                        } elseif ($autoSync == 'AUTO') {
                            $customerLog['runMode'] = 'Automatic';
                        }
                        if ($syncType == 'COMPLETE') {
                            $customerLog['autoSync'] = 'Complete';
                            $customerLog['action'] = "Batch Process";
                        } elseif ($syncType == 'INDIVIDUAL') {
                            $customerLog['autoSync'] = 'Individual';
                            $customerLog['action'] = "Individual";
                        }
                        $this->customerLogHelper->customerManualSync($customerLog);
                        $txt = "Error: " . $customerLog['messages'];
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                    } else {
                        /**
                         * logs here for starting sync
                         */
                        if ($scheduleId != '') {
                            $customerLog['schedule_id'] = $scheduleId;
                        } else {
                            $customerLog['schedule_id'] = "";
                        }
                        $customerLog['store_id'] = $storeId;
                        $customerLog['job_code'] = "customer"; //job code
                        $customerLog['status'] = "success"; //status
                        $customerLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                        $customerLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                        $customerLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                        if ($autoSync == 'MANUAL') {
                            $customerLog['runMode'] = 'Manual';
                        } elseif ($autoSync == 'AUTO') {
                            $customerLog['runMode'] = 'Automatic';
                        }
                        $txt = "Info : Server time is in sync.";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $customerLog['messages'] = "Customer manual sync initiated";
                        $txt = "Info : " . $customerLog['messages'];
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $this->customerLogHelper->customerManualSync($customerLog);
                        unset($customerLog['created_at']);
                        unset($customerLog['scheduled_at']);
                        unset($customerLog['executed_at']);
                        if ($syncType == 'COMPLETE') {
                            $this->syncResourceModel->updateSyncAttribute($syncId, 'STARTED', $storeId);
                            $customerLog['autoSync'] = 'Complete';
                            $customerLog['action'] = "Batch Process";
                            $this->completeSync($flag, $storeId, $customerLog, $logViewFileName, $syncId);
                        } else if ($syncType == 'INDIVIDUAL') {
                            $customerLog['autoSync'] = 'Individual';
                            $customerLog['action'] = "Individual";
                            $customerLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $this->individualSync($individualCustomerId, $orderData, $flag, $storeId, $customerLog, $logViewFileName);
                        }
                    }
                }
            } else {
                $this->messageManager->addError("Customer sync stopped");

                if ($scheduleId != '') {
                    $customerLog['schedule_id'] = $scheduleId;
                } else {
                    $customerLog['schedule_id'] = "";
                }
                $customerLog['job_code'] = "customer";
                $customerLog['messages'] = "Customer sync stopped";
                $customerLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                $customerLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                $customerLog['executed_at'] = '';
                $customerLog['finished_at'] = '';
                $customerLog['store_id'] = $storeId;
                $customerLog['status'] = "notice"; //status
                if ($autoSync == 'MANUAL') {
                    $customerLog['runMode'] = 'Manual';
                } elseif ($autoSync == 'AUTO') {
                    $customerLog['runMode'] = 'Automatic';
                }
                if ($autoSync == 'COMPLETE') {
                    $customerLog['autoSync'] = 'Complete';
                    $customerLog['action'] = "Batch Process";
                } elseif ($autoSync == 'INDIVIDUAL') {
                    $customerLog['autoSync'] = 'Individual';
                    $customerLog['action'] = "Individual";
                }
                $this->customerLogHelper->customerManualSync($customerLog);
                $txt = "Notice: " . $customerLog['messages'];
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
            }
            $this->resourceModelAmconnectorCustomer->enableSync();
            $txt = "Info : Sync process completed!";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
        } catch (Exception $e) {
            /**
             *logs here for Exception
             */
            $customerLog['messages'] = $e->getMessage(); //message
            $this->customerLogHelper->customerManualSync($customerLog);
            $txt = "Error: " . $customerLog['messages'];
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
            $this->messageManager->addError("Sync error occurred. Please try again.");
        }

    }


    /**
     * @param $flag
     * @param $storeId
     * @param $customerLog
     * @param $logViewFileName
     * @param $syncId
     */
    public function completeSync($flag, $storeId, $customerLog,  $logViewFileName, $syncId)
    {
        $insertedId = $this->syncResourceModel->checkConnectionFlag($syncId, 'customer', $storeId);

        if ($insertedId == NULL) {
            $this->messageManager->addError("Sync in Progress - please wait for the current sync to finish.");
            /**
             *logs here for another sync is already executing
             */
            $customerLog['status'] = "error"; //status
            $customerLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
            $customerLog['messages'] = "Another Sync is already executing"; //messages
            $this->customerLogHelper->customerManualSync($customerLog);
            $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);

            $txt = "Info : Sync in Progress - please wait for the current sync to finish";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

        } else {
            $this->syncResourceModel->updateConnection($insertedId, 'PROCESS',$storeId);
            $this->syncResourceModel->updateSyncAttribute($syncId, 'PROCESSING', $storeId);
            /**
             * Sync to magento
             */
            $mappingAttributes = $this->resourceModelAmconnectorCustomer->getMagentoAttributes($storeId);


            /**
             * check mapping is done or not
             */
            $customerMappingCheck = $this->resourceModelAmconnectorCustomer->checkCustomerMapping($storeId);
            if ($customerMappingCheck == 0 ) {
                $this->messageManager->addError("Attributes are not mapped");
                $customerLog['status'] = "error"; //status
                $customerLog['messages'] = "Attributes are not mapped"; //messages
                $customerLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                $this->customerLogHelper->customerManualSync($customerLog);
                $txt = "Notice: Customer Attributes not mapped. Please try again.";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->syncResourceModel->updateConnection($insertedId, 'FAILURE', $storeId);
                $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
            } else {

                $this->resourceModelAmconnectorCustomer->truncateDataFromTempTables($storeId);
                $acumaticaData = $this->getDataFromAcumatica($syncId, $storeId);
                $insertedData = $this->resourceModelAmconnectorCustomer->insertDataIntoTempTables($acumaticaData, $syncId, $storeId);

                if ($this->resourceModelAmconnectorCustomer->StopSyncValue() == 1) {

                    if ($this->scopeConfigInterface->getValue('amconnectorsync/customersync/syncdirection') == 1 || $this->scopeConfigInterface->getValue('amconnectorsync/customersync/syncdirection') == 3) {
                        if (isset($insertedData['magento'])) {
                            foreach ($insertedData['magento'] as $aData) {
                                if ($this->resourceModelAmconnectorCustomer->StopSyncValue() == 1) {
                                    if ($aData['acumatica_id'] != '' && $aData['magento_id'] != '') {
                                        $directionFlg = 1;
                                    } else {
                                        $directionFlg = 0;
                                    }

                                    if ($aData['entity_ref'] == NULL && $aData['email'] != '') {
                                        $syncToMagentoResult = $this->amconnectorCustomerFactory->create()->syncToMagento($acumaticaData['Entity'], $mappingAttributes, $customerLog,  $storeId, $logViewFileName,$this->customerLogHelper, $directionFlg);
                                    } else {
                                        if($aData['email'] != ''){
                                            $syncToMagentoResult = $this->amconnectorCustomerFactory->create()->syncToMagento($acumaticaData['Entity'][$aData['entity_ref']], $mappingAttributes, $customerLog,  $storeId, $logViewFileName,$this->customerLogHelper, $directionFlg);
                                        }
                                    }
                                } else {
                                    $this->messageManager->addError("Customer sync stopped");
                                    $customerLog['status'] = "notice"; //status
                                    $customerLog['messages'] = "Customer sync stopped";
                                    $customerLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                    $this->customerLogHelper->customerManualSync($customerLog);
                                    $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                    break;
                                }
                            }
                        }

                    }
                    /**
                     * Sync to Acumatica
                     */
                    if ($this->resourceModelAmconnectorCustomer->StopSyncValue() == 1) {
                        if ($this->scopeConfigInterface->getValue('amconnectorsync/customersync/syncdirection') == 2 || $this->scopeConfigInterface->getValue('amconnectorsync/customersync/syncdirection') == 3) {
                            $acumaticaAttributes = $this->resourceModelAmconnectorCustomer->getAcumaticaAttributes($storeId);
                            if (isset($insertedData['acumatica'])) {
                                foreach ($insertedData['acumatica'] as $aData) {
                                    if ($aData['acumatica_id'] != '' && $aData['magento_id'] != '') {
                                        $directionFlg = 1;
                                    } else {
                                        $directionFlg = 0;
                                    }
                                    if ($this->resourceModelAmconnectorCustomer->StopSyncValue() == 1) {
                                        $syncToAcumaticaResult = $this->amconnectorCustomerFactory->create()->syncToAcumatica($aData, $acumaticaAttributes, $customerLog,  $storeId, $logViewFileName,$this->customerLogHelper, $directionFlg, $flag);
                                    } else {

                                        $this->messageManager->addError("Customer sync stopped");
                                        $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                        break;
                                    }
                                }

                            }
                        }
                    }

                    if ($this->resourceModelAmconnectorCustomer->StopSyncValue() == 0) {
                        $customerLog['status'] = "notice"; //status
                        $customerLog['messages'] = "Customer sync stopped";
                        $customerLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                        $this->customerLogHelper->customerManualSync($customerLog);
                        $txt = "Notice : Customer sync stopped";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    }

                    /**
                     * we need to add 3 seconds sleep because the last synced item is matching with last sync date in magento
                     */
                    sleep(3);

                    if ((isset($syncToMagentoResult) && count($syncToMagentoResult)) >= 1 || (isset($syncToAcumaticaResult) && count($syncToAcumaticaResult)) >= 1) {
                        $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                    } else {
                        $this->syncResourceModel->updateSyncAttribute($syncId, 'SUCCESS', $storeId);
                    }

                    if ($this->resourceModelAmconnectorCustomer->StopSyncValue() == 1) {
                        /**
                         *logs here for Sync Success
                         */
                        $customerLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                        $customerLog['messages'] = "Customer Sync completed successfully!"; //messages
                        $this->customerLogHelper->customerManualSync($customerLog);
                        $txt = "Info : " . $customerLog['messages'];
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $this->messageManager->addSuccess("Customer Sync completed successfully!");
                    }
                } else {
                    $this->messageManager->addError("Customer sync stopped");
                    $customerLog['status'] = "notice"; //status
                    $customerLog['messages'] = "Customer sync stopped";
                    $customerLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                    $this->customerLogHelper->customerManualSync($customerLog);
                    $txt = "Notice:" . $customerLog['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                }
                $this->syncResourceModel->updateConnection($insertedId, 'SUCCESS',$storeId);
            }
        }
    }

    /**
     * @param $individualCustomerId
     * @param $orderData
     * @param $flag
     * @param $storeId
     * @param $customerLog
     * @param $logViewFileName
     */
    public function individualSync($individualCustomerId, $orderData, $flag, $storeId, $customerLog, $logViewFileName)
    {
        $customerLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
        if ($individualCustomerId != '' || !empty($flag)) {
            $magentoCustomerId = $individualCustomerId;
            /*From Order*/
            if ($flag == "ORDER") {
                if ($orderData['customer_id'] == '') {
                    $magentoCustomerId = '';
                    $magentoOrderId = $orderData['entity_id'];
                    /**
                     * need to check that customer exist in magento or not
                     * if exist check the acumatica customer id is there in magento, if not create that customer in acumatica
                     */
                } elseif ($orderData['customer_id'] != '') {
                    $magentoCustomerId = '';
                    $magentoCustomerId = $orderData['customer_id'];
                    $flag = 'CUSTOMER';
                }
            }

            /*End Order*/
            $customerAcumaticaId = $acumaticaCustomerData = '';
            $mappingAttributes = $this->resourceModelAmconnectorCustomer->getMagentoAttributes($storeId);
            $customerLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
            if($magentoCustomerId > 0){
                $magentoCustomerData = $this->resourceModelAmconnectorCustomer->getCustomerById($magentoCustomerId);//$this->customerFactory->create()->load($magentoCustomerId);
                $customerAcumaticaId = $this->resourceModelAmconnectorCustomer->getAcmCustomerId($magentoCustomerId);
                $customerEmail = $magentoCustomerData['email'];
            }
            if ($customerAcumaticaId != '') {
                $acumaticaCustomerData = $this->getIndividualDataFromAcumatica($customerAcumaticaId, 'id',$storeId);
            } else {
                if(empty($magentoCustomerId)){
                    $customerEmail =  $orderData['customer_email'];
                }
                $acumaticaCustomerData = $this->getIndividualDataFromAcumatica($customerEmail, 'email',$storeId);
            }
            if (isset($acumaticaCustomerData) && !empty($acumaticaCustomerData)) {
                $acumaticaUpdatedDate = '';

                if(isset($acumaticaCustomerData['LastModified']))
                    $acumaticaUpdatedDate = $acumaticaCustomerData['LastModified']['Value'];
                $magentoUpdatedDate = $magentoCustomerData['updated_at'];
                $updatedDate = $this->date->date('Y-m-d H:i:s', strtotime($magentoUpdatedDate));
                if (strtotime($acumaticaUpdatedDate) > strtotime($updatedDate)) {
                    /**
                     * Sync To Magento
                     */
                    $result = $this->amconnectorCustomerFactory->create()->syncToMagento($acumaticaCustomerData, $mappingAttributes, $customerLog,  $storeId, $logViewFileName,$this->customerLogHelper, NULL);
                    $customerLog['status'] = "success"; //status
                    $customerLog['messages'] = "Customer Sync completed successfully!"; //messages
                    $customerLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                    $this->customerLogHelper->customerManualSync($customerLog);
                    $txt = "Info : " . $customerLog['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->messageManager->addSuccess("Customer Data sync executed successfully");
                } else {
                    /**
                     * Sync To acumatica
                     */
                    $acumaticaAttributes = $this->resourceModelAmconnectorCustomer->getAcumaticaAttributes($storeId);
                    if(isset($magentoOrderId) && $magentoOrderId != '')
                    {
                        $aData['magento_id'] = $magentoOrderId;
                    }else{
                        $aData['magento_id'] = $magentoCustomerId;
                    }
                    $result = $this->amconnectorCustomerFactory->create()->syncToAcumatica($aData, $acumaticaAttributes, $customerLog,  $storeId, $logViewFileName, $this->customerLogHelper,$directionFlg = 0, $flag);
                    $customerLog['status'] = "success"; //status
                    $customerLog['messages'] = "Customer Sync completed successfully!"; //messages
                    $customerLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                    $this->customerLogHelper->customerManualSync($customerLog);
                    $txt = "Info : " . $customerLog['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->messageManager->addSuccess("Customer Data sync executed successfully");
                }
            } else {
                /**
                 * Sync to acumatica directly
                 */
                if(isset($orderData) && !empty($orderData))
                {
                    $acumaticaAttributes = $this->resourceModelAmconnectorCustomer->getAcumaticaAttributesForOrder($storeId);
                }else {
                    $acumaticaAttributes = $this->resourceModelAmconnectorCustomer->getAcumaticaAttributes($storeId);
                }

                if(isset($magentoOrderId) && $magentoOrderId != '')
                {
                    $aData['magento_id'] = $magentoOrderId;
                }else{
                    $aData['magento_id'] = $magentoCustomerId;
                }
                $result = $this->amconnectorCustomerFactory->create()->syncToAcumatica($aData, $acumaticaAttributes, $customerLog,   $storeId, $logViewFileName,$this->customerLogHelper, $directionFlg = 0, $flag);
                $customerLog['status'] = "success"; //status
                $customerLog['messages'] = "Customer Sync completed successfully!"; //messages
                $customerLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                $this->customerLogHelper->customerManualSync($customerLog);
                $txt = "Info : " . $customerLog['messages'];
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->messageManager->addSuccess("Customer Data sync executed successfully");
            }
        }

    }

    /**
     * @param $syncId
     * @param $storeId
     * @return mixed
     */
    public function getDataFromAcumatica($syncId, $storeId)
    {
		$totalData =  array(); $data = '';
        $configParameters = $this->dataHelper->getConfigParameters($storeId);
        $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId, $storeId);
        $getlastSyncDateByTimezone =  $this->timezone->date($lastSyncDate,null,true);
        $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
        $toDate = $this->date->date('Y-m-d H:i:s', strtotime("+1 day"));
        $csvCustomerDataFromAcumatica = $this->common->getEnvelopeData('GETCUSTOMERS');
        $XMLGetRequest = $csvCustomerDataFromAcumatica['envelope'];
        $XMLGetRequest = str_replace('{{LASTMODIFIED}}', trim($fromDate), $XMLGetRequest);
        $XMLGetRequest = str_replace('{{LASTMODIFIED2}}', trim($toDate), $XMLGetRequest);
        $action = $csvCustomerDataFromAcumatica['envName'].'/'.$csvCustomerDataFromAcumatica['envVersion'].'/'.$csvCustomerDataFromAcumatica['methodName'];
        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl','stores',$storeId);
        if(!isset($serverUrl))
        {
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
        }
        $url = $this->common->getBasicConfigUrl($serverUrl);
        $xml = $this->common->getAcumaticaResponse($configParameters,$XMLGetRequest,$url,$action);
	if(isset($xml->Body->GetListResponse->GetListResult))
        $data = $xml->Body->GetListResponse->GetListResult;
        $totalData = $this->xmlHelper->xml2array($data);
        return $totalData;
    }


    /**
     * @param $customerEmail
     * @param $storeId
     * @return string
     * Get Acumatica Customer By Email
     */
    public function getAcumaticaCustomerByEmail($customerEmail,$storeId)
    {
        $configParameters = $this->dataHelper->getConfigParameters($storeId);
        $csvCustomerEmailFromAcumatica = $this->common->getEnvelopeData('GETCUSTOMERBYEMAILAPI');
        $XMLGetRequest = $csvCustomerEmailFromAcumatica['envelope'];
        $XMLGetRequest = str_replace('{{EMAIL}}', trim($customerEmail), $XMLGetRequest);
        $action = $csvCustomerEmailFromAcumatica['envName'].'/'.$csvCustomerEmailFromAcumatica['envVersion'].'/'.$csvCustomerEmailFromAcumatica['methodName'];
        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl','stores',$storeId);
        if(!isset($serverUrl))
        {
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
        }
        $url = $this->common->getBasicConfigUrl($serverUrl);
        $xml = $this->common->getAcumaticaResponse($configParameters,$XMLGetRequest,$url,$action);
        $xmlData = $xml->Body->GetListResponse->GetListResult;
        $totalData = $this->xmlHelper->xml2array($xmlData);
        $oneRecordFlag = false;
        $data = '';
        if (isset($totalData['Entity']))
        {
            foreach ($totalData['Entity'] as $key => $value)
            {
                if (!is_numeric($key)) {
                    $oneRecordFlag = true;
                    break;
                }
                $data = $value->CustomerID->Value;
            }
            if ($oneRecordFlag)
            {
                $data = $totalData['Entity']['CustomerID']['Value'];
            }
        }
        return $data;
    }

    /**
     * @param $customerId
     * @param $storeId
     * @return string
     * get Acumatica Customer By Id
     */

    public function getAcumaticaCustomerById($customerId,$storeId)
    {
        $configParameters = $this->dataHelper->getConfigParameters($storeId);
        $csvCustomerIdFromAcumatica = $this->common->getEnvelopeData('GETCUSTOMERBYID');
        $XMLGetRequest = $csvCustomerIdFromAcumatica['envelope'];
        $XMLGetRequest = str_replace('{{CUSTOMERID}}', trim($customerId), $XMLGetRequest);
        $action = $csvCustomerIdFromAcumatica['envName'].'/'.$csvCustomerIdFromAcumatica['envVersion'].'/'.$csvCustomerIdFromAcumatica['methodName'];
        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl','stores',$storeId);
        if(!isset($serverUrl))
        {
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
        }
        $url = $this->common->getBasicConfigUrl($serverUrl);
        $xml = $this->common->getAcumaticaResponse($configParameters,$XMLGetRequest,$url,$action);
        if (!empty($xml->Body->GetResponse->GetResult) && isset($xml->Body->GetResponse->GetResult))
        {
            $data = $xml->Body->GetResponse->GetResult->CustomerID->Value;
            return $data;
        }else {
            $data = '';
            return $data;
        }
    }

    /**
     * @param $entity
     * @param $type
     * @return mixed
     */
    public function getIndividualDataFromAcumatica($entity, $type,$storeId)
    {
        $configParameters = $this->dataHelper->getConfigParameters($storeId);
        if ($type == 'id') {
            $csvGetCustomerById = $this->common->getEnvelopeData('GETCUSTOMERBYID');
            $XMLGetRequest = $csvGetCustomerById['envelope'];
            $XMLGetRequest = str_replace('{{CUSTOMERID}}',$entity,$XMLGetRequest);
            $action = $csvGetCustomerById['envVersion'].'/'.$csvGetCustomerById['envName'].'/'.$csvGetCustomerById['methodName'];
        } else {
            $csvGetCustomerByEmail = $this->common->getEnvelopeData('GETCUSTOMERBYEMAIL');
            $XMLGetRequest = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><GetList xmlns="http://www.acumatica.com/entity/KemsConfig/6.00.001/"><entity xsi:type="CustomerByEmail"><ID xsi:nil="true" /><Delete>false</Delete><ReturnBehavior>All</ReturnBehavior><CustomerDetails xsi:nil="true" /><Email xsi:type="StringSearch"><Value>{{EMAIL}}</Value><Condition>Equal</Condition></Email></entity></GetList></soap:Body></soap:Envelope>';//$csvGetCustomerByEmail['envelope'];*/
            $XMLGetRequest = str_replace('{{EMAIL}}',$entity,$XMLGetRequest);
            $action = $csvGetCustomerByEmail['envName'].'/'.$csvGetCustomerByEmail['envVersion'].'/'.$csvGetCustomerByEmail['methodName'];
        }
        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl','stores',$storeId);
        if(!isset($serverUrl))
        {
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
        }
        $url = $this->common->getBasicConfigUrl($serverUrl);
        $data = '';
        $totalData = '';
        if ($type == 'id') {
            $xml = $this->common->getAcumaticaResponse($configParameters,$XMLGetRequest,$url,$action);
            if(isset($xml->Body->GetResponse->GetResult))
            {
                $data = $xml->Body->GetResponse->GetResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
        } else {
            $xml = $this->common->getAcumaticaResponse($configParameters,$XMLGetRequest,$url,$action);
            if(isset($xml->Body->GetListResponse->GetListResult))
            {
                $data = $xml->Body->GetListResponse->GetListResult;
                $totalEmailData = $this->xmlHelper->xml2array($data);
                $oneRecordFlag = false;
                if(isset($totalEmailData['CustomerDetails'])) {
					$totalData = $totalEmailData;
				}
                /*
                if (isset($totalEmailData['Entity'])) {
                    foreach ($totalEmailData['Entity'] as $key => $value) {
                        if (!is_numeric($key)) {
                            $oneRecordFlag = true;
                            break;
                        }
                        if ($key == 0) {
                            $data = $value;
                        }
                    }
                    if ($oneRecordFlag) {
                        $data = $totalEmailData['Entity'];
                    }
                    $totalData = $this->xmlHelper->xml2array($data);
                }*/
            }
        }
        return $totalData;
    }
}
