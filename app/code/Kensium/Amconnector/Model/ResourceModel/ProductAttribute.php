<?php
/**
 *
 * @category   Product Inventory Sync
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */


namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Webapi\Soap;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magento\Framework\Stdlib\DateTime\Timezone as TimeZone;
use Kensium\Lib;

/**
 * Class ProductAttribute
 * @package Kensium\Amconnector\Model\ResourceModel
 */
class ProductAttribute extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var
     */
    protected  $urlHelper;
    /**
     * @var
     */
    protected $clientHelper;

    /**
     * @var
     */
    protected $xmlHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    /**
     * @var
     */
    protected $timezone;

    /**
     * @var \Kensium\Amconnector\Helper\Time
     */
    protected $timeHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;


    /**
     * @var \Kensium\Synclog\Helper\Data
     */

    protected $dataHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Licensecheck
     */
    protected $licenseCheck;


    const IS_TIME_VALID = "Valid";


    /**
     * @var \Kensium\Synclog\Helper\ProductInventory
     */
    protected $prodAttHelper;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $syncResourceModel;
    /**
     *
     * @var \Kensium\Amconnector\Helper\Sync
     */
    protected $syncHelper;
    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;
    protected $common;

    const IS_LICENSE_INVALID = "Invalid";

    const IS_LICENSE_VALID = "Valid";

    /**
     * @var TimeZone
     */
    protected $timeZone;

    /**
     * @var
     */
    public $sucessMsg;

    /**
     * @var
     */
    public  $errorInMagento;


    /**
     * @param Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param TimeZone $timezone
     * @param \Kensium\Amconnector\Helper\Client $clientHelper
     * @param \Kensium\Amconnector\Helper\Data $dataHelper
     * @param \Kensium\Amconnector\Helper\Time $timeHelper
     * @param \Kensium\Amconnector\Helper\Xml $xmlHelper
     * @param \Kensium\Amconnector\Helper\Url $urlHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Kensium\Synclog\Helper\ProductAttribute $prodAttHelper
     * @param Sync $syncResourceModel
     * @param \Kensium\Amconnector\Helper\Sync $syncHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param Licensecheck $licenseCheck
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param TimeZone $timeZone
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Eav\Model\Entity\AttributeFactory $abstractAttribute
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $attributeCollection
     * @param \Magento\Eav\Model\Entity $entityModel
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $setCollection
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Eav\Model\Entity\Attribute\Set $setModel
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        TimeZone $timezone,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Kensium\Synclog\Helper\ProductAttribute $prodAttHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseCheck,
        \Magento\Framework\Stdlib\DateTime\Timezone  $timeZone,
        \Magento\Eav\Model\Entity\AttributeFactory $abstractAttribute,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $attributeCollection,
        \Magento\Eav\Model\Entity $entityModel,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $setCollection,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\Entity\Attribute\Set $setModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        Lib\Common $common,
        $connectionName = null

    )
    {
        parent::__construct($context, $connectionName);
        $this->date = $date;
        $this->timezone = $timezone;
        $this->urlHelper =  $urlHelper;
        $this->clientHelper = $clientHelper;
        $this->dataHelper = $dataHelper;
        $this->timeHelper = $timeHelper;
        $this->xmlHelper = $xmlHelper;
        $this->messageManager = $messageManager;
        $this->prodAttHelper = $prodAttHelper;
        $this->syncResourceModel = $syncResourceModel;
        $this->syncHelper = $syncHelper;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->productFactory = $productFactory;
        $this->licenseCheck = $licenseCheck;
        $this->resourceConnection = $context->getResources();
        $this->abstractAttribute = $abstractAttribute;
        $this->attributeCollection = $attributeCollection;
        $this->entityModel = $entityModel;
        $this->setCollection = $setCollection;
        $this->setModel = $setModel;
        $this->_objectManager = $objectManager;
        $this->common = $common;
    }

    public function _construct()
    {
        $this->errorInMagento = array();
        $this->sucessMsg = 0;
        $this->_init('amconnector_productattribute', 'id');
    }

    /**
     * @param $oldVals
     * @param $newVals
     * @return int
     */
    public function compareValues($oldVals, $newVals) {
        $nomatch = 0;
        if (count($oldVals) != count($newVals)) {
            $nomatch++;
        } else {
            foreach ($oldVals as $key => $value) {
                if ($newVals[$key] == $oldVals[$key]) {
                    $matched = 0;
                } else {
                    $nomatch++;
                }
            }
        }
        if ($nomatch > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Fetching attributes from CSV
     */

    public function getDetailsFromArray()
    {
        $data_entries = array(
            array(1,4,'SKU','sku',1,'text',1,'','','','','',''),
            array(2,4,'Description','name',1,'text',0,'','','','','',''),
            array(3,4,'Description','description',1,'textarea',0,'','','','','',''),
            array(4,4,'ProductWorkgroup','productworkgroup',0,'text',0,'','','','','',''),
            array(5,4,'ProductManager','productmanager',0,'text',0,'','','','','',''),
            array(6,4,'ItemStatus','itemstatus',0,'select',0,'Active','No Sales','No Purchases','No Request','In active','Marked for Deletion'),
            array(7,4,'ItemClass','itemclass',1,'text',0,'','','','','',''),
            array(8,4,'Type','itemtype',0,'select',0,'Finshed Good','Component Part','Subassembly','','',''),
            array(9,4,'IsAKit','isakit',0,'select',0,'true','false','','','',''),
            array(10,4,'PriceClass','priceclass',0,'text',0,'','','','','',''),
            array(11,4,'DefaultWarehouse','default_warehouse',0,'text',0,'','','','','',''),
            array(12,4,'BaseUnit','base_unit',0,'text',0,'','','','','',''),
            array(13,4,'SalesUnit','sales_unit',0,'text',0,'','','','','',''),
            array(14,4,'PurchaseUnit','purchase_unit',0,'text',0,'','','','','',''),
            array(15,4,'LastPrice','lastprice',0,'text',0,'','','','','',''),
            array(16,4,'CurrentPrice','price',1,'text',0,'','','','','',''),
            array(17,4,'msrp','msrp',1,'text',0,'','','','','',''),
            array(18,4,'CategoryID','category_ids',1,'text',0,'','','','','',''),
            array(19,4,'Weight','weight',1,'text',0,'','','','','',''),
            array(20,4,'WeightUOM','weight_uom',0,'text',0,'','','','','',''),
            array(21,4,'Volume','volume',0,'text',0,'','','','','',''),
            array(22,4,'VolumeUOM','vloume_uom',0,'text',0,'','','','','',''),
            array(23,4,'Active','status',1,'text',0,'','','','','',''),
            array(24,4,'AllowReviews','allow_reviews',0,'select',0,'true','false','','','',''),
            array(25,4,'QuoteItem','quote_item',0,'select',0,'true','false','','','',''),
            array(26,4,'ExtraShipFee','extra_ship_fee',0,'text',0,'','','','','',''),
            array(27,4,'PageName','url_key',1,'text',0,'','','','','',''),
            array(28,4,'AlternateSearchKeywords','search_keywords',0,'text',0,'','','','','',''),
            array(29,4,'MetaDescription','meta_description',1,'text',0,'','','','','',''),
            array(30,4,'MetaKeywords','meta_keyword',1,'text',0,'','','','','',''),
            array(31,4,'MetaTitle','meta_title',1,'text',0,'','','','','',''),
            array(32,4,'MinMarkup','min_mrkp',0,'text',0,'','','','','',''),
            array(33,4,'Markup','markp',0,'text',0,'','','','','',''),
            array(34,4,'Length','length',0,'text',0,'','','','','',''),
            array(35,4,'Width','width',0,'text',0,'','','','','',''),
            array(36,4,'Height','height',0,'text',0,'','','','','',''),
            array(37,4,'BoxID','boxid',0,'text',0,'','','','','',''),
            array(38,4,'UOM','uom',0,'text',0,'','','','','',''),
            array(39,4,'QtyOnHand','qty',1,'text',0,'','','','','',''),
            array(40,4,'Warehouse','warehouse',0,'text',0,'','','','','',''),
            array(41,4,'Default','warehse_deflt',0,'select',0,'true','false','','','',''),
            array(42,4,'VendorID','vendorid',0,'text',0,'','','','','',''),
            array(43,4,'VendorName','vendorname',0,'text',0,'','','','','',''),
            array(44,4,'Vendor Location','vendor_location',0,'text',0,'','','','','',''),
            array(44,4,'AcumaticaTaxCategory','taxcategory',0,'text',0,'','','','','',''),
            array(45,4,'Best Seller','best_seller',0,'select',0,'true','false','','','',''),
            array(46,4,'Home Page','home_page',0,'select',0,'true','false','','','',''),
            array(47,4,'Is NonStock','is_non_stock',0,'boolean',0,'','','','','',''),
        );

        return $this->fileData = $data_entries;
    }

    /**
     * @param $setId
     * @return string
     */
    public function attributeSetDefaultCreation($setId) {

        $sql = "SELECT `attribute_group_id` FROM " . $this->getTable('eav_attribute_group')." WHERE `attribute_group_name`='Acumatica' AND  `attribute_set_id`=" . $setId;
        try {
            $result = $this->resourceConnection->getConnection()->fetchOne($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        if (empty($result)) {

            $qry = " INSERT INTO " . $this->getTable('eav_attribute_group')." ( `attribute_set_id` , `attribute_group_name` , `sort_order` , `attribute_group_code` , `tab_group_code`) VALUES (".$setId.",'Acumatica',5,'acumatica',NULL)";
            try {
                $attributesetId = $this->resourceConnection->getConnection()->query($qry);
                $sql = "SELECT `attribute_group_id` FROM " . $this->getTable('eav_attribute_group')." WHERE `attribute_group_name`='Acumatica' ";
                try {
                    $result = $this->resourceConnection->getConnection()->fetchOne($sql);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                return $result;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            return $result;
        }
    }

    /**
     * Inserting attribute values in mapping table
     */

    public function syncMappingTable($syncLogID,$logViewFileName)
    {
        $i = 0;
        $sync = 0;
        $oldValues = '';
        $checkArray = array();
        $query = "SELECT * FROM " .$this->getTable('amconnector_product_attribute_mapping');
        $check = $this->resourceConnection->getConnection()->fetchAll($query);
        foreach($check as $key => $value){
            $checkArray[] = $value['acumatica_attribute_code'];
        }
        foreach ($this->fileData as $line) {
            try{
                if(!in_array($line[2],$checkArray)){
                    $query = "SELECT * FROM " .$this->getTable('amconnector_product_attribute_mapping')." WHERE `acumatica_attribute_code`='" . $line[2] . "'  AND `entity_type`=4 AND `is_common` =0 ";
                    $checkAttribute = $this->resourceConnection->getConnection()->fetchAll($query);

                    if(!empty($checkAttribute))
                        $oldValues = $checkAttribute[0]['field_values'];
                    $dropdownVal = array();
                    foreach($line as $key=>$value){
                        if($key>6){
                            if($line[$key]){
                                $dropdownVal[] = $line[$key];
                            }
                        }
                    }
                    if (!empty($line[7])) {
                        $newValues = $dropdownVal;
                        if (empty($line[7])) {
                            $fieldValues = json_encode($newValues);
                        } else {
                            $chkFlg = $this->compareValues(json_decode($oldValues), $newValues);
                            if ($chkFlg == 1)
                                $fieldValues = json_encode($newValues);
                        }
                    } else {
                        if (!empty($oldValues)) {
                            $fieldValues = 'T';
                        } else {
                            $fieldValues = '';
                        }
                    }
                    if (!empty($checkAttribute[0]['acumatica_attribute_code'])) {
                        if (!empty($fieldValues)) {
                            if ('T' == $fieldValues)
                                $fieldValues = '';
                            $flag = 1;
                            try {
                                $sql = $this->resourceConnection->getConnection()->query("update ".$this->getTable('amconnector_product_attribute_mapping')."  set flag=" . $flag . " ,field_values ='" . $fieldValues . "' WHERE acumatica_attribute_code='" . $line[2] . "' AND entity_type=4");
                                $sync++;
                            } catch (Exception $e) {
                                echo $e->getMessage();
                            }
                        }
                    } else {
                        $flag = 1;
                        if (!empty($line[2])) {
                            try {
                                $sql = $this->resourceConnection->getConnection()->query("insert into ".$this->getTable('amconnector_product_attribute_mapping')." (entity_type,acumatica_attribute_code,magento_attribute_code,is_common,field_type,is_required,field_values,flag)
                            values ($line[1],'" . $line[2] . "', '" . $line[3] . "',$line[4],'" . $line[5] . "',$line[6],'" . $fieldValues . "','" . $flag . "')");

                                $sync++;
                            } catch (Exception $e) {
                                echo $e->getMessage();
                            }
                        }
                    }
                    $i++;
                }

            }catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    /*
    * Importing Acumatica and Magento values from mapping table to magento
    */


    public function importIntoMagento($syncId , $syncLogID , $logViewFileName, $storeId)
    {
        $create = 0;
        $productModel = $this->productFactory->create();
        $data = $this->resourceConnection->getConnection()->fetchAll("SELECT  `id`,`entity_type`,`acumatica_attribute_code`,`magento_attribute_code`,`is_common`,`field_type`,`is_required`,`is_unique`,`field_values`,`magento_field_values`
FROM ".$this->getTable('amconnector_product_attribute_mapping')."  WHERE `flag`=1 AND `entity_type`=4 AND `is_common` =0 order by id asc");
        $i=0;

        foreach ($data as $row) {
            $check = $this->resourceConnection->getConnection()->fetchAll("SELECT * FROM " . $this->getTable('eav_attribute')." WHERE `attribute_code`='" . $row['magento_attribute_code'] . "'  AND `entity_type_id`=4");

            if ($row['field_type'] == 'boolean') {
                $label = "input";
                $type = "boolean";
                $fldLabel = "type";
                $fldType = "tinyint";
            } else {
                $label = "";
                $type = "";
                $fldLabel = "";
                $fldType = "";
            }

            if($row['field_type'] == 'select'){
                $isConfig = 1;
            }else{
                $isConfig = 0;
            }
            if($type == 'select' || $type == 'multiselect')
            {
                $scope = 'global';
                $isGlobal = 1;
            }else{
                $scope = 'store';
                $isGlobal = 0;
            }
            $attributeToCreate = array(
                "attribute_code" => $row['magento_attribute_code'],
                "scope" => $scope,
                "is_global" => $isGlobal,
                "frontend_input" => $row['field_type'],
                "is_unique" => $row['is_unique'],
                "is_required" => $row['is_required'],
                "is_configurable" => $isConfig,
                "is_searchable" => 0,
                "is_visible_in_advanced_search" => 0,
                $label => $type,
                $fldLabel => $fldType,
                "used_in_product_listing" => 0,
                "additional_fields" => array(
                    "is_filterable" => 0,
                    "is_filterable_in_search" => 0,
                    "position" => $i,
                    "used_for_sort_by" => 0
                ),
                "frontend_label" => $row['acumatica_attribute_code']
            );

            $attrCheck = $productModel->getResource()->getAttribute($row['magento_attribute_code']);
            if (!empty($attrCheck)) {

                if (!empty($row['field_values'])) {
                    $field_values = $row['field_values'];
                    $new_options = json_decode($field_values);
                    $j = 0;

                    foreach ($new_options as $newVal) {
                        $itemId = $attrCheck->getSource()->getOptionId($newVal);
                        if (empty($itemId)) {
                            $optionval = 'option_' . $j;
                            $attributeToCreate['option']['value'][$optionval] = array(trim($newVal), '', '');
                            /*if ($j == 0) {
                                $attributeToCreate['default'][0] = $optionval;
                            }*/
                            $j++;
                        }
                    }
                }

                try {
                    ob_start();

                    /**
                     * Update attribute
                     */
                    $model = $this->abstractAttribute->create()->loadByCode('4',$attributeToCreate['attribute_code']);
                    $model->addData($attributeToCreate);
                    $model->save();

                    $msg = "Attribute : " . $row['acumatica_attribute_code'] ." updated successfully!";
                    $txt = "Info : " . $msg;
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    $productAttributeArray['schedule_id'] = $syncLogID;
                    $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                    $productAttributeArray['acumatica_attribute_code'] = $row['acumatica_attribute_code'];
                    $productAttributeArray['description'] = $msg;
                    $productAttributeArray['runMode'] = "Manual";
                    $productAttributeArray['messageType'] = "Success";
                    $productAttributeArray['syncDirection'] = 'syncToMagento';
                    $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                    $this->sucessMsg++;


                    if (!empty($row['field_values'])) {
                        $attributeInfo = $this->attributeCollection
                            ->setCodeFilter($row['magento_attribute_code'])
                            ->getFirstItem();
                        $options = $attributeInfo->getSource()->getAllOptions(false);
                        $optionsData = array();
                        foreach ($options as $resData) {
                            $optionsData[] = $resData['value'] . ":" . $resData['label'];
                        }
                        $options_decode = json_encode($optionsData);
                        $magentoFieldValues = $options_decode;
                    } else {
                        $magentoFieldValues = "";
                    }
                    $this->resourceConnection->getConnection()->query("update " .$this->getTable('amconnector_product_attribute_mapping')."  set flag=0,magento_field_values='" . $magentoFieldValues . "'   WHERE magento_attribute_code='" . $row['magento_attribute_code'] . "'AND entity_type=4");
                    ob_end_flush();

                } catch (Exception $e) {
                    $msg = $e->getMessage();
                    $txt = "Error: " . $msg;
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    $productAttributeArray['schedule_id'] = $syncLogID;
                    $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                    $productAttributeArray['acumatica_attribute_code'] = $row['acumatica_attribute_code'];
                    $productAttributeArray['description'] = $msg;
                    $productAttributeArray['long_message'] = $msg;
                    $productAttributeArray['runMode'] = "Manual";
                    $productAttributeArray['messageType'] = "Failure";
                    $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                    $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                    $this->errorInMagento[] = 1;
                }
            } else {

                if (!empty($row['field_values'])) {
                    $field_values = $row['field_values'];
                    $new_options = json_decode($field_values);
                    $j = 0;

                    foreach ($new_options as $newVal) {
                        $optionval = 'option_' . $j;
                        $attributeToCreate['option']['value'][$optionval] = array(trim($newVal), '', '');
                        /*if ($j == 0) {
                            $attributeToCreate['default'][0] = $optionval;
                        }*/
                        $j++;
                    }
                }

                try {
                    ob_start();
                    $model = $this->abstractAttribute->create();

                    if (!isset($attributeToCreate['is_configurable'])) {
                        $attributeToCreate['is_configurable'] = 0;
                    }
                    if (!isset($attributeToCreate['is_filterable'])) {
                        $attributeToCreate['is_filterable'] = 0;
                    }
                    if (!isset($attributeToCreate['is_filterable_in_search'])) {
                        $attributeToCreate['is_filterable_in_search'] = 0;
                    }

                    if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                        $attributeToCreate['backend_type'] = $model->getBackendTypeByInput($attributeToCreate['frontend_input']);
                    }

                    $defaultValueField = $model->getDefaultValueByInput($attributeToCreate['frontend_input']);

                    $model->addData($attributeToCreate);
                    $model->setEntityTypeId($this->entityModel->setType('catalog_product')->getTypeId());
                    $model->setIsUserDefined(1);

                    try {
                        $model->save();
                        $attributeId = $model->getId();

                        $msg = "Attribute : " .$row['acumatica_attribute_code'] ." created successfully!";
                        $txt = "Info : " . $msg;
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                        $productAttributeArray['schedule_id'] = $syncLogID;
                        $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                        $productAttributeArray['acumatica_attribute_code'] = $row['acumatica_attribute_code'];
                        $productAttributeArray['description'] = $msg;
                        $productAttributeArray['runMode'] = "Manual";
                        $productAttributeArray['messageType'] = "Success";
                        $productAttributeArray['syncDirection'] = 'syncToMagento';
                        $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                        $this->sucessMsg++;

                    } catch (Exception $e) {
                        $msg = $e->getMessage();
                        $txt = "Error: " . $msg;
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                        $productAttributeArray['schedule_id'] = $syncLogID;
                        $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                        $productAttributeArray['acumatica_attribute_code'] = $row['acumatica_attribute_code'];
                        $productAttributeArray['description'] = $msg;
                        $productAttributeArray['long_message'] = $msg;
                        $productAttributeArray['runMode'] = "Manual";
                        $productAttributeArray['messageType'] = "Failure";
                        $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                        $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                        $this->errorInMagento[] = 1;
                    }
                    $attrcollection = $this->setCollection->setEntityTypeFilter(4);

                    try {
                        foreach($attrcollection as $setAttributeSet) {
                            $setId = $this->attributeSetDefaultCreation($setAttributeSet->getId());
                            $query = "SELECT count(*) FROM " . $this->getTable('eav_entity_attribute') . "  WHERE attribute_set_id = " . $setAttributeSet->getId() . " AND attribute_id = '" . $attributeId . "'";
                            $count = $this->resourceConnection->getConnection()->fetchOne($query);
                            $groupQuery = "SELECT count(*) FROM " . $this->getTable('eav_entity_attribute') . "  WHERE attribute_group_id = " .$setId. " AND attribute_id = '" . $attributeId . "'";
                            $grpCount = $this->resourceConnection->getConnection()->fetchOne($groupQuery);
                            if ($count == 0 && $grpCount == 0)
                            {
                                $qry = "INSERT INTO  " . $this->getTable('eav_entity_attribute') . " (`entity_type_id`,`attribute_set_id`,`attribute_group_id`,`attribute_id`) VALUES (4," . $setAttributeSet->getId() . "," . $setId . "," . $attributeId . ")";
                                $this->resourceConnection->getConnection()->query($qry);
                            }
                        }
                    } catch(Exception $e) {
                        echo $e->getMessage();
                    }

                    if(!empty($row['field_values'])) {

                        $attributeInfo = $this->attributeCollection
                            ->setCodeFilter($row['magento_attribute_code'])->getFirstItem();

                        $options = $attributeInfo->getSource()->getAllOptions(false);
                        $optionsData = array();
                        foreach ($options as $resData) {
                            $optionsData[] = $resData['value'] . ":" . $resData['label'];
                        }
                        $options_decode = json_encode($optionsData);
                        $magentoFieldValues = $options_decode;
                    } else {
                        $magentoFieldValues = "";
                    }
                    $sql = $this->resourceConnection->getConnection()->query("update " .$this->getTable('amconnector_product_attribute_mapping')."  set flag=0,magento_field_values='" . $magentoFieldValues . "'   WHERE magento_attribute_code='" . $row['magento_attribute_code'] . "'AND entity_type=4");
                    $this->sucessMsg++;
                    ob_end_flush();
                } catch (Exception $e) {
                    $msg = $e->getMessage();
                    $txt = "Error:" . $msg;
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    $productAttributeArray['schedule_id'] = $syncLogID;
                    $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                    $productAttributeArray['acumatica_attribute_code'] = $row['acumatica_attribute_code'];
                    $productAttributeArray['description'] = $msg;
                    $productAttributeArray['runMode'] = "Manual";
                    $productAttributeArray['messageType'] = "Failure";
                    $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                    $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                    $this->errorInMagento[] = 1;
                }
            }
            $i++;
        }
    }

    /**
     *  1. Get all attribute by date
     *  2. Create all attribute
     *  3. Get all Item class by date
     *  4. Create Item class
     *  5. Get attribute of item class
     *  6. Assign attribute to item class
     */

    public function syncToMagento($syncId , $syncLogID , $logViewFileName ,$storeId)
    {
        $txt = "Info : Fetching the Attributes";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);

        /* Get acumatica attribute Ids */
        $attributes  = $this->getAllAttribute($syncId,$storeId);
        $trialSyncRecordCount = 0 ;

        foreach($attributes as $attribute){
            if(!isset($attribute[0])){
                if($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord){
                    $row = $this->xmlHelper->xml2array($attribute);
                    $attrCode = strtolower($row['AttributeID']['Value']);
                    if ($row['ControlType']['Value'] == 'boolean') {
                        $label = "input";
                        $type = "boolean";
                        $fldLabel = "type";
                        $fldType = "tinyint";
                    } elseif($row['ControlType']['Value'] == 'Combo') {
                        $label = "";
                        $type = "select";
                        $fldLabel = "";
                        $fldType = "";
                    }elseif($row['ControlType']['Value'] == 'Multi Select Combo') {
                        $label = "";
                        $type = "multiselect";
                        $fldLabel = "";
                        $fldType = "";
                    }elseif($row['ControlType']['Value'] == 'Datetime') {
                        $label = "";
                        $type = "date";
                        $fldLabel = "";
                        $fldType = "";
                    }elseif($row['ControlType']['Value'] == 'Text') {
                        $label = "";
                        $type = "text";
                        $fldLabel = "";
                        $fldType = "";
                    }elseif($row['ControlType']['Value'] == 'Checkbox') {
                        $label = "";
                        $type = "boolean";
                        $fldLabel = "";
                        $fldType = "";
                    }else {
                        $label = "";
                        $type = "";
                        $fldLabel = "";
                        $fldType = "";
                    }

                    if(isset($row['Description']['Value'])){
                        $fieldLabel = trim($row['Description']['Value']);
                    }else{
                        $fieldLabel = trim($row['AttributeID']['Value']);
                    }
                    /**
                     * here we are inserting/updating custom attributes to show in mapping
                     */
                    $this->insertCustomAttributes($row['AttributeID']['Value'],$fieldLabel,$row['ControlType']['Value'],$storeId);
                    $productModel = $this->productFactory->create();
                    if($type == 'select'){
                        $isConfig = 1;
                    }else{
                        $isConfig = 0;
                    }
                    if($type == 'select' || $type == 'multiselect')
                    {
                        $scope = 'global';
                        $isGlobal = 1;
                    }else{
                        $scope = 'store';
                        $isGlobal = 0;
                    }
                    $attributeToCreate = array(
                        "attribute_code" => $attrCode ,
                        "scope" => $scope,
                        "is_global" => $isGlobal,
                        "frontend_input" => $type,
                        "is_unique" => 0,
                        "is_required" => 0,
                        "is_configurable" => $isConfig,
                        "is_searchable" => 0,
                        "is_visible_in_advanced_search" => 0,
                        $label => $type,
                        $fldLabel => $fldType,
                        "used_in_product_listing" => 0,
                        "additional_fields" => array(
                            "is_filterable" => 0,
                            "is_filterable_in_search" => 0,
                            "used_for_sort_by" => 0
                        ),
                        "frontend_label" => $fieldLabel
                    );
                    $attrCheck = $productModel->getResource()->getAttribute($attrCode);

                    if (!empty($attrCheck)) {
                        $new_options  = array();
                        if(isset($row['Values']['AttributeDefinitionValue'])) {
                            foreach ($row['Values']['AttributeDefinitionValue'] as $value)
                            {
                                if(isset($value->Description->Value) && $value->Description->Value != ''){
                                    $new_options[trim($value->ValueID->Value)] = trim($value->Description->Value);
                                }else{
				    if(isset($value->ValueID->Value)){
                                       $new_options[trim($value->ValueID->Value)] = trim($value->ValueID->Value);
				  }
				}
                            }
                        }

                        $j = 0;
                        foreach ($new_options as $newoptionkey => $newVal) {
                            $allOptions = $attrCheck->getSource()->getAllOptions(); //getOptionId($newVal);
                            $itemId = array();
                            if(isset($allOptions) && !empty($allOptions))
                            {
                                foreach($allOptions as $singleOptin)
                                {
                                    if($singleOptin['label'] == $newVal)
                                        $itemId[] = $singleOptin['value'];
                                }
                            }
                            if (empty($itemId)) {
                                $optionval = 'option_' . $j;
                                $attributeToCreate['option']['value'][$optionval] = array(trim($newoptionkey),trim($newVal), '');
                                /*if ($j == 0) {
                                    $attributeToCreate['default'][0] = $optionval;
                                }*/
                                $j++;
                            }
                        }
                        try {
                            ob_start();

                            /**
                             * Update Attribute
                             */
                            $model = $this->abstractAttribute->create()->loadByCode('4',$attributeToCreate['attribute_code']);
                            $model->addData($attributeToCreate);
                            $model->save();

                            $msg = "Attribute : " . $attrCode ." updated successfully!";
                            $txt = "Info : " . $msg;
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                            $productAttributeArray['schedule_id'] = $syncLogID;
                            $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                            $productAttributeArray['description'] = $msg;
                            $productAttributeArray['runMode'] = "Manual";
                            $productAttributeArray['messageType'] = "Success";
                            $productAttributeArray['syncDirection'] = 'syncToMagento';
                            $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                            $this->sucessMsg++;
                            ob_end_flush();

                        } catch(Exception $e){
                            $msg = $e->getMessage();
                            $txt = "Error: " . $msg;
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                            $productAttributeArray['schedule_id'] = $syncLogID;
                            $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                            $productAttributeArray['description'] = $msg;
                            $productAttributeArray['long_message'] = $msg;
                            $productAttributeArray['runMode'] = "Manual";
                            $productAttributeArray['messageType'] = "Failure";
                            $productAttributeArray['syncDirection'] = 'syncToMagento';
                            $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                            $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                            $this->errorInMagento[] = 1;
                        }
                    }
                    else{
                        if($row['ControlType']['Value'] != 'Checkbox'){
                            $new_options  = array();
                            if(!isset($row['Values']['AttributeDefinitionValue'][0])){
                                if(isset($row['Values']['AttributeDefinitionValue']['ValueID']['Value']))
                                    if(isset($row['Values']['AttributeDefinitionValue']['Description']['Value']) && $row['Values']['AttributeDefinitionValue']['Description']['Value'] != '')
                                        $new_options[trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value'])] = trim($row['Values']['AttributeDefinitionValue']['Description']['Value']);
                                    else
                                        $new_options[trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value'])] = trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value']);
                            }else{
                                foreach($row['Values']['AttributeDefinitionValue'] as $value){
                                    if(isset($value->Description->Value) && $value->Description->Value != ''){
                                        $new_options[trim($value->ValueID->Value)] = trim($value->Description->Value);
                                   }else{
					if(isset($value->ValueID->Value)){
                                          $new_options[trim($value->ValueID->Value)] = trim($value->ValueID->Value);
					}
				 }
                                }
                            }
                            $j = 0;
                            foreach ($new_options as $newoptionkey => $newVal) {
                                $optionval = 'option_' . $j;
                                $attributeToCreate['option']['value'][$optionval] = array(trim($newoptionkey), trim($newVal), '');
                                /*if ($j == 0) {
                                    $attributeToCreate['default'][0] = $optionval;
                                }*/
                                $j++;
                            }
                        }
                        try{
                            ob_start();
                            $model = $this->abstractAttribute->create();

                            if (!isset($attributeToCreate['is_configurable'])) {
                                $attributeToCreate['is_configurable'] = 0;
                            }
                            if (!isset($attributeToCreate['is_filterable'])) {
                                $attributeToCreate['is_filterable'] = 0;
                            }
                            if (!isset($attributeToCreate['is_filterable_in_search'])) {
                                $attributeToCreate['is_filterable_in_search'] = 0;
                            }

                            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                                $attributeToCreate['backend_type'] = $model->getBackendTypeByInput($attributeToCreate['frontend_input']);
                            }

                            if($attributeToCreate['frontend_input'] == 'multiselect'){
                                $attributeToCreate['backend_model'] = 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend';
                                $attributeToCreate['is_global'] = 1;
                            }

                            $model->addData($attributeToCreate);
                            $model->setEntityTypeId($this->entityModel->setType('catalog_product')->getTypeId());
                            $model->setIsUserDefined(1);

                            try{
                                $model->save();
                                $attributeId = $model->getId();
                                ob_end_flush();

                                $msg = "Attribute : " .$attrCode." created successfully!";
                                $txt = "Info : " . $msg;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $productAttributeArray['schedule_id'] = $syncLogID;
                                $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                $productAttributeArray['description'] = $msg;
                                $productAttributeArray['runMode'] = "Manual";
                                $productAttributeArray['messageType'] = "Success";
                                $productAttributeArray['syncDirection'] = 'syncToMagento';
                                $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                $this->sucessMsg++;

                            } catch(Exception $e){
                                $msg = $e->getMessage();
                                $txt = "Error: " . $msg;


                                $productAttributeArray['schedule_id'] = $syncLogID;
                                $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                $productAttributeArray['description'] = $msg;
                                $productAttributeArray['long_message'] = $msg;
                                $productAttributeArray['runMode'] = "Manual";
                                $productAttributeArray['messageType'] = "Failure";
                                $productAttributeArray['syncDirection'] = 'syncToMagento';
                                $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                                $this->errorInMagento[] = 1;
                            }
                        } catch(Exception $e){
                            $msg = $e->getMessage();
                            $txt = "Error: " . $msg;
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                            $productAttributeArray['schedule_id'] = $syncLogID;
                            $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                            $productAttributeArray['description'] = $msg;
                            $productAttributeArray['long_message'] = $msg;
                            $productAttributeArray['runMode'] = "Manual";
                            $productAttributeArray['messageType'] = "Failure";
                            $productAttributeArray['syncDirection'] = 'syncToMagento';
                            $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                            $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                            $this->errorInMagento[] = 1;
                        }
                    }
                    $trialSyncRecordCount++ ;
                }
                if($this->licenseType != 'trial'){
                    $row = $this->xmlHelper->xml2array($attribute);
                    if ($row['ControlType']['Value'] == 'boolean') {
                        $label = "input";
                        $type = "boolean";
                        $fldLabel = "type";
                        $fldType = "tinyint";
                    } elseif($row['ControlType']['Value'] == 'Combo') {
                        $label = "";
                        $type = "select";
                        $fldLabel = "";
                        $fldType = "";
                    }elseif($row['ControlType']['Value'] == 'Multi Select Combo') {
                        $label = "";
                        $type = "multiselect";
                        $fldLabel = "";
                        $fldType = "";
                    }elseif($row['ControlType']['Value'] == 'Datetime') {
                        $label = "";
                        $type = "date";
                        $fldLabel = "";
                        $fldType = "";
                    }elseif($row['ControlType']['Value'] == 'Text') {
                        $label = "";
                        $type = "text";
                        $fldLabel = "";
                        $fldType = "";
                    }elseif($row['ControlType']['Value'] == 'Checkbox') {
                        $label = "";
                        $type = "boolean";
                        $fldLabel = "";
                        $fldType = "";
                    }else {
                        $label = "";
                        $type = "";
                        $fldLabel = "";
                        $fldType = "";
                    }

                    if(isset($row['Description']['Value'])){
                        $fieldLabel = trim($row['Description']['Value']);
                    }else{
                        $fieldLabel = trim($row['AttributeID']['Value']);
                    }
                    /**
                     * here we are inserting/updating custom attributes to show in mapping
                     */
                    $this->insertCustomAttributes($row['AttributeID']['Value'],$fieldLabel,$row['ControlType']['Value'],$storeId);
                    $productModel = $this->productFactory->create();
                    if($type == 'select'){
                        $isConfig = 1;
                    }else{
                        $isConfig = 0;
                    }
                    if($type == 'select' || $type == 'multiselect')
                    {
                        $scope = 'global';
                        $isGlobal = 1;
                    }else{
                        $scope = 'store';
                        $isGlobal = 0;
                    }
                    $attributeToCreate = array(
                        "attribute_code" => $attrCode ,
                        "scope" => $scope,
                        "is_global" => $isGlobal,
                        "frontend_input" => $type,
                        "is_unique" => 0,
                        "is_required" => 0,
                        "is_configurable" => $isConfig,
                        "is_searchable" => 0,
                        "is_visible_in_advanced_search" => 0,
                        $label => $type,
                        $fldLabel => $fldType,
                        "used_in_product_listing" => 0,
                        "additional_fields" => array(
                            "is_filterable" => 0,
                            "is_filterable_in_search" => 0,
                            "used_for_sort_by" => 0
                        ),
                        "frontend_label" => $fieldLabel
                    );
                    $attrCheck = $productModel->getResource()->getAttribute($attrCode);

                    if (!empty($attrCheck)) {
                        $new_options  = array();
                        if(isset($row['Values']['AttributeDefinitionValue'])) {
                            foreach ($row['Values']['AttributeDefinitionValue'] as $value)
                            {
                                if(isset($value->Description->Value) && $value->Description->Value != '') {
                                    $new_options[trim($value->ValueID->Value)] = trim($value->Description->Value);
                                }else {
				    if(isset($value->ValueID->Value)){
                                       $new_options[trim($value->ValueID->Value)] = trim($value->ValueID->Value);
				    }
                                }
                            }
                        }

                        $j = 0;
                        foreach ($new_options as $newoptionkey => $newVal) {
                            $allOptions = $attrCheck->getSource()->getAllOptions();
                            $itemId = array();
                            if(isset($allOptions) && !empty($allOptions))
                            {
                                foreach($allOptions as $singleOptin)
                                {
                                    if($singleOptin['label'] == $newVal)
                                        $itemId[] = $singleOptin['value'];
                                }
                            }
                            if (empty($itemId)) {
                                $optionval = 'option_' . $j;
                                $attributeToCreate['option']['value'][$optionval] = array(trim($newoptionkey), trim($newVal), '');
                                /*if ($j == 0) {
                                    $attributeToCreate['default'][0] = $optionval;
                                }*/
                                $j++;
                            }
                        }
                        try {
                            ob_start();

                            /**
                             * Update Attribute
                             */

                            $model = $this->abstractAttribute->create()->loadByCode('4',$attributeToCreate['attribute_code']);
                            $model->addData($attributeToCreate);
                            $model->save();

                            $msg = "Attribute : " . $attrCode ." updated successfully!";
                            $txt = "Info : " . $msg;
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                            $productAttributeArray['schedule_id'] = $syncLogID;
                            $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                            $productAttributeArray['description'] = $msg;
                            $productAttributeArray['runMode'] = "Manual";
                            $productAttributeArray['messageType'] = "Success";
                            $productAttributeArray['syncDirection'] = 'syncToMagento';
                            $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                            $this->sucessMsg++;
                            ob_end_flush();

                        } catch(Exception $e){
                            $msg = $e->getMessage();
                            $txt = "Error: " . $msg;
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                            $productAttributeArray['schedule_id'] = $syncLogID;
                            $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                            $productAttributeArray['description'] = $msg;
                            $productAttributeArray['long_message'] = $msg;
                            $productAttributeArray['runMode'] = "Manual";
                            $productAttributeArray['messageType'] = "Failure";
                            $productAttributeArray['syncDirection'] = 'syncToMagento';
                            $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                            $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                            $this->errorInMagento[] = 1;
                        }
                    }
                    else{
                        if($row['ControlType']['Value'] != 'Checkbox'){
                            $new_options  = array();
                            if(isset($row['Values']['AttributeDefinitionValue'])) {
                                if (!$row['Values']['AttributeDefinitionValue'][0])
                                {
                                    if(isset($row['Values']['AttributeDefinitionValue']['Description']['Value']) && $row['Values']['AttributeDefinitionValue']['Description']['Value'] != '')
                                    {
                                        $new_options[trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value'])] = trim($row['Values']['AttributeDefinitionValue']['Description']['Value']);
                                    }else
                                    {
                                        $new_options[trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value'])] = trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value']);
                                    }
                                } else {
                                    foreach ($row['Values']['AttributeDefinitionValue'] as $value)
                                    {
                                        if(isset($value->Description->Value) && $value->Description->Value != '') {
                                            $new_options[trim($value->ValueID->Value)] = trim($value->Description->Value);
                                        }else {
					   if(isset($value->ValueID->Value)){
                                              $new_options[trim($value->ValueID->Value)] = trim($value->ValueID->Value);
					  }
                                        }
                                    }
                                }
                            }
                            $j = 0;
                            foreach ($new_options as $newoptionkey => $newVal) {
                                $optionval = 'option_' . $j;
                                $attributeToCreate['option']['value'][$optionval] = array(trim($newoptionkey), trim($newVal), '');
                                /*if ($j == 0) {
                                    $attributeToCreate['default'][0] = $optionval;
                                }*/
                                $j++;
                            }
                        }
                        try{
                            ob_start();
                            $model = $this->abstractAttribute->create();

                            if (!isset($attributeToCreate['is_configurable'])) {
                                $attributeToCreate['is_configurable'] = 0;
                            }
                            if (!isset($attributeToCreate['is_filterable'])) {
                                $attributeToCreate['is_filterable'] = 0;
                            }
                            if (!isset($attributeToCreate['is_filterable_in_search'])) {
                                $attributeToCreate['is_filterable_in_search'] = 0;
                            }

                            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                                $attributeToCreate['backend_type'] = $model->getBackendTypeByInput($attributeToCreate['frontend_input']);
                            }

                            if($attributeToCreate['frontend_input'] == 'multiselect'){
                                $attributeToCreate['backend_model'] = 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend';
                                $attributeToCreate['is_global'] = 1;
                            }

                            $model->addData($attributeToCreate);
                            $model->setEntityTypeId($this->entityModel->setType('catalog_product')->getTypeId());
                            $model->setIsUserDefined(1);

                            try{
                                $model->save();
                                $attributeId = $model->getId();
                                ob_end_flush();

                                $msg = "Attribute : " .$attrCode." created successfully!";
                                $txt = "Info : " . $msg;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $productAttributeArray['schedule_id'] = $syncLogID;
                                $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                $productAttributeArray['description'] = $msg;
                                $productAttributeArray['runMode'] = "Manual";
                                $productAttributeArray['messageType'] = "Success";
                                $productAttributeArray['syncDirection'] = 'syncToMagento';
                                $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                $this->sucessMsg++;

                            } catch(Exception $e){
                                $msg = $e->getMessage();
                                $txt = "Error: " . $msg;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $productAttributeArray['schedule_id'] = $syncLogID;
                                $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                $productAttributeArray['description'] = $msg;
                                $productAttributeArray['long_message'] = $msg;
                                $productAttributeArray['runMode'] = "Manual";
                                $productAttributeArray['messageType'] = "Failure";
                                $productAttributeArray['syncDirection'] = 'syncToMagento';
                                $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                                $this->errorInMagento[] = 1;
                            }
                        } catch(Exception $e){
                            $msg = $e->getMessage();
                            $txt = "Error: " . $msg;
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                            $productAttributeArray['schedule_id'] = $syncLogID;
                            $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                            $productAttributeArray['description'] = $msg;
                            $productAttributeArray['long_message'] = $msg;
                            $productAttributeArray['runMode'] = "Manual";
                            $productAttributeArray['messageType'] = "Failure";
                            $productAttributeArray['syncDirection'] = 'syncToMagento';
                            $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                            $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                            $this->errorInMagento[] = 1;
                        }
                    }
                }

                /* SINGLE ENTITY END */

            }else{
                foreach($attribute as $row) {
                    if($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord){
                        $row = $this->xmlHelper->xml2array($row);
                        $attrCode = strtolower($row['AttributeID']['Value']);
                        if ($row['ControlType']['Value'] == 'boolean') {
                            $label = "input";
                            $type = "boolean";
                            $fldLabel = "type";
                            $fldType = "tinyint";
                        } elseif($row['ControlType']['Value'] == 'Combo') {
                            $label = "";
                            $type = "select";
                            $fldLabel = "";
                            $fldType = "";
                        }elseif($row['ControlType']['Value'] == 'Multi Select Combo') {
                            $label = "";
                            $type = "multiselect";
                            $fldLabel = "";
                            $fldType = "";
                        }elseif($row['ControlType']['Value'] == 'Datetime') {
                            $label = "";
                            $type = "date";
                            $fldLabel = "";
                            $fldType = "";
                        }elseif($row['ControlType']['Value'] == 'Text') {
                            $label = "";
                            $type = "text";
                            $fldLabel = "";
                            $fldType = "";
                        }elseif($row['ControlType']['Value'] == 'Checkbox') {
                            $label = "";
                            $type = "boolean";
                            $fldLabel = "";
                            $fldType = "";
                        }else {
                            $label = "";
                            $type = "";
                            $fldLabel = "";
                            $fldType = "";
                        }

                        if(isset($row['Description']['Value'])){
                            $fieldLabel = trim($row['Description']['Value']);
                        }else{
                            $fieldLabel = trim($row['AttributeID']['Value']);
                        }
                        /**
                         * here we are inserting/updating custom attributes to show in mapping
                         */
                        $this->insertCustomAttributes($row['AttributeID']['Value'],$fieldLabel,$row['ControlType']['Value'],$storeId);
                        $productModel = $this->productFactory->create();
                        if($type == 'select'){
                            $isConfig = 1;
                        }else{
                            $isConfig = 0;
                        }
                        if($type == 'select' || $type == 'multiselect')
                        {
                            $scope = 'global';
                            $isGlobal = 1;
                        }else{
                            $scope = 'store';
                            $isGlobal = 0;
                        }
                        $attributeToCreate = array(
                            "attribute_code" => $attrCode ,
                            "scope" => $scope,
                            "is_global" => $isGlobal,
                            "frontend_input" => $type,
                            "is_unique" => 0,
                            "is_required" => 0,
                            "is_configurable" => $isConfig,
                            "is_searchable" => 0,
                            "is_visible_in_advanced_search" => 0,
                            $label => $type,
                            $fldLabel => $fldType,
                            "used_in_product_listing" => 0,
                            "additional_fields" => array(
                                "is_filterable" => 0,
                                "is_filterable_in_search" => 0,
                                "used_for_sort_by" => 0
                            ),
                            "frontend_label" => $fieldLabel
                        );
                        $attrCheck = $productModel->getResource()->getAttribute($attrCode);

                        if (!empty($attrCheck)) {
                            $new_options  = array();
                            if(isset($row['Values']['AttributeDefinitionValue'])) {
                                if (isset($row['Values']['AttributeDefinitionValue'])) {
                                    foreach ($row['Values']['AttributeDefinitionValue'] as $value) {
                                        if(isset($value->Description->Value) && $value->Description->Value != '')
                                        {
                                            $new_options[trim($value->ValueID->Value)] = trim($value->Description->Value);
                                        }else{
					    if(isset($value->ValueID->Value)){
                                              $new_options[trim($value->ValueID->Value)] = trim($value->ValueID->Value);
					 }
                                        }
                                    }
                                }
                            }
                            $j = 0;
                            foreach ($new_options as $newoptionkey => $newVal) {
                                $allOptions = $attrCheck->getSource()->getAllOptions();
                                $itemId = array();
                                if(isset($allOptions) && !empty($allOptions))
                                {
                                    foreach($allOptions as $singleOptin)
                                    {
                                        if($singleOptin['label'] == $newVal)
                                            $itemId[] = $singleOptin['value'];
                                    }
                                }
                                if (empty($itemId)) {
                                    $optionval = 'option_' . $j;
                                    $attributeToCreate['option']['value'][$optionval] = array(trim($newoptionkey), trim($newVal), '');
                                    /*if ($j == 0) {
                                        $attributeToCreate['default'][0] = $optionval;
                                    }*/
                                    $j++;
                                }
                            }

                            try {
                                ob_start();

                                /**
                                 * Update Attribute
                                 */

                                $model = $this->abstractAttribute->create()->loadByCode('4',$attributeToCreate['attribute_code']);
                                $model->addData($attributeToCreate);
                                $model->save();

                                $msg = "Attribute : " . $attrCode ." updated successfully!";
                                $txt = "Info : " . $msg;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $productAttributeArray['schedule_id'] = $syncLogID;
                                $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                $productAttributeArray['description'] = $msg;
                                $productAttributeArray['runMode'] = "Manual";
                                $productAttributeArray['messageType'] = "Success";
                                $productAttributeArray['syncDirection'] = 'syncToMagento';
                                $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                $this->sucessMsg++;
                                ob_end_flush();

                            } catch(Exception $e){
                                $msg = $e->getMessage();
                                $txt = "Error: " . $msg;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $productAttributeArray['schedule_id'] = $syncLogID;
                                $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                $productAttributeArray['description'] = $msg;
                                $productAttributeArray['long_message'] = $msg;
                                $productAttributeArray['runMode'] = "Manual";
                                $productAttributeArray['messageType'] = "Failure";
                                $productAttributeArray['syncDirection'] = 'syncToMagento';
                                $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                                $this->errorInMagento[] = 1;
                            }
                        }
                        else{
                            if($row['ControlType']['Value'] != 'Checkbox'){
                                $new_options  = array();
                                if(isset($row['Values']['AttributeDefinitionValue'])) {
                                    if (!isset($row['Values']['AttributeDefinitionValue'][0])) {
                                        if(isset($row['Values']['AttributeDefinitionValue']['Description']['Value']) && $row['Values']['AttributeDefinitionValue']['Description']['Value'] != '')
                                        {
                                            $new_options[trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value'])] = trim($row['Values']['AttributeDefinitionValue']['Description']['Value']);
                                        }else{
                                            $new_options[trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value'])] = trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value']);
                                        }
                                    } else {
                                        foreach ($row['Values']['AttributeDefinitionValue'] as $value) {
                                            if(isset($value->Description->Value)  && $value->Description->Value != '')
                                            {
                                                $new_options[trim($value->ValueID->Value)] = trim($value->Description->Value);
                                            }else{
						if(isset($value->ValueID->Value)){
                                                  $new_options[trim($value->ValueID->Value)] = trim($value->ValueID->Value);
						}
                                            }
                                        }
                                    }
                                }
                                $j = 0;
                                foreach ($new_options as $newoptionkey => $newVal) {
                                    $optionval = 'option_' . $j;
                                    $attributeToCreate['option']['value'][$optionval] = array(trim($newoptionkey), trim($newVal), '');
                                    /*if ($j == 0) {
                                        $attributeToCreate['default'][0] = $optionval;
                                    }*/
                                    $j++;
                                }
                            }
                            try{
                                ob_start();
                                $model = $this->abstractAttribute->create();

                                if (!isset($attributeToCreate['is_configurable'])) {
                                    $attributeToCreate['is_configurable'] = 0;
                                }
                                if (!isset($attributeToCreate['is_filterable'])) {
                                    $attributeToCreate['is_filterable'] = 0;
                                }
                                if (!isset($attributeToCreate['is_filterable_in_search'])) {
                                    $attributeToCreate['is_filterable_in_search'] = 0;
                                }

                                if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                                    $attributeToCreate['backend_type'] = $model->getBackendTypeByInput($attributeToCreate['frontend_input']);
                                }
                                if($attributeToCreate['frontend_input'] == 'multiselect'){
                                    $attributeToCreate['backend_model'] = 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend';
                                    $attributeToCreate['is_global'] = 1;
                                }

                                $model->addData($attributeToCreate);
                                $model->setEntityTypeId($this->entityModel->setType('catalog_product')->getTypeId());
                                $model->setIsUserDefined(1);

                                try{
                                    $model->save();
                                    $attributeId = $model->getId();
                                    ob_end_flush();

                                    $msg = "Attribute : " .$attrCode." created successfully!";
                                    $txt = "Info : " . $msg;
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                    $productAttributeArray['schedule_id'] = $syncLogID;
                                    $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                    $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                    $productAttributeArray['description'] = $msg;
                                    $productAttributeArray['runMode'] = "Manual";
                                    $productAttributeArray['messageType'] = "Success";
                                    $productAttributeArray['syncDirection'] = 'syncToMagento';
                                    $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                    $this->sucessMsg++;

                                } catch(Exception $e){
                                    $msg = $e->getMessage();
                                    $txt = "Error: " . $msg;
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                    $productAttributeArray['schedule_id'] = $syncLogID;
                                    $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                    $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                    $productAttributeArray['description'] = $msg;
                                    $productAttributeArray['long_message'] = $msg;
                                    $productAttributeArray['runMode'] = "Manual";
                                    $productAttributeArray['messageType'] = "Failure";
                                    $productAttributeArray['syncDirection'] = 'syncToMagento';
                                    $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                    $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                                    $this->errorInMagento[] = 1;
                                }
                            } catch(Exception $e){
                                $msg = $e->getMessage();
                                $txt = "Error: " . $msg;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $productAttributeArray['schedule_id'] = $syncLogID;
                                $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                $productAttributeArray['description'] = $msg;
                                $productAttributeArray['long_message'] = $msg;
                                $productAttributeArray['runMode'] = "Manual";
                                $productAttributeArray['messageType'] = "Failure";
                                $productAttributeArray['syncDirection'] = 'syncToMagento';
                                $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                                $this->errorInMagento[] = 1;
                            }
                        }
                        $trialSyncRecordCount++ ;
                    }
                    if($this->licenseType != 'trial'){
                        $row = $this->xmlHelper->xml2array($row);

                        if ($row['ControlType']['Value'] == 'boolean') {
                            $label = "input";
                            $type = "boolean";
                            $fldLabel = "type";
                            $fldType = "tinyint";
                        } elseif($row['ControlType']['Value'] == 'Combo') {
                            $label = "";
                            $type = "select";
                            $fldLabel = "";
                            $fldType = "";
                        }elseif($row['ControlType']['Value'] == 'Multi Select Combo') {
                            $label = "";
                            $type = "multiselect";
                            $fldLabel = "";
                            $fldType = "";
                        }elseif($row['ControlType']['Value'] == 'Datetime') {
                            $label = "";
                            $type = "date";
                            $fldLabel = "";
                            $fldType = "";
                        }elseif($row['ControlType']['Value'] == 'Text') {
                            $label = "";
                            $type = "text";
                            $fldLabel = "";
                            $fldType = "";
                        }elseif($row['ControlType']['Value'] == 'Checkbox') {
                            $label = "";
                            $type = "boolean";
                            $fldLabel = "";
                            $fldType = "";
                        }else {
                            $label = "";
                            $type = "";
                            $fldLabel = "";
                            $fldType = "";
                        }

                        if(isset($row['Description']['Value'])){
                            $fieldLabel = trim($row['Description']['Value']);
                        }else{
                            $fieldLabel = trim($row['AttributeID']['Value']);
                        }
                        /**
                         * here we are inserting/updating custom attributes to show in mapping
                         */
                        $this->insertCustomAttributes($row['AttributeID']['Value'],$fieldLabel,$row['ControlType']['Value'],$storeId);
                        $productModel = $this->productFactory->create();
                        if($type == 'select'){
                            $isConfig = 1;
                        }else{
                            $isConfig = 0;
                        }
                        if($type == 'select' || $type == 'multiselect')
                        {
                            $scope = 'global';
                            $isGlobal = 1;
                        }else{
                            $scope = 'store';
                            $isGlobal = 0;
                        }
                        $attributeToCreate = array(
                            "attribute_code" => $attrCode ,
                            "scope" => $scope,
                            "is_global" => $isGlobal,
                            "frontend_input" => $type,
                            "is_unique" => 0,
                            "is_required" => 0,
                            "is_configurable" => $isConfig,
                            "is_searchable" => 0,
                            "is_visible_in_advanced_search" => 0,
                            $label => $type,
                            $fldLabel => $fldType,
                            "used_in_product_listing" => 0,
                            "additional_fields" => array(
                                "is_filterable" => 0,
                                "is_filterable_in_search" => 0,
                                "used_for_sort_by" => 0
                            ),
                            "frontend_label" => $fieldLabel
                        );

                        $attrCheck = $productModel->getResource()->getAttribute($attrCode);

                        if (!empty($attrCheck)) {
                            $new_options  = array();
                            if(isset($row['Values']['AttributeDefinitionValue'])) {
                                if (isset($row['Values']['AttributeDefinitionValue'])) {
                                    foreach ($row['Values']['AttributeDefinitionValue'] as $value) {
                                        if(isset($value->Description->Value) && $value->Description->Value != '')
                                        {
                                            $new_options[trim($value->ValueID->Value)] = trim($value->Description->Value);
                                        }else{
					    if(isset($value->ValueID->Value)){
                                              $new_options[trim($value->ValueID->Value)] = trim($value->ValueID->Value);
					 }	
                                        }
                                    }
                                }
                            }
                            $j = 0;
                            foreach ($new_options as $newoptionkey => $newVal) {
                                $allOptions = $attrCheck->getSource()->getAllOptions();
                                $itemId = array();
                                if(isset($allOptions) && !empty($allOptions))
                                {
                                    foreach($allOptions as $singleOptin)
                                    {
                                        if($singleOptin['label'] == $newVal)
                                            $itemId[] = $singleOptin['value'];
                                    }
                                }
                                if (empty($itemId)) {
                                    $optionval = 'option_' . $j;
                                    $attributeToCreate['option']['value'][$optionval] = array(trim($newoptionkey),trim($newVal), '');
                                    /*if ($j == 0) {
                                        $attributeToCreate['default'][0] = $optionval;
                                    }*/
                                    $j++;
                                }
                            }

                            try {
                                ob_start();

                                /**
                                 * Update Attribute
                                 */

                                $model = $this->abstractAttribute->create()->loadByCode('4',$attributeToCreate['attribute_code']);
                                $model->addData($attributeToCreate);
                                $model->save();

                                $msg = "Attribute : " . $attrCode ." updated successfully!";
                                $txt = "Info : " . $msg;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $productAttributeArray['schedule_id'] = $syncLogID;
                                $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                $productAttributeArray['description'] = $msg;
                                $productAttributeArray['runMode'] = "Manual";
                                $productAttributeArray['messageType'] = "Success";
                                $productAttributeArray['syncDirection'] = 'syncToMagento';
                                $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                $this->sucessMsg++;
                                ob_end_flush();

                            } catch(Exception $e){
                                $msg = $e->getMessage();
                                $txt = "Error: " . $msg;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $productAttributeArray['schedule_id'] = $syncLogID;
                                $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                $productAttributeArray['description'] = $msg;
                                $productAttributeArray['long_message'] = $msg;
                                $productAttributeArray['runMode'] = "Manual";
                                $productAttributeArray['messageType'] = "Failure";
                                $productAttributeArray['syncDirection'] = 'syncToMagento';
                                $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                                $this->errorInMagento[] = 1;
                            }
                        }
                        else{
                            if($row['ControlType']['Value'] != 'Checkbox'){
                                $new_options  = array();
                                if(isset($row['Values']['AttributeDefinitionValue'])) {
                                    if (!$row['Values']['AttributeDefinitionValue'][0]) {
                                        if(isset($row['Values']['AttributeDefinitionValue']['Description']['Value']) && $row['Values']['AttributeDefinitionValue']['Description']['Value'] != '') {
                                            $new_options[trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value'])] = trim($row['Values']['AttributeDefinitionValue']['Description']['Value']);
                                        }else {
                                            $new_options[trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value'])] = trim($row['Values']['AttributeDefinitionValue']['ValueID']['Value']);
                                        }
                                    } else {
                                        foreach ($row['Values']['AttributeDefinitionValue'] as $value) {
                                            if(isset($value->Description->Value) && $value->Description->Value != '') {
                                                $new_options[trim($value->ValueID->Value)] = trim($value->Description->Value);
                                            }else {
						if(isset($value->ValueID->Value)){
                                                   $new_options[trim($value->ValueID->Value)] = trim($value->ValueID->Value);
					      }
                                            }
                                        }
                                    }
                                }
                                $j = 0;
                                foreach ($new_options as $newoptionkey => $newVal) {
                                    $optionval = 'option_' . $j;
                                    $attributeToCreate['option']['value'][$optionval] = array(trim($newoptionkey),trim($newVal),'');
                                    /*if ($j == 0) {
                                        $attributeToCreate['default'][0] = $optionval;
                                    }*/
                                    $j++;
                                }
                            }
                            try{
                                ob_start();
                                $model = $this->abstractAttribute->create();

                                if (!isset($attributeToCreate['is_configurable'])) {
                                    $attributeToCreate['is_configurable'] = 0;
                                }
                                if (!isset($attributeToCreate['is_filterable'])) {
                                    $attributeToCreate['is_filterable'] = 0;
                                }
                                if (!isset($attributeToCreate['is_filterable_in_search'])) {
                                    $attributeToCreate['is_filterable_in_search'] = 0;
                                }

                                if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                                    $attributeToCreate['backend_type'] = $model->getBackendTypeByInput($attributeToCreate['frontend_input']);
                                }
                                if($attributeToCreate['frontend_input'] == 'multiselect'){
                                    $attributeToCreate['backend_model'] = 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend';
                                    $attributeToCreate['is_global'] = 1;
                                }

                                $model->addData($attributeToCreate);
                                $model->setEntityTypeId($this->entityModel->setType('catalog_product')->getTypeId());
                                $model->setIsUserDefined(1);

                                try{
                                    $model->save();
                                    $attributeId = $model->getId();
                                    ob_end_flush();

                                    $msg = "Attribute : " .$attrCode." created successfully!";
                                    $txt = "Info : " . $msg;
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                    $productAttributeArray['schedule_id'] = $syncLogID;
                                    $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                    $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                    $productAttributeArray['description'] = $msg;
                                    $productAttributeArray['runMode'] = "Manual";
                                    $productAttributeArray['messageType'] = "Success";
                                    $productAttributeArray['syncDirection'] = 'syncToMagento';
                                    $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                    $this->sucessMsg++;

                                } catch(Exception $e){
                                    $msg = $e->getMessage();
                                    $txt = "Error: " . $msg;
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                    $productAttributeArray['schedule_id'] = $syncLogID;
                                    $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                    $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                    $productAttributeArray['description'] = $msg;
                                    $productAttributeArray['long_message'] = $msg;
                                    $productAttributeArray['runMode'] = "Manual";
                                    $productAttributeArray['messageType'] = "Failure";
                                    $productAttributeArray['syncDirection'] = 'syncToMagento';
                                    $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                    $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                                    $this->errorInMagento[] = 1;
                                }
                            } catch(Exception $e){
                                $msg = $e->getMessage();
                                $txt = "Error: " . $msg;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $productAttributeArray['schedule_id'] = $syncLogID;
                                $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                                $productAttributeArray['description'] = $msg;
                                $productAttributeArray['long_message'] = $msg;
                                $productAttributeArray['runMode'] = "Manual";
                                $productAttributeArray['messageType'] = "Failure";
                                $productAttributeArray['syncDirection'] = 'syncToMagento';
                                $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                                $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                                $this->errorInMagento[] = 1;
                            }
                        }
                    }
                }
            }
        }

        if($trialSyncRecordCount < $this->totalTrialRecord){
            $txt = "Info : Fetching Attribute sets";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
        }
        if($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord){
            $attributeSets = $this->getAllAttributeSet($syncId,$storeId);
        }
        if($this->licenseType != 'trial'){
            $attributeSets = $this->getAllAttributeSet($syncId,$storeId);
        }

        foreach($attributeSets as $attributeSet){
            if($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord){
                if(!empty($attributeSet)){
                    $attributeSetName = strtolower($attributeSet);

                    $entityTypeId = $this->productFactory->create()
                        ->getResource()
                        ->getEntityType()
                        ->getId();

                    /**
                     * Check attribute set already exist or not
                     */

                    $sqlQuery = "SELECT `main_table`.* FROM " . $this->getTable('eav_attribute_set')." AS `main_table` WHERE entity_type_id = ".$entityTypeId;
                    $attributeSetChecks = $this->resourceConnection->getConnection()->fetchAll($sqlQuery);
                    $check =0;
                    foreach ($attributeSetChecks as $attributeSetCheck) {
                        if(($attributeSetName == strtolower($attributeSetCheck['attribute_set_name'])) ||($attributeSetName == 'DEFAULT') ){
                            $check++;
                        }
                    }

                    if($check == 0){
                        ob_start();
                        /* Create Attribute Set*/


                        $attributeSetModel = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
                            ->setEntityTypeId($entityTypeId)
                            ->setAttributeSetName($attributeSetName);
                        try {
                            $attributeSetModel->validate();
                            $attributeSetModel->save();
                            $attributeSetModel->initFromSkeleton(4)->save();
                            ob_end_flush();

                            $msg = "Attribute set : " . $attributeSetName ." created successfully!";
                            $txt = "Info : " . $msg;
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                            $productAttributeArray['schedule_id'] = $syncLogID;
                            $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productAttributeArray['acumatica_attribute_code'] = $attributeSetName;
                            $productAttributeArray['description'] = $msg;
                            $productAttributeArray['runMode'] = "Manual";
                            $productAttributeArray['messageType'] = "Success";
                            $productAttributeArray['syncDirection'] = 'syncToMagento';
                            $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                            $this->sucessMsg++;

                        } catch(Exception $e){
                            $msg = $e->getMessage();
                            $txt = "Error: " . $msg;
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                            $productAttributeArray['schedule_id'] = $syncLogID;
                            $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                            $productAttributeArray['description'] = $msg;
                            $productAttributeArray['long_message'] = $msg;
                            $productAttributeArray['runMode'] = "Manual";
                            $productAttributeArray['messageType'] = "Failure";
                            $productAttributeArray['syncDirection'] = 'syncToMagento';
                            $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                            $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                            $this->errorInMagento[] = 1;
                        }

                    }

                    /* Get all attributes of item class */

                    $itemClassAttributes = $this->getItemClassAttributes($syncId,$attributeSetName,$storeId);

                    /* Assign attribute to attribute set in Magento */
                    foreach($itemClassAttributes  as $item){

                        $attributeSetIdQuery = "SELECT attribute_set_id FROM " . $this->getTable('eav_attribute_set')."  WHERE entity_type_id = ".$entityTypeId." AND  attribute_set_name = '".$attributeSetName."'";
                        $attributeSetId = $this->resourceConnection->getConnection()->fetchOne($attributeSetIdQuery);
                        $item = $item;
                        $attributeId = $this->abstractAttribute->create()->getIdByCode('catalog_product', $item);
                        try {
                            $setId = $this->attributeSetCreation($attributeSetId);
                            if($attributeId){
                                $query = "SELECT count(*) FROM " . $this->getTable('eav_entity_attribute') . "  WHERE attribute_set_id = " . $attributeSetId . " AND  attribute_id = '" . $attributeId . "'";
                                $count = $this->resourceConnection->getConnection()->fetchOne($query);
                                if ($count == 0) {
                                    $qry = "INSERT INTO " . $this->getTable('eav_entity_attribute') . " (`entity_type_id`,`attribute_set_id`,`attribute_group_id`,`attribute_id`) VALUES (" . $entityTypeId . "," . $attributeSetId . "," . $setId . "," . $attributeId . ")";
                                    $this->resourceConnection->getConnection()->query($qry);

                                    $txt = "Info : Assigned attribute '" . $item . "' to  " . $attributeSetName;
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                    $this->sucessMsg;
                                }
                            }
                        } catch(Exception $e){
                            echo $e->getMessage();
                        }
                    }
                }
                $trialSyncRecordCount++ ;
            }
            if($this->licenseType != 'trial'){
                if(!empty($attributeSet)){
                    $attributeSetName = $attributeSet ;

                    $entityTypeId = $this->productFactory->create()
                        ->getResource()
                        ->getEntityType()
                        ->getId();

                    /**
                     * Check attribute set already exist or not
                     */

                    $sqlQuery = "SELECT `main_table`.* FROM " . $this->getTable('eav_attribute_set')." AS `main_table` WHERE entity_type_id = ".$entityTypeId;
                    $attributeSetChecks = $this->resourceConnection->getConnection()->fetchAll($sqlQuery);

                    $check =0;
                    foreach ($attributeSetChecks as $attributeSetCheck) {
                        if(($attributeSetName == $attributeSetCheck['attribute_set_name']) ||($attributeSetName == 'DEFAULT') ){
                            $check++;
                        }
                    }

                    if($check == 0){
                        ob_start();
                        /* Create Attribute Set*/

                        $attributeSetModel = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
                            ->setEntityTypeId($entityTypeId)
                            ->setAttributeSetName($attributeSetName);
                        try {
                            $attributeSetModel->validate();
                            $attributeSetModel->save();
                            $attributeSetModel->initFromSkeleton(4)->save();
                            ob_end_flush();

                            $msg = "Attribute set : " . $attributeSetName ." created successfully!";
                            $txt = "Info : " . $msg;
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                            $productAttributeArray['schedule_id'] = $syncLogID;
                            $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productAttributeArray['acumatica_attribute_code'] = $attributeSetName;
                            $productAttributeArray['description'] = $msg;
                            $productAttributeArray['runMode'] = "Manual";
                            $productAttributeArray['messageType'] = "Success";
                            $productAttributeArray['syncDirection'] = 'syncToMagento';
                            $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                            $this->sucessMsg++;

                        } catch(Exception $e){
                            $msg = $e->getMessage();
                            $txt = "Error: " . $msg;
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                            $productAttributeArray['schedule_id'] = $syncLogID;
                            $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productAttributeArray['acumatica_attribute_code'] = $attrCode;
                            $productAttributeArray['description'] = $msg;
                            $productAttributeArray['long_message'] = $msg;
                            $productAttributeArray['runMode'] = "Manual";
                            $productAttributeArray['messageType'] = "Failure";
                            $productAttributeArray['syncDirection'] = 'syncToMagento';
                            $this->prodAttHelper->productAttributeSyncSuccessLogs($productAttributeArray);
                            $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                            $this->errorInMagento[] = 1;
                        }

                    }

                    /* Get all attributes of item class */

                    $itemClassAttributes = $this->getItemClassAttributes($syncId,$attributeSetName,$storeId);

                    /* Assign attribute to attribute set in Magento */

                    foreach($itemClassAttributes  as $item){

                        $attributeSetIdQuery = "SELECT attribute_set_id FROM " . $this->getTable('eav_attribute_set')."  WHERE entity_type_id = ".$entityTypeId." AND  attribute_set_name = '".$attributeSetName."'";
                        $attributeSetId = $this->resourceConnection->getConnection()->fetchOne($attributeSetIdQuery);
                        $item = $item;
                        $attributeId = $this->abstractAttribute->create()->getIdByCode('catalog_product', $item);

                        try {
                            $setId = $this->attributeSetCreation($attributeSetId);
                            $query = "SELECT count(*) FROM " . $this->getTable('eav_entity_attribute')."  WHERE attribute_set_id = ".$attributeSetId." AND  attribute_id = '".$attributeId."'";
                            $count = $this->resourceConnection->getConnection()->fetchOne($query);
                            if($count == 0) {
                                $qry = "INSERT INTO " . $this->getTable('eav_entity_attribute') . " (`entity_type_id`,`attribute_set_id`,`attribute_group_id`,`attribute_id`) VALUES (" . $entityTypeId . "," . $attributeSetId . "," . $setId . "," . $attributeId . ")";
                                $this->resourceConnection->getConnection()->query($qry);

                                $txt = "Info : Assigned attribute '" . $item . "' to  " . $attributeSetName;
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->sucessMsg;
                            }

                        } catch(Exception $e){
                            echo $e->getMessage();
                        }
                    }
                }
            }
        }
        return $trialSyncRecordCount ;
    }


    /*
     * Product Attribute sync
     */

    public function syncProductAttributes($autoSync, $syncType, $syncId, $scheduleId = NULL,$sessionStoreId)
    {
        $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'productAttribute', NULL);
        $this->totalTrialRecord = $this->common->numberOfRecordSyncInTrialLicense();
        try{

            $txt = "Info : Sync process started!";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            if ($sessionStoreId == 0) {
                $storeId = 1;
            }else{
                $storeId = $sessionStoreId;
            }
            $this->licenseType = $this->licenseCheck->checkLicenseTypes($storeId);
            $txt = "Info : License verification is in progress";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

            $licenseStatus = $this->licenseCheck->getLicenseStatus($storeId);

            $session = $this->messageManager;
            $insertedId = '';
            if ($licenseStatus != self::IS_LICENSE_VALID) {
                if ($scheduleId != '') {
                    $productAttributeArray['schedule_id'] = $scheduleId;
                } else {
                    $productAttributeArray['schedule_id'] = "";
                }
                $productAttributeArray['store_id'] = $storeId;
                $productAttributeArray['job_code'] = "productattribute";
                $productAttributeArray['status'] = "error";
                $productAttributeArray['messages'] = "Invalid License Key";
                $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                $productAttributeArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                if($syncType == 'MANUAL'){
                    $productAttributeArray['runMode'] = 'Manual';
                }elseif($syncType == 'AUTO'){
                    $productAttributeArray['runMode'] = 'Automatic';
                }
                if($autoSync == 'COMPLETE'){
                    $productAttributeArray['autoSync'] = 'Complete';
                }elseif($autoSync == 'INDIVIDUAL'){
                    $productAttributeArray['autoSync'] = 'Individual';
                }
                $txt = "Error: Invalid License Key. Please verify and try again";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->prodAttHelper->productAttributeManualSync($productAttributeArray);
                $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                $session->addError('Invalid license key.');
                $this->errorInMagento[] = 1;
            } else {
                $txt = "Info : License verified successfully!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $txt = "Info : Server time verification is in progress";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $timeSyncCheck = $this->timeHelper->timeSyncCheck($storeId);

                if ($timeSyncCheck != self::IS_TIME_VALID) {
                    if ($scheduleId != '') {
                        $productAttributeArray['schedule_id'] = $scheduleId;
                    } else {
                        $productAttributeArray['schedule_id'] = "";
                    }
                    $productAttributeArray['store_id'] = $storeId;
                    $productAttributeArray['job_code'] = "productattribute";
                    $productAttributeArray['status'] = "error";
                    $productAttributeArray['messages'] = "Time is not synced";
                    $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                    $productAttributeArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                    if($syncType == 'MANUAL'){
                        $productAttributeArray['runMode'] = 'Manual';
                    }elseif($syncType == 'AUTO'){
                        $productAttributeArray['runMode'] = 'Automatic';
                    }
                    if($autoSync == 'COMPLETE'){
                        $productAttributeArray['autoSync'] = 'Complete';
                    }elseif($autoSync == 'INDIVIDUAL'){
                        $productAttributeArray['autoSync'] = 'Individual';
                    }

                    $txt = "Error: Server time is not in sync";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    $this->prodAttHelper->productAttributeManualSync($productAttributeArray);
                    $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                    $this->errorInMagento[] = 1;
                } else {

                    $txt = "Info : Server time is in sync";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    if ($scheduleId != '') {
                        $productAttributeArray['schedule_id'] = $scheduleId;
                    } else {
                        $productAttributeArray['schedule_id'] = "";
                    }
                    $productAttributeArray['job_code'] = "productattribute";
                    $productAttributeArray['status'] = "success";
                    $productAttributeArray['messages'] = "Product Attribute manual sync initiated";
                    $productAttributeArray['store_id'] = $storeId;
                    $productAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                    $productAttributeArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                    if($syncType == 'MANUAL'){
                        $productAttributeArray['runMode'] = 'Manual';
                    }elseif($syncType == 'AUTO'){
                        $productAttributeArray['runMode'] = 'Automatic';
                    }
                    if($autoSync == 'COMPLETE'){
                        $productAttributeArray['autoSync'] = 'Complete';
                    }elseif($autoSync == 'INDIVIDUAL'){
                        $productAttributeArray['autoSync'] = 'Individual';
                    }

                    $txt = "Info : " . $productAttributeArray['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    $syncLogID = $this->prodAttHelper->productAttributeManualSync($productAttributeArray);
                    $this->syncResourceModel->updateSyncAttribute($syncId,'STARTED',$storeId);
                    if ($autoSync == 'COMPLETE') {
                        $insertedId = $this->syncResourceModel->checkConnectionFlag($syncId, 'PRODUCT_ATTRIBUTE_SYNC',$storeId);
                        if ($insertedId == NULL) {
                            $this->messageManager->addError("Sync in Progress - please wait for the current sync to finish.");
                            $productAttributeArray['id'] = $syncLogID;
                            $productAttributeArray['job_code'] = "productattribute";
                            $productAttributeArray['status'] = "error";
                            $productAttributeArray['messages'] = "Another Sync is already executing";
                            $productAttributeArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
                            $productAttributeArray['finished_at'] = $this->date->date('Y-m-d H:i:s');
                            if($syncType == 'MANUAL'){
                                $productAttributeArray['runMode'] = 'Manual';
                            }elseif($syncType == 'AUTO'){
                                $productAttributeArray['runMode'] = 'Automatic';
                            }
                            if($autoSync == 'COMPLETE'){
                                $productAttributeArray['autoSync'] = 'Complete';
                            }elseif($autoSync == 'INDIVIDUAL'){
                                $productAttributeArray['autoSync'] = 'Individual';
                            }
                            $txt = "Info : Sync in Progress - please wait for the current sync to finish";
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->prodAttHelper->productAttributeManualSync($productAttributeArray);
                            $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                            $this->errorInMagento[] = 1;
                        } else {
                            $this->syncResourceModel->updateSyncAttribute($syncId,'PROCESSING',$storeId);
                            $this->syncResourceModel->updateConnection($insertedId, 'PROCESS',$storeId);

                            /* Default attribute sync*/

                            $this->arrData = $this->getDetailsFromArray();
                            $this->syncMappingTable($syncLogID,$logViewFileName);
                            $this->importIntoMagento($syncId , $syncLogID , $logViewFileName, $storeId);

                            /* Custom attribute sync*/

                            $syncedRecord = $this->syncToMagento($syncId , $syncLogID , $logViewFileName, $storeId);

                            if ($this->sucessMsg == 0) {
                                $productAttributeArray['id'] = $syncLogID;
                                $productAttributeArray['status'] = "success";
                                $productAttributeArray['messages'] = "All attributes are in sync, no attribute has been updated";
                                $productAttributeArray['job_code'] = "productattribute";
                                $productAttributeArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                if($syncType == 'MANUAL'){
                                    $productAttributeArray['runMode'] = 'Manual';
                                }elseif($syncType == 'AUTO'){
                                    $productAttributeArray['runMode'] = 'Automatic';
                                }
                                if($autoSync == 'COMPLETE'){
                                    $productAttributeArray['autoSync'] = 'Complete';
                                }elseif($autoSync == 'INDIVIDUAL'){
                                    $productAttributeArray['autoSync'] = 'Individual';
                                }
                                $this->prodAttHelper->productAttributeManualSync($productAttributeArray);

                                $txt = "Info : " . $productAttributeArray['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->messageManager->addSuccess("Product attribute sync executed successfully!");

                            } else {
                                $productAttributeArray['id'] = $syncLogID;

                                if(count($this->errorInMagento) >= 1){
                                    $productAttributeArray['status'] = "error";
                                } else{
                                    $productAttributeArray['status'] = "success";
                                }
                                if($this->licenseType == 'trial' && $syncedRecord == $this->totalTrialRecord){
                                    $productAttributeArray['messages'] = "Trial license allow only ".$this->totalTrialRecord." records per sync!";
                                    $txt = "Info : " . $productAttributeArray['messages'];
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                }else{
                                    $productAttributeArray['messages'] = "Attributes created successfully!";
                                    $txt = "Info : " . $productAttributeArray['messages'];
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                }
                                $productAttributeArray['job_code'] = "productattribute";
                                $productAttributeArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                $productAttributeArray['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                if($syncType == 'MANUAL'){
                                    $productAttributeArray['runMode'] = 'Manual';
                                }elseif($syncType == 'AUTO'){
                                    $productAttributeArray['runMode'] = 'Automatic';
                                }
                                if($autoSync == 'COMPLETE'){
                                    $productAttributeArray['autoSync'] = 'Complete';
                                }elseif($autoSync == 'INDIVIDUAL'){
                                    $productAttributeArray['autoSync'] = 'Individual';
                                }
                                $this->prodAttHelper->productAttributeManualSync($productAttributeArray);
                                $this->messageManager->addSuccess("Product attribute sync executed successfully!");
                            }
                        }
                    }
                }
            }
            if(count($this->errorInMagento) >= 1){
                $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
            } else {
                $this->syncResourceModel->updateSyncAttribute($syncId,'SUCCESS',$storeId);
            }
            if($insertedId){
                $this->syncResourceModel->updateConnection($insertedId, 'SUCCESS',$storeId);
            }
        } catch (Exception $e) {
            if ($scheduleId != '') {
                $productAttributeArray['id'] = $scheduleId;
            } else {
                $productAttributeArray['id'] = $syncLogID;
            }
            $productAttributeArray['store_id'] = $storeId;
            $productAttributeArray['job_code'] = "productattribute";
            $productAttributeArray['status'] = "error";
            $productAttributeArray['messages'] = $e->getMessage();
            $productAttributeArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
            $productAttributeArray['finished_at'] = $this->date->date('Y-m-d H:i:s');
            if($syncType == 'MANUAL'){
                $productAttributeArray['runMode'] = 'Manual';
            }elseif($syncType == 'AUTO'){
                $productAttributeArray['runMode'] = 'Automatic';
            }
            if($autoSync == 'COMPLETE'){
                $productAttributeArray['autoSync'] = 'Complete';
            }elseif($autoSync == 'INDIVIDUAL'){
                $productAttributeArray['autoSync'] = 'Individual';
            }
            $txt = "Error: " . $productAttributeArray['messages'];
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->prodAttHelper->productAttributeManualSync($productAttributeArray);
            $this->messageManager->addError("Sync error occurred. Please try again.");
            $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
            $this->errorInMagento[] = 1;
        }
        $txt = "Info : Sync process completed!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
    }

    /**
     * Create Attribute Group Acumatica
     */

    public function attributeSetCreation($setId) {
        $sql = "SELECT `attribute_group_id` FROM " . $this->getTable('eav_attribute_group')." WHERE `attribute_group_name`='Acumatica' AND  `attribute_set_id`=" . $setId;
        try {
            $result = $this->resourceConnection->getConnection()->fetchOne($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        if (empty($result)) {
            $qry = " INSERT INTO " . $this->getTable('eav_attribute_group')." (`attribute_set_id`,`attribute_group_name`,`sort_order`,`attribute_group_code`) VALUES ($setId,'Acumatica',50,'acumatica')";
            try {
                $attributesetId = $this->resourceConnection->getConnection()->query($qry);

                $sql = "SELECT `attribute_group_id` FROM " . $this->getTable('eav_attribute_group')." WHERE `attribute_group_name`='Acumatica' ";
                try {
                    $result = $this->resourceConnection->getConnection()->fetchOne($sql);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                return $result;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            return $result;
        }
    }

    /**
     * @param $syncId
     * @param $attributeSetName
     * @param $storeId
     */

    public function getItemClassAttributes($syncId,$attributeSetName,$storeId){
        try {
            if ($storeId ==1)
            {
                $scopeType = 'default';
            }
            else
            {
                $scopeType = 'stores';
            }
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $attributeSet = $attributeSetName;
            $csvItemClassAttributesData = $this->common->getEnvelopeData('ATTRIBUTESBYITEMCLASS');
            $XMLGetRequest = $csvItemClassAttributesData['envelope'];
            $XMLGetRequest = str_replace('{{ITEMCLASS}}', trim($attributeSet), $XMLGetRequest);
            $action = $csvItemClassAttributesData['envName'].'/'.$csvItemClassAttributesData['envVersion'].'/'.$csvItemClassAttributesData['methodName'];
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$storeId);
            $amconnectorConfigUrl = $this->common->getBasicConfigUrl($serverUrl);
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $amconnectorConfigUrl, $action);
            $data = $xmlResponse->Body->GetListResponse->GetListResult;
            $totalData = $this->xmlHelper->xml2array($data);
            $totalData = json_decode(json_encode($totalData), 1);
            $attributes = array();
            $defaultWarehouse = $this->scopeConfigInterface->getValue('amconnectorsync/defaultwarehouses/defaultwarehouse',$scopeType ,$storeId);
            if($totalData['Entity']['DefaultWarehouseID']['Value'] == $defaultWarehouse){
                if(!isset($totalData['Entity']['Attributes']['ItemClassAtrribute'][0])){
                    if(isset($totalData['Entity']['Attributes']['ItemClassAtrribute'])) {
                        $attributes[] = trim($totalData['Entity']['Attributes']['ItemClassAtrribute']['AttributeID']['Value']);
                    }
                }else{
                    foreach($totalData['Entity']['Attributes']['ItemClassAtrribute'] as $attributeList) {
                        $attributes[] = trim($attributeList['AttributeID']['Value']);
                    }
                }
            }
            return $attributes;

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * @param $syncId
     * @param $storeId
     */

    public function getAllAttributeSet( $syncId,$storeId){
        try {
            if ($storeId == 1)
            {
                $scopeType = 'default';
            }
            else
            {
                $scopeType = 'stores';
            }
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId,$storeId);
            $getlastSyncDateByTimezone = $this->timezone->date($lastSyncDate,null,true);
            $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
            $toDate = $this->date->date('Y-m-d H:i:s',strtotime("+1 day"));
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$storeId);
            $amconnectorConfigUrl = $this->common->getBasicConfigUrl($serverUrl);
            $csvItemClassListData = $this->common->getEnvelopeData('ITEMCLASSLISTBYDATE');
            $XMLGetRequest = $csvItemClassListData['envelope'];
            $XMLGetRequest = str_replace('{{FROMDATE}}', trim($fromDate), $XMLGetRequest);
            $XMLGetRequest = str_replace('{{TODATE}}', trim($toDate), $XMLGetRequest);
            $action = $csvItemClassListData['envName'].'/'.$csvItemClassListData['envVersion'].'/'.$csvItemClassListData['methodName'];
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $amconnectorConfigUrl, $action);
            $data = $xmlResponse->Body->GetListResponse->GetListResult;
            $totalData = $this->xmlHelper->xml2array($data);

            $attributeSets  = array();
            $defaultWarehouse = $this->scopeConfigInterface->getValue('amconnectorsync/defaultwarehouses/defaultwarehouse',$scopeType ,$storeId);
            if(!isset($totalData['Entity'][0])){
                if(isset($totalData['Entity']['DefaultWarehouseID']['Value']))
                    if(trim($totalData['Entity']['DefaultWarehouseID']['Value']) == $defaultWarehouse){
                        $attributeSets[] = $totalData['Entity']['ClassID']['Value'];
                    }
            }else{
                foreach($totalData['Entity']  as $itemClass) {
                    if(trim($itemClass->DefaultWarehouseID->Value) == $defaultWarehouse){
                        $attributeSets[] = trim($itemClass->ClassID->Value);
                    }
                }
            }
            return $attributeSets;

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $syncId
     * @param $storeId
     */

    public function getAllAttribute($syncId,$storeId)
    {
        try {
            if ($storeId ==1)
            {
                $scopeType = 'default';
            }
            else
            {
                $scopeType = 'stores';
            }
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId,$storeId);
            $getlastSyncDateByTimezone = $this->timezone->date($lastSyncDate,null,true);
            $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
            $toDate = $this->date->date('Y-m-d H:i:s',strtotime("+1 day"));
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$storeId);
            $amconnectorConfigUrl = $this->common->getBasicConfigUrl($serverUrl);
            $csvAttributesData = $this->common->getEnvelopeData('GETATTRIBUTESBYDATE');
            $XMLGetRequest = $csvAttributesData['envelope'];
            $XMLGetRequest = str_replace('{{FROMDATE}}', trim($fromDate), $XMLGetRequest);
            $XMLGetRequest = str_replace('{{TODATE}}', trim($toDate), $XMLGetRequest);
            $action = $csvAttributesData['envName'].'/'.$csvAttributesData['envVersion'].'/'.$csvAttributesData['methodName'];
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $amconnectorConfigUrl, $action);
            $data = $xmlResponse->Body->GetListResponse->GetListResult;
            $totalData = $this->xmlHelper->xml2array($data);
            return $totalData;

        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

    /**
     * @param $attributeId
     * @param $description
     * @param $controllType
     */
    public function insertCustomAttributes($attributeId,$description,$controllType,$storeId){
        $count = $this->resourceConnection->getConnection()->fetchOne("SELECT count(*) from " . $this->getTable('amconnector_custom_product_attributes')."  where attributeid='" . $attributeId . "' and store_id='".$storeId."' ");
        if ($count) {
            $this->resourceConnection->getConnection()->query("UPDATE " . $this->getTable('amconnector_custom_product_attributes') . " set description='" . $description . "', controlltype='" . $controllType . "' where attributeid='" . $attributeId . "' and store_id = '".$storeId."' ");
        }
        else {
            $this->resourceConnection->getConnection()->query("INSERT INTO  " . $this->getTable('amconnector_custom_product_attributes') . " (`id`,`attributeid`, `description`,`controlltype`,`store_id`)VALUES (NULL,'$attributeId','$description','$controllType','$storeId')");
        }
    }
}
