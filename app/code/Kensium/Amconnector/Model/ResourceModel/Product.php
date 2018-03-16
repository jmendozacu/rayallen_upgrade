<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Kensium\Amconnector\Helper\Data;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\Timezone as TimeZone;
use Symfony\Component\Config\Definition\Exception\Exception;
use Psr\Log\LoggerInterface as Logger;
use Kensium\Lib;

/**
 * Class Product
 * @package Kensium\Amconnector\Model\ResourceModel
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var Data
     */
    protected $dataHelper;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var Manager
     */
    protected $cacheManager;
    /**
     * @var
     */
    protected $resourceModelSync;
    /**
     * @var
     */
    protected $productFactory;
    /**
     * @var
     */
    protected $scopeConfigInterface;

    /**
     * @var \Magento\Eav\Model\Entity
     */
    protected $entityModel;

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param Manager $cacheManager
     * @param \Magento\Customer\Model\GroupFactory $customerGroup
     * @param Sync $resourceModelSync
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param TimeZone $timezone
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Kensium\Synclog\Helper\Productprice $productPriceHelper
     * @param \Magento\Eav\Model\Entity $entityModel
     * @param Logger $logger
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        Manager $cacheManager,
        \Magento\Customer\Model\GroupFactory $customerGroup,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        TimeZone $timezone,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface ,
        \Kensium\Synclog\Helper\Productprice $productPriceHelper,
        \Magento\Eav\Model\Entity $entityModel,
        Logger $logger,
        Lib\Common $common,
        $connectionName = null
    )
    {
	parent::__construct($context, $connectionName);
        $this->dataHelper = $dataHelper;
        $this->timezone = $timezone;
        $this->resourceModelSync = $resourceModelSync;
        $this->date = $date;
        $this->productPriceHelper = $productPriceHelper;
        $this->cacheManager = $cacheManager;
        $this->_storeManager = $storeManager;
        $this->productFactory = $productFactory;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->logger = $logger;
        $this->customerGroup = $customerGroup;
        $this->entityModel = $entityModel;
        $this->common = $common;
    }

    /**
     * @param null $storeId
     * Truncate Mapping Table
     */
    public function truncateMappingTable($storeId = null)
    {
        $this->getConnection()->query("DELETE FROM ".$this->getTable("amconnector_product_mapping")." WHERE store_id = $storeId");
    }
    /**
     * Define main table
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amconnector_product_mapping', 'id');
    }
    /**
     * @return string
     */
    public function getAcumaticaAttrCount()
    {
        $acumaticaCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_acumatica_product_attributes'));
        return $acumaticaCount;
    }
    /**
     *Checking count of the mapping table
     */
    public function checkProductMapping($storeId)
    {
        $productAttributes = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_product_mapping') . " WHERE store_id =" . $storeId);
        return $productAttributes;
    }
    /**
     * @param array $data
     */
    public function updateProductSchema($data = array(),$storeId)
    {
	$storeId = 1;
        $this->getConnection()->query("DELETE FROM " . $this->getTable("amconnector_acumatica_product_attributes") . " WHERE store_id = '".$storeId."' ");
            $objectName = $data['NAME'];
            if (isset($data['FIELDS'][0]['FIELD'])) {
                foreach ($data['FIELDS'][0]['FIELD'] as $newXmlData)
                {
                    $this->getConnection()->query("INSERT INTO " . $this->getTable("amconnector_acumatica_product_attributes") . " set label='" . $objectName . ' ' . $newXmlData['NAME'] . "',code='" . $newXmlData['NAME'] . "',field_type='" . $newXmlData['TYPE'] . "',store_id = '" . $storeId . "' ");
                }
            }

    }
    /**
     * Get Id of Acumatica Code for product schema
     * Array key relates to following attribute in magento
     * 0  -> Tax Class
     * 1  -> Description
     * 2  -> Meta Description
     * 3  -> Meta Keywords
     * 4  -> Meta Title
     * 5  -> Name
     * 6  -> Price
     * 7  -> Short Description
     * 8  -> SKU
     * 9  -> Status
     * 10 -> Visibility
     * 11 -> Weight
     */
    public function getProductAttributeLabelId()
    {

        $acumaticaCodes = array(
            0 => 'TaxCategory',
            1 => 'DescriptionLong',
            2 => 'MetaDescription',
            3 => 'MetaKeywords',
            4 => 'MetaTitle',
            5 => 'Description',
            6 => 'DefaultPrice',
            7 => 'DescriptionShort',
            8 => 'InventoryID',
            9 => 'Active',
            10 => 'Visibility',
            11 => 'Weight',
        );

        foreach ($acumaticaCodes as $code) {
            $query = "SELECT id FROM " . $this->getTable("amconnector_acumatica_product_attributes") . " WHERE code = '" . $code . "' ";
            $result[] = $this->getConnection()->fetchOne($query);

        }
        return $result;
    }
    /**
     * @param array $description
     * @param array $inventoryId
     * @param array $itemStatus
     * @param array $defaultPrice
     * @param array $metaTitle
     * @param array $metaKeyWords
     * @param array $qtyOnHand
     */
    public function updateProductData($description = array(), $inventoryId = array(), $itemStatus = array(), $defaultPrice = array(), $metaTitle = array(), $metaKeyWords = array(), $qtyOnHand = array())
    {
        $this->getConnection()->query("INSERT INTO " . $this->getTable("amconnector_amconnector_acumatica_productdata_temp")."
                (`id`,`description`, `inventory_id`,`item_status`, `default_price`,`meta_title`, `meta_keywords`,`qty_on_hand`)
                VALUES (NULL,'$description','$inventoryId','$itemStatus','$defaultPrice','$metaTitle','$metaKeyWords','$qtyOnHand')");
    }
    /**
     * @param $acumaticaData
     * @param $logViewFileName
     * @return int
     */
    public function updatePriceDataIntoMagento($acumaticaData, $logViewFileName, $syncLogID, $storeId, $syncId)
    {
        $productPriceArray = array();
        $updated = 0;
        $this->licenseType = 'trial';//static value provided once licenseType method is created,thi part needs to be changed

        if ($this->licenseType == 'trial') {
            try {
                $trialSyncRecordCount = 0;
                $totalTrialRecord = $this->common->numberOfRecordSyncInTrialLicense();
                $query = "SELECT entity_type_id  FROM " . $this->getTable('eav_entity_type') . " WHERE entity_type_code = 'catalog_product'";
                $entityTypeId = $this->getConnection()->fetchOne($query);
                $query = "SELECT attribute_id FROM " . $this->getTable('eav_attribute') . " WHERE entity_type_id = '" . $entityTypeId . "' AND attribute_code = 'price'";
                $attributeId = $this->getConnection()->fetchOne($query);
                if (count($acumaticaData)) {
                    $productPriceArray['schedule_id'] = $syncLogID;
                    foreach ($acumaticaData as $key => $value) {
                        $query = "SELECT entity_id  FROM " . $this->getTable('catalog_product_entity') . " WHERE sku = '" . $key . "'";
                        $entityId = $this->getConnection()->fetchOne($query);
                        if ($entityId && $trialSyncRecordCount < $totalTrialRecord) {
                            $this->getConnection()->query("UPDATE " . $this->getTable('catalog_product_entity_decimal') . " SET value='" . $value . "' WHERE attribute_id='" . $attributeId . "' AND entity_id='" . $entityId . "'");
                            $productPriceArray['status'] = "success";
                            $productPriceArray['sync_direction'] = "Acumatica To Magento";
                            $productPriceArray['acumatica_attribute_code'] =  $key;
                            $productPriceArray['messages'] = $txt = "Info : sku - $key price $value has been updated!";
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->productPriceHelper->productPriceSyncSuccessLogs($productPriceArray);
                            $trialSyncRecordCount++;
                            $updated++;
                        }

                    }
                    $productPriceArray['scheduled_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                    $productPriceArray['store_id'] = $storeId;
                    $productPriceArray['messages'] = "Info : Trial license allow only " . $totalTrialRecord . " records per sync!";
                    $productPriceArray['job_code'] = "productprice";
                    $productPriceArray['store_id'] = $storeId;
                    $productPriceArray['executed_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                    $productPriceArray['finished_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                    $productPriceArray['schedule_id'] = $syncLogID;
                    $this->productPriceHelper->ProductPriceManualSync($productPriceArray);
                    $txt = "Info : Trial license allow only " . $totalTrialRecord . " records per sync!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->cacheManager->flush(array('full_page'));

                }
                return $updated;
            } catch (Exception $e) {
                $msg = $e->getMessage();
                $productPriceArray['job_code'] = "productprice";
                $productPriceArray['scheduled_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                $productPriceArray['status'] = "error";
                $productPriceArray['messages'] = $msg;
                $productPriceArray['long_message'] = $msg;
                $productPriceArray['executed_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                $productPriceArray['finished_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                $txt = "Info : " . $productPriceArray['messages'];
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->productPriceHelper->ProductPriceManualSync($productPriceArray);
                $this->productPriceHelper->productPriceSyncSuccessLogs($productPriceArray);
                $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
                $this->errorMsg = 1;

            }
        }

        if ($this->licenseType != 'trial') {

            try {

                $query = "SELECT entity_type_id  FROM " . $this->getTable('eav_entity_type') . " WHERE entity_type_code = 'catalog_product'";
                $entityTypeId = $this->getConnection()->fetchOne($query);
                $query = "SELECT attribute_id FROM " . $this->getTable('eav_attribute') . " WHERE entity_type_id = '" . $entityTypeId . "' AND attribute_code = 'price'";
                $attributeId = $this->getConnection()->fetchOne($query);
                if (count($acumaticaData)) {
                    $productPriceArray['schedule_id'] = $syncLogID;
                    foreach ($acumaticaData as $key => $value) {
                        $query = "SELECT entity_id  FROM " . $this->getTable('catalog_product_entity') . " WHERE sku = '" . $key . "'";
                        $entityId = $this->getConnection()->fetchOne($query);
                        if ($entityId) {
                            $this->getConnection()->query("UPDATE " . $this->getTable('catalog_product_entity_decimal') . " SET value='" . $value . "' WHERE attribute_id='" . $attributeId . "' AND entity_id='" . $entityId . "'");
                            $productPriceArray['sync_direction'] = "Acumatica To Magento";
                            $productPriceArray['status'] = "success";
                            $productPriceArray['messages']= $txt = "Info : sku - $key with price .$value. has been updated!";
                            $productPriceArray['acumatica_attribute_code'] = $key;
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->productPriceHelper->productPriceSyncSuccessLogs($productPriceArray);
                            $updated++;
                        }
                    }
                    $productPriceArray['messages'] = "Product Price sync Successfully Completed!";
                    $productPriceArray['store_id'] = $storeId;
                    $productPriceArray['job_code'] = "productprice";
                    $productPriceArray['store_id'] = $storeId;
                    $productPriceArray['executed_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                    $productPriceArray['finished_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                    $this->productPriceHelper->ProductPriceManualSync($productPriceArray);
                    $this->cacheManager->flush(array('full_page'));
                }
                return $updated;
            } catch (Exception $e) {
                $msg = $e->getMessage();
                $productPriceArray['id'] = $syncLogID;
                $productPriceArray['job_code'] = "productprice";
                $productPriceArray['status'] = "error";
                $productPriceArray['messages'] = $msg;
                $productPriceArray['long_message'] = $msg;
                $productPriceArray['executed_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                $productPriceArray['finished_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                $txt = "Info : " . $productPriceArray['messages'];
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->productPriceHelper->ProductPriceManualSync($productPriceArray);
                $this->productPriceHelper->productPriceSyncSuccessLogs($productPriceArray);
                $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
                $this->errorMsg = 1;
            }

        }
    }
    /**
     * @param $prodEntId
     * @return int
     */
    public function getProdQuantInMagentoByEntId($prodEntId)
    {
        $qty = $this->getConnection()->query("SELECT qty FROM ".$this->getTable('cataloginventory_stock_item')." WHERE product_id = $prodEntId");
        $qty = $qty->fetchObject();
        return $qty;
    }
    /**
     * @param $prodArray
     * @return int
     */
    public function updateProdQuantInMagentoByEntId($prodArray)
    {
        $status = $this->getConnection()->query("UPDATE ".$this->getTable('cataloginventory_stock_item')." SET qty = ".$prodArray['qty']."  WHERE product_id = ".$prodArray['product_id']."");

        return $status->rowCount();
    }
    /**
     * returns the stop sync value from core_config_data table
     * @return int
     */
    public function stopSyncValue()
    {
        $value = '';
        $query = "SELECT value FROM " . $this->getTable("core_config_data") . " WHERE path ='amconnectorsync/productsync/syncstopflg' ";
        try{
            $value = $this->getConnection()->fetchOne($query);
        }catch(Exception $e){
            echo $e->getMessage();
        }
        return $value;
    }
    /**
     * Truncate Data from product temp tables
     */
    public function truncateDataFromTempTables()
    {
        $query = "TRUNCATE TABLE " . $this->getTable("amconnector_product_sync_temp");
        try{
            $this->getConnection()->query($query);
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }
    /**
     * @param $acumaticaData
     * @param $syncId
     * @param null $storeId
     * @return array
     * Fetching the data based on last sync date
     * and Inserting into the temp table
     *
     * First fetching from Acumatica and if same record is updated in Magento
     * then updating the same record with customer detail and updated date in temp table
     */
    public function insertDataIntoTempTables($acumaticaData, $syncId, $scopeType=NULL,$storeId = NULL,$nonStockFlg = NULL)
    {
        if($storeId == NULL)
            $storeId = 1;

        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();

        /**
         * Based on the last sync date get the data from Acumatica and insert into temporary table
         */
        $oneRecordFlag=false;

        $productSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/syncdirection',$scopeType,$storeId);
        if(!isset($productSyncDirection))
        {
            $productSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/syncdirection');
        }
        if ($productSyncDirection == 1 || $productSyncDirection == 3) {
            if (isset($acumaticaData['Entity'])) {
                $defaultWarehouse = $this->scopeConfigInterface->getValue('amconnectorsync/defaultwarehouses/defaultwarehouse', $scopeType, $storeId);
                if (!isset($defaultWarehouse)) {
                    $defaultWarehouse = $this->scopeConfigInterface->getValue('amconnectorsync/defaultwarehouses/defaultwarehouse');
                }
                foreach ($acumaticaData['Entity'] as $key => $value) {
                    if (!is_numeric($key)) {
                        $oneRecordFlag = true;
                        break;
                    }
 		    $acumaticaWarehouse = '';
                    if($nonStockFlg == 1){
                       if(isset($value->DefaultWarehouseID->Value))
                          $acumaticaWarehouse = trim($value->DefaultWarehouseID->Value); // Default warehouse for product in acumatica
                    }else{
                    if(isset($value->WarehouseDetails->StockItemWarehouseDetail->WarehouseID->Value))
                      $acumaticaWarehouse = trim($value->WarehouseDetails->StockItemWarehouseDetail->WarehouseID->Value); // Default warehouse for product in acumatica
                    }

                    if ($acumaticaWarehouse == $defaultWarehouse) {
                            $acumaticaId = $value->InventoryID->Value;
                            $acumaticaModifiedDate = $this->date->date('Y-m-d H:i:s', strtotime($value->LastModified->Value));
                            $acumaticaRecordCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable("amconnector_product_sync_temp") . "
                WHERE acumatica_inventory_id ='" . $acumaticaId . "' and store_id = '" . $storeId . "'  ");
                            if ($acumaticaRecordCount) {
                                $this->getConnection()->query("UPDATE " . $this->getTable("amconnector_product_sync_temp") . "
                set acumatica_lastsyncdate='" . $acumaticaModifiedDate . "' where acumatica_inventory_id='" . $acumaticaId . "' and store_id= '" . $storeId . "' ");
                            } else {
                                $this->getConnection()->query("INSERT INTO `" . $this->getTable("amconnector_product_sync_temp") . "` (`id`, `acumatica_inventory_id`, `magento_sku`, `magento_id`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `store_id`, `entity_ref`, `flg`)
                VALUES (NULL,  '" . $acumaticaId . "', NULL, NULL, NULL, '" . $acumaticaModifiedDate . "', '" . $storeId . "','" . $key . "', '0')");
                            }

                    }
                }
                if ($oneRecordFlag) {
                    $acumaticaId = $acumaticaData['Entity']['InventoryID']['Value'];
		    $acumaticaWarehouse = '';
                    if($nonStockFlg == 1){
                       if(isset($acumaticaData['Entity']['DefaultWarehouseID']['Value']))
                          $acumaticaWarehouse = trim($acumaticaData['Entity']['DefaultWarehouseID']['Value']); // Default warehouse for product in acumatica
                    }else{
                    if(isset($acumaticaData['Entity']['WarehouseDetails']['StockItemWarehouseDetail']['WarehouseID']['Value']))
                        $acumaticaWarehouse = trim($acumaticaData['Entity']['WarehouseDetails']['StockItemWarehouseDetail']['WarehouseID']['Value']); // Default warehouse for product in acumatica
                    }
                    if ($acumaticaWarehouse == $defaultWarehouse) {
                        $acumaticaModifiedDate = date('Y-m-d H:i:s', strtotime($acumaticaData['Entity']['LastModified']['Value']));
                        $acumaticaRecordCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable("amconnector_product_sync_temp") . "
                WHERE acumatica_inventory_id ='" . $acumaticaId . "' and store_id = '" . $storeId . "'  ");
                        if ($acumaticaRecordCount) {
                            $this->getConnection()->query("UPDATE " . $this->getTable("amconnector_product_sync_temp") . "
                set acumatica_lastsyncdate='" . $acumaticaModifiedDate . "' where acumatica_inventory_id='" . $acumaticaId . "' and store_id= '" . $storeId . "' ");
                        } else {

                            $this->getConnection()->query("INSERT INTO `" . $this->getTable("amconnector_product_sync_temp") . "` (`id`, `acumatica_inventory_id`, `magento_sku`, `magento_id`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `store_id`, `entity_ref`, `flg`)
                VALUES (NULL,  '" . $acumaticaId . "', NULL, NULL, NULL, '" . $acumaticaModifiedDate . "', '" . $storeId . "',NULL, '0')");

                        }
                    }
                }
            }
        }

        /**
         * Get website id based on store id
         * Based on the last sync date get the data from Magento and insert/update into temporary table
         * Here we are getting all the updated products based on last sync data but we need to verify that the particular product is from current store id or not
         * if yes insert/update the table otherwise skip
         */
        if ($productSyncDirection == 2 || $productSyncDirection == 3) {
            $lastSyncDate = $this->date->gmtDate('Y-m-d H:i:s', $this->resourceModelSync->getLastSyncDate($syncId, $storeId));

            $magentoData = $this->getConnection()->fetchAll("SELECT row_id,entity_id,sku,updated_at FROM " . $this->getTable("catalog_product_entity") . " WHERE updated_at >='" . $lastSyncDate . "'  and type_id = 'simple'");
            $originalMagentoData = array();
            if($nonStockFlg == 1)
            {
                if(count($magentoData) > 0)
                {
                    $k = 0;
                    foreach($magentoData as $singleData)
                    {
                        $nonStockItemStatus = $this->getIsNonStock($singleData['row_id'],$storeId);
                        if($nonStockItemStatus == 1)
                        {
                            $originalMagentoData[$k]['entity_id'] = $singleData['entity_id'];
                            $originalMagentoData[$k]['updated_at'] = $singleData['updated_at'];
                            $originalMagentoData[$k]['sku'] = $singleData['sku'];
                            $k++;
                        }
                    }
                }
            }else{
                if(count($magentoData) > 0)
                {
                    $k = 0;
                    foreach($magentoData as $singleData)
                    {
                        $nonStockItemStatus = $this->getIsNonStock($singleData['row_id'],$storeId);
                        if($nonStockItemStatus != 1)
                        {
                            $originalMagentoData[$k]['entity_id'] = $singleData['entity_id'];
                            $originalMagentoData[$k]['updated_at'] = $singleData['updated_at'];
                            $originalMagentoData[$k]['sku'] = $singleData['sku'];
                            $k++;
                        }
                    }
                }
            }
            if(count($originalMagentoData) > 0)
            {
                foreach ($originalMagentoData as $mData) {
                    $updatedDate = $this->timezone->date($mData['updated_at'], null, true);
                    $updatedDate = $updatedDate->format('Y-m-d H:i:s');
                    $magentoId = trim($mData['entity_id']);
                    $magentoSku = strtoupper($mData['sku']);
                    $item = $this->productFactory->create()->load($magentoId);
                    $magentoStoreIds = $item->getStoreIds();
                    if (is_array($magentoStoreIds) && in_array($storeId, $magentoStoreIds)) {
                        $recordCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable("amconnector_product_sync_temp") . "
                WHERE acumatica_inventory_id ='" . $magentoSku . "' and store_id = '" . $storeId . "'  ");
                        if ($recordCount) {
                            $this->getConnection()->query("UPDATE " . $this->getTable("amconnector_product_sync_temp") . "
                set magento_sku='" . $mData['sku'] . "', magento_id='" . $magentoId . "' ,magento_lastsyncdate='" . $updatedDate . "'
                where acumatica_inventory_id='" . $magentoSku . "' and store_id= '" . $storeId . "' ");
                        } else {
                            $this->getConnection()->query("INSERT INTO `" . $this->getTable("amconnector_product_sync_temp") . "`
                (`id`,`acumatica_inventory_id`, `magento_sku`, `magento_id`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `store_id`, `flg`)
                VALUES (NULL, NULL, '" . $magentoSku . "','" . $magentoId . "', '" . $updatedDate . "', NULL, '" . $storeId . "', '0')");
                        }
                    }
                }
            }
        }
        try{
            $records = $this->getConnection()->fetchAll("SELECT * FROM `".$this->getTable("amconnector_product_sync_temp")."` WHERE store_id = '".$storeId."'  ");
            $data = array();
            $results = $this->getConnection()->fetchAll("SELECT magento_attr_code,acumatica_attr_code,sync_direction FROM `".$this->getTable("amconnector_product_mapping")."` WHERE store_id =".$storeId);
            foreach($results as $result){
                $attrCode = $result['magento_attr_code'];
                $mappingAttributes[$attrCode] = $result['acumatica_attr_code'] ."|". $result['sync_direction'];
            }
            foreach($records as $record){
                $magFlag = 0;
                $acuFlag = 0;
                foreach($mappingAttributes as $attributeStr){
                    $attrArray = explode('|',$attributeStr);
                    $biDirectional[] = $attrArray[1];
                    if($attrArray[1] == 'Bi-Directional (Acumatica Wins)')
                        $attrArray[1] = "Acumatica to Magento";
                    if($attrArray[1] == 'Bi-Directional (Magento Wins)')
                        $attrArray[1] = "Magento to Acumatica";

                    $direction[] = $attrArray[1];
                }
                $direction = array_unique($direction);
                if(in_array('Acumatica to Magento', $direction))
                    $magFlag = 1;

                if(in_array('Magento to Acumatica', $direction))
                    $acuFlag = 1;

                if(in_array('Bi-Directional (Last Update Wins)', $direction)){
                    if($record['magento_lastsyncdate'] > $record['acumatica_lastsyncdate'])
                        $acuFlag = 1;
                    else
                        $magFlag = 1;
                }
                if(count(array_unique($biDirectional)) === 1 && in_array('Bi-Directional (Magento Wins)',$biDirectional))
                {
                    $magFlag = 1;
                }
                if(count(array_unique($biDirectional)) === 1 && in_array('Bi-Directional (Acumatica Wins)',$biDirectional))
                {
                    $acuFlag = 1;
                }
                if($magFlag){
                    $data['magento'][] = array(
                        "id" => $record['id'],
                        "acumatica_inventory_id" => $record['acumatica_inventory_id'],
                        "magento_sku" => $record['magento_sku'],
                        "magento_id" => $record['magento_id'],
                        "magento_lastsyncdate" => $record['magento_lastsyncdate'],
                        "acumatica_lastsyncdate" => $record['acumatica_lastsyncdate'],
                        "store_id" => $record['store_id'],
                        "entity_ref" => $record['entity_ref'],
                        "flg" => $record['flg']
                    );
                }
                if($acuFlag){
                    $data['acumatica'][] = array(
                        "id" => $record['id'],
                        "acumatica_inventory_id" => $record['acumatica_inventory_id'],
                        "magento_sku" => $record['magento_sku'],
                        "magento_id" => $record['magento_id'],
                        "magento_lastsyncdate" => $record['magento_lastsyncdate'],
                        "acumatica_lastsyncdate" => $record['acumatica_lastsyncdate'],
                        "store_id" => $record['store_id'],
                        "entity_ref" => $record['entity_ref'],
                        "flg" => $record['flg']
                    );
                }
            }
        } catch(Exception $e){
            echo $e->getMessage();
        }
        return $data;
    }
    /**
     * To return all the attribute which are mapped to Magento direction
     *
     * @param $storeId
     * @return array
     */
    public function getMagentoAttributes($storeId)
    {
        $results = $this->getConnection()->query("SELECT magento_attr_code,acumatica_attr_code,sync_direction FROM ".$this->getTable('amconnector_product_mapping')." where sync_direction in('Bi-Directional (Last Update Wins)', 'Acumatica to Magento', 'Bi-Directional (Acumatica Wins)','Bi-Directional (Magento Wins)') and store_id =".$storeId);
        $attributes = array();
        foreach($results as $result){
            $attrCode = $result['magento_attr_code'];
            $attributes[$attrCode] = $result['acumatica_attr_code'].'|'.$result['sync_direction'];
        }
        return $attributes;
    }

    /**
     * @param $storeId
     * @return array
     */
    public function getAcumaticaAttributes($storeId)
    {
        $results = $this->getConnection()->fetchAll("SELECT acumatica_attr_code,magento_attr_code,sync_direction FROM ".$this->getTable('amconnector_product_mapping')." where sync_direction in('Bi-Directional (Last Update Wins)', 'Magento to Acumatica', 'Bi-Directional (Magento Wins)','Bi-Directional (Acumatica Wins)') and store_id =".$storeId);
        $attributes = array();
        foreach($results as $result){
            $attrCode = $result['magento_attr_code'];
            $attributes[$attrCode] = $result['acumatica_attr_code'].'|'.$result['sync_direction'];
        }
        return $attributes;
    }

    /**
     * @param $sku
     * @return string
     */
    public function getProductBySku($sku)
    {
        $product = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_entity') . " WHERE sku ="."'".$sku."'");
        return $product;
    }

    /**
     * @param $id
     * @return string
     */
    public function getAcumaticaAttrCode($id)
    {
        $data = explode("_",$id);
        if(isset($data[1]) && count($data) > 1){
            $acumaticaLable = $this->getConnection()->fetchOne("SELECT attributeid FROM ".$this->getTable('amconnector_custom_product_attributes')." where id = $data[0] ");
        }else{
            $acumaticaLable = $this->getConnection()->fetchOne("SELECT label FROM ".$this->getTable('amconnector_acumatica_product_attributes')." where code = '".$id. "'");
        }
        return trim($acumaticaLable);
    }

    /**
     * get tax class name of the product
     * @param $id
     */
    public function getTaxClassName($id)
    {
        $taxClass = $this->getConnection()->fetchOne("SELECT class_name FROM ".$this->getTable('tax_class')." where class_id = $id ");
        return strtoupper($taxClass);
    }

    /**
     * @param $attributeCode
     * @return string
     */
    public function magentoAttributeType($attributeCode)
    {
        $attributeType = $this->getConnection()->fetchOne("SELECT frontend_input from ".$this->getTable('eav_attribute')." where attribute_code='".$attributeCode."'");
        return $attributeType;
    }

    public function magentoAttributeCodeByLabel($attributeLabel)
    {
        $attributeCode = $this->getConnection()->fetchOne("SELECT attribute_code FROM ".$this->getTable('eav_attribute')." WHERE attribute_code LIKE 'acu_in%' AND frontend_label='".addslashes($attributeLabel)."'");
        return $attributeCode;
    }

    /**
     * @param $entityTypeId
     * @param $itemClassName
     * @return string
     */
    public function getAttributeSetId($entityTypeId,$itemClassName)
    {
        $attributeSetId = $this->getConnection()->fetchOne("SELECT attribute_set_id from ".$this->getTable('eav_attribute_set')." where entity_type_id =".$entityTypeId." and attribute_set_name = '".$itemClassName."'");
        return $attributeSetId;
    }

    public function getAttributeSetName($entityTypeId,$attributeSetId)
    {
        $attributeSetId = $this->getConnection()->fetchOne("SELECT attribute_set_name from ".$this->getTable('eav_attribute_set')." where entity_type_id =".$entityTypeId." and attribute_set_id = ".$attributeSetId);
        return $attributeSetId;
    }

    /**
     * @param $acumaticaId
     * @param $storeId
     * @return string
     * check category in magento using acumatica category id
     */
    public function checkCategoryInMagento($acumaticaId,$storeId)
    {
        $categoryAttributeId = $this->getConnection()->fetchOne("SELECT attribute_id FROM ".$this->getTable('eav_attribute')." WHERE attribute_code in ('acumatica_category_id') AND entity_type_id = 3");
        if($storeId >1)
            $categoryEntityId = $this->getConnection()->fetchOne("SELECT row_id FROM ".$this->getTable('catalog_category_entity_varchar')." WHERE attribute_id =".$categoryAttributeId." AND value = '".$acumaticaId."' AND store_id =".$storeId);
        else
            $categoryEntityId = $this->getConnection()->fetchOne("SELECT row_id FROM ".$this->getTable('catalog_category_entity_varchar')." WHERE attribute_id =".$categoryAttributeId." AND value = '".$acumaticaId."'");

        return $categoryEntityId;
    }

    /**
     * @param $taxClassName
     * @return string
     */
    public function getTaxClassId($taxClassName)
    {
        $taxClassId = $this->getConnection()->fetchOne("SELECT class_id FROM ".$this->getTable('tax_class')." WHERE class_name = '".$taxClassName."' AND class_type = 'PRODUCT'");
        return $taxClassId;
    }

    public function getAttributeData(){
        $dbObject = $this->getConnection();
        $arrAttribute = $dbObject->fetchAll('SELECT attribute_code, attribute_id,backend_type from '.$this->getTable('eav_attribute').' where entity_type_id =4');
        $attribute = array();
        foreach($arrAttribute as $attr){
            $attribute[$attr['backend_type']][$attr['attribute_code']] = $attr['attribute_id'];
        }
        return $attribute;
    }

    public function productSequence(){
        $dbObject = $this->getConnection();
        $tableName = $this->getTable('sequence_product');
        $dbObject->insert($tableName, []);
        return $dbObject->lastInsertId($tableName);
    }


    public function getStringInsertQueryData($productId,$arrVarAttributes,$productData,$storeId,$type=null){
        $string = '';
        foreach($arrVarAttributes as $attributeCode=>$attributeId){
            if(isset($productData[$attributeCode])) {
                if($productData[$attributeCode] == "NULL")
                    $productData[$attributeCode] = NULL;
                $attributeValue = addslashes($productData[$attributeCode]);
                if (empty($string))
                    $string = "($attributeId,$storeId,$productId,'$attributeValue')";
                else
                    $string = $string . ",($attributeId,$storeId,$productId,'$attributeValue')";
            }
        }
        return $string;

    }

    public function getAttributeOptionId($attributeCode, $attrValue,$storeId){
        $dbObject = $this->getConnection();
        $attributeIds = $dbObject->fetchAll("SELECT eav.attribute_id,eaov.option_id from ".$this->getTable('eav_attribute')." eav inner join ".$this->getTable('eav_attribute_option')." eao on eao.attribute_id=eav.attribute_id inner join eav_attribute_option_value eaov on eaov.option_id = eao.option_id where eav.attribute_code = '$attributeCode' and eaov.store_id=$storeId and eaov.value='$attrValue' ");
        $attributeInfo = array();
        foreach ($attributeIds as $attribute) {
            $attributeInfo['attribute_id'] = $attribute['attribute_id'];
            $attributeInfo['option_id']  = $attribute['option_id'];
        }
        return $attributeInfo;
    }


    public function enableSync()
    {
        $path = 'amconnectorsync/productsync/syncstopflg';
        $query = "update ".$this->getTable('core_config_data')." set value = 1 where path ='".$path."'";
        $this->getConnection()->query($query);
    }

    /**
     * @param $syncData
     * @param $storeId
     * @param $realCategories
     * @param $upsell
     * @param $csell
     * @return array|bool
     */
    public function createSimpleProduct($syncData,$storeId,$realCategories,$upsell,$csell)
    {
        $arrAttributes = $this->getAttributeData();
        if($storeId <= 1)
            $storeId = 0;

            $productData = array(
                "qty" => 0,
                "country_of_manufacture" => "NULL",
                "image" => "no_selection",
                "small_image" => "no_selection",
                "thumbnail" => "no_selection",
                "custom_design" => "NULL",
                "page_layout" => "NULL",
                "options_container" => "container2",
                "custom_layout_update" => "NULL",
                "custom_design_from" => "NULL",
                "custom_design_to" => "NULL",
                "quantity_and_stock_status" => 1
            );

        $productData = array_merge($syncData,$productData);
        $price = $productData['price'];
        $qty = $productData['qty'];
        $sku = $productData['sku'];
        $attribute_set = $productData['attribute_set'];
        $website = $productData['websites'];
        $taxClassId = $productData['tax_class_id'];
        //check Child product is not created or not
        $dbObject = $this->getConnection();
        $childProduct = $dbObject->fetchAll("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$sku'");
        if(count($childProduct) == 0) {
            $entityId = $this->productSequence();
            $maxUpdateVersion = \Magento\Staging\Model\VersionManager::MAX_VERSION;
            $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity')." (`entity_id`,`created_in`,`updated_in`,`attribute_set_id`, `type_id`, `sku`, `has_options`, `required_options`, `created_at`, `updated_at`) VALUES
                              ($entityId,'1','$maxUpdateVersion',$attribute_set, 'simple', '$sku', '0', '0', NOW(), NOW())");
            $rowId = $this->getConnection()->lastInsertId();
            if ($rowId) {
                if(isset($realCategories) && !empty($realCategories))
                {
                    foreach($realCategories as $categoryId)
                    {
                        $dbObject->query("INSERT INTO ".$this->getTable('catalog_category_product')." (`category_id`, `product_id`, `position`) VALUES ($categoryId, $entityId, '0')");
                    }
                }

                //Website
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_website')." (`product_id`, `website_id`) VALUES ($entityId,$website)");
                //Product Details
                $getVarCharInsertQueryData = $this->getStringInsertQueryData($rowId, $arrAttributes['varchar'], $productData, $storeId);
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity_varchar')." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES
                        $getVarCharInsertQueryData"
                );
                $getTextInsertQueryData = $this->getStringInsertQueryData($rowId, $arrAttributes['text'], $productData, $storeId);
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity_text')." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES
                     $getTextInsertQueryData ");

                $getDateInsertQueryData = $this->getStringInsertQueryData($rowId, $arrAttributes['datetime'], $productData, $storeId);
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity_datetime')." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES
                      $getDateInsertQueryData");
                $getDecimalInsertQueryData = $this->getStringInsertQueryData($rowId, $arrAttributes['decimal'], $productData, $storeId);
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity_decimal')." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES
                      $getDecimalInsertQueryData");
                $getIntInsertQueryData = $this->getStringInsertQueryData($rowId, $arrAttributes['int'], $productData, $storeId);
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity_int')." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES
                      $getIntInsertQueryData");

                if(isset($realCategories) && !empty($realCategories)) {
                    foreach($realCategories as $_categoryId)
                    {
                        $dbObject->query("INSERT INTO ".$this->getTable('catalog_category_product_index')." (`category_id`, `product_id`, `position`, `is_parent`, `store_id`, `visibility`) VALUES
                          ($_categoryId, $entityId, 0, 1, $storeId, 4)");
                    }
                }
                /**
                 * Price for all customer groups
                 */
                $customerGroupCollection = $this->customerGroup->create()->getCollection()->getData();
                if(isset($customerGroupCollection) && !empty($customerGroupCollection))
                {
                    foreach($customerGroupCollection as $customerGroup)
                    {
                        $customerGroupId = $customerGroup['customer_group_id'];
                        $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_index_price') . " (`entity_id`, `customer_group_id`, `website_id`, `tax_class_id`, `price`, `final_price`, `min_price`, `max_price`, `tier_price`) VALUES
                          ($entityId, $customerGroupId, $website, $taxClassId, $price, $price, $price, $price, NULL)");
                    }
                }

                $dbObject->query("INSERT INTO ".$this->getTable('cataloginventory_stock_item')." (`product_id`, `stock_id`, `qty`, `min_qty`, `use_config_min_qty`, `is_qty_decimal`, `backorders`, `use_config_backorders`, `min_sale_qty`, `use_config_min_sale_qty`, `max_sale_qty`, `use_config_max_sale_qty`, `is_in_stock`, `low_stock_date`, `notify_stock_qty`, `use_config_notify_stock_qty`, `manage_stock`, `use_config_manage_stock`, `stock_status_changed_auto`, `use_config_qty_increments`, `qty_increments`, `use_config_enable_qty_inc`, `enable_qty_increments`, `is_decimal_divided`,`website_id`,`deferred_stock_update`,`use_config_deferred_stock_update`) VALUES
                         ($entityId, 1, $qty, '0.0000', 1, 0, 0, 1, '0.0000', 1, '1.0000', 1, 1, NULL, NULL, 1, 1, 1, 0, 1, '0.0000', 1, 0, 0,$website,0,0)");
                $dbObject->query("INSERT INTO ".$this->getTable('cataloginventory_stock_status')." (`product_id`, `website_id`, `stock_id`, `qty`, `stock_status`) VALUES
                              ($entityId, $website, '1', $qty, '1')");

                if(isset($upsell) && !empty($upsell))
                {
                    $upsellPosition = 1;
                    foreach($upsell as $upsellId)
                    {
                        if(!is_array($upsellId))
                            $upsellEntityId = $dbObject->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$upsellId'");
                        if(isset($upsellEntityId) && $upsellEntityId != '')
                        {
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($rowId,$upsellEntityId,4)");
                            $upsellLinkId = $this->getConnection()->lastInsertId();
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (2, $upsellLinkId,$upsellPosition)");
                            $upsellPosition++;
                        }
                    }
                }

                if(isset($csell) && !empty($csell))
                {
                    $csellPosition = 1;
                    foreach($csell as $csellId)
                    {
                        if(!is_array($csellId))
                            $csellEntityId = $dbObject->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$csellId'");
                        if(isset($csellEntityId) && $csellEntityId != '')
                        {
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($rowId, $csellEntityId,5)");
                            $csellLinkId = $this->getConnection()->lastInsertId();
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (3, $csellLinkId,$csellPosition)");
                            $csellPosition++;
                        }
                    }
                }
		//Product Creation
                return array("entityId"=>$entityId);
            } else {
                return false;
            }
        }else{
            return array("entityId"=>$childProduct[0]['entity_id']);
        }
    }

    /**
     * @param $syncData
     * @param $storeId
     * @param $realCategories
     * @param $upsell
     * @param $csell
     * @return array|bool
     */
    public function updateSimpleProduct($syncData,$storeId,$realCategories,$upsell,$csell)
    {
        $arrAttributes = $this->getAttributeData();
        if($storeId <= 1)
            $storeId = 0;
        $productData = $syncData;
        $price = $productData['price'];
        $sku = $productData['sku'];
        $attribute_set = $productData['attribute_set'];
        $website = $productData['websites'];
        $taxClassId = $productData['tax_class_id'];
        //check Child product is not created or not
        $dbObject = $this->getConnection();
        $childProduct = $dbObject->fetchAll("SELECT row_id,entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$sku'");
        if(count($childProduct)) {
            $entityId = $childProduct[0]['entity_id'];
            $rowId = $childProduct[0]['row_id'];
            if ($rowId) {
                if(isset($realCategories) && !empty($realCategories))
                {
                    foreach($realCategories as $categoryId)
                    {
                        $productCategories = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_category_product') . " WHERE product_id =".$entityId." AND category_id =".$categoryId);
                        if($productCategories == 0)
                        {
                            $dbObject->query("INSERT INTO ".$this->getTable('catalog_category_product')." (`category_id`, `product_id`, `position`) VALUES ($categoryId, $entityId, '0')");
                        }
                    }
                }
                //Website
                $productWebsite = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_website') . " WHERE product_id =".$entityId." AND website_id =".$website);
                if($productWebsite == 0)
                {
                    $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_website') . " (`product_id`, `website_id`) VALUES ($entityId,$website)");
                }

                /**
                 * updating Product Details
                 */
                if(isset($arrAttributes['varchar']) && !empty($arrAttributes['varchar']))
                    $this->updateProductDetails($rowId, $arrAttributes['varchar'], $productData, $storeId,'varchar');

                if(isset($arrAttributes['text']) && !empty($arrAttributes['text']))
                    $this->updateProductDetails($rowId, $arrAttributes['text'], $productData, $storeId,'text');

                if(isset($arrAttributes['datetime']) && !empty($arrAttributes['datetime']))
                    $this->updateProductDetails($rowId, $arrAttributes['datetime'], $productData, $storeId,'datetime');

                if(isset($arrAttributes['decimal']) && !empty($arrAttributes['decimal']))
                    $this->updateProductDetails($rowId, $arrAttributes['decimal'], $productData, $storeId,'decimal');

                if(isset($arrAttributes['int']) && !empty($arrAttributes['int']))
                    $this->updateProductDetails($rowId, $arrAttributes['int'], $productData, $storeId,'int');

                if(isset($realCategories) && !empty($realCategories))
                {
                    foreach($realCategories as $_categoryId)
                    {
                        /**
                         * check condition
                         */
                        $categoryPosition = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_category_product_index') . " WHERE product_id =".$entityId." AND category_id =".$_categoryId." AND store_id=".$storeId);
                        if($categoryPosition == 0)
                        {
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_category_product_index') . " (`category_id`, `product_id`, `position`, `is_parent`, `store_id`, `visibility`) VALUES
                          ($_categoryId, $entityId, 0, 1, $storeId, 4)");
                        }
                    }
                }
                /**
                 * Price updation for all customer groups
                 */
                $customerGroupCollection = $this->customerGroup->create()->getCollection()->getData();
                if(isset($customerGroupCollection) && !empty($customerGroupCollection))
                {
                    foreach($customerGroupCollection as $customerGroup)
                    {
                        $customerGroupId = $customerGroup['customer_group_id'];

                        $productPrice = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_index_price') . " WHERE entity_id =".$entityId." AND customer_group_id =".$customerGroupId." AND website_id =".$website." AND tax_class_id =".$taxClassId);
                        if(isset($productPrice) && $productPrice != '')
                        {
                            $dbObject->query("UPDATE " . $this->getTable('catalog_product_index_price') . " set price=".$price.",final_price=".$price.",min_price=".$price.",max_price=".$price." where entity_id='" . $entityId . "' and customer_group_id= '" . $customerGroupId . "' and website_id =".$website." and tax_class_id =".$taxClassId);
                        }else {
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_index_price') . " (`entity_id`, `customer_group_id`, `website_id`, `tax_class_id`, `price`, `final_price`, `min_price`, `max_price`, `tier_price`) VALUES
                          ($entityId, $customerGroupId, $website, $taxClassId, $price, $price, $price, $price, NULL)");
                        }
                    }
                }
                if(isset($upsell) && !empty($upsell))
                {
                    $upsellPosition = 1;
                    foreach($upsell as $upsellId)
                    {
                        $upsellEntityId = '';
                        if(!is_array($upsellId))
                          $upsellEntityId = $dbObject->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$upsellId'");
                        if(isset($upsellEntityId) && $upsellEntityId != '')
                        {
                            $upsellCheck = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_link') . " WHERE product_id =" . $rowId . " AND linked_product_id =" . $upsellEntityId . " AND link_type_id = 4");
                            if ($upsellCheck == 0) {
                                $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($rowId, $upsellEntityId,4)");
                                $upsellLinkId = $this->getConnection()->lastInsertId();
                                $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (2, $upsellLinkId,$upsellPosition)");
                            }
                            $upsellPosition++;
                        }
                    }
                }

                if(isset($csell) && !empty($csell))
                {
                    $csellPosition = 1;
                    foreach($csell as $csellId)
                    {
                        if(!is_array($csellId))
                            $csellEntityId = $dbObject->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$csellId'");
                        if(isset($csellEntityId) && $csellEntityId != '')
                        {
                            $csellCheck = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_link') . " WHERE product_id =" . $rowId . " AND linked_product_id =" . $csellEntityId . " AND link_type_id = 5");
                            if ($csellCheck == 0) {
                                $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($rowId, $csellEntityId,5)");
                                $csellLinkId = $this->getConnection()->lastInsertId();
                                $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (3, $csellLinkId,$csellPosition)");
                            }
                            $csellPosition++;
                        }
                    }
                }
		// Product Update
                $this->updateProductDate($rowId);
                return array("entityId"=>$entityId);
            } else {
                return false;
            }
        }
    }

    /**
     * @param $rowId
     * @param $arrAttributes
     * @param $productData
     * @param $storeId
     * @param $type
     */
    public function updateProductDetails($rowId, $arrAttributes, $productData, $storeId,$type)
    {
        if($type == 'varchar')
        {
            $tableName = $this->getTable("catalog_product_entity_varchar");
        }else if($type == 'text')
        {
            $tableName = $this->getTable("catalog_product_entity_text");
        }else if($type == 'int')
        {
            $tableName = $this->getTable("catalog_product_entity_int");
        }else if($type == 'datetime')
        {
            $tableName = $this->getTable("catalog_product_entity_datetime");
        }else if($type == 'decimal')
        {
            $tableName = $this->getTable("catalog_product_entity_decimal");
        }
        foreach($arrAttributes as $attributeCode=>$attributeId){
            if(isset($productData[$attributeCode])) {
                if($productData[$attributeCode] == "NULL")
                    $productData[$attributeCode] = NULL;
                $attributeValue = addslashes($productData[$attributeCode]);
                $attributeOptionCheck = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $tableName . " WHERE attribute_id =".$attributeId." AND store_id =".$storeId." AND row_id =".$rowId);
                if(isset($attributeOptionCheck) && $attributeOptionCheck != 0)
                {
                    $this->getConnection()->query("UPDATE " . $tableName . " set value='" . $attributeValue . "' where attribute_id='" . $attributeId . "' and row_id =".$rowId);
                }else{
                    $this->getConnection()->query("INSERT INTO ".$tableName." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES ($attributeId,$storeId,$rowId,'".$attributeValue."')");
                }
            }
        }
    }

    public function updateProductDate($rowId)
    {
        $updatedAt = $this->date->date('Y-m-d H:i:s',time());
        $this->getConnection()->query("UPDATE " . $this->getTable('catalog_product_entity') . " set updated_at='" .$updatedAt."' where row_id =".$rowId);

    }

    /**
     * @param $realCategories
     * @param $productId
     */
    public function assignCategories($realCategories,$productId)
    {
        foreach ($realCategories as $categoryId)
        {
            $productCategories = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_category_product') . " WHERE product_id =" . $productId . " AND category_id =" . $categoryId);
            if ($productCategories == 0)
            {
                $this->getConnection()->query("INSERT INTO " . $this->getTable('catalog_category_product') . " (`category_id`, `product_id`, `position`) VALUES ($categoryId, $productId, '0')");
            }
        }
    }

    /**
     * @param $entityId
     * @return string
     */
    public function getSkuById($entityId)
    {
        $productSku = $this->getConnection()->fetchOne("SELECT sku FROM " . $this->getTable('catalog_product_entity') . " WHERE entity_id =" . $entityId);
        return $productSku;
    }

    /*
     * @param $productRowId
     */
    /*public function updateParentWithChild($productRowId)
    {
        $costAttributeId = $this->magentoAttributeIdByCode("acu_in_sbcost");
        $vendorPriceAttributeId = $this->magentoAttributeIdByCode("acu_in_vendorprice");
        $contributionAttributeId = $this->magentoAttributeIdByCode("acu_in_contributioncost");
        $dsHandlingAttributeId = $this->magentoAttributeIdByCode("acu_in_dshandlingfees");
        $shippingCostAttributeId = $this->magentoAttributeIdByCode("acu_in_shippingcost");
        $query = "SELECT row_id,entity_id,sku FROM " . $this->getTable('catalog_product_entity') . " WHERE type_id='configurable' AND row_id =".$productRowId;
        $results = $this->getConnection()->fetchAll($query);
        foreach($results as $result) {
            $productRowId = $result['row_id'];
            $getChildProductQuery = "SELECT product_id FROM " . $this->getTable('catalog_product_super_link') . " WHERE parent_id = $productRowId LIMIT 1";
            $getChildProduct = $this->getConnection()->fetchOne($getChildProductQuery);
            if(isset($getChildProduct)){
                $childEntityId = $getChildProduct;
                $query = "SELECT attribute_id,value FROM " . $this->getTable('catalog_product_entity_decimal') . " cpev left join " . $this->getTable('catalog_product_entity') . " cpe ON cpe.row_id = cpev.row_id WHERE attribute_id IN ($costAttributeId,$vendorPriceAttributeId,$contributionAttributeId,$dsHandlingAttributeId,$shippingCostAttributeId) AND store_id = 0 AND cpe.entity_id = $childEntityId";
                $costInfo = $this->getConnection()->fetchAll($query);
                $cost= $vendorPrice= $contributionCost = $dsHandlingCost = $shippingCost ='';
                foreach($costInfo as $cInfo){
                    if($cInfo['attribute_id'] == $costAttributeId){
                        $cost = $cInfo['value'];
                    }elseif($cInfo['attribute_id'] == $vendorPriceAttributeId){
                        $vendorPrice = $cInfo['value'];
                    }elseif($cInfo['attribute_id'] == $contributionAttributeId){
                        $contributionCost = $cInfo['value'];
                    }elseif($cInfo['attribute_id'] == $dsHandlingAttributeId){
                        $dsHandlingCost = $cInfo['value'];
                    }elseif($cInfo['attribute_id'] == $shippingCostAttributeId){
                        $shippingCost = 6;
                    }
                }
                //COST UPDATE
                if(!empty($cost)) {
                     $query = "SELECT value FROM " . $this->getTable('catalog_product_entity_decimal') . " WHERE row_id = $productRowId AND attribute_id =$costAttributeId  AND store_id = 0";
                    $parentCostInfo = $this->getConnection()->fetchOne($query);
                    if($parentCostInfo)
                    {
                       $query = "UPDATE " . $this->getTable('catalog_product_entity_decimal') . " SET value = '".$cost."' WHERE row_id = $productRowId AND attribute_id =$costAttributeId ";
                        $this->getConnection()->query($query);
                    }else{
                        //COST, VENDOR PRICE, CONTRIBUTION COST
                        $query  =" INSERT INTO " . $this->getTable('catalog_product_entity_decimal') . " (`row_id` ,`store_id` ,`attribute_id` ,`value`) VALUES ($productRowId , 0, $costAttributeId, '" . $cost . "')";
                        $this->getConnection()->query($query);
                    }
                }
                //$vendorPrice
                if(!empty($vendorPrice)) {
                    $query = "SELECT value FROM " . $this->getTable('catalog_product_entity_decimal') . " WHERE row_id = $productRowId  AND attribute_id =$vendorPriceAttributeId  AND store_id = 0";
                    $parentCostInfo = $this->getConnection()->fetchOne($query);
                    if($parentCostInfo)
                    {
                        $query = "UPDATE " . $this->getTable('catalog_product_entity_decimal') . " SET value = '".$vendorPrice."' WHERE row_id = $productRowId AND attribute_id =$vendorPriceAttributeId  ";
                        $this->getConnection()->query($query);
                    }else{
                        //COST, VENDOR PRICE, CONTRIBUTION COST
                        $query  =" INSERT INTO " . $this->getTable('catalog_product_entity_decimal') . " (`row_id` ,`store_id` ,`attribute_id` ,`value`) VALUES ($productRowId , 0, $vendorPriceAttributeId, '" . $vendorPrice . "')";
                        $this->getConnection()->query($query);
                    }
                }

                //$contributionCost
                if(!empty($contributionCost)) {
                    $query = "SELECT value FROM " . $this->getTable('catalog_product_entity_decimal') . " WHERE row_id = $productRowId AND attribute_id =$contributionAttributeId  AND store_id = 0";
                    $parentCostInfo = $this->getConnection()->fetchOne($query);
                    if($parentCostInfo)
                    {
                        $query = "UPDATE " . $this->getTable('catalog_product_entity_decimal') . " SET value = '".$contributionCost."' WHERE row_id = $productRowId AND attribute_id =$contributionAttributeId  ";
                        $this->getConnection()->query($query);
                    }else{
                        //COST, VENDOR PRICE, CONTRIBUTION COST
                        $query  =" INSERT INTO " . $this->getTable('catalog_product_entity_decimal') . " (`row_id` ,`store_id` ,`attribute_id` ,`value`) VALUES ($productRowId , 0, $contributionAttributeId, '" . $contributionCost . "')";
                        $this->getConnection()->query($query);
                    }
                }

                //$dsHandlingCost
                if(!empty($dsHandlingCost)) {
                    $query = "SELECT value FROM " . $this->getTable('catalog_product_entity_decimal') . " WHERE row_id = $productRowId AND attribute_id =$dsHandlingAttributeId  AND store_id = 0";
                    $parentCostInfo = $this->getConnection()->fetchOne($query);
                    if($parentCostInfo)
                    {
                        $query = "UPDATE " . $this->getTable('catalog_product_entity_decimal') . " SET value = '".$dsHandlingCost."' WHERE row_id = $productRowId AND attribute_id =$dsHandlingAttributeId  ";
                        $this->getConnection()->query($query);
                    }else{
                        //COST, VENDOR PRICE, CONTRIBUTION COST
                        $query  =" INSERT INTO " . $this->getTable('catalog_product_entity_decimal') . " (`row_id` ,`store_id` ,`attribute_id` ,`value`) VALUES ($productRowId , 0, $dsHandlingAttributeId, '" . $dsHandlingCost . "')";
                        $this->getConnection()->query($query);
                    }
                }

                //$shippingCost
                if(!empty($shippingCost)) {
                    $query = "SELECT value FROM " . $this->getTable('catalog_product_entity_decimal') . " WHERE row_id = $productRowId AND attribute_id =$shippingCostAttributeId  AND store_id = 0";
                    $parentCostInfo = $this->getConnection()->fetchOne($query);
                    if($parentCostInfo)
                    {
                        $query = "UPDATE " . $this->getTable('catalog_product_entity_decimal') . " SET value = '".$shippingCost."' WHERE row_id = $productRowId AND attribute_id =$shippingCostAttributeId  ";
                        $this->getConnection()->query($query);
                    }else{
                        //COST, VENDOR PRICE, CONTRIBUTION COST
                        $query  =" INSERT INTO " . $this->getTable('catalog_product_entity_decimal') . " (`row_id` ,`store_id` ,`attribute_id` ,`value`) VALUES ($productRowId , 0, $shippingCostAttributeId, '" . $shippingCost . "')";
                        $this->getConnection()->query($query);
                    }
                }
            }
        }
    }*/

    /**
     * @param $attributeCode
     * @return string
     */
    public function magentoAttributeIdByCode($attributeCode)
    {
        $attributeId = $this->getConnection()->fetchOne("SELECT attribute_id from ".$this->getTable('eav_attribute')." where attribute_code='".$attributeCode."'");
        return $attributeId;
    }

    /**
     * @param $Id
     * @param int $storeId
     * @return string
     */
    public function getIsNonStock($Id,$storeId = 0)
    {
        if($storeId <= 1)
            $storeId = 0;

        $attributeId = $this->magentoAttributeIdByCode('is_non_stock');
        $status = $this->getConnection()->fetchOne("SELECT value from ".$this->getTable('catalog_product_entity_int')." where attribute_id='".$attributeId."' and store_id = '".$storeId."' and row_id=".$Id);
        return $status;
    }

    /**
     * @param $syncData
     * @param $storeId
     * @param $realCategories
     * @param $upsell
     * @param $csell
     * @return array|bool
     */
    public function updateSimpleNonStockProduct($syncData,$storeId,$realCategories,$upsell,$csell)
    {
        $arrAttributes = $this->getAttributeData();
        if($storeId <= 1)
            $storeId = 0;
        $productData = $syncData;
        $price = $productData['price'];
        $sku = $productData['sku'];
        $attribute_set = $productData['attribute_set'];
        $website = $productData['websites'];
        $taxClassId = $productData['tax_class_id'];
        //check Child product is not created or not
        $dbObject = $this->getConnection();
        $childProduct = $dbObject->fetchAll("SELECT row_id,entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$sku'");
        if(count($childProduct)) {
            $entityId = $childProduct[0]['entity_id'];
            $rowId = $childProduct[0]['row_id'];
            if ($rowId) {
                if(isset($realCategories) && !empty($realCategories))
                {
                    foreach($realCategories as $categoryId)
                    {
                        $productCategories = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_category_product') . " WHERE product_id =".$entityId." AND category_id =".$categoryId);
                        if($productCategories == 0)
                        {
                            $dbObject->query("INSERT INTO ".$this->getTable('catalog_category_product')." (`category_id`, `product_id`, `position`) VALUES ($categoryId, $entityId, '0')");
                        }
                    }
                }
                //Website
                $productWebsite = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_website') . " WHERE product_id =".$entityId." AND website_id =".$website);
                if($productWebsite == 0)
                {
                    $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_website') . " (`product_id`, `website_id`) VALUES ($entityId,$website)");
                }

                /**
                 * updating Product Details
                 */
                if(isset($arrAttributes['varchar']) && !empty($arrAttributes['varchar']))
                    $this->updateProductDetails($rowId, $arrAttributes['varchar'], $productData, $storeId,'varchar');

                if(isset($arrAttributes['text']) && !empty($arrAttributes['text']))
                    $this->updateProductDetails($rowId, $arrAttributes['text'], $productData, $storeId,'text');

                if(isset($arrAttributes['datetime']) && !empty($arrAttributes['datetime']))
                    $this->updateProductDetails($rowId, $arrAttributes['datetime'], $productData, $storeId,'datetime');

                if(isset($arrAttributes['decimal']) && !empty($arrAttributes['decimal']))
                    $this->updateProductDetails($rowId, $arrAttributes['decimal'], $productData, $storeId,'decimal');

                if(isset($arrAttributes['int']) && !empty($arrAttributes['int']))
                    $this->updateProductDetails($rowId, $arrAttributes['int'], $productData, $storeId,'int');

                if(isset($realCategories) && !empty($realCategories))
                {
                    foreach($realCategories as $_categoryId)
                    {
                        /**
                         * check condition
                         */
                        $categoryPosition = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_category_product_index') . " WHERE product_id =".$entityId." AND category_id =".$_categoryId." AND store_id=".$storeId);
                        if($categoryPosition == 0)
                        {
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_category_product_index') . " (`category_id`, `product_id`, `position`, `is_parent`, `store_id`, `visibility`) VALUES
                          ($_categoryId, $entityId, 0, 1, $storeId, 4)");
                        }
                    }
                }
                /**
                 * Price updation for all customer groups
                 */
                $customerGroupCollection = $this->customerGroup->create()->getCollection()->getData();
                if(isset($customerGroupCollection) && !empty($customerGroupCollection))
                {
                    foreach($customerGroupCollection as $customerGroup)
                    {
                        $customerGroupId = $customerGroup['customer_group_id'];

                        $productPrice = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_index_price') . " WHERE entity_id =".$entityId." AND customer_group_id =".$customerGroupId." AND website_id =".$website." AND tax_class_id =".$taxClassId);
                        if(isset($productPrice) && $productPrice != '')
                        {
                            $dbObject->query("UPDATE " . $this->getTable('catalog_product_index_price') . " set price=".$price.",final_price=".$price.",min_price=".$price.",max_price=".$price." where entity_id='" . $entityId . "' and customer_group_id= '" . $customerGroupId . "' and website_id =".$website." and tax_class_id =".$taxClassId);
                        }else {
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_index_price') . " (`entity_id`, `customer_group_id`, `website_id`, `tax_class_id`, `price`, `final_price`, `min_price`, `max_price`, `tier_price`) VALUES
                          ($entityId, $customerGroupId, $website, $taxClassId, $price, $price, $price, $price, NULL)");
                        }
                    }
                }
                if(isset($upsell) && !empty($upsell))
                {
                    $upsellPosition = 1;
                    foreach($upsell as $upsellId)
                    {
                        $upsellEntityId = '';
                        if(!is_array($upsellId))
                            $upsellEntityId = $dbObject->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$upsellId'");
                        if(isset($upsellEntityId) && $upsellEntityId != '')
                        {
                            $upsellCheck = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_link') . " WHERE product_id =" . $rowId . " AND linked_product_id =" . $upsellEntityId . " AND link_type_id = 4");
                            if ($upsellCheck == 0) {
                                $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($rowId, $upsellEntityId,4)");
                                $upsellLinkId = $this->getConnection()->lastInsertId();
                                $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (2, $upsellLinkId,$upsellPosition)");
                            }
                            $upsellPosition++;
                        }
                    }
                }

                if(isset($csell) && !empty($csell))
                {
                    $csellPosition = 1;
                    foreach($csell as $csellId)
                    {
                        if(!is_array($csellId))
                            $csellEntityId = $dbObject->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$csellId'");
                        if(isset($csellEntityId) && $csellEntityId != '')
                        {
                            $csellCheck = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_link') . " WHERE product_id =" . $rowId . " AND linked_product_id =" . $csellEntityId . " AND link_type_id = 5");
                            if ($csellCheck == 0) {
                                $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($rowId, $csellEntityId,5)");
                                $csellLinkId = $this->getConnection()->lastInsertId();
                                $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (3, $csellLinkId,$csellPosition)");
                            }
                            $csellPosition++;
                        }
                    }
                }
                // Product Update
                $this->updateProductDate($rowId);
                return array("entityId"=>$entityId);
            } else {
                return false;
            }
        }
    }

    /**
     * @param $syncData
     * @param $storeId
     * @param $realCategories
     * @param $upsell
     * @param $csell
     * @return array|bool
     */
    public function createSimpleNonStockProduct($syncData,$storeId,$realCategories,$upsell,$csell)
    {
        $arrAttributes = $this->getAttributeData();
        if($storeId <= 1)
            $storeId = 0;

        $productData = array(
            "qty" => 0,
            "country_of_manufacture" => "NULL",
            "image" => "no_selection",
            "small_image" => "no_selection",
            "thumbnail" => "no_selection",
            "custom_design" => "NULL",
            "page_layout" => "NULL",
            "options_container" => "container2",
            "custom_layout_update" => "NULL",
            "custom_design_from" => "NULL",
            "custom_design_to" => "NULL",
            "quantity_and_stock_status" => 1
        );

        $productData = array_merge($syncData,$productData);
        $price = $productData['price'];
        $qty = $productData['qty'];
        $sku = $productData['sku'];
        $attribute_set = $productData['attribute_set'];
        $website = $productData['websites'];
        $taxClassId = $productData['tax_class_id'];
        //check Child product is not created or not
        $dbObject = $this->getConnection();
        $childProduct = $dbObject->fetchAll("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$sku'");
        if(count($childProduct) == 0) {
            $entityId = $this->productSequence();
            $maxUpdateVersion = \Magento\Staging\Model\VersionManager::MAX_VERSION;
            $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity')." (`entity_id`,`created_in`,`updated_in`,`attribute_set_id`, `type_id`, `sku`, `has_options`, `required_options`, `created_at`, `updated_at`) VALUES
                              ($entityId,'1','$maxUpdateVersion',$attribute_set, 'simple', '$sku', '0', '0', NOW(), NOW())");
            $rowId = $this->getConnection()->lastInsertId();
            if ($rowId) {
                if(isset($realCategories) && !empty($realCategories))
                {
                    foreach($realCategories as $categoryId)
                    {
                        $dbObject->query("INSERT INTO ".$this->getTable('catalog_category_product')." (`category_id`, `product_id`, `position`) VALUES ($categoryId, $entityId, '0')");
                    }
                }

                //Website
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_website')." (`product_id`, `website_id`) VALUES ($entityId,$website)");
                //Product Details
                $getVarCharInsertQueryData = $this->getStringInsertQueryData($rowId, $arrAttributes['varchar'], $productData, $storeId);
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity_varchar')." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES
                        $getVarCharInsertQueryData"
                );
                $getTextInsertQueryData = $this->getStringInsertQueryData($rowId, $arrAttributes['text'], $productData, $storeId);
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity_text')." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES
                     $getTextInsertQueryData ");

                $getDateInsertQueryData = $this->getStringInsertQueryData($rowId, $arrAttributes['datetime'], $productData, $storeId);
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity_datetime')." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES
                      $getDateInsertQueryData");
                $getDecimalInsertQueryData = $this->getStringInsertQueryData($rowId, $arrAttributes['decimal'], $productData, $storeId);
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity_decimal')." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES
                      $getDecimalInsertQueryData");
                $getIntInsertQueryData = $this->getStringInsertQueryData($rowId, $arrAttributes['int'], $productData, $storeId);
                $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity_int')." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES
                      $getIntInsertQueryData");

                if(isset($realCategories) && !empty($realCategories)) {
                    foreach($realCategories as $_categoryId)
                    {
                        $dbObject->query("INSERT INTO ".$this->getTable('catalog_category_product_index')." (`category_id`, `product_id`, `position`, `is_parent`, `store_id`, `visibility`) VALUES
                          ($_categoryId, $entityId, 0, 1, $storeId, 4)");
                    }
                }
                /**
                 * Price for all customer groups
                 */
                $customerGroupCollection = $this->customerGroup->create()->getCollection()->getData();
                if(isset($customerGroupCollection) && !empty($customerGroupCollection))
                {
                    foreach($customerGroupCollection as $customerGroup)
                    {
                        $customerGroupId = $customerGroup['customer_group_id'];
                        $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_index_price') . " (`entity_id`, `customer_group_id`, `website_id`, `tax_class_id`, `price`, `final_price`, `min_price`, `max_price`, `tier_price`) VALUES
                          ($entityId, $customerGroupId, $website, $taxClassId, $price, $price, $price, $price, NULL)");
                    }
                }

                $dbObject->query("INSERT INTO ".$this->getTable('cataloginventory_stock_item')." (`product_id`, `stock_id`, `qty`, `min_qty`, `use_config_min_qty`, `is_qty_decimal`, `backorders`, `use_config_backorders`, `min_sale_qty`, `use_config_min_sale_qty`, `max_sale_qty`, `use_config_max_sale_qty`, `is_in_stock`, `low_stock_date`, `notify_stock_qty`, `use_config_notify_stock_qty`, `manage_stock`, `use_config_manage_stock`, `stock_status_changed_auto`, `use_config_qty_increments`, `qty_increments`, `use_config_enable_qty_inc`, `enable_qty_increments`, `is_decimal_divided`,`website_id`,`deferred_stock_update`,`use_config_deferred_stock_update`) VALUES
                         ($entityId, 1, $qty, '0.0000', 1, 0, 0, 1, '0.0000', 1, '1.0000', 1, 1, NULL, NULL, 1, 0, 0, 0, 1, '0.0000', 1, 0, 0,$website,0,0)");
                $dbObject->query("INSERT INTO ".$this->getTable('cataloginventory_stock_status')." (`product_id`, `website_id`, `stock_id`, `qty`, `stock_status`) VALUES
                              ($entityId, $website, '1', $qty, '1')");

                if(isset($upsell) && !empty($upsell))
                {
                    $upsellPosition = 1;
                    foreach($upsell as $upsellId)
                    {
                        if(!is_array($upsellId))
                            $upsellEntityId = $dbObject->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$upsellId'");
                        if(isset($upsellEntityId) && $upsellEntityId != '')
                        {
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($rowId,$upsellEntityId,4)");
                            $upsellLinkId = $this->getConnection()->lastInsertId();
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (2, $upsellLinkId,$upsellPosition)");
                            $upsellPosition++;
                        }
                    }
                }

                if(isset($csell) && !empty($csell))
                {
                    $csellPosition = 1;
                    foreach($csell as $csellId)
                    {
                        if(!is_array($csellId))
                            $csellEntityId = $dbObject->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$csellId'");
                        if(isset($csellEntityId) && $csellEntityId != '')
                        {
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($rowId, $csellEntityId,5)");
                            $csellLinkId = $this->getConnection()->lastInsertId();
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (3, $csellLinkId,$csellPosition)");
                            $csellPosition++;
                        }
                    }
                }
                //Product Creation
                return array("entityId"=>$entityId);
            } else {
                return false;
            }
        }else{
            return array("entityId"=>$childProduct[0]['entity_id']);
        }
    }

    /**
     * @return array
     */
    public function getAllAttributeSets()
    {
        $sqlQuery = "SELECT attribute_set_id,attribute_set_name FROM " . $this->getTable('eav_attribute_set')." WHERE entity_type_id = 4";
        $attributeSetChecks = $this->getConnection()->fetchAll($sqlQuery);
        return $attributeSetChecks;
    }

}
