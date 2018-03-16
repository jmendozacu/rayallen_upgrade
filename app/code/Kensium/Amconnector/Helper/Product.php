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
use Magento\Framework\Stdlib\DateTime\Timezone as TimeZone;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Config\Definition\Exception\Exception;
use Kensium\Lib;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var Url
     */
    protected $urlHelper;
    /**
     * @var Xml
     */
    protected $xmlHelper;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Product
     */
    protected $resourceModelProduct;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    /**
     * @var TimeZone
     */
    protected $timezone;
    /**
     * @var Time
     */
    protected $timeHelper;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $syncResourceModel;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var Data
     */
    protected $dataHelper;
    /**
     * @var
     */
    protected $successMsg;
    /**
     * @var
     */
    public $totalTrialRecord;
    /**
     * @var
     */
    protected $errorCheck;
    /**
     * @var \Kensium\Synclog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Product
     */
    protected $productResourceModel;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Licensecheck
     */
    protected $licenseResourceModel;
    /**
     * @var ScopeConfigInterface
     */
    protected  $scopeConfigInterface;

    /**
     * @var \Kensium\Amconnector\Model\Product
     */
    protected $amconnectorProductFactory;

    /**
     * @var \Magento\Indexer\Model\ProcessorFactory
     */
    protected $processorFactory;

    /**
     * @var \Kensium\Synclog\Helper\Product
     */
    protected $productLogHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

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
     * @param TimeZone $timezone
     * @param Data $dataHelper
     * @param Time $timeHelper
     * @param \Kensium\Synclog\Helper\Productprice $productPriceHelper
     * @param Xml $xmlHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct
     * @param Url $urlHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Kensium\Amconnector\Model\ResourceModel\Product $productResourceModel
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Indexer\Model\Processor $processorFactory
     * @param \Kensium\Amconnector\Model\ProductFactory $amconnectorProductFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Kensium\Synclog\Helper\Product $productLogHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel
     * @param ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(
        Context $context,
        DateTime $date,
        TimeZone $timezone,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Synclog\Helper\Productprice $productPriceHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Amconnector\Model\ResourceModel\Product $productResourceModel,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Indexer\Model\Processor $processorFactory,
        \Kensium\Amconnector\Model\ProductFactory $amconnectorProductFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Kensium\Synclog\Helper\Product $productLogHelper,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel,
	Lib\Common $common
    )
    {
        parent::__construct($context);
        $this->date             = $date;
        $this->timezone         = $timezone;
        $this->productPriceHelper = $productPriceHelper;
        $this->urlHelper        = $urlHelper;
        $this->dataHelper       = $dataHelper;
        $this->timeHelper       = $timeHelper;
        $this->xmlHelper        = $xmlHelper;
        $this->resourceModelProduct = $resourceModelProduct;
        $this->syncResourceModel= $syncResourceModel;
        $this->messageManager   = $messageManager;
        $this->amconnectorProductFactory = $amconnectorProductFactory;
        $this->productResourceModel = $productResourceModel;
        $this->licenseResourceModel = $licenseResourceModel;
        $this->processor =$processorFactory;
        $this->productFactory = $productFactory;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->productLogHelper = $productLogHelper;
        $this->common = $common;
    }

    /**
     * @param $url
     * @param null $storeId
     */
    public function getProductSchema($url, $storeId = null)
    {
        try {
            if ($storeId == 0 || $storeId == NULL) {
                $storeId = 1;
            }
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvProductSchemaData = $this->common->getEnvelopeData('GETPRODUCTSCHEMA');
            $XMLGetRequest = $csvProductSchemaData['envelope'];
            $action = $csvProductSchemaData['methodName'];
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $action);
            $schemaData = array();
            if($xmlResponse->Body->GetSchemaResponse->GetSchemaResult){
                $data = $xmlResponse->Body->GetSchemaResponse->GetSchemaResult;
                $arrayData = $this->xmlHelper->XMLtoArray($data);
                $schemaData = $arrayData['ENDPOINT']['TOPLEVELENTITY'];
            }
            return $schemaData;
        } catch (SoapFault $e) {
            echo "Last request:<pre>" . htmlentities($e->getMessage()) . "</pre>";
        }
    }

    /**
     * @param $autoSync
     * @param $syncType
     * @param $syncId
     * @param null $scheduleId
     * @param $cronStoreId
     * @param null $individualProductId
     * @param null $configurator
     * @param null $logViewFileName
     * @return int
     */
    public function getProductSync($autoSync, $syncType, $syncId, $scheduleId = NULL,$cronStoreId,$individualProductId = NULL,$configurator = NULL , $logViewFileName = NULL)
    {

        if($configurator == NULL && $logViewFileName == NULL){
            $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'product', NULL);
        }
        $this->totalTrialRecord = $this->common->numberOfRecordSyncInTrialLicense();
        $trialSyncRecordCount = 0;
        try{
            if($configurator == NULL){
                $txt = "Info : Sync process started!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            }
            if($syncType != "AUTO") {
                if ($cronStoreId == 0) {
                    $storeId = 1;
                }else{
                    $storeId = $cronStoreId;
                }
            }else{
                $storeId = $cronStoreId;
            }
            if($cronStoreId == 0){
                $scopeType = 'default';
            }else{
                $scopeType = 'stores';
            }
            $this->licenseType = $this->licenseResourceModel->checkLicenseTypes($storeId);
            if($this->productResourceModel->stopSyncValue() == 1) {

                $licenseStatus = "";
                if($configurator == NULL)
                {
                    $txt = "Info : License verification is in progress";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $licenseStatus = $this->licenseResourceModel->getLicenseStatus($storeId);
                }
                if ($licenseStatus != self::IS_LICENSE_VALID && $configurator == NULL){
                    /**
                     * logs here for Invalid License
                     */
                    if ($scheduleId != '') {
                        $productLog['schedule_id'] = $scheduleId;
                    } else {
                        $productLog['schedule_id'] = "";
                    }
                    $productLog['store_id'] = $storeId;
                    $productLog['job_code'] = "product";
                    $productLog['status'] = "error";
                    $productLog['messages'] = "Invalid License Key";
                    $productLog['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                    $productLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s', time());
                    if ($syncType  == 'MANUAL') {
                        $productLog['runMode'] = 'Manual';
                    } elseif ($syncType == 'AUTO') {
                        $productLog['runMode'] = 'Automatic';
                    }
                    if ($autoSync == 'COMPLETE') {
                        $productLog['autoSync'] = 'Complete';
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $productLog['autoSync'] = 'Individual';
                    }
                    $txt = "Error: " . $productLog['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                    $this->productLogHelper->productManualSync($productLog);
                }else{
                    $timeSyncCheck = "";
                    if($configurator == NULL) {
                        $txt = "Info : License verified successfully!";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                        $txt = "Info : Server time verification is in progress";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        /**
                         * Server time check
                         */
                        $timeSyncCheck = $this->timeHelper->timeSyncCheck($storeId);
                    }
                    if ($timeSyncCheck != self::IS_TIME_VALID && $configurator == NULL) {
                        /**
                         * logs here for Time Not Synced
                         */
                        if ($scheduleId != '') {
                            $productLog['schedule_id'] = $scheduleId;
                        } else {
                            $productLog['schedule_id'] = "";
                        }
                        $productLog['store_id'] = $storeId;
                        $productLog['job_code'] = "product"; //job code
                        $productLog['status'] = "error"; //status
                        $productLog['messages'] = "Server time is not in sync"; //messages
                        $productLog['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                        $productLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s', time());
                        if ($syncType  == 'MANUAL') {
                            $productLog['runMode'] = 'Manual';
                        } elseif ($syncType == 'AUTO') {
                            $productLog['runMode'] = 'Automatic';
                        }
                        if ($autoSync == 'COMPLETE') {
                            $productLog['autoSync'] = 'Complete';
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $productLog['autoSync'] = 'Individual';
                        }
                        $txt = "Error: " . $productLog['messages'];
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                        $this->productLogHelper->productManualSync($productLog);
                    }else{
                        /**
                         * Log here for starting Sync
                         * If $scheduleId == NULL; it means Manual Sync ( Individual or COMPLETE)
                         * If $scheduleId != NULL; AUTO Sync via Cron
                         */
                        if($configurator == NULL){
                            $txt = "Info : Server time is in sync.";
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        }

                        if ($scheduleId != '') {
                            $productLog['schedule_id'] = $scheduleId;
                        } else {
                            $productLog['schedule_id'] = "";
                        }
                        $productLog['store_id'] = $storeId;
                        $productLog['job_code'] = "product";
                        $productLog['status'] = "success";
                        $productLog['messages'] = "Product manual sync initiated";

                        if ($syncType  == 'MANUAL') {
                            $productLog['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                            $productLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s', time());
                            $productLog['runMode'] = 'Manual';
                        } elseif ($syncType == 'AUTO') {
                            $productLog['runMode'] = 'Automatic';
                        }
                        if ($autoSync == 'COMPLETE') {
                            $productLog['autoSync'] = 'Complete';
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $productLog['autoSync'] = 'Individual';
                        }
                        if($configurator == NULL) {
                            $txt = "Info : " . $productLog['messages'];
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        }
                        $syncLogID = $this->productLogHelper->productManualSync($productLog);
                        $this->syncResourceModel->updateSyncAttribute($syncId, 'STARTED', $storeId);

                        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$storeId);
                        if(!isset($serverUrl))
                            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
                        $loginUrl = $this->common->getBasicConfigUrl($serverUrl);
                        if ($autoSync == 'COMPLETE') {
                            /**
                             * Hold the connection flag for product sync
                             */
                            $insertedId = $this->syncResourceModel->checkConnectionFlag($syncId, 'product', $storeId);
                            if ($insertedId == NULL) {
                                $productLog['id'] = $syncLogID;
                                $productLog['job_code'] = "product";
                                $productLog['status'] = "error";
                                $productLog['messages'] = "Another Sync is already executing"; //messages
                                if ($syncType == 'MANUAL') {
                                    $productLog['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    $productLog['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                }
                                $txt = "Info : Sync in Progress - please wait for the current sync to finish";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->productLogHelper->productManualSyncUpdate($productLog);
                            }else{
                                $this->syncResourceModel->updateConnection($insertedId, 'PROCESS',$storeId);
                                $this->syncResourceModel->updateSyncAttribute($syncId, 'PROCESSING', $storeId);
                                $this->productResourceModel->truncateDataFromTempTables();
                                $acumaticaData = $this->getDataFromAcumatica($loginUrl,$syncId, $storeId);
                                $productMappingCheck = $this->productResourceModel->checkProductMapping($storeId);

                                if ($productMappingCheck == 0) {
                                    $productArray['id'] = $syncLogID;
                                    $productArray['job_code'] = "product"; //job code
                                    $productArray['status'] = "error"; //status
                                    $productArray['messages'] = "Product attributes are not mapped for the selected store"; //messages
                                    if ($syncType == 'MANUAL') {
                                        $productLog['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                        $productArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    }
                                    $txt = "Notice :" . $productArray['messages'];
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                    $this->productLogHelper->productManualSyncUpdate($productLog);
                                    $this->syncResourceModel->updateConnection($insertedId, 'ERROR',$storeId);
                                    $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                                } else {
                                    $insertedData = $this->productResourceModel->insertDataIntoTempTables($acumaticaData, $syncId, $scopeType,$storeId);
                                    /**
                                     * Need add attribute Mapping Condition
                                     * Sync to magento
                                     */
                                    $syncToMagentoResult = array();
                                    $mappingAttributes = $this->productResourceModel->getMagentoAttributes($storeId);
                                    $trialSyncRecordCount = 0 ;
                                    if ($this->productResourceModel->stopSyncValue() == 1) {
                                        $productSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/syncdirection',$scopeType,$storeId);
                                        if(!isset($productSyncDirection))
                                        {
                                            $productSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/syncdirection');
                                        }
                                        if ($productSyncDirection == 1 || $productSyncDirection == 3) {
                                            /**
                                             * Here we are doing array reverse because if any upsell and crossell products coming in response they will create before
                                             */
                                            if(isset($insertedData['magento']))
                                                $syncToMagentoReverseData = array_reverse($insertedData['magento'], true);
                                            if(!empty($syncToMagentoReverseData))
                                                foreach ($syncToMagentoReverseData as $aData) {
                                                    if ($this->productResourceModel->stopSyncValue() == 1) {
                                                        if ($aData['acumatica_inventory_id'] != '' && $aData['magento_sku'] != '') {
                                                            $directionFlg = 1;
                                                        } elseif ($aData['acumatica_inventory_id'] != '' && $aData['magento_sku'] == NULL) {
                                                            $_product = $this->productResourceModel->getProductBySku(str_replace(" ","_",$aData['magento_sku']));
                                                            if ($_product == 0) {
                                                                $directionFlg = 0;
                                                            } else {
                                                                $directionFlg = 1;
                                                            }
                                                        } else {
                                                            $directionFlg = 0;
                                                        }
                                                        if ($aData['entity_ref'] == NULL) {
                                                            if (isset($acumaticaData['Entity']['InventoryID']['Value']) && $aData['acumatica_inventory_id'] == $acumaticaData['Entity']['InventoryID']['Value']) {
                                                                if($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord){

                                                                    $syncToMagentoResult = $this->amconnectorProductFactory->create()->syncToMagento($acumaticaData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl,$configurator);                                                                    $trialSyncRecordCount++ ;
                                                                }
                                                                if($this->licenseType != 'trial'){

                                                                    $syncToMagentoResult = $this->amconnectorProductFactory->create()->syncToMagento($acumaticaData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl, $configurator);
                                                                }
                                                            }
                                                        } else {
                                                            if($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord){

                                                                $syncToMagentoResult = $this->amconnectorProductFactory->create()->syncToMagento($acumaticaData['Entity'][$aData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl,$configurator);
                                                                $trialSyncRecordCount++ ;
                                                            }
                                                            if($this->licenseType != 'trial'){

                                                                $syncToMagentoResult = $this->amconnectorProductFactory->create()->syncToMagento($acumaticaData['Entity'][$aData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl,$configurator);
                                                            }
                                                        }
                                                    } else {
                                                        $this->messageManager->addError("Product sync stopped");
                                                        $productArray['id'] = $syncLogID;
                                                        $productArray['job_code'] = "product"; //job code
                                                        $productArray['status'] = "notice"; //status
                                                        $productArray['messages'] = "Product sync stopped"; //messages
                                                        $productArray['finished_at'] = '';
                                                        $this->productLogHelper->productManualSyncUpdate($productArray);
                                                        $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                                        break;
                                                    }
                                                }
                                        }
                                        /**
                                         * Sync to Acumatica
                                         */
                                        $syncToAcumaticaResult = array();
                                        if ($this->productResourceModel->stopSyncValue() == 1) {
                                            if ($productSyncDirection == 2 || $productSyncDirection == 3) {
                                                if ($configurator == NULL) {
                                                    $acumaticaAttributes = $this->productResourceModel->getAcumaticaAttributes($storeId);
                                                    if(isset($insertedData['acumatica']))
                                                        foreach ($insertedData['acumatica'] as $aData) {
                                                            if ($this->productResourceModel->stopSyncValue() == 1) {
                                                                if ($aData['acumatica_inventory_id'] != '' && $aData['magento_sku'] != '') {
                                                                    $directionFlg = 1;
                                                                } else if ($aData['acumatica_inventory_id'] == NULL && $aData['magento_sku'] != '') {
                                                                    $productAvailable = $this->getProductBySku($loginUrl, str_replace("_"," ",$aData['magento_sku']),$storeId);
                                                                    if ($productAvailable)
                                                                    {
                                                                        $directionFlg = 1;
                                                                    } else {
                                                                        $directionFlg = 0;
                                                                    }
                                                                } else {
                                                                    $directionFlg = 0;
                                                                }
                                                                if($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord){

                                                                    $syncToAcumaticaResult = $this->amconnectorProductFactory->create()->syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl, $configurator,$scopeType);
                                                                    $trialSyncRecordCount++ ;
                                                                }
                                                                if($this->licenseType != 'trial'){

                                                                    $syncToAcumaticaResult = $this->amconnectorProductFactory->create()->syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl, $configurator,$scopeType);
                                                                }
                                                            } else {
                                                                $this->messageManager->addError("Product sync stopped");
                                                                $productArray['id'] = $syncLogID;
                                                                $productArray['job_code'] = "product"; //job code
                                                                $productArray['status'] = "notice"; //status
                                                                $productArray['messages'] = "Product sync stopped"; //messages
                                                                $productArray['finished_at'] = '';
                                                                $this->productLogHelper->productManualSyncUpdate($productArray);
                                                                $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                                                break;
                                                            }
                                                        }
                                                }
                                            }
                                        }

                                        /**
                                         * Non Stock Sync
                                         */
                                        $nonstockSyncToMagentoResult = array();
                                        $nonStockSyncToAcumaticaResult = array();
                                        $nonstockSync = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/nonstocksync', $scopeType, $storeId);
                                        if (!isset($nonstockSync))
                                            $nonstockSync = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/nonstocksync');

                                        if ($nonstockSync == 1)
                                        {
                                            $txt = "Info : Non stock product manual sync initiated";
                                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                            $acumaticaNonStockItemData = $this->getNonstockItemDataFromAcumatica($loginUrl,$syncId, $storeId);
                                            $this->productResourceModel->truncateDataFromTempTables();
                                            $insertedNonStockData = $this->productResourceModel->insertDataIntoTempTables($acumaticaNonStockItemData, $syncId, $scopeType, $storeId,$nonStockFlg = 1);

                                            if ($productSyncDirection == 1 || $productSyncDirection == 3)
                                            {
                                                if (isset($insertedNonStockData['magento']))
                                                    $syncToMagentoReverseNonstockData = array_reverse($insertedNonStockData['magento'], true);
                                                if (!empty($syncToMagentoReverseNonstockData))
                                                    foreach ($syncToMagentoReverseNonstockData as $aData) {
                                                        if ($this->productResourceModel->stopSyncValue() == 1) {
                                                            if ($aData['acumatica_inventory_id'] != '' && $aData['magento_sku'] != '') {
                                                                $directionFlg = 1;
                                                            } elseif ($aData['acumatica_inventory_id'] != '' && $aData['magento_sku'] == NULL) {
                                                                $_product = $this->productResourceModel->getProductBySku(str_replace(" ", "_", $aData['magento_sku']));
                                                                if ($_product == 0) {
                                                                    $directionFlg = 0;
                                                                } else {
                                                                    $directionFlg = 1;
                                                                }
                                                            } else {
                                                                $directionFlg = 0;
                                                            }
                                                            if ($aData['entity_ref'] == NULL) {
                                                                if (isset($acumaticaNonStockItemData['Entity']['InventoryID']['Value']) && $aData['acumatica_inventory_id'] == $acumaticaNonStockItemData['Entity']['InventoryID']['Value']) {
                                                                    if ($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord) {
                                                                        $nonstockSyncToMagentoResult = $this->amconnectorProductFactory->create()->syncToMagento($acumaticaNonStockItemData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl, $configurator);
                                                                        $trialSyncRecordCount++;
                                                                    }
                                                                    if ($this->licenseType != 'trial') {
                                                                        $nonstockSyncToMagentoResult = $this->amconnectorProductFactory->create()->syncToMagento($acumaticaNonStockItemData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl, $configurator);
                                                                    }
                                                                }
                                                            } else {
                                                                if ($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord) {
                                                                    $nonstockSyncToMagentoResult = $this->amconnectorProductFactory->create()->syncToMagento($acumaticaNonStockItemData['Entity'][$aData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl, $configurator);
                                                                    $trialSyncRecordCount++;
                                                                }
                                                                if ($this->licenseType != 'trial') {

                                                                    $nonstockSyncToMagentoResult = $this->amconnectorProductFactory->create()->syncToMagento($acumaticaNonStockItemData['Entity'][$aData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl, $configurator);
                                                                }
                                                            }
                                                        } else {
                                                            $this->messageManager->addError("Product sync stopped");
                                                            $productArray['id'] = $syncLogID;
                                                            $productArray['job_code'] = "product"; //job code
                                                            $productArray['status'] = "notice"; //status
                                                            $productArray['messages'] = "Product sync stopped"; //messages
                                                            $productArray['finished_at'] = '';
                                                            $this->productLogHelper->productManualSyncUpdate($productArray);
                                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                                            break;
                                                        }
                                                    }
                                            }
                                            if ($this->productResourceModel->stopSyncValue() == 1)
                                            {
                                                if ($productSyncDirection == 2 || $productSyncDirection == 3)
                                                {
                                                    if ($configurator == NULL) {
                                                        $acumaticaAttributes = $this->productResourceModel->getAcumaticaAttributes($storeId);
                                                        if (isset($insertedNonStockData['acumatica']))
                                                            foreach ($insertedNonStockData['acumatica'] as $aData) {
                                                                if ($this->productResourceModel->stopSyncValue() == 1) {
                                                                    if ($aData['acumatica_inventory_id'] != '' && $aData['magento_sku'] != '') {
                                                                        $directionFlg = 1;
                                                                    } else if ($aData['acumatica_inventory_id'] == NULL && $aData['magento_sku'] != '') {
                                                                        $productAvailable = $this->getProductBySku($loginUrl, str_replace("_", " ", $aData['magento_sku']), $storeId,1);
                                                                        if ($productAvailable) {
                                                                            $directionFlg = 1;
                                                                        } else {
                                                                            $directionFlg = 0;
                                                                        }
                                                                    } else {
                                                                        $directionFlg = 0;
                                                                    }
                                                                    if ($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord) {

                                                                        $nonStockSyncToAcumaticaResult = $this->amconnectorProductFactory->create()->syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl, $configurator, $scopeType);
                                                                        $trialSyncRecordCount++;
                                                                    }
                                                                    if ($this->licenseType != 'trial') {

                                                                        $nonStockSyncToAcumaticaResult = $this->amconnectorProductFactory->create()->syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl, $configurator, $scopeType);
                                                                    }
                                                                } else {
                                                                    $this->messageManager->addError("Product sync stopped");
                                                                    $productArray['id'] = $syncLogID;
                                                                    $productArray['job_code'] = "product"; //job code
                                                                    $productArray['status'] = "notice"; //status
                                                                    $productArray['messages'] = "Product sync stopped"; //messages
                                                                    $productArray['finished_at'] = '';
                                                                    $this->productLogHelper->productManualSyncUpdate($productArray);
                                                                    $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                                                    break;
                                                                }
                                                            }
                                                    }
                                                }
                                            }
                                        }
                                        if (count($syncToMagentoResult) > 0 || count($syncToAcumaticaResult) > 0 || count($nonstockSyncToMagentoResult) > 0 || count($nonStockSyncToAcumaticaResult) > 0) {
                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                                        } else {
                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'SUCCESS', $storeId);
                                        }

                                        if ($this->productResourceModel->stopSyncValue() == 0) {
                                            $txt = "Notice: Product sync stopped";
                                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                        }

                                        sleep(3);
                                        if ($this->productResourceModel->stopSyncValue() == 1) {
                                            /**
                                             *logs here for Sync Success
                                             */
                                            $productArray['id'] = $syncLogID;
                                            $productArray['job_code'] = "product"; //job code
                                            if (count($syncToMagentoResult) >= 1 || count($syncToAcumaticaResult) >= 1 || count($nonstockSyncToMagentoResult) >= 1 || count($nonStockSyncToAcumaticaResult) > 1) {
                                                $productArray['status'] = "error"; //status
                                            }else{
                                                $productArray['status'] = "success"; //status
                                            }
                                            if ($syncType == 'MANUAL') {
                                                $productArray['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                                $productArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                            }
                                            if($this->licenseType == 'trial' && $trialSyncRecordCount == $this->totalTrialRecord){
                                                $productArray['messages'] = "Trial license allow only ".$this->totalTrialRecord." records per sync!"; //messages
                                                if($configurator == NULL){
                                                    $txt = "Info : " . $productArray['messages'];
                                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                                }
                                            }
                                            if($configurator == NULL && $this->licenseType != 'trial'){
                                                $productArray['messages'] = "Product sync executed successfully!"; //messages
                                                $txt ="Info : " . $productArray['messages'];
                                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                            }else{
                                                $productArray['messages'] = "Product sync executed successfully!";
                                            }
                                            $this->productLogHelper->productManualSyncUpdate($productArray);
                                            $this->messageManager->addSuccess("Product sync executed successfully!");
                                        }
                                    } else {
                                        $this->messageManager->addError("Product sync stopped");
                                        $productArray['id'] = $syncLogID;
                                        $productArray['job_code'] = "product";
                                        $productArray['status'] = "notice";
                                        $productArray['messages'] = "Product sync stopped";
                                        $productArray['finished_at'] = '';
                                        $txt = "Notice: Product sync stopped";
                                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                        $this->productLogHelper->productManualSyncUpdate($productArray);
                                        $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                    }
                                    if($this->productResourceModel->stopSyncValue() == 0){
                                        $this->syncResourceModel->updateConnection($insertedId, 'NOTICE',$storeId);
                                    }else{
                                        if (count($syncToMagentoResult) >= 1 || count($syncToAcumaticaResult) >= 1  || count($nonstockSyncToMagentoResult) >= 1  || count($nonStockSyncToAcumaticaResult) > 1) {
                                            $this->syncResourceModel->updateConnection($insertedId, 'SUCCESS',$storeId);
                                        }else{
                                            $this->syncResourceModel->updateConnection($insertedId, 'SUCCESS',$storeId);
                                        }
                                    }
                                }
                            }
                        }elseif ($autoSync == 'INDIVIDUAL') {
                            /**
                             * Individual Sync
                             */
                            if ($individualProductId != '' || $individualProductId != NULL) {
                                $magentoProductId = $individualProductId;
                                $magentoProductData = $this->productFactory->create()->load($magentoProductId);
                                $AcumaticaStockItem = strtoupper($magentoProductData->getSku());
                                $nonStockItemStatus = $this->resourceModelProduct->getIsNonStock($magentoProductData->getRowId(),$storeId);
                                $nonStockItemFlg = NULL;
                                if($nonStockItemStatus == 1)
                                {
                                    $nonStockItemFlg = 1;
                                }
                                $mappingAttributes = $this->resourceModelProduct->getMagentoAttributes($storeId);

                                $productMappingCheck = $this->resourceModelProduct->checkProductMapping($storeId);
                                if ($productMappingCheck == 0) {
                                    $this->messageManager->addError("Product attributes are not mapped for the selected store");
                                    $productArray['id'] = $syncLogID;
                                    $productArray['job_code'] = "product"; //job code
                                    $productArray['status'] = "error"; //status
                                    $productArray['messages'] = "Product attributes are not mapped for the selected store"; //messages
                                    $productArray['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    $productArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    $this->productLogHelper->productManualSyncUpdate($productArray);
                                } else {
                                    if ($AcumaticaStockItem != '' && $nonStockItemFlg == NULL)
                                    {
                                        $acumaticaProductData = $this->getProductBySkuForIndividual($loginUrl, $AcumaticaStockItem,$storeId);
                                    }else if($AcumaticaStockItem != '' && $nonStockItemFlg)
                                    {
                                        $acumaticaProductData = $this->getNonStockProductBySkuForIndividual($loginUrl, $AcumaticaStockItem,$storeId);
                                    }
                                    if (!empty($acumaticaProductData)) {
                                        $acumaticaUpdatedDate = $acumaticaProductData['LastModified']['Value'];
                                        $this->date->date('Y-m-d H:i:s', strtotime($acumaticaUpdatedDate));
                                        $magentoUpdatedDate = $magentoProductData->getUpdatedAt();
                                        $magentoUpdatedDateByTimezone =  $this->timezone->date($magentoUpdatedDate,null,true);
                                        $updatedDate = $magentoUpdatedDateByTimezone->format('Y-m-d H:i:s');
                                        if (strtotime($acumaticaUpdatedDate) > strtotime($updatedDate)) {
                                            /**
                                             * Sync To Magento
                                             */
                                            $result = $this->amconnectorProductFactory->create()->syncToMagento($acumaticaProductData, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg = 0, $loginUrl,NULL);
                                            $productArray['id'] = $syncLogID;
                                            $productArray['job_code'] = "product"; //job code
                                            $productArray['status'] = "success"; //status
                                            $productArray['messages'] = "Sync executed successfully!"; //messages
                                            $productArray['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                            $productArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                            $this->productLogHelper->productManualSyncUpdate($productArray);
                                            $this->messageManager->addSuccess("Product sync executed successfully!");
                                        } else {
                                            /**
                                             * Sync To acumatica
                                             */
                                            $acumaticaAttributes = $this->resourceModelProduct->getAcumaticaAttributes($storeId);
                                            $aData['magento_sku'] = $AcumaticaStockItem;
                                            $aData['magento_id'] = $magentoProductData->getId();
                                            $result = $this->amconnectorProductFactory->create()->syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg = 0, $loginUrl,$configurator = NULL,$scopeType);
                                            $productArray['id'] = $syncLogID;
                                            $productArray['job_code'] = "product"; //job code
                                            $productArray['status'] = "success"; //status
                                            $productArray['messages'] = "Sync executed successfully!"; //messages
                                            $productArray['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                            $productArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                            $this->productLogHelper->productManualSyncUpdate($productArray);
                                            $this->messageManager->addSuccess("Product sync executed successfully!");

                                        }
                                    } else {
                                        /**
                                         * Sync to acumatica directly
                                         */
                                        $acumaticaAttributes = $this->resourceModelProduct->getAcumaticaAttributes($storeId);
                                        $aData['magento_sku'] = $AcumaticaStockItem;
                                        $aData['magento_id'] = $magentoProductData->getId();
                                        $result = $this->amconnectorProductFactory->create()->syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg = 0, $loginUrl,$configurator = NULL,$scopeType);
                                        $productArray['id'] = $syncLogID;
                                        $productArray['job_code'] = "product"; //job code
                                        $productArray['status'] = "success"; //status
                                        $productArray['messages'] = "Sync executed successfully!"; //messages
                                        $productArray['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                        $productArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                        $this->productLogHelper->productManualSyncUpdate($productArray);
                                        $this->messageManager->addSuccess("Product sync executed successfully!");
                                    }
                                }
                            }
                        }
                    }
                }
            }else {
                if ($scheduleId != '') {
                    $productArray['schedule_id'] = $scheduleId;
                } else {
                    $productArray['schedule_id'] = "";
                }
                $productArray['store_id'] = $storeId;
                $productArray['job_code'] = "product";
                $productArray['status'] = "notice";
                $productArray['messages'] = "Product sync stopped";
                $productArray['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                $productArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s', time());
                if ($syncType == 'MANUAL') {
                    $productArray['runMode'] = 'Manual';
                } elseif ($syncType == 'AUTO') {
                    $productArray['runMode'] = 'Automatic';
                }
                if ($autoSync == 'COMPLETE') {
                    $productArray['autoSync'] = 'Complete';
                } elseif ($autoSync == 'INDIVIDUAL') {
                    $productArray['autoSync'] = 'Individual';
                }
                $txt = "Notice: Product sync stopped";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->productLogHelper->productManualSync($productArray);
                $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
            }
        }catch(Exception $e){
            /**
             *logs here for Exception
             */
            if ($scheduleId != '') {
                $productArray['id'] = $scheduleId;
            } else {
                $productArray['id'] = $syncLogID;
            }
            $productArray['job_code'] = "product"; //job code
            $productArray['status'] = "error"; //status
            $productArray['messages'] = $e->getMessage(); //message
            $productArray['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
            $productArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
            $txt = "Error: Sync error occurred. Please try again";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->productLogHelper->productManualSyncUpdate($productArray);
            $this->messageManager->addError($e->getMessage());
            $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
            $this->syncResourceModel->updateConnection($insertedId, 'ERROR',$storeId);
        }
        $this->productResourceModel->enableSync();
        /**
         * Reindexing Magento Indexes
         */
        $productIndex = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/reindex',$scopeType,$storeId);
        if(!isset($productIndex))
        {
            $productIndex = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/reindex');
        }
        if($productIndex == 1){
            $this->processor->reindexAll();
        }
        if($configurator != NULL){
            return $trialSyncRecordCount;
        }
        if($configurator == NULL){
            $txt = "Info : Sync process completed!";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
        }
    }

    /**
     * @param $url
     * @param $syncId
     * @param $storeId
     *
     * Get product from acumatica by date
     */
    public function getDataFromAcumatica($url,$syncId,$storeId)
    {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvProductData = $this->common->getEnvelopeData('GETPRODUCTS');
            $XMLGetRequest = $csvProductData['envelope'];
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId,$storeId);
            $getlastSyncDateByTimezone =  $this->timezone->date($lastSyncDate,null,true);
            $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
            $toDate = $this->date->date('Y-m-d H:i:s',strtotime("+1 day"));
            $XMLGetRequest = str_replace('{{FROMDATE}}',$fromDate,$XMLGetRequest);
            $XMLGetRequest = str_replace('{{TODATE}}',$toDate,$XMLGetRequest);
            $productAction = $csvProductData['envName'] . "/" . $csvProductData['envVersion'] . "/" . $csvProductData['methodName'];
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $totalData = array();
            if(isset($xmlResponse->Body->GetListResponse->GetListResult)){
                $data = $xmlResponse->Body->GetListResponse->GetListResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            return $totalData;
        }catch (SoapFault $e) {
            echo "Last request:<pre>" . $e->getMessage() . "</pre>";
        }
    }


    /**
     * fetching data from Acumatica based on last sync date for individual
     * @param $url
     * @param $productSku
     */
    public function getProductBySkuForIndividual($url,$productSku,$storeId)
    {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvProductData = $this->common->getEnvelopeData('GETPRODUCTBYSKUID');
            $XMLGetRequest = $csvProductData['envelope'];
            $XMLGetRequest = str_replace('{{INVENTORYID}}',$productSku,$XMLGetRequest);
            $productAction = $csvProductData['envName'] . "/" . $csvProductData['envVersion'] . "/" . $csvProductData['methodName'];
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $totalData = array();
            if(isset($xmlResponse->Body->GetResponse->GetResult)){
                $data = $xmlResponse->Body->GetResponse->GetResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            return $totalData;
        }catch (SoapFault $e) {
            echo "Last request:<pre>" . $e->getMessage() . "</pre>";
        }
    }

    /**
     * @param $url
     * @param $productSku
     * @param $storeId
     */
    public function getProductBySku($url, $productSku,$storeId,$nonStockFlg = NULL)
    {
        try {

            if($nonStockFlg != NULL){
                $csvProductData = $this->common->getEnvelopeData('GETNONSTOCKPRODUCTBYSKUID');
            }else {
                $csvProductData = $this->common->getEnvelopeData('GETPRODUCTBYSKUID');
            }
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $XMLGetRequest = $csvProductData['envelope'];
            $XMLGetRequest = str_replace('{{INVENTORYID}}',$productSku,$XMLGetRequest);
            $productAction = $csvProductData['envName'] . "/" . $csvProductData['envVersion'] . "/" . $csvProductData['methodName'];
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            if(isset($xmlResponse->Body->GetResponse->GetResult)){
                $data = $xmlResponse->Body->GetResponse->GetResult;
                $totalData = $this->xmlHelper->xml2array($data);
                if (isset($totalData['InventoryID'])) {
                    if ($totalData['InventoryID']['Value'] != '') {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }else{
                return false;
            }
        } catch (SoapFault $e) {
            echo "Last request:<pre>" .($e->getMessage()) . "</pre>";
        }
    }


    /**
     * fetching data from Acumatica based on last sync date for individual
     * @param $url
     * @param $productSku
     */
    public function getNonStockProductBySkuForIndividual($url, $productSku,$storeId)
    {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvProductData = $this->common->getEnvelopeData('GETNONSTOCKPRODUCTBYSKUID');
            $XMLGetRequest = $csvProductData['envelope'];
            $XMLGetRequest = str_replace('{{INVENTORYID}}',$productSku,$XMLGetRequest);
            $productAction = $csvProductData['envName'] . "/" . $csvProductData['envVersion'] . "/" . $csvProductData['methodName'];
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $totalData = '';
            if(isset($xmlResponse->Body->GetResponse->GetResult)){
                $data = $xmlResponse->Body->GetResponse->GetResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            return $totalData;
        } catch (SoapFault $e) {
            echo "Last request:<pre>" .($e->getMessage()) . "</pre>";
        }
    }


    /**
     * @param $url
     * @param $syncId
     * @param $storeId
     */
    public function getNonstockItemDataFromAcumatica($url, $syncId, $storeId)
    {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvProductData = $this->common->getEnvelopeData('GETNONSTOCKPRODUCTS');
            $XMLGetRequest = $csvProductData['envelope'];
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId, $storeId);
            $getlastSyncDateByTimezone = $this->timezone->date($lastSyncDate, null, true);
            $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
            $toDate = $this->date->date('Y-m-d H:i:s', strtotime("+1 day"));
            $XMLGetRequest = str_replace('{{FROMDATE}}', $fromDate, $XMLGetRequest);
            $XMLGetRequest = str_replace('{{TODATE}}', $toDate, $XMLGetRequest);
            $productAction = $csvProductData['envName'] . "/" . $csvProductData['envVersion'] . "/" . $csvProductData['methodName'];
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $totalData = array();
            if(isset($xmlResponse->Body->GetListResponse->GetListResult)){
                $data = $xmlResponse->Body->GetListResponse->GetListResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            return $totalData;
        } catch (SoapFault $e) {
            echo "Last request:<pre>" . $e->getMessage() . "</pre>";
        }
    }
}
