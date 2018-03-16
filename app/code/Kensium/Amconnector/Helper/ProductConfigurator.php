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
class ProductConfigurator extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Context
     */
    protected $context;
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
     * @var
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
     * @var \Kensium\Synclog\Helper\ProductConfigurator
     */
    protected $productConfiguratorLogHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var
     */
    protected $licensecheck;

    /**
     * @var
     */
    protected $productConfiguratorResourceModel;

    /**
     * @var \Kensium\Amconnector\Model\ProductConfiguratorFactory
     */
    protected $productConfiguratorFactory;
    
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
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param Xml $xmlHelper
     * @param Product $productHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Kensium\Amconnector\Model\ResourceModel\Product $productResourceModel
     * @param \Kensium\Amconnector\Model\ResourceModel\ProductConfigurator $productConfiguratorResourceModel
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Indexer\Model\Processor $processorFactory
     * @param \Kensium\Amconnector\Model\ProductFactory $amconnectorProductFactory
     * @param \Kensium\Amconnector\Model\ProductConfiguratorFactory $productConfiguratorFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Kensium\Synclog\Helper\ProductConfigurator $productConfiguratorLogHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        DateTime $date,
        TimeZone $timezone,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Synclog\Helper\Productprice $productPriceHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Helper\Product $productHelper,
        \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Amconnector\Model\ResourceModel\Product $productResourceModel,
        \Kensium\Amconnector\Model\ResourceModel\ProductConfigurator $productConfiguratorResourceModel,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Indexer\Model\Processor $processorFactory,
        \Kensium\Amconnector\Model\ProductFactory $amconnectorProductFactory,
        \Kensium\Amconnector\Model\ProductConfiguratorFactory $productConfiguratorFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Kensium\Synclog\Helper\ProductConfigurator $productConfiguratorLogHelper,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel,
         Lib\Common $common
    )
    {
        parent::__construct($context);
        $this->date             = $date;
        $this->timezone         = $timezone;
        $this->productPriceHelper = $productPriceHelper;
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
        $this->productHelper = $productHelper;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->productConfiguratorLogHelper = $productConfiguratorLogHelper;
        $this->productConfiguratorFactory = $productConfiguratorFactory;
        $this->logger = $context->getLogger();
        $this->objectManager = $objectManager;
        $this->productConfiguratorResourceModel = $productConfiguratorResourceModel;
        $this->common = $common;
    }

    /**
     * @param $autoSync
     * @param $syncType
     * @param $syncId
     * @param null $scheduleId
     * @param $cronStoreId
     * @param null $individualProductId
     * @return int
     */
    public function getProductConfiguratorSync($autoSync, $syncType, $syncId, $scheduleId = NULL,$cronStoreId,$individualProductId = NULL)
    {
        $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'productConfigurator', NULL);
        $this->totalTrialRecord = $this->common->numberOfRecordSyncInTrialLicense();
        try{

            $txt = "Info : Sync process started!";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
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
            if($this->productConfiguratorResourceModel->stopSyncValue() == 1) {
                $txt = "Info : License verification is in progress";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $licenseStatus = $this->licenseResourceModel->getLicenseStatus($storeId);
                if ($licenseStatus != self::IS_LICENSE_VALID) {
                    /**
                     * logs here for Invalid License
                     */
                    if ($scheduleId != '') {
                        $productLog['schedule_id'] = $scheduleId;
                    } else {
                        $productLog['schedule_id'] = "";
                    }
                    $productLog['store_id'] = $storeId;
                    $productLog['job_code'] = "productConfigurator";
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
                    $this->productConfiguratorLogHelper->productManualSync($productLog);
                }else{
                    $txt = "Info : License verified successfully!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    $txt = "Info : Server time verification is in progress";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    /**
                     * Server time check
                     */
                    $timeSyncCheck = $this->timeHelper->timeSyncCheck($storeId);
                    if ($timeSyncCheck != self::IS_TIME_VALID) {
                        /**
                         * logs here for Time Not Synced
                         */
                        if ($scheduleId != '') {
                            $productLog['schedule_id'] = $scheduleId;
                        } else {
                            $productLog['schedule_id'] = "";
                        }
                        $productLog['store_id'] = $storeId;
                        $productLog['job_code'] = "productConfigurator"; //job code
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
                        $this->productConfiguratorLogHelper->productManualSync($productLog);
                    }else{
                        /**
                         * Log here for starting Sync
                         * If $scheduleId == NULL; it means Manual Sync ( Individual or COMPLETE)
                         * If $scheduleId != NULL; AUTO Sync via Cron
                         */
                        $txt = "Info : Server time is in sync.";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        if ($scheduleId != '') {
                            $productLog['schedule_id'] = $scheduleId;
                        } else {
                            $productLog['schedule_id'] = "";
                        }
                        $productLog['store_id'] = $storeId;
                        $productLog['job_code'] = "productConfigurator";
                        $productLog['status'] = "success";
                        $productLog['messages'] = "Product Configurator manual sync initiated";

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
                        $txt = "Info : " . $productLog['messages'];
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $syncLogID = $this->productConfiguratorLogHelper->productManualSync($productLog);
                        $this->syncResourceModel->updateSyncAttribute($syncId, 'STARTED', $storeId);

                        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$storeId);
                        if(!isset($serverUrl))
                            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
                        $loginUrl = $this->common->getBasicConfigUrl($serverUrl);
                        if ($autoSync == 'COMPLETE') {
                            /**
                             * Hold the connection flag for product sync
                             */
                            $insertedId = $this->syncResourceModel->checkConnectionFlag($syncId, 'productConfigurator', $storeId);
                            if ($insertedId == NULL) {
                                $productLog['id'] = $syncLogID;
                                $productLog['job_code'] = "productConfigurator";
                                $productLog['status'] = "error";
                                $productLog['messages'] = "Another Sync is already executing"; //messages
                                if ($syncType == 'MANUAL') {
                                    $productLog['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    $productLog['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                }
                                $txt = "Info : Sync in Progress - please wait for the current sync to finish";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->productConfiguratorLogHelper->productManualSyncUpdate($productLog);
                            }else{
                                $this->syncResourceModel->updateConnection($insertedId, 'PROCESS','',$storeId);
                                $this->syncResourceModel->updateSyncAttribute($syncId, 'PROCESSING', $storeId);
                                /**
                                 * Need add attribute Mapping Condition
                                 * Sync to magento
                                 */
                                $productMappingCheck = $this->productResourceModel->checkProductMapping($storeId);
                                if ($productMappingCheck == 0) {
                                    //$productArray['id'] = $syncLogID;
                                    $productArray['job_code'] = "productConfigurator"; //job code
                                    $productArray['status'] = "error"; //status
                                    $productArray['messages'] = "Product attributes are not mapped for the selected store"; //messages
                                    if ($syncType == 'MANUAL') {
                                        $productLog['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                        $productArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    }
                                    $txt = "Notice :" . $productArray['messages'];
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                    $this->productConfiguratorLogHelper->productManualSyncUpdate($productLog);
                                    $this->syncResourceModel->updateConnection($insertedId, 'ERROR','',$storeId);
                                    $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                                } else {


                                    /**
                                     * simple Product Sync
                                     */
                                    $simpleProductSyncId = $this->syncResourceModel->getSyncId('product',$storeId);
                                    $syncRecord = $this->productHelper->getProductSync($autoSync, $syncType, $simpleProductSyncId , $scheduleId = NULL,$storeId,$individualProductId = NULL,$configurator = 1,$logViewFileName);

                                    $productSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/configuratorsync/syncdirection',$scopeType,$storeId);
                                    if(!isset($productSyncDirection))
                                    {
                                        $productSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/configuratorsync/syncdirection');
                                    }

                                    $syncToMagentoResult = array();
                                    $mappingAttributes = $this->productResourceModel->getMagentoAttributes($storeId);
                                    $trialSyncRecordCount = 0 ;
                                    if ($this->productConfiguratorResourceModel->stopSyncValue() == 1)
                                    {
                                        /**
                                         * Configurable product
                                         */
                                        $configurableSyncToMagentoResult = array();
                                        if ($this->productConfiguratorResourceModel->stopSyncValue() == 1)
                                        {
                                            $this->productConfiguratorResourceModel->truncateDataFromTempTables('configurable');
                                            $configurableAcumaticaData = $this->getConfigurableProductDataFromAcumatica($loginUrl, $syncId,$storeId);
                                            $configurableInsertedData = $this->productConfiguratorResourceModel->insertDataIntoTempTables($configurableAcumaticaData, $syncId,$scopeType, $storeId,"configurable");
                                            if ($productSyncDirection == 1 || $productSyncDirection == 3)
                                            {
                                                if(isset($configurableInsertedData['magento']) && !empty($configurableInsertedData['magento']))
                                                {
                                                    foreach ($configurableInsertedData['magento'] as $configurableData)
                                                    {
                                                        if ($this->productConfiguratorResourceModel->stopSyncValue() == 1)
                                                        {
                                                            if ($configurableData['acumatica_inventory_id'] != '' && $configurableData['magento_sku'] != '') {
                                                                $configurableDirectionFlg = 1;
                                                            } elseif ($configurableData['acumatica_inventory_id'] != '' && $configurableData['magento_sku'] == NULL)
                                                            {
                                                                $_product = $this->productResourceModel->getProductBySku(str_replace(" ","_",$configurableData['acumatica_inventory_id']));
                                                                if ($_product == 0) {
                                                                    $configurableDirectionFlg = 0;
                                                                } else {
                                                                    $configurableDirectionFlg = 1;
                                                                }
                                                            } else {
                                                                $configurableDirectionFlg = 0;
                                                            }
                                                            if ($configurableData['entity_ref'] == NULL)
                                                            {
                                                                if (isset($configurableAcumaticaData['Entity']['InventoryID']['Value']) && $configurableData['acumatica_inventory_id'] == $configurableAcumaticaData['Entity']['InventoryID']['Value'])
                                                                {
                                                                    if ($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord /*&& $syncRecord < $this->totalTrialRecord*/) {
                                                                        $configurableSyncToMagentoResult = $this->productConfiguratorFactory->create()->configurableSyncToMagento($configurableAcumaticaData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $configurableDirectionFlg, $loginUrl);
                                                                        $trialSyncRecordCount++;
                                                                    }
                                                                    if ($this->licenseType != 'trial') {
                                                                        $configurableSyncToMagentoResult = $this->productConfiguratorFactory->create()->configurableSyncToMagento($configurableAcumaticaData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $configurableDirectionFlg, $loginUrl);
                                                                    }
                                                                }
                                                            } else {
                                                                if ($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord /*&& $syncRecord < $this->totalTrialRecord*/) {
                                                                    $configurableSyncToMagentoResult = $this->productConfiguratorFactory->create()->configurableSyncToMagento($configurableAcumaticaData['Entity'][$configurableData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $configurableDirectionFlg, $loginUrl);
                                                                    $trialSyncRecordCount++;
                                                                }
                                                                if ($this->licenseType != 'trial') {
                                                                    $configurableSyncToMagentoResult = $this->productConfiguratorFactory->create()->configurableSyncToMagento($configurableAcumaticaData['Entity'][$configurableData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $configurableDirectionFlg, $loginUrl);
                                                                }
                                                            }
                                                        } else {
                                                            $this->messageManager->addError("Product Configurator sync stopped");
                                                            $productArray['id'] = $syncLogID;
                                                            $productArray['job_code'] = "productConfigurator"; //job code
                                                            $productArray['status'] = "notice"; //status
                                                            $productArray['messages'] = "Product Configurator sync stopped"; //messages
                                                            $productArray['finished_at'] = '';
                                                            $this->productConfiguratorLogHelper->productManualSyncUpdate($productArray);
                                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        /**
                                         * Group Product
                                         */
                                        if ($this->productConfiguratorResourceModel->stopSyncValue() == 1) {
                                            if ($productSyncDirection == 1 || $productSyncDirection == 3) {
                                                $this->productConfiguratorResourceModel->truncateDataFromTempTables('grouped');
                                                $groupedAcumaticaData = $this->getGroupProductDataFromAcumatica($loginUrl, $syncId, $storeId);
                                                if (isset($groupedAcumaticaData) && !empty($groupedAcumaticaData))
                                                    $insertedData = $this->productConfiguratorResourceModel->insertDataIntoTempTables($groupedAcumaticaData, $syncId, $scopeType, $storeId, 'grouped');

                                                if (isset($insertedData['magento']))
                                                    $syncToMagentoReverseData = array_reverse($insertedData['magento'], true);
                                                if (!empty($syncToMagentoReverseData))
                                                    foreach ($syncToMagentoReverseData as $aData) {
                                                        if ($this->productConfiguratorResourceModel->stopSyncValue() == 1) {
                                                            if ($aData['acumatica_inventory_id'] != '' && $aData['magento_sku'] != '') {
                                                                $directionFlg = 1;
                                                            } elseif ($aData['acumatica_inventory_id'] != '' && $aData['magento_sku'] == NULL) {
                                                                $_product = $this->productResourceModel->getProductBySku($aData['magento_sku']);
                                                                if ($_product == 0) {
                                                                    $directionFlg = 0;
                                                                } else {
                                                                    $directionFlg = 1;
                                                                }
                                                            } else {
                                                                $directionFlg = 0;
                                                            }
                                                            if ($aData['entity_ref'] == NULL) {
                                                                if (isset($groupedAcumaticaData['Entity']['InventoryID']['Value']) && $aData['acumatica_inventory_id'] == $groupedAcumaticaData['Entity']['InventoryID']['Value']) {
                                                                    if ($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord) {
                                                                        $syncToMagentoResult = $this->productConfiguratorFactory->create()->groupedSyncToMagento($groupedAcumaticaData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl);
                                                                        $trialSyncRecordCount++;
                                                                    }
                                                                    if ($this->licenseType != 'trial') {

                                                                        $syncToMagentoResult = $this->productConfiguratorFactory->create()->groupedSyncToMagento($groupedAcumaticaData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl);
                                                                    }
                                                                }
                                                            } else {
                                                                if ($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord) {

                                                                    $syncToMagentoResult = $this->productConfiguratorFactory->create()->groupedSyncToMagento($groupedAcumaticaData['Entity'][$aData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl);
                                                                    $trialSyncRecordCount++;
                                                                }
                                                                if ($this->licenseType != 'trial') {

                                                                    $syncToMagentoResult = $this->productConfiguratorFactory->create()->groupedSyncToMagento($groupedAcumaticaData['Entity'][$aData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg, $loginUrl);
                                                                }
                                                            }
                                                        } else {
                                                            $this->messageManager->addError("Product Configurator sync stopped");
                                                            $productArray['id'] = $syncLogID;
                                                            $productArray['job_code'] = "productConfigurator"; //job code
                                                            $productArray['status'] = "notice"; //status
                                                            $productArray['messages'] = "Product sync stopped"; //messages
                                                            $productArray['finished_at'] = '';
                                                            $this->productConfiguratorLogHelper->productManualSyncUpdate($productArray);
                                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                                            break;
                                                        }
                                                    }
                                            }
                                        }
                                        /**
                                         * Bundle product
                                         */
                                        $bundleSyncToMagentoResult = array();
                                        if ($this->productConfiguratorResourceModel->stopSyncValue() == 1)
                                        {
                                            $this->productConfiguratorResourceModel->truncateDataFromTempTables('bundle');
                                            $bundleAcumaticaData = $this->getBundleProductDataFromAcumatica($loginUrl, $syncId,$storeId);
                                            $bundleInsertedData = $this->productConfiguratorResourceModel->insertDataIntoTempTables($bundleAcumaticaData, $syncId,$scopeType,$storeId,"bundle");
                                            if ($productSyncDirection == 1 || $productSyncDirection == 3) {
                                                if(isset($bundleInsertedData['magento']) && !empty($bundleInsertedData['magento']))
                                                {
                                                    foreach ($bundleInsertedData['magento'] as $bundleData) {
                                                        if ($this->productConfiguratorResourceModel->stopSyncValue() == 1)
                                                        {
                                                            if ($bundleData['acumatica_inventory_id'] != '' && $bundleData['magento_sku'] != '') {
                                                                $bundledirectionFlg = 1;
                                                            } elseif ($bundleData['acumatica_inventory_id'] != '' && $bundleData['magento_sku'] == NULL) {
                                                                $_product = $this->productResourceModel->getProductBySku($bundleData['acumatica_inventory_id']);
                                                                if ($_product == 0)
                                                                {
                                                                    $bundledirectionFlg = 0;
                                                                } else {
                                                                    $bundledirectionFlg = 1;
                                                                }
                                                            } else {
                                                                $bundledirectionFlg = 0;
                                                            }
                                                            if ($bundleData['entity_ref'] == NULL) {
                                                                if (isset($bundleAcumaticaData['Entity']['InventoryID']['Value']) && $bundleData['acumatica_inventory_id'] == $bundleAcumaticaData['Entity']['InventoryID']['Value'])
                                                                {
                                                                    if($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord)
                                                                    {
                                                                        $bundleSyncToMagentoResult = $this->productConfiguratorFactory->create()->bundleSyncToMagento($bundleAcumaticaData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $bundledirectionFlg, $loginUrl);
                                                                        $trialSyncRecordCount++;
                                                                    }
                                                                    if ($this->licenseType != 'trial') {
                                                                        $bundleSyncToMagentoResult = $this->productConfiguratorFactory->create()->bundleSyncToMagento($bundleAcumaticaData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $bundledirectionFlg, $loginUrl);
                                                                    }
                                                                }
                                                            } else {
                                                                if($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord)
                                                                {
                                                                    $bundleSyncToMagentoResult = $this->productConfiguratorFactory->create()->bundleSyncToMagento($bundleAcumaticaData['Entity'][$bundleData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $bundledirectionFlg, $loginUrl);
                                                                    $trialSyncRecordCount++;
                                                                }
                                                                if ($this->licenseType != 'trial') {
                                                                    $bundleSyncToMagentoResult = $this->productConfiguratorFactory->create()->bundleSyncToMagento($bundleAcumaticaData['Entity'][$bundleData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $bundledirectionFlg, $loginUrl);
                                                                }
                                                            }
                                                        } else {
                                                            $this->messageManager->addError("Product Configurator sync stopped");
                                                            $productArray['id'] = $syncLogID;
                                                            $productArray['job_code'] = "productConfigurator"; //job code
                                                            $productArray['status'] = "notice"; //status
                                                            $productArray['messages'] = "Product Configurator sync stopped"; //messages
                                                            $productArray['finished_at'] = '';
                                                            $this->productConfiguratorLogHelper->productManualSyncUpdate($productArray);
                                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        if (count($configurableSyncToMagentoResult) >= 1 || count($syncToMagentoResult) >= 1 || count($bundleSyncToMagentoResult) >=1)
                                        {
                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                                        } else {
                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'SUCCESS', $storeId);
                                        }

                                        if ($this->productConfiguratorResourceModel->stopSyncValue() == 0) {
                                            $txt = "Notice: Product sync stopped";
                                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                        }

                                        sleep(3);
                                        if ($this->productConfiguratorResourceModel->stopSyncValue() == 1) {
                                            /**
                                             *logs here for Sync Success
                                             */
                                            $productArray['id'] = $syncLogID;
                                            $productArray['job_code'] = "productConfigurator"; //job code
                                            if (count($configurableSyncToMagentoResult) >= 1)
                                            {
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
                                                $txt = "Info : " . $productArray['messages'];
                                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                            }
                                            if($this->licenseType != 'trial')
                                            {
                                                $productArray['messages'] = "Product sync executed successfully!"; //messages
                                                $txt ="Info : " . $productArray['messages'];
                                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                            }else{
                                                $productArray['messages'] = "Product sync executed successfully!";
                                            }
                                            $this->productConfiguratorLogHelper->productManualSyncUpdate($productArray);
                                            $this->messageManager->addSuccess("Product sync executed successfully!");
                                        }
                                    } else {
                                        $this->messageManager->addError("Product sync stopped");
                                        $productArray['id'] = $syncLogID;
                                        $productArray['job_code'] = "productConfigurator";
                                        $productArray['status'] = "notice";
                                        $productArray['messages'] = "Product sync stopped";
                                        $productArray['finished_at'] = '';
                                        $txt = "Notice: Product sync stopped";
                                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                        $this->productConfiguratorLogHelper->productManualSyncUpdate($productArray);
                                        $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                    }
                                    if($this->productConfiguratorResourceModel->stopSyncValue() == 0){
                                        $this->syncResourceModel->updateConnection($insertedId, 'NOTICE','',$storeId);
                                    }else{
                                        if (count($configurableSyncToMagentoResult) >= 1 || count($syncToMagentoResult) >= 1 || count($bundleSyncToMagentoResult) >=1)
                                        {
                                            $this->syncResourceModel->updateConnection($insertedId, 'SUCCESS','',$storeId);
                                        }else{
                                            $this->syncResourceModel->updateConnection($insertedId, 'SUCCESS','',$storeId);
                                        }
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
                $productArray['job_code'] = "productConfigurator";
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
                $this->productConfiguratorLogHelper->productManualSync($productArray);
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
            $productArray['job_code'] = "productConfigurator"; //job code
            $productArray['status'] = "error"; //status
            $productArray['messages'] = $e->getMessage(); //message
            $productArray['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
            $productArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
            $txt = "Error: Sync error occurred. Please try again";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->productConfiguratorLogHelper->productManualSyncUpdate($productArray);
            $this->messageManager->addError($e->getMessage());
            $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
            $this->syncResourceModel->updateConnection($insertedId, 'ERROR','',$storeId);
        }
        $txt = "Info : Sync process completed!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
        $this->productConfiguratorResourceModel->enableSync();
        /**
         * Reindexing Magento Indexes
         */
        $productIndex = $this->scopeConfigInterface->getValue('amconnectorsync/configuratorsync/reindex',$scopeType,$storeId);
        if(!isset($productIndex))
        {
            $productIndex = $this->scopeConfigInterface->getValue('amconnectorsync/configuratorsync/reindex');
        }
        if($productIndex == 1)
        {
            $this->processor->reindexAll();
        }
    }

    /**
     * fetching data from Acumatica based on last sync date for individual
     * @param $url
     * @param $productSku
     */
    public function getProductBySkuForIndividual($url, $productSku,$storeId)
    {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvProductData = $this->common->getEnvelopeData('GETPRODUCTBYSKUID');
            $XMLGetRequest = $csvProductData['envelope'];
            $XMLGetRequest = str_replace('{{INVENTORYID}}',$productSku,$XMLGetRequest);
            $productAction = $csvProductData['envVersion'] . "/" . $csvProductData['envName'] . "/" . $csvProductData['methodName'];
            $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $totalData = array();
            if(isset($xml->Body->GetResponse->GetResult)){
                $data = $xml->Body->GetResponse->GetResult;
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
    public function getProductBySku($url, $productSku,$storeId)
    {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvProductData = $this->common->getEnvelopeData('GETPRODUCTBYSKUID');
            $XMLGetRequest = $csvProductData['envelope'];
            $XMLGetRequest = str_replace('{{INVENTORYID}}',$productSku,$XMLGetRequest);
            $productAction = $csvProductData['envVersion'] . "/" . $csvProductData['envName'] . "/" . $csvProductData['methodName'];
            $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $totalData = array();
            if(isset($xml->Body->GetResponse->GetResult)){
                $data = $xml->Body->GetResponse->GetResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            if(isset($totalData['InventoryID'])) {
                if ($totalData['InventoryID']['Value'] != '') {
                    return true;
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
            $productAction = $csvProductData['envVersion'] . "/" . $csvProductData['envName'] . "/" . $csvProductData['methodName'];
            $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $totalData = array();
            if(isset($xml->Body->GetResponse->GetResult)){
                $data = $xml->Body->GetResponse->GetResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            return $totalData;
        } catch (SoapFault $e) {
            echo "Last request:<pre>" . htmlentities($e->getMessage()) . "</pre>";
        }
    }

    /**
     * @param $url
     * @param $syncId
     * @param $storeId
     * @return array|string
     */
    public function getGroupProductDataFromAcumatica($url, $syncId,$storeId)
    {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvProductData = $this->common->getEnvelopeData('GETGROUPEDPRODUCTS');
            $XMLGetRequest = $csvProductData['envelope'];
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId,$storeId);
            $getlastSyncDateByTimezone =  $this->timezone->date($lastSyncDate,null,true);
            $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
            $toDate = $this->date->date('Y-m-d H:i:s',strtotime("+1 day"));
            $XMLGetRequest = str_replace('{{FROMDATE}}',$fromDate,$XMLGetRequest);
            $XMLGetRequest = str_replace('{{TODATE}}',$toDate,$XMLGetRequest);
            $productAction = $csvProductData['envName'] . "/" . $csvProductData['envVersion'] . "/" . $csvProductData['methodName'];
            $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $totalData = array();
            if(isset($xml->Body->GetListResponse->GetListResult)){
                $data = $xml->Body->GetListResponse->GetListResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            if(isset($totalData) && !empty($totalData)){
                return $totalData;
            }
        }catch (SoapFault $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $url
     * @param $syncId
     * @param $storeId
     * @return array|string
     */
    public function getBundleProductDataFromAcumatica($url, $syncId,$storeId)
    {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvProductData = $this->common->getEnvelopeData('GETBUNDLEPRODUCTS');
            $XMLGetRequest = $csvProductData['envelope'];
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId,$storeId);
            $getlastSyncDateByTimezone =  $this->timezone->date($lastSyncDate,null,true);
            $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
            $toDate = $this->date->date('Y-m-d H:i:s',strtotime("+1 day"));
            $XMLGetRequest = str_replace('{{FROMDATE}}',$fromDate,$XMLGetRequest);
            $XMLGetRequest = str_replace('{{TODATE}}',$toDate,$XMLGetRequest);
            $productAction = $csvProductData['envName'] . "/" . $csvProductData['envVersion'] . "/" . $csvProductData['methodName'];
            $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $totalData = array();
            if(isset($xml->Body->GetListResponse->GetListResult)){
                $data = $xml->Body->GetListResponse->GetListResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            if(isset($totalData) && !empty($totalData)){
                return $totalData;
            }
        }catch (SoapFault $e) {
            return $e->getMessage();
        }
    }
    /**
     * @param $url
     * @param $syncId
     * @param $storeId
     * @return array|string
     */
    public function getConfigurableProductDataFromAcumatica($url, $syncId,$storeId)
    {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvProductData = $this->common->getEnvelopeData('GETCONFIGURABLEPRODUCTS');
            $XMLGetRequest = $csvProductData['envelope'];
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId,$storeId);
            $getlastSyncDateByTimezone =  $this->timezone->date($lastSyncDate,null,true);
            $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
            $toDate = $this->date->date('Y-m-d H:i:s',strtotime("+1 day"));
            $XMLGetRequest = str_replace('{{FROMDATE}}',$fromDate,$XMLGetRequest);
            $XMLGetRequest = str_replace('{{TODATE}}',$toDate,$XMLGetRequest);
            $productAction = $csvProductData['envName'] . "/" . $csvProductData['envVersion'] . "/" . $csvProductData['methodName'];
            $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $totalData = array();
            if(isset($xml->Body->GetListResponse->GetListResult)){
                $data = $xml->Body->GetListResponse->GetListResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            if(isset($totalData) && !empty($totalData)){
                return $totalData;
            }
        }catch (SoapFault $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $url
     * @param $inventoryId
     * @param $storeId
     * @return array|string
     * fetching data from Acumatica based on inventory id
     */
    public function getConfigurableAttributesFromAcumatica($url, $inventoryId,$storeId)
    {
        try{
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvProductData = $this->common->getEnvelopeData('GETCONFIGURABLEATTRIBUTES');
            $XMLGetRequest = $csvProductData['envelope'];
            $XMLGetRequest = str_replace('{{INVENTORYID}}',$inventoryId,$XMLGetRequest);
            $productAction = $csvProductData['envName'] . "/" . $csvProductData['envVersion'] . "/" . $csvProductData['methodName'];
            $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $totalData = array();
            if(isset($xml->Body->GetListResponse->GetListResult)){
                $data = $xml->Body->GetListResponse->GetListResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            if(isset($totalData) && !empty($totalData)){
                return $totalData;
            }
        } catch (SoapFault $e) {
            return $e->getMessage();
        }
    }
}