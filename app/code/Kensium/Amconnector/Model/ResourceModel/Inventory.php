<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\TestFramework\Event\Magento;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Kensium\Lib;

/**
 * Class Inventory
 * @package Kensium\Amconnector\Model\ResourceModel
 */
class Inventory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    /**
     * @var DateTime
     */
    protected  $date;
    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $dataHelper;
    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;
    /**
     * @var
     */
    protected $syncResourceModel;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;
    /**
     * @var Licensecheck
     */
    protected $licenseResourceModel;
    /**
     * @var
     */
    protected $_messageManager;
    /**
     * @var
     */
    protected $xmlHelper;
    /**
     * @var
     */
    public $sucessMsg;
    /**
     * @var
     */
    public $errorMsg;
    /**
     * @var
     */
    public $stopSyncFlg;
    /**
     * @var
     */
    public $totalTrialRecord;
    /**
     * @var
     */
    public $licenseType;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var
     */
    protected $productResourceModel;
    /**
     * @var
     */
    protected $productApi;
    /**
     * @var \Magento\Indexer\Model\ProcessorFactory
     */
    protected $processorFactory;

    protected $common;

    const IS_LICENSE_INVALID = "Invalid";
    const IS_LICENSE_VALID = "Valid";
    const IS_TIME_VALID = "Valid";

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param DateTime $date
     * @param Timezone $timezone
     * @param \Kensium\Amconnector\Helper\Data $dataHelper
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param Sync $syncResourceModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Kensium\Amconnector\Helper\Xml $xmlHelper
     * @param \Kensium\Amconnector\Helper\Time $timeHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param Licensecheck $licenseResourceModel
     * @param Product $productResourceModel
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productApi
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Kensium\Synclog\Helper\ProductInventory $inventoryLogHelper
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        DateTime $date,
        Timezone $timezone,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Magento\Indexer\Model\Processor $processorFactory,
        \Magento\Eav\Model\Entity $entityModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel,
        \Kensium\Amconnector\Model\ResourceModel\Product $productResourceModel,
        \Magento\Catalog\Api\ProductRepositoryInterface $productApi,
        \Magento\Directory\Model\Currency $currency,
        \Kensium\Synclog\Helper\ProductInventory $inventoryLogHelper,
        Lib\Common $common,
        $connectionName = null
    )
    {
	parent::__construct($context, $connectionName);
        $this->date = $date;
        $this->storeRepository = $storeRepository;
        $this->dataHelper = $dataHelper;
        $this->xmlHelper = $xmlHelper;
        $this->timeHelper = $timeHelper;
        $this->syncResourceModel= $syncResourceModel;
        $this->_storeManager = $storeManager;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->timezone = $timezone;
        $this->productFactory = $productFactory;
        $this->licenseResourceModel = $licenseResourceModel;
        $this->productResourceModel = $productResourceModel;
        $this->productApi = $productApi;
        $this->_currency = $currency;
        $this->processor = $processorFactory;
        $this->inventoryLogHelper = $inventoryLogHelper;
        $this->entityModel = $entityModel;
		$this->common = $common;
    }

    protected function _construct()
    {
        $this->_init('amconnector_inventory_mapping', 'id');
        $this->sucessMsg = 0;
    }

    /**
     * @param $autoSync
     * @param $syncType
     * @param $syncId
     * @param null $scheduleId
     * @param $cronStoreId
     *
     * Product Inventory and Price Sync
     */
    public function syncProductInventoryAndPrice($autoSync, $syncType, $syncId, $scheduleId = NULL, $cronStoreId)
    {
        $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'productInventory', NULL);
        $configParameters = $this->dataHelper->getConfigParameters($cronStoreId);
        $this->totalTrialRecord = $this->common->numberOfRecordSyncInTrialLicense();
        $this->stopSyncFlg = 0;
        try {

            $txt = "Info : Sync process started!"; 
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

            if ($syncType != "AUTO") {
                if ($cronStoreId == 0) {
                    $storeId = 1;
                } else {
                    $storeId = $cronStoreId;
                }
            } else {
                $storeId = $cronStoreId;
            }

            if($cronStoreId == 0){
                $scopeType = 'default';
            }else{
                $scopeType = 'stores';
            }
            $this->licenseType = $this->licenseResourceModel->checkLicenseTypes($storeId);
            if ($this->stopSyncValue() == 1) {
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
                        $inventoryLog['schedule_id'] = $scheduleId;
                    } else {
                        $inventoryLog['schedule_id'] = "";
                    }
                    $inventoryLog['store_id'] = $storeId;
                    $inventoryLog['job_code'] = "productinventory";
                    $inventoryLog['status'] = "error";
                    $inventoryLog['messages'] = "Invalid License Key";
                    $inventoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                    $inventoryLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                    if ($syncType  == 'MANUAL') {
                        $inventoryLog['runMode'] = 'Manual';
                    } elseif ($syncType == 'AUTO') {
                        $inventoryLog['runMode'] = 'Automatic';
                    }
                    if ($autoSync == 'COMPLETE') {
                        $inventoryLog['autoSync'] = 'Complete';
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $inventoryLog['autoSync'] = 'Individual';
                    }
                    $this->errorMsg = 1;
                    $txt = "Error: " . $inventoryLog['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                    $this->inventoryLogHelper->inventoryManualSync($inventoryLog);
                }
                else {

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
                            $inventoryLog['schedule_id'] = $scheduleId;
                        } else {
                            $inventoryLog['schedule_id'] = "";
                        }
                        $inventoryLog['store_id'] = $storeId;
                        $inventoryLog['job_code'] = "productinventory"; //job code
                        $inventoryLog['status'] = "error"; //status
                        $inventoryLog['messages'] = "Server time is not in sync"; //messages
                        $inventoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                        $inventoryLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                        if ($syncType  == 'MANUAL') {
                            $inventoryLog['runMode'] = 'Manual';
                        } elseif ($syncType == 'AUTO') {
                            $inventoryLog['runMode'] = 'Automatic';
                        }
                        if ($autoSync == 'COMPLETE') {
                            $inventoryLog['autoSync'] = 'Complete';
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $inventoryLog['autoSync'] = 'Individual';
                        }
                        $this->errorMsg = 1;
                        $txt = "Error: " . $inventoryLog['messages'];
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                        $this->inventoryLogHelper->inventoryManualSync($inventoryLog);
                    } else {
                        /**
                         * Log here for starting Sync
                         * If $scheduleId == NULL; it means Manual Sync ( Individual or COMPLETE)
                         * If $scheduleId != NULL; AUTO Sync via Cron
                         */
                        $txt = "Info : Server time is in sync.";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        if($this->stopSyncValue() == 1) {

                            if ($scheduleId != '') {
                                $inventoryLog['schedule_id'] = $scheduleId;
                            } else {
                                $inventoryLog['schedule_id'] = "";
                            }
                            $inventoryLog['store_id'] = $storeId;
                            $inventoryLog['job_code'] = "productinventory";
                            $inventoryLog['status'] = "success";
                            $inventoryLog['messages'] = "Product inventory manual sync initiated";

                            $inventoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $inventoryLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                            if ($syncType  == 'MANUAL') {
                                $inventoryLog['runMode'] = 'Manual';
                            } elseif ($syncType == 'AUTO') {
                                $inventoryLog['runMode'] = 'Automatic';
                            }
                            if ($autoSync == 'COMPLETE') {
                                $inventoryLog['autoSync'] = 'Complete';
                            } elseif ($autoSync == 'INDIVIDUAL') {
                                $inventoryLog['autoSync'] = 'Individual';
                            }
                            $txt = "Info : " . $inventoryLog['messages'];
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $syncLogID = $this->inventoryLogHelper->inventoryManualSync($inventoryLog);
                            $this->syncResourceModel->updateSyncAttribute($syncId, 'STARTED', $storeId);
                            if($this->stopSyncValue() == 1) {
                                /**
                                 * There are two type of Sync
                                 * 1. Complete Sync
                                 * 2. Individual Sync
                                 */

                                if ($autoSync == 'COMPLETE') {
                                    $insertedId = $this->syncResourceModel->checkConnectionFlag($syncId, 'inventory', $storeId);
                                    if ($insertedId == NULL) {
                                        $inventoryLog['id'] = $syncLogID;
                                        $inventoryLog['job_code'] = "productinventory";
                                        $inventoryLog['status'] = "error";
                                        $inventoryLog['messages'] = "Another Sync is already executing"; //messages

                                        $inventoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                        $inventoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                        $txt = "Info : Sync in Progress - please wait for the current sync to finish";
                                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                        $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                                        $this->inventoryLogHelper->inventoryManualSyncUpdate($inventoryLog);
                                    } else {
                                        $this->syncResourceModel->updateConnection($insertedId, 'PROCESS',$storeId);
                                        $this->syncResourceModel->updateSyncAttribute($syncId, 'PROCESSING', $storeId);

                                        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl','stores',$storeId);
                                        if(!isset($serverUrl)){
                                            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
                                        }


                                        $acumaticaUrl = $this->common->getBasicConfigUrl($serverUrl);


                                        /**
                                         * Calling Acumatica
                                         * Return number of record synced to magento if trial license
                                         */
                                        $syncedRecord = $this->syncToMagento($acumaticaUrl, $syncId, $syncLogID, $logViewFileName, $scopeType, $storeId, $configParameters);
                                        if ($this->sucessMsg == 0) {
                                            $inventoryLog['id'] = $syncLogID;
                                            $inventoryLog['status'] = "success";
                                            $inventoryLog['messages'] = "Product Inventory already in sync, no inventory has been updated";

                                            $inventoryLog['job_code'] = "productinventory";
                                            $inventoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                            $inventoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                            $this->inventoryLogHelper->inventoryManualSyncUpdate($inventoryLog);
                                            $txt = "Info : ".$inventoryLog['messages'];
                                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                        } else {
                                            $inventoryLog['id'] = $syncLogID;
                                            $inventoryLog['status'] = "success";
                                            if($syncedRecord == $this->totalTrialRecord){
                                                $inventoryLog['messages'] = "Trial license allow only ".$this->totalTrialRecord." records per sync!"; //messages

                                                $txt = "Info : ".$inventoryLog['messages'];
                                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                            }else{
                                                $inventoryLog['messages'] = "Product Inventory sync executed successfully";

                                                $txt = "Info : ".$inventoryLog['messages'];
                                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                            }
                                            $inventoryLog['job_code'] = "productinventory";
                                            $inventoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                            $inventoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                            $this->inventoryLogHelper->inventoryManualSyncUpdate($inventoryLog);
                                        }
                                    }
                                }
                            }else{
                                /**
                                 * Stop sync logs here
                                 */
                                $this->stopSyncFlg = 0;
                            }
                        }else{
                            /**
                             * Stop Sync logs Here
                             */
                            $this->stopSyncFlg = 0;
                        }
                    }
                }
                if($this->stopSyncFlg != 1){
                    if($this->errorMsg == 1){
                        $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                    }else {
                        $this->syncResourceModel->updateSyncAttribute($syncId,'SUCCESS',$storeId);
                    }
                    if(isset($insertedId)){
                        $this->syncResourceModel->updateConnection($insertedId, 'SUCCESS',$storeId);
                    }
                }
            }else{
                /**
                 * Sync stopped Logs Here
                 */
                $this->stopSyncFlg = 0;
            }
            if($this->stopSyncFlg == 1){
                if ($scheduleId != '') {
                    $inventoryLog['id'] = $scheduleId;
                } else {
                    $inventoryLog['id'] = "";
                }
                $inventoryLog['store_id'] = $storeId;
                $inventoryLog['job_code'] = "productinventory";
                $inventoryLog['status'] = "notice";
                $inventoryLog['messages'] = "Product inventory sync stopped";
                $inventoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                $inventoryLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                if($syncType == 'MANUAL'){
                    $inventoryLog['runMode'] = 'Manual';
                }elseif($syncType == 'AUTO'){
                    $inventoryLog['runMode'] = 'Automatic';
                }
                if($autoSync == 'COMPLETE'){
                    $inventoryLog['autoSync'] = 'Complete';
                }elseif($autoSync == 'INDIVIDUAL'){
                    $inventoryLog['autoSync'] = 'Individual';
                }

                $txt = "Notice: Product inventory sync stopped";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $this->inventoryLogHelper->inventoryManualSyncUpdate($inventoryLog);
                if (isset($insertedId) && $insertedId != '') {
                    $this->syncResourceModel->updateConnection($insertedId, 'ERROR', $storeId);
                }
                $this->syncResourceModel->updateSyncAttribute($syncId,'NOTICE',$storeId);
            }
        }catch (Exception $e){
            if($scheduleId != ''){
                $inventoryLog['id'] = $scheduleId;
            }else{
                $inventoryLog['id'] = $syncLogID;
            }
            $inventoryLog['job_code'] = "productinventory"; //job code
            $inventoryLog['status'] = "error"; //status
            $inventoryLog['messages'] = $e->getMessage(); //message

            $inventoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
            $inventoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
            $this->inventoryLogHelper->inventoryManualSyncUpdate($inventoryLog);
        }
        /**
        * Reindexing
        */
        $productInventoryIndex = $this->syncResourceModel->getDataFromCoreConfig('amconnectorsync/productinventorysync/reindex',$scopeType,$storeId);
        if($productInventoryIndex == '')
          $productInventoryIndex = $this->syncResourceModel->getDataFromCoreConfig('amconnectorsync/productinventorysync/reindex',NULL,NULL);

        if($productInventoryIndex == 1)
            $this->processor->reindexAll();

        $this->enableSync();
        $txt = "Info : Sync process completed!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
    }

    public function syncToMagento($url, $syncId, $syncLogID, $logViewFileName, $scope, $storeId, $configParameters)
    {
        $trialSyncRecordCount = 0;
        if ($this->stopSyncValue() == 1) {
            $arrAttributes = array();
            $productData = array();
            /**
             * Check the value of inventory sync options
             * $syncStatus = 1 -> Inventory only
             * $syncStatus = 2 -> Price only
             * $syncStatus = 3 -> Inventory and Price
             */
            $path = 'amconnectorsync/productinventorysync/inventoryoptions';
            $syncStatus = $this->syncResourceModel->getDataFromCoreConfig($path,$scope,$storeId);
            if($syncStatus == ''){
                $syncStatus = $this->syncResourceModel->getDataFromCoreConfig($path,NULL,NULL);
            }
            if($syncStatus == 1 || $syncStatus == 3)
            {
                /* Get inventory from acumatica */
                $inventoryData = $this->getInventoryIdCollection($url, $syncId, $scope, $storeId, $configParameters);

                /* Update inventory in Magento */
                foreach($inventoryData as $sku => $qty)
                {
                    if($this->stopSyncValue() == 1) {
                        $isInStock = 0;
                        if ($qty > 0) {
                            $isInStock = 1;
                        }
                        $syncData = array(
                            'sku' => trim($sku),
                            'is_in_stock' => $isInStock,
                            'use_config_manage_stock' => 0,
                            'qty' => (int)$qty
                        );

                        if ($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord)
                        {
                            try {
                                $this->UpdateProductQuantityByEntityId($syncData);
                                $msg = "SKU (" . $sku . ") with quantity (" . round($qty, 2) . ") updated successfully";
                                $txt = "Info : " . $msg;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $inventoryLog['store_id'] = $storeId;
                                $inventoryLog['schedule_id'] = $syncLogID;
                                $inventoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $inventoryLog['acumatica_attribute_code'] = $sku;
                                $inventoryLog['description'] = $msg;
                                $inventoryLog['runMode'] = "Manual";
                                $inventoryLog['messageType'] = "Success";
                                $inventoryLog['syncDirection'] = 'syncToMagento';
                                $this->inventoryLogHelper->inventorySyncLogs($inventoryLog);
                                $this->sucessMsg++;

                            } catch (Exception $e) {

                                $msg = $e->getMessage();
                                $inventoryLog['id'] = $syncLogID;
                                $inventoryLog['job_code'] = "productinventory";
                                $inventoryLog['status'] = "error";
                                $inventoryLog['messages'] = $msg;
                                $inventoryLog['longMessage'] = $msg;
                                $inventoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                $inventoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');

                                $txt = "Info : " . $inventoryLog['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->inventoryLogHelper->inventorySyncLogs($inventoryLog);
                                $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                                $this->errorMsg = 1;
                            }
                            $trialSyncRecordCount++;
                        }
                        if ($this->licenseType != 'trial')
                        {
                            try {
                                $this->UpdateProductQuantityByEntityId($syncData);
                                $msg = "SKU (" . $sku . ") with quantity (" . round($qty, 2) . ") updated successfully";
                                $txt = "Info : " . $msg;

                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $inventoryLog['store_id'] = $storeId;
                                $inventoryLog['schedule_id'] = $syncLogID;
                                $inventoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $inventoryLog['acumatica_attribute_code'] = $sku;
                                $inventoryLog['description'] = $msg;
                                $inventoryLog['runMode'] = "Manual";
                                $inventoryLog['messageType'] = "Success";
                                $inventoryLog['syncDirection'] = 'syncToMagento';
                                $this->inventoryLogHelper->inventorySyncLogs($inventoryLog);
                                $this->sucessMsg++;
                            } catch (Exception $e) {
                                $msg = $e->getMessage();
                                $inventoryLog['id'] = $syncLogID;
                                $inventoryLog['job_code'] = "productinventory";
                                $inventoryLog['status'] = "error";
                                $inventoryLog['messages'] = $msg;
                                $inventoryLog['longMessage'] = $msg;
                                $inventoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                $inventoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                $txt = "Info : " . $inventoryLog['messages'];

                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->inventoryLogHelper->inventorySyncLogs($inventoryLog);
                                $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                                $this->errorMsg = 1;
                            }
                        }
                    }else{
                        $this->stopSyncFlg = 1;
                        break;
                    }
                }
            }
            if(($syncStatus == 2 || $syncStatus == 3) && $trialSyncRecordCount < $this->totalTrialRecord)
            {
                /* Get price from acumatica */
                $priceData = $this->getPriceCollection($url, $syncId, $storeId, $configParameters);

                /* Update price in magento */
                foreach($priceData as $priceItem)
                {
                    if($this->stopSyncValue() == 1 && isset($priceItem['sku']) && $priceItem['sku'] != '' && isset($priceItem['price']))
                    {
                        $syncData = array(
                            'sku' => trim($priceItem['sku']),
                            'price' => $priceItem['price'],
                        );
                        $typeId =  $this->getProductTypeBySku(trim($priceItem['sku']));


                        if($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord){
                            try {
				$arrAttributes = array();
				$productData = array();
                                $this->updateProductPrice($syncData,$arrAttributes,$productData,$storeId);
                                $currencySymbol  = $this->_currency->getCurrencySymbol();
                                $msg = "SKU (" . $priceItem['sku'] .") with price (".$currencySymbol.round($priceItem['price'],2).") updated successfully";
                                $txt = "Info : " . $msg;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $inventoryLog['store_id'] = $storeId;
                                $inventoryLog['schedule_id'] = $syncLogID;
                                $inventoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $inventoryLog['acumatica_attribute_code'] = $priceItem['sku'];
                                $inventoryLog['description'] = $msg;
                                $inventoryLog['runMode'] = "Manual";
                                $inventoryLog['messageType'] = "Success";
                                $inventoryLog['syncDirection'] = 'syncToMagento';
                                $this->inventoryLogHelper->inventorySyncLogs($inventoryLog);
                                $this->sucessMsg ++;

                            } catch(Exception $e) {

                                $msg = $e->getMessage();
                                $inventoryLog['id'] = $syncLogID;
                                $inventoryLog['job_code'] = "productinventory";
                                $inventoryLog['status'] = "error";
                                $inventoryLog['messages'] = $msg;
                                $inventoryLog['longMessage'] = $msg;
                                $inventoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                $inventoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                $txt = "Info : " . $inventoryLog['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $this->inventoryLogHelper->inventorySyncLogs($inventoryLog);
                                $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                                $this->errorMsg = 1;
                            }
                            $trialSyncRecordCount++ ;
                        }
                        if($this->licenseType != 'trial'){
                            try {
				$arrAttributes = $productData = array();
                                $this->updateProductPrice($syncData,$arrAttributes,$productData,$storeId);
                                $currencySymbol  = $this->_currency->getCurrencySymbol();
                                $msg = "SKU (" . $priceItem['sku'] .") with price (".$currencySymbol.round($priceItem['price'],2).") updated successfully";
                                $txt = "Info : " . $msg."!";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $inventoryLog['store_id'] = $storeId;
                                $inventoryLog['schedule_id'] = $syncLogID;
                                $inventoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $inventoryLog['acumatica_attribute_code'] = $priceItem['sku'];
                                $inventoryLog['description'] = $msg;
                                $inventoryLog['runMode'] = "Manual";
                                $inventoryLog['messageType'] = "Success";
                                $inventoryLog['syncDirection'] = 'syncToMagento';
                                $this->inventoryLogHelper->inventorySyncLogs($inventoryLog);
                                $this->sucessMsg ++;
                            } catch(Exception $e) {
                                $msg = $e->getMessage();
                                $inventoryLog['id'] = $syncLogID;
                                $inventoryLog['job_code'] = "productinventory";
                                $inventoryLog['status'] = "error";
                                $inventoryLog['messages'] = $msg;
                                $inventoryLog['longMessage'] = $msg;
                                $inventoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                $inventoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');

                                $txt = "Info : " . $inventoryLog['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->inventoryLogHelper->inventorySyncLogs($inventoryLog);
                                $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                                $this->errorMsg = 1;
                            }
                        }
                    }else{
                        $this->stopSyncFlg = 0;
                        //break;
                    }
                }
            }
        }else{
            $this->stopSyncFlg = 1;
        }

        return $trialSyncRecordCount;
    }

    public function getInventoryIdCollection($url,$syncId, $scope, $storeId, $configParameters)
    {
        $path = 'amconnectorsync/defaultwarehouses/defaultwarehouse';
        $warehouse = $this->syncResourceModel->getDataFromCoreConfig($path, $scope, $storeId);
        if ($warehouse == '') {
            $warehouse = $this->syncResourceModel->getDataFromCoreConfig($path, NULL, NULL);
        }
        try {
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId, $storeId);
            $getlastSyncDateByTimezone = $this->timezone->date($lastSyncDate, null, true);
            $fromDate = $getlastSyncDateByTimezone->format('m/d/Y H:i:s');
            $toDate = $this->date->date('m/d/Y H:i:s',strtotime("+1 day"));

            //With Lib changes
            $csvProductInventoryData = $this->common->getEnvelopeData('PRODUCTINVENTORYBYDATE');


            $XMLGetRequest = $csvProductInventoryData['envelope'];
	        $XMLGetRequest = str_replace('{{TODATE}}', trim($toDate), $XMLGetRequest);
            $XMLGetRequest = str_replace('{{FROMDATE}}', trim($fromDate), $XMLGetRequest);
            $XMLGetRequest = str_replace('{{WAREHOUSE}}', trim($warehouse), $XMLGetRequest);

            $action = $csvProductInventoryData['envName']."/".$csvProductInventoryData['envVersion']."/".$csvProductInventoryData['methodName'];

            //With Lib changes
            $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $action);

            if(isset($xml->Body->GetListResponse)) {
                $data = $xml->Body->GetListResponse->GetListResult->Entity->ProductInventoryResults;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            $inventoryIds = array();
            /**
             * If isset SKU(InventoryIDInventoryIteminventoryCD) means only one item'
             * Else and array of items for sync
             */
            if(isset($totalData['ProductInventoryResults']['InventoryIDInventoryIteminventoryCD']) ){
                $sku = str_replace(' ','_',trim($totalData['ProductInventoryResults']['InventoryIDInventoryIteminventoryCD']['Value']));
                $qty = $totalData['ProductInventoryResults']['QtyAvailable']['Value'];

                $product = $this->productFactory->create();
                $productId = $product->getIdBySku($sku);
                $acumaticaQty = trim($qty);
                if($productId != '' && $productId >0){
                    $inventoryIds[$sku] = (int)$acumaticaQty;
                }
            }else{
                if(isset($totalData['ProductInventoryResults']) ) {
                    foreach ($totalData['ProductInventoryResults'] as $inventoryData) {
                        $sku = str_replace(' ', '_', trim($inventoryData->InventoryIDInventoryIteminventoryCD->Value));
                        $product = $this->productFactory->create();
                        $productId = $product->getIdBySku($sku);
                        $acumaticaQty = trim($inventoryData->QtyAvailable->Value);
                        if ($productId != '' && $productId >0) {
                            $inventoryIds[$sku] = (int)$acumaticaQty;
                        }
                    }
                }
            }
            return $inventoryIds ;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $url
     * @param $syncId
     * @param $storeId
     * Get Price from acumatica
     */
    public function getPriceCollection($url, $syncId, $storeId, $configParameters)
    {
        try {
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId, $storeId);
            $getlastSyncDateByTimezone = $this->timezone->date($lastSyncDate, null, true);
            $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
            $toDate = $this->date->date('Y-m-d H:i:s', strtotime("+1 day"));

            $csvProductPriceData = $this->common->getEnvelopeData('PRODUCTWITHPRICE');
            $XMLGetRequest = $csvProductPriceData['envelope'];
            $XMLGetRequest = str_replace('{{FROMDATE}}', str_replace(" ", "T", trim($fromDate)), $XMLGetRequest);
            $XMLGetRequest = str_replace('{{TODATE}}', str_replace(" ", "T", trim($toDate)), $XMLGetRequest);

            $action = $csvProductPriceData['envName']."/".$csvProductPriceData['envVersion']."/".$csvProductPriceData['methodName'];
            $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $action);
            if(isset($xml->Body->GetListResponse)) {
                $data = $xml->Body->GetListResponse->GetListResult->Entity->ProductResults;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            $inventoryIds = array();
            if (isset($totalData['ProductResults']['InventoryID'])) {
                $sku = str_replace(' ', '_', trim($totalData['ProductResults']['InventoryID']['Value']));
                $price = $totalData['ProductResults']['DefaultPrice']['Value'];

                $product = $this->productFactory->create();
                $productId = $product->getIdBySku($sku);
                if ($productId != '' && $productId > 0) {
                    $inventoryIds[0]['sku'] = $sku;
                    $inventoryIds[0]['price'] = (float)trim($price);
                }
            } else {
                if (isset($totalData['ProductResults']) && !empty($totalData['ProductResults'])) {
                    foreach ($totalData['ProductResults'] as $key => $inventoryData) {
                      $sku = str_replace(' ', '_', trim($inventoryData->InventoryID->Value));
                        $product = $this->productFactory->create();
                        $productId = $product->getIdBySku($sku);

                        if ($productId != '' && $productId > 0) {
                            $inventoryIds[$key]['sku'] = $sku;
                            $inventoryIds[$key]['price'] = (float)trim($inventoryData->DefaultPrice->Value);
                        }
                    }
                }
            }
            return $inventoryIds;

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $syncData
     */
    public function UpdateProductQuantityByEntityId($syncData)
    {
        $product = $this->productApi->get($syncData['sku']);
        $productEntityId = $product->getEntityId();
        try{
            if($productEntityId){    
            $this->getConnection()->query("UPDATE ".$this->getTable('cataloginventory_stock_item')." SET qty = '".$syncData['qty']."' ,use_config_manage_stock = '".$syncData['use_config_manage_stock']." ' ,is_in_stock = '".$syncData['is_in_stock']."'  WHERE product_id = ".$productEntityId."");
            }
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * @param $syncData
     * Update price of product in Magento
     */
    public function updateProductPrice($syncData,$arrAttributes,$productData,$storeId)
    {
        try{
            $query = "SELECT entity_type_id  FROM " . $this->getTable('eav_entity_type') . " WHERE entity_type_code = 'catalog_product'";
            $entityTypeId = $this->getConnection()->fetchOne($query);

            $query = "SELECT attribute_id FROM " . $this->getTable('eav_attribute') . " WHERE entity_type_id = '" . $entityTypeId . "' AND attribute_code = 'price'";
            $attributeId = $this->getConnection()->fetchOne($query);
        }catch (Exception $e){
            echo $e->getMessage();
        }
        if($this->productFactory->create()->getIdBySku($syncData['sku'])){
        $product = $this->productApi->get($syncData['sku']);
        }
        if(isset($product)){
        $productEntityId = $product->getRowId();
        }
        try {
            if(isset($productEntityId) != '' && isset($attributeId)){   
            $this->getConnection()->query("UPDATE " . $this->getTable('catalog_product_entity_decimal') . " SET value='" . $syncData['price'] . "' WHERE attribute_id='".$attributeId."' AND row_id='".$productEntityId."'");
            }

        }catch (Exception $e){
            echo $e->getMessage();
        }
    }

    public function getProductTypeBySku($sku)
    {
        try{
            $productType = $this->getConnection()->fetchOne("SELECT type_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$sku'");
            return $productType;
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * @param $productEntityId
     * @return string
     */
    public function getProductQuantityInMagento($productEntityId)
    {
        try{
            $productQty = $this->getConnection()->fetchOne("SELECT qty from " .$this->getTable('cataloginventory_stock_item'). " where product_id='".$productEntityId."' ");
            return $productQty;
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * Stop sync value
     */
    public function stopSyncValue()
    {
        $path = 'amconnectorsync/inventorysync/syncstopflg';
        $stopSyncValueInDb = $this->syncResourceModel->getDataFromCoreConfig($path,NULL,NULL);
	$stopSyncValueInDb = 1;
        return $stopSyncValueInDb;
    }
    /**
     * Enable sync
     */
    public function enableSync()
    {
        $path = 'amconnectorsync/inventorysync/syncstopflg';
        try{
            $query = "update " . $this->getTable("core_config_data")." set value = 1 where path ='" . $path . "'";
            $result = $this->getConnection()->query($query);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}
