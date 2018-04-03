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

/**
 * Class Product
 * @package Kensium\Amconnector\Model\ResourceModel
 */
class ProductConfigurator extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        Logger $logger,
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
     * @param $syncId
     * @param $status
     * @param $storeId
     */
    public function updateSyncAttribute($syncId,$status,$storeId)
    {

        $updatedDate = $this->date->date('Y-m-d H:i:s', time());
        $this->getConnection()->query("UPDATE ".$this->getTable("amconnector_attribute_sync")." set status='".$status."', last_sync_date='".$updatedDate."' where id='".$syncId."' and store_id= '".$storeId."' ");
    }

    public function truncateDataFromTempTables($type)
    {
        if($type == "configurable")
        {
            $tableName = "amconnector_configurable_product_sync_temp";

        }elseif($type == "bundle")
        {
            $tableName = "amconnector_bundle_product_sync_temp";

        }elseif($type == "grouped")
        {
            $tableName = "amconnector_group_product_sync_temp";
        }

        $this->getConnection()->query("TRUNCATE table " .$this->getTable("$tableName"));
    }

    /**
     * Fetching the data based on last sync date
     * and Inserting into the temp table
     * First fetching from Acumatica and if same record is updated in Magento
     * then updating the same record with customer detail and updated date in temp table
     * @param $acumaticaData
     * @param $syncId
     * @param null $storeId
     * @param null $type
     * @return array
     */
    public function insertDataIntoTempTables($acumaticaData, $syncId, $scopeType=NULL,$storeId = NULL,$type = NULL )
    {
        if($storeId == NULL)
            $storeId = 1;
        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
        if($type == "configurable")
        {
            $tableName = "amconnector_configurable_product_sync_temp";

        }elseif($type == "bundle")
        {
            $tableName = "amconnector_bundle_product_sync_temp";

        }elseif($type == "grouped")
        {
            $tableName = "amconnector_group_product_sync_temp";
        }
        /**
         * Based on the last sync date get the data from Acumatica and insert into temporary table
         */
        $oneRecordFlag=false;

        $productSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/configuratorsync/syncdirection',$scopeType,$storeId);
        if(!isset($productSyncDirection))
        {
            $productSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/configuratorsync/syncdirection');
        }
        if ($productSyncDirection == 1 || $productSyncDirection == 3)
        {
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
                    $acumaticaWarehouse = trim($value->DefaultWarehouse->Value); // Default warehouse for product in acumatica
                    if ($acumaticaWarehouse == $defaultWarehouse) {
                        $acumaticaId = $value->InventoryID->Value;
                        $acumaticaModifiedDate = $this->date->date('Y-m-d H:i:s', strtotime($value->LastModified->Value));
                        $acumaticaRecordCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable("$tableName") . " WHERE acumatica_inventory_id ='" . $acumaticaId . "' and website_id = '" . $websiteId . "'  ");
                        if ($acumaticaRecordCount) {
                            $this->getConnection()->query("UPDATE " . $this->getTable("$tableName") . " set acumatica_lastsyncdate='" . $acumaticaModifiedDate . "' where acumatica_inventory_id='" . $acumaticaId . "' and website_id= '" . $websiteId . "' ");
                        } else {
                            $this->getConnection()->query("INSERT INTO `" . $this->getTable("$tableName") . "` (`id`, `acumatica_inventory_id`, `magento_sku`, `magento_id`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `website_id`, `entity_ref`, `flg`)
                VALUES (NULL,  '" . $acumaticaId . "', NULL, NULL, NULL, '" . $acumaticaModifiedDate . "', '" . $websiteId . "','" . $key . "', '0')");

                        }
                    }
                }
                if ($oneRecordFlag) {
                    $acumaticaId = $acumaticaData['Entity']['InventoryID']['Value'];
                    $acumaticaModifiedDate = $this->date->date('Y-m-d H:i:s', strtotime($acumaticaData['Entity']['LastModified']['Value']));
                    $acumaticaRecordCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable("$tableName") . "
                WHERE acumatica_inventory_id ='" . $acumaticaId . "' and website_id = '" . $websiteId . "'  ");
                    if ($acumaticaRecordCount) {
                        $this->getConnection()->query("UPDATE " . $this->getTable("$tableName") . "
                set acumatica_lastsyncdate='" . $acumaticaModifiedDate . "' where acumatica_inventory_id='" . $acumaticaId . "' and website_id= '" . $websiteId . "' ");
                    } else {
                        $this->getConnection()->query("INSERT INTO `" . $this->getTable("$tableName") . "` (`id`, `acumatica_inventory_id`, `magento_sku`, `magento_id`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `website_id`, `entity_ref`, `flg`)
                VALUES (NULL,  '" . $acumaticaId . "', NULL, NULL, NULL, '" . $acumaticaModifiedDate . "', '" . $websiteId . "',NULL, '0')");

                    }
                }
            }
        }

        /**
         * Get website id based on store id
         * Based on the last sync date get the data from Magento and insert/update into temporary table
         */
        if ($productSyncDirection == 2 || $productSyncDirection == 3)
        {
            $lastSyncDate = $this->date->gmtDate('Y-m-d H:i:s', $this->resourceModelSync->getLastSyncDate($syncId, $storeId));
            $magentoData = $this->getConnection()->fetchAll("SELECT entity_id,sku,updated_at FROM " . $this->getTable("catalog_product_entity") . " WHERE updated_at >='" . $lastSyncDate . "' and type_id = '" . $type . "'");
            foreach ($magentoData as $mData) {
                $updatedDate = $this->date->date('Y-m-d H:i:s', strtotime($mData['updated_at']));
                $magentoId = trim($mData['entity_id']);
                $magentoSku = strtoupper($mData['sku']);
                $recordCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable("$tableName") . "
                WHERE acumatica_inventory_id ='" . $magentoSku . "' and website_id = '" . $websiteId . "'  ");
                if ($recordCount) {
                    $this->getConnection()->query("UPDATE " . $this->getTable("$tableName") . "
                set magento_sku='" . $mData['sku'] . "', magento_id='" . $magentoId . "' ,magento_lastsyncdate='" . $updatedDate . "'
                where acumatica_inventory_id='" . $magentoSku . "' and website_id= '" . $websiteId . "' ");
                } else {
                    $this->getConnection()->query("INSERT INTO `" . $this->getTable("$tableName") . "` (`id`,`acumatica_inventory_id`, `magento_sku`, `magento_id`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `website_id`, `flg`)
                VALUES (NULL, NULL, '" . $magentoSku . "','" . $magentoId . "', '" . $updatedDate . "', NULL, '" . $websiteId . "', '0')");
                }
            }
        }
        try{
            $records = $this->getConnection()->fetchAll("SELECT * FROM `".$this->getTable("$tableName")."` WHERE website_id = '".$websiteId."'  ");
            $data = array();
            $results = $this->getConnection()->fetchAll("SELECT magento_attr_code,acumatica_attr_code,sync_direction FROM ".$this->getTable("amconnector_product_mapping")." WHERE store_id =".$storeId);
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
                        "website_id" => $record['website_id'],
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
                        "website_id" => $record['website_id'],
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
    /**
     * @param $syncData
     * @param $storeId
     * @param $realCategories
     * @return array|bool
     */
    public function createGroupedProduct($syncData,$storeId,$realCategories,$upsell,$csell,$childProducts)
    {
        $arrAttributes = $this->getAttributeData();
        if($storeId <= 1)
            $storeId = 0;
        $productData = array(
            "qty" => 0,
            "country_of_manufacture"=> "NULL",
            "image"=>"no_selection",
            "small_image"=>"no_selection",
            "thumbnail"=>"no_selection",
            "custom_design"=>"NULL",
            "page_layout"=>"NULL",
            "options_container"=>"container2",
            "custom_layout_update"=>"NULL",
            "custom_design_from"=>"NULL",
            "custom_design_to"=>"NULL",
            "quantity_and_stock_status"=>1
        );
        $productData = array_merge($syncData,$productData);
        $price = $productData['price'];
        $qty = $productData['qty'];
        $sku = $productData['sku'];
        $attribute_set = $productData['attribute_set'];
        $website = $productData['websites'];
        $taxClassId = $productData['tax_class_id'];

        /**
         * find the min and max price of the child products
         */
        $childProductsPrice = array();
        if(isset($childProducts) && !empty($childProducts))
        {
            foreach($childProducts as $childPro)
            {
                $childProductsPrice[] = $childPro['price'];
            }
            $minPrice = min($childProductsPrice);
            $maxPrice = max($childProductsPrice);
        }else{
            $minPrice = NULL;
            $maxPrice = NULL;
        }
        //check Child product is not created or not
        $dbObject = $this->getConnection();
        $childProduct = $dbObject->fetchAll("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$sku'");
        if(count($childProduct) == 0) {
            $entityId = $this->productSequence();
            $maxUpdateVersion = \Magento\Staging\Model\VersionManager::MAX_VERSION;
            $dbObject->query("INSERT INTO ".$this->getTable('catalog_product_entity')." (`entity_id`,`created_in`,`updated_in`,`attribute_set_id`, `type_id`, `sku`, `has_options`, `required_options`, `created_at`, `updated_at`) VALUES
                              ($entityId,'1','$maxUpdateVersion',$attribute_set, 'grouped', '$sku', '0', '0', NOW(), NOW())");
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
                          ($entityId, $customerGroupId, $website, $taxClassId, NULL, NULL, $minPrice, $maxPrice, NULL)");
                    }
                }

                $dbObject->query("INSERT INTO ".$this->getTable('cataloginventory_stock_item')." (`product_id`, `stock_id`, `qty`, `min_qty`, `use_config_min_qty`, `is_qty_decimal`, `backorders`, `use_config_backorders`, `min_sale_qty`, `use_config_min_sale_qty`, `max_sale_qty`, `use_config_max_sale_qty`, `is_in_stock`, `low_stock_date`, `notify_stock_qty`, `use_config_notify_stock_qty`, `manage_stock`, `use_config_manage_stock`, `stock_status_changed_auto`, `use_config_qty_increments`, `qty_increments`, `use_config_enable_qty_inc`, `enable_qty_increments`, `is_decimal_divided`,`website_id`,`deferred_stock_update`,`use_config_deferred_stock_update`) VALUES
                         ($entityId, 1, $qty, '0.0000', 1, 0, 0, 1, '0.0000', 1, '1.0000', 1, 1, NULL, NULL, 1, 0, 1, 0, 1, '0.0000', 1, 0, 0,$website,0,0)");
                $dbObject->query("INSERT INTO ".$this->getTable('cataloginventory_stock_status')." (`product_id`, `website_id`, `stock_id`, `qty`, `stock_status`) VALUES
                              ($entityId, $website, '1', $qty, '1')");

                if(isset($upsell) && !empty($upsell))
                {
                    $upsellPosition = 1;
                    foreach($upsell as $upsellId)
                    {
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
                if(isset($childProducts) && !empty($childProducts))
                {
                    foreach($childProducts as $_childProduct)
                    {
                        $childProductSku = $_childProduct['sku'];
                        $childProductPosition = $_childProduct['position'];
                        $childProductQty = $_childProduct['qty'];
                        $childEntityId = $dbObject->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$childProductSku'");
                        if(isset($childEntityId) && $childEntityId != '')
                        {
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($rowId, $childEntityId,3)");
                            $childLinkId = $this->getConnection()->lastInsertId();
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (4, $childLinkId,$childProductPosition)");
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_decimal') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (5, $childLinkId,$childProductQty)");
                        }
                    }
                }
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
     * @return array|bool
     */
    public function updateGroupedProduct($syncData,$storeId,$realCategories,$upsell,$csell,$childProducts)
    {
        $arrAttributes = $this->getAttributeData();
        if($storeId <= 1)
            $storeId = 0;
        $productData = $syncData;
        $price = NULL;
        $sku = $productData['sku'];
        $attribute_set = $productData['attribute_set'];
        $website = $productData['websites'];
        $taxClassId = $productData['tax_class_id'];

        /**
         * find the min and max price of the child products
         */
        $childProductsPrice = array();
        if(isset($childProducts) && !empty($childProducts))
        {
            foreach($childProducts as $childPro)
            {
                $childProductsPrice[] = $childPro['price'];
            }
            $minPrice = min($childProductsPrice);
            $maxPrice = max($childProductsPrice);
        }else{
            $minPrice = NULL;
            $maxPrice = NULL;
        }

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
                            $dbObject->query("UPDATE " . $this->getTable('catalog_product_index_price') . " set min_price=".$minPrice.",max_price=".$maxPrice." where entity_id='" . $entityId . "' and customer_group_id= '" . $customerGroupId . "' and website_id =".$website." and tax_class_id =".$taxClassId);
                        }else {
                            $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_index_price') . " (`entity_id`, `customer_group_id`, `website_id`, `tax_class_id`, `price`, `final_price`, `min_price`, `max_price`, `tier_price`) VALUES
                          ($entityId, $customerGroupId, $website, $taxClassId, $price, $minPrice, $maxPrice, $price, NULL)");
                        }
                    }
                }

                if(isset($upsell) && !empty($upsell) && count($upsell) > 0)
                {
                    $upsellPosition = 1;
                    foreach($upsell as $upsellId)
                    {
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
                if(isset($childProducts) && !empty($childProducts))
                {
                    foreach($childProducts as $_childProduct)
                    {
                        $childProductSku = $_childProduct['sku'];
                        $childProductPosition = $_childProduct['position'];
                        $childProductQty = $_childProduct['qty'];
                        $childEntityId = $dbObject->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$childProductSku'");
                        if(isset($childEntityId) && $childEntityId != '') {
                            $childCheck = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_link') . " WHERE product_id =" . $entityId . " AND linked_product_id =" . $childEntityId . " AND link_type_id = 3");
                            if ($childCheck == 0) {
                                $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($entityId, $childEntityId,3)");
                                $childLinkId = $this->getConnection()->lastInsertId();
                                $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (4, $childLinkId,$childProductPosition)");
                                $dbObject->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_decimal') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (5, $childLinkId,$childProductQty)");
                            }
                        }
                    }
                }
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
                    $this->getConnection()->query("UPDATE " . $tableName . " set value='" . addslashes($attributeValue) . "' where attribute_id='" . $attributeId . "' and store_id= '" . $storeId . "' and row_id =".$rowId);
                }else{
                    $this->getConnection()->query("INSERT INTO ".$tableName." (`attribute_id`, `store_id`, `row_id`, `value`) VALUES ($attributeId,$storeId,$rowId,'$attributeValue')");
                }
            }
        }
    }

    /**
     * @param $rowId
     */
    public function updateProductDate($rowId)
    {
        $updatedAt = $this->date->date('Y-m-d H:i:s',time());
        $this->getConnection()->query("UPDATE " . $this->getTable('catalog_product_entity') . " set updated_at='" .$updatedAt."' where row_id =".$rowId);

    }

    /**
     * @param $upsell
     * @param $csell
     * @param $entityId
     */
    public function linkedProducts($upsell,$csell,$rowId)
    {
        if(isset($upsell) && !empty($upsell) && count($upsell) > 0)
        {
            $upsellPosition = 1;
            foreach($upsell as $upsellId)
            {
                $upsellEntityId = '';
                $upsellEntityId = $this->getConnection()->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$upsellId'");
                if(isset($upsellEntityId) && $upsellEntityId != '')
                {
                    $upsellCheck = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_link') . " WHERE product_id =" . $rowId . " AND linked_product_id =" . $upsellEntityId . " AND link_type_id = 4");
                    if ($upsellCheck == 0) {
                        $this->getConnection()->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($rowId, $upsellEntityId,4)");
                        $upsellLinkId = $this->getConnection()->lastInsertId();
                        $this->getConnection()->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (2, $upsellLinkId,$upsellPosition)");
                    }
                    $upsellPosition++;
                }
            }
        }

        if(isset($csell) && !empty($csell) && count($csell) > 0)
        {
            $csellPosition = 1;
            foreach($csell as $csellId)
            {
                $csellEntityId = '';
                $csellEntityId = $this->getConnection()->fetchOne("SELECT entity_id FROM ".$this->getTable('catalog_product_entity')." WHERE `sku`='$csellId'");
                if(isset($csellEntityId) && $csellEntityId != '')
                {
                    $csellCheck = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('catalog_product_link') . " WHERE product_id =" . $rowId . " AND linked_product_id =" . $csellEntityId . " AND link_type_id = 5");
                    if ($csellCheck == 0) {
                        $this->getConnection()->query("INSERT INTO " . $this->getTable('catalog_product_link') . " (`product_id`, `linked_product_id`, `link_type_id`) VALUES
                              ($rowId, $csellEntityId,5)");
                        $csellLinkId = $this->getConnection()->lastInsertId();
                        $this->getConnection()->query("INSERT INTO " . $this->getTable('catalog_product_link_attribute_int') . " (`product_link_attribute_id`, `link_id`, `value`) VALUES
                              (3, $csellLinkId,$csellPosition)");
                    }
                    $csellPosition++;
                }
            }
        }

    }

    /**
     * returns the stop sync value from core_config_data table
     * @return int
     */
    public function stopSyncValue()
    {
        $value = '';
        $query = "SELECT value FROM " . $this->getTable("core_config_data") . " WHERE path ='amconnectorsync/configuratorsync/syncstopflg' ";
        try{
            $value = $this->getConnection()->fetchOne($query);
        }catch(Exception $e){
            echo $e->getMessage();
        }
        return $value;
    }

    /**
     *
     */
    public function enableSync()
    {
        $path = 'amconnectorsync/configuratorsync/syncstopflg';
        $query = "update ".$this->getTable('core_config_data')." set value = 1 where path ='".$path."'";
        $this->getConnection()->query($query);
    }

    /**
     * @param $attributeId
     * @param $rowId
     * @param $storeId
     */
    public function updateStatus($attributeId,$rowId,$storeId)
    {
        if($storeId <= 1)
        {
            $storeId = array(0,1);
        }
        if(is_array($storeId))
        {
            foreach($storeId as $store)
            {
                $getProductStatus = '';
                $getProductStatus = $this->getConnection()->fetchOne("SELECT value_id FROM " . $this->getTable('catalog_product_entity_int') . " WHERE `attribute_id`='$attributeId' and `row_id`='$rowId' and `store_id`='$store'");

                if($getProductStatus != '')
                {
                    $this->getConnection()->query("UPDATE " . $this->getTable('catalog_product_entity_int') . " set value=1 where value_id =".$getProductStatus);
                }
            }

        }else
        {
            $getProductStatus = $this->getConnection()->fetchOne("SELECT value_id FROM " . $this->getTable('catalog_product_entity_int') . " WHERE `attribute_id`='$attributeId' and `row_id`='$rowId' and `store_id`='$storeId'");

            if($getProductStatus != '')
            {
                $this->getConnection()->query("UPDATE " . $this->getTable('catalog_product_entity_int') . " set value=1 where value_id =".$getProductStatus);
            }
        }

    }
}