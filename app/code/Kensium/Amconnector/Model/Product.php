<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model;

use Psr\Log\LoggerInterface as Logger;
use Kensium\Lib;
class Product extends \Magento\Framework\Model\AbstractModel
{
    public $errorCheckInMagento = array();
    public $errorCheckInAcumatica = array();

    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Product
     */
    protected $productResourceModel;
    /**
     * @var \Magento\Eav\Model\Entity
     */
    protected $entityModel;
    /**
     * @var \Kensium\Amconnector\Helper\Category
     */
    protected $categoryHelper;

    /**
     * @var ResourceModel\Category
     */
    protected $categoryResourceModel;

    /**
     * @var Category
     */
    protected $categoryModel;

    /**
     * @var \Kensium\Amconnector\Helper\Product
     */
    protected $productHelper;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollection;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Kensium\Synclog\Helper\Product
     */
    protected $productSyncLogs;

    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $abstractAttribute;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;


    /**
     * @var \Kensium\Amconnector\Helper\Xml
     */
    protected $xmlHelper;

    /**
     * @var \Kensium\Amconnector\Helper\Url
     */
    protected $urlHelper;

    protected $common;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Product $productResourceModel
     * @param \Kensium\Amconnector\Helper\Data $dataHelper
     * @param \Kensium\Amconnector\Helper\Url $urlHelper
     * @param \Kensium\Amconnector\Helper\Category $categoryHelper
     * @param \Kensium\Amconnector\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Kensium\Synclog\Helper\Product $productSyncLogs
     * @param ResourceModel\Product\Collection $resourceCollection
     * @param ResourceModel\Category $categoryResourceModel
     * @param Category $categoryModel
     * @param \Magento\Eav\Model\Entity $entityModel
     * @param \Kensium\Amconnector\Helper\Xml $xmlHelper
     * @param \Magento\Eav\Model\Entity\AttributeFactory $abstractAttribute
     * @param \Magento\Store\Model\Website $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection
     * @param array $data
     */
    public function __construct(

        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Kensium\Amconnector\Model\ResourceModel\Product $productResourceModel,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Helper\Category $categoryHelper,
        \Kensium\Amconnector\Helper\Product $productHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Kensium\Synclog\Helper\Product $productSyncLogs,
        \Kensium\Amconnector\Model\ResourceModel\Product\Collection $resourceCollection = null,
        \Kensium\Amconnector\Model\ResourceModel\Category $categoryResourceModel,
        \Kensium\Amconnector\Model\Category $categoryModel,
        \Magento\Eav\Model\Entity $entityModel,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Magento\Eav\Model\Entity\AttributeFactory $abstractAttribute,
        \Magento\Store\Model\Website $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface ,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection,
         Lib\Common $common
    )
    {
        $this->logger = $context->getLogger();
        $this->productResourceModel = $productResourceModel;
        $this->entityModel = $entityModel;
        $this->categoryHelper = $categoryHelper;
        $this->productHelper = $productHelper;
        $this->dataHelper = $dataHelper;
        $this->categoryResourceModel = $categoryResourceModel;
        $this->categoryModel = $categoryModel;
        $this->categoryCollection = $categoryCollection;
        $this->storeManager = $storeManager;
        $this->productFactory = $productFactory;
        $this->categoryFactory=$categoryFactory;
        $this->productSyncLogs = $productSyncLogs;
        $this->urlHelper = $urlHelper;
        $this->xmlHelper = $xmlHelper;
        $this->abstractAttribute = $abstractAttribute;
        $this->scopeConfigInterface = $scopeConfigInterface;
	$this->common = $common;
        parent::__construct($context, $registry, $productResourceModel, $resourceCollection);
    }

    /**
     * constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Kensium\Amconnector\Model\ResourceModel\Product');
    }


    /**
     * @param $aData
     * @param $mappingAttributes
     * @param $syncType
     * @param $autoSync
     * @param $syncLogID
     * @param $storeId
     * @param $logViewFileName
     * @param $directionFlag
     * @param $url
     * @param null $configurator
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function syncToMagento($aData, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url, $configurator = NULL)
    {
        if (!is_array($aData)) {
            $aData = json_decode(json_encode($aData), 1);
        }
        $nonStockItem  = 0;
        /**
         * Here we are preparing an array based on the mapping attribute
         */
        $visibilityClass = array("Not Visible Individually" => "1", "Catalog" => "2", "Search" => "3", "Catalog, Search" => "4");
        $syncData = array();
        $quantity = 0;
        foreach ($mappingAttributes as $key => $value) {
            $acumaticaFieldValue = '';
            $mappingData = explode('|', $value);
            if ($directionFlag && $mappingData[1] == 'Bi-Directional (Magento Wins)') {
                continue;
            }
            if ($mappingData[0] != '') {
                $acumaticaLabel = $this->productResourceModel->getAcumaticaAttrCode($mappingData[0]);
            }
            $acumaticaAttrCode = explode(" ", $acumaticaLabel); //array[0] will be section and array[1] will be attribute code
            if ($acumaticaAttrCode[0] == "ProductSchema") {
                if (isset($aData[$acumaticaAttrCode[1]]['Value']))
                    $acumaticaFieldValue = $aData[$acumaticaAttrCode[1]]['Value'];
                else
                    $acumaticaFieldValue = '';
            } else {
                if (isset($aData['Attributes']['AttributeValue']) && count($aData['Attributes']['AttributeValue']) > 0) {
                    $aData['Attributes']['AttributeValue'] = json_decode(json_encode($aData['Attributes']['AttributeValue']), 1);
                    $oneAttributeRecordFlag = false;
                    $acumaticaFieldValue = '';
                    foreach ($aData['Attributes']['AttributeValue'] as $attributeKey => $attributeValue) {
                        if (!is_numeric($attributeKey)) {
                            $oneAttributeRecordFlag = true;
                            break;
                        }
                        if (isset($attributeValue['AttributeID']['Value']) && isset($attributeValue['Value']['Value']))
                        {
                            $magentoAttributeCode = strtolower($attributeValue['AttributeID']['Value']);
                            $magentoAttributeValue = $attributeValue['Value']['Value'];
                            if (strtolower($acumaticaAttrCode[0]) == $magentoAttributeCode)
                            {
                                $magentoAttributeType = $this->productResourceModel->magentoAttributeType($magentoAttributeCode);
                                if ($magentoAttributeType == 'boolean') {
                                    if ($magentoAttributeValue == 'True') {
                                        $acumaticaFieldValue = '1';
                                        break;
                                    } else {
                                        $acumaticaFieldValue = '0';
                                        break;
                                    }
                                } elseif($magentoAttributeType == 'select')
                                {
                                    $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                    $allOptions = $attributeDetails->getSource()->getAllOptions();
                                    if(isset($allOptions) && !empty($allOptions))
                                    {
                                        foreach($allOptions as $singleOptin)
                                        {
                                            if($singleOptin['label'] == $magentoAttributeValue)
                                            {
                                                $acumaticaFieldValue = $singleOptin['value'];
                                                break;
                                            }
                                        }
                                    }
                                }else if($magentoAttributeType == 'multiselect')
                                {
                                    if(isset($magentoAttributeValue) && $magentoAttributeValue != '') {
                                        $optinValues = explode(',', $magentoAttributeValue);
                                        $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                        $realIds = array();
                                        foreach($optinValues as $optinValue)
                                        {
                                            $allOptions = $attributeDetails->getSource()->getAllOptions();
                                            foreach($allOptions as $singleOptin)
                                            {
                                                if($singleOptin['label'] == $optinValue)
                                                {
                                                    $realIds[] = $singleOptin['value'];
                                                }
                                            }
                                        }
                                        if(isset($realIds) && !empty($realIds))
                                        {
                                            $acumaticaFieldValue = implode(',',$realIds);
                                            break;
                                        }
                                    }
                                }else{
                                    if (isset($magentoAttributeValue))
                                    {
                                        $acumaticaFieldValue = $magentoAttributeValue;
                                    }
                                    break;
                                }
                            }
                        }
                    }
                    if ($oneAttributeRecordFlag) {
                        if (isset($aData['Attributes']['AttributeValue']['AttributeID']['Value']) && isset($aData['Attributes']['AttributeValue']['Value']['Value']))
                        {
                            $magentoAttributeCode = strtolower($aData['Attributes']['AttributeValue']['AttributeID']['Value']);
                            $magentoAttributeValue = $aData['Attributes']['AttributeValue']['Value']['Value'];
                            if (strtolower($acumaticaAttrCode[0]) == $magentoAttributeCode) {
                                /**
                                 * Check attribute type
                                 * if boolean then change value from Yes to True and No to False
                                 */
                                $magentoAttributeType = $this->productResourceModel->magentoAttributeType($magentoAttributeCode);
                                if ($magentoAttributeType == 'boolean') {
                                    if ($magentoAttributeValue == 'True') {
                                        $acumaticaFieldValue = '1';
                                    } else {
                                        $acumaticaFieldValue = '0';
                                    }
                                }elseif($magentoAttributeType == 'select') {
                                    $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                    $allOptions = $attributeDetails->getSource()->getAllOptions();
                                    if(isset($allOptions) && !empty($allOptions))
                                    {
                                        foreach($allOptions as $singleOptin)
                                        {
                                            if($singleOptin['label'] == $magentoAttributeValue)
                                            {
                                                $acumaticaFieldValue = $singleOptin['value'];
                                                break;
                                            }
                                        }
                                    }
                                }else if($magentoAttributeType == 'multiselect')
                                {
                                    if(isset($magentoAttributeValue) && $magentoAttributeValue != '') {
                                        $optinValues = explode(',', $magentoAttributeValue);
                                        $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                        $realIds = array();
                                        foreach($optinValues as $optinValue)
                                        {
                                            $allOptions = $attributeDetails->getSource()->getAllOptions();
                                            foreach($allOptions as $singleOptin)
                                            {
                                                if($singleOptin['label'] == $optinValue)
                                                {
                                                    $realIds[] = $singleOptin['value'];
                                                }
                                            }
                                        }
                                        if(isset($realIds) && !empty($realIds))
                                        {
                                            $acumaticaFieldValue = implode(',',$realIds);
                                            break;
                                        }
                                    }
                                } else {
                                    if (isset($magentoAttributeValue) && $magentoAttributeValue != '')
                                        $acumaticaFieldValue = $magentoAttributeValue;
                                }
                            }
                        }
                    }
                }
            }
            if ($key == "visibility") {
                if ($acumaticaFieldValue != '') {
                    $acumaticaFieldValue = $visibilityClass[$acumaticaFieldValue];
                } else {
                    $acumaticaFieldValue = "1";
                }
            }
            if ($key == "description") {
                if ($acumaticaFieldValue == '') {
                    if(isset($aData['Description']['Value']))
                        $acumaticaFieldValue = $aData['Description']['Value'];
                } else {

                    $acumaticaFieldValue = $acumaticaFieldValue;
                }
            }
            if ($key == "status") {
                if ($acumaticaFieldValue == "true") {
                    $acumaticaFieldValue = 1;
                } else {
                    $acumaticaFieldValue = 2;
                }
            }
            if(isset($acumaticaFieldValue) && $acumaticaFieldValue != '')
                $syncData[$key] = $acumaticaFieldValue;
        }
        if(isset($aData['ItemType']['Value']) && $aData['ItemType']['Value'] == 'Non-Stock Item')
        {
            $nonStockItem = 1;
            $syncData['is_non_stock'] = 1;
        }
        /**
         * Get Item class and check that item class is exist or not
         */
        if (!empty($syncData)) {
            $itemClassName = '';
            if(isset($aData['ItemClass']['Value']))
            {
                $itemClassName = $aData['ItemClass']['Value'];
            }else {
                if($nonStockItem == 1)
                {
                    $nonstockItemClass = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/itemclass');

                    if($nonstockItemClass != '')
                    {
                        $syncData['attribute_set'] = $nonstockItemClass;
                    }
                }
            }
            if ($itemClassName != '') {
                $entityTypeId = $this->entityModel->setType('catalog_product')->getTypeId();
                $attributeSetId = $this->productResourceModel->getAttributeSetId($entityTypeId, $itemClassName);
                if (isset($attributeSetId) && $attributeSetId != '') {
                    $syncData['attribute_set'] = $attributeSetId;
                }
            }
            /**
             * If SIE sync is running then no need to check for categroy
             * here we need to add category mapping
             * get categories of product
             * check that product exist in magento
             * if exist assign to product
             * if not exist create category and assign to product
             */
            $realCategories = array();
            if ($configurator == NULL) {
                $oneCategoryRecordFlag = false;
                $getCategories = array();
                if($nonStockItem == 1)
                {
                    if (isset($aData['Categories']['NonCategories']))
                        $getCategories = json_decode(json_encode($aData['Categories']['NonCategories']), 1);
                }else {
                    if (isset($aData['Categories']['Categories']))
                        $getCategories = json_decode(json_encode($aData['Categories']['Categories']), 1);
                }
                if (!empty($getCategories)) {
                    foreach ($getCategories as $catkey => $catValue) {
                        if (!is_numeric($catkey)) {
                            $oneCategoryRecordFlag = true;
                            break;
                        }
                        $categoryId = $catValue['CategoryID']['Value'];
                        /**
                         * check category exist in magento
                         */
                        $checkCategoryInMagento = $this->productResourceModel->checkCategoryInMagento($categoryId, $storeId);
                        if (isset($checkCategoryInMagento) && $checkCategoryInMagento != '') {
                            $realCategories[] = $checkCategoryInMagento;
                        }
                    }
                    if ($oneCategoryRecordFlag) {
                        $categoryId = $getCategories['CategoryID']['Value'];
                        /**
                         * check category exist in magento
                         */
                        $checkCategoryInMagento = $this->productResourceModel->checkCategoryInMagento($categoryId, $storeId);
                        if ($checkCategoryInMagento != '') {
                            $realCategories[] = $checkCategoryInMagento;
                        }
                    }
                }
            }
        }

        /**
         * replacing spaces with underscore
         */
        if(isset($syncData['sku']) && $syncData['sku'] != '')
        {
            $syncData['sku'] = str_replace(" ","_",$syncData['sku']);
        }
        /**
         * get item status to sync product.if it is in active the product can not be sync
         * get product status for disable/enable.
         */
        try {
            /**
             * checking condition that required fields
             * first we need to check attribute set exist in magento or not
             * if exist then proceed further otherwise don't sync this product
             */
            $productDisableFlag = 0;
            if(isset($aData['ItemStatus']['Value']))
                $itemStatus = $aData['ItemStatus']['Value'];
            else
                $itemStatus = '';
            if(isset($aData['Active']['Value']))
                $productActive = $aData['Active']['Value'];
            else
                $productActive = '';

            if(isset($syncData['tax_class_id']) && $syncData['tax_class_id'] != '')
            {
                if($this->productResourceModel->getTaxClassId($syncData['tax_class_id']) != '')
                    $syncData['tax_class_id'] = $this->productResourceModel->getTaxClassId($syncData['tax_class_id']);
                else
                    $syncData['tax_class_id'] = '';
            }
            if (isset($syncData['attribute_set']))
                if ($syncData['attribute_set'] != '' && isset($syncData['sku']) && $syncData['sku'] != '') {
                    if ($aData['InventoryID']['Value'] != '') {
                        $acumaticaInventoryId = str_replace(" ","_",$aData['InventoryID']['Value']);
                    }
                    $_product = $this->productResourceModel->getProductBySku($acumaticaInventoryId);
                    $createFlag = 0;
                    if (isset($_product) && $_product != 0) {
                        if ($syncData['sku'] != '')
                            $createFlag = 1;
                    } else {
                        if ($syncData['sku'] != '' && $syncData['name'] != '' && $syncData['price'] != '' && /*$syncData['description'] != '' && $syncData['short_description'] != '' &&*/ $syncData['weight'] != '' && $syncData['status'] != '' && $syncData['tax_class_id'] != '')
                            $createFlag = 1;

                        if ($itemStatus != "Active" || $productActive != "true") {
                            $productDisableFlag = 1;
                        }
                    }
                    if (!$productDisableFlag) {
                        if ($createFlag) {
                            /**
                             * Here we are checking that the item status & Active field are Enabled
                             * if both fields are enabled then we need to enable the product in magento
                             * if any field is disabled then while updating product disable the product in magento
                             * while creating product don't sync to magento
                             */
                            if (isset($_product) && $_product != 0) {
                                $productBeforeData =  $this->productFactory->create()->loadByAttribute('sku',$syncData['sku']);
                                $productArray['before_change'] = json_encode($productBeforeData->getData());
                                if ($itemStatus == "Active" && $productActive == "true") {
                                    $syncData['status'] = "1"; //if both are active we are enabled the product
                                } else {
                                    $syncData['status'] = "2"; //else disabled the product
                                }
                            } else {
                                if ($itemStatus != "Active" || $productActive != "true") {
                                    $productDisableFlag = 1;
                                }
                            }
                            if (!$productDisableFlag) {
                                /**
                                 * Here need to set upsell and cross sell product
                                 * if upsell/crossell product is not exist in magento need to create
                                 * If SIE sync is running then no need to create UpSells and Cross Sells
                                 */
                                $upsell = array();
                                $csell = array();
                                if ($configurator == NULL) {
                                    $upsellData = array();
                                    if($nonStockItem == 1)
                                    {
                                        if (isset($aData['UpSells']['NonUpSells']))
                                            $upsellData = json_decode(json_encode($aData['UpSells']['NonUpSells']), 1);
                                    }else {
                                        if (isset($aData['UpSells']['UpSells']))
                                            $upsellData = json_decode(json_encode($aData['UpSells']['UpSells']), 1);
                                    }
                                    if (!empty($upsellData)) {
                                        $oneUpsellRecordFlag = false;
                                        foreach ($upsellData as $_key => $_value) {
                                            if (!is_numeric($_key)) {
                                                $oneUpsellRecordFlag = true;
                                                break;
                                            }
                                            $acumaticaUpSellSku = str_replace(" ","_",$_value['InventoryID']['Value']);
                                            $upSellProduct = $this->productResourceModel->getProductBySku($acumaticaUpSellSku);
                                            if (isset($upSellProduct) && $upSellProduct != '') {
                                                $upsell[] = $acumaticaUpSellSku;
                                            } /*else {
                                                $upsell[] = $this->createProductInMagento($_value['InventoryID']['Value'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                            }*/
                                        }
                                        if ($oneUpsellRecordFlag) {
                                            $acumaticaUpSellSku = str_replace(" ","_",$upsellData['InventoryID']['Value']);
                                            $upSellProduct = $this->productResourceModel->getProductBySku($acumaticaUpSellSku);
                                            if ($upSellProduct) {
                                                $upsell[] = $acumaticaUpSellSku;
                                            } /*else {

                                                $upsell[] = $this->createProductInMagento($upsellData['InventoryID']['Value'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                            }*/
                                        }
                                    }
                                    $crosSellData = array();
                                    if($nonStockItem == 1)
                                    {
                                        if (isset($aData['CrossSells']['NonCrossSells']))
                                            $crosSellData = json_decode(json_encode($aData['CrossSells']['NonCrossSells']), 1);
                                    }else {
                                        if (isset($aData['CrossSells']['CrossSells']))
                                            $crosSellData = json_decode(json_encode($aData['CrossSells']['CrossSells']), 1);
                                    }
                                    if (!empty($crosSellData)) {
                                        $oneRecordFlag = false;
                                        foreach ($crosSellData as $crossellkey => $crossellvalue) {
                                            if (!is_numeric($crossellkey)) {
                                                $oneRecordFlag = true;
                                                break;
                                            }
                                            $acumaticaCrosSellSku = str_replace(" ","_",$crossellvalue['InventoryID']['Value']);
                                            $crosSellProduct = $this->productResourceModel->getProductBySku($acumaticaCrosSellSku);
                                            if ($crosSellProduct) {
                                                $csell[] = $acumaticaCrosSellSku;
                                            } /*else {
                                                $csell[] = $this->createProductInMagento($crossellvalue['InventoryID']['Value'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                            }*/
                                        }
                                        if ($oneRecordFlag) {
                                            $acumaticaCrosSellSku = str_replace(" ","_",$crosSellData['InventoryID']['Value']);
                                            $crosSellProduct = $this->productResourceModel->getProductBySku($acumaticaCrosSellSku);
                                            if ($crosSellProduct) {
                                                $csell[] = $acumaticaCrosSellSku;
                                            } /*else {
                                                $csell[] = $this->createProductInMagento($crosSellData['InventoryID']['Value'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                            }*/
                                        }
                                    }
                                }

                                if ($storeId > 1) {
                                    $storeInfo = $this->storeManager->load($storeId);
                                    $syncData["store"] = $storeInfo->getCode();
                                    $websiteInfo = $this->storeManager->load($storeInfo->getWebsiteId());
                                    $syncData["websites"] = $websiteInfo->getCode();
                                } else {
                                    $syncData["websites"] = 1;
                                }
                                if(isset($syncData["name"]) && $syncData["name"] != '' && isset($syncData['sku']) && $syncData['sku'] != '')
                                {
                                    $urlKey = str_replace(" ","-",$syncData["name"])."-".str_replace("_","-",$syncData['sku']);
                                    $syncData["url_key"] = strtolower($urlKey);
                                }
                                if (isset($_product) && $_product != 0)
                                {
                                    if($nonStockItem == 1) {
                                        $syncInMagento = $this->productResourceModel->updateSimpleNonStockProduct($syncData, $storeId, $realCategories, $upsell, $csell);
                                    }else {
                                        $syncInMagento = $this->productResourceModel->updateSimpleProduct($syncData, $storeId, $realCategories, $upsell, $csell);
                                    }
                                } else {
                                    if($nonStockItem == 1) {
                                        $syncInMagento = $this->productResourceModel->createSimpleNonStockProduct($syncData, $storeId, $realCategories, $upsell, $csell);
                                    }else {
                                        $syncInMagento = $this->productResourceModel->createSimpleProduct($syncData, $storeId, $realCategories, $upsell, $csell);
                                    }
                                }
                                /**
                                 * logs here for sync success
                                 */
                                $productData = $this->productFactory->create()->loadByAttribute('sku', $syncData['sku']);
                                if (isset($productData) && !empty($productData))
                                {
                                    $productArray['storeId'] = $storeId;
                                    $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                    if (isset($_product) && $_product != 0)
                                    {
                                        $productArray['description'] = "SKU " . $syncData['sku'] . " updated in Magento"; //Descripton
                                        $productArray['syncAction'] = "Product Updated Into Magento";
                                    } else {
                                        $productArray['description'] = "SKU " . $syncData['sku'] . " inserted in Magento"; //Descripton
                                        $productArray['syncAction'] = "Product Inserted Into Magento";
                                    }
                                    if ($productData) {
                                        $productArray['productId'] = $productData->getId(); //Manual/Auto/Individual
                                    }
                                    $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                                    if ($autoSync == 'COMPLETE') {
                                        $productArray['action'] = "Batch Process";//This needs to be dynamic value
                                    } elseif ($autoSync == 'INDIVIDUAL') {
                                        $productArray['action'] = "Individual";//This needs to be dynamic value
                                    }
                                    $productArray['acumaticaStockItem'] = $syncData['sku'];
                                    $productArray['syncDirection'] = "syncToMagento";
                                    $productArray['messageType'] = "Success";
                                    $this->productSyncLogs->productSyncSuccessLogs($productArray);
                                    $txt = "Info : " . $productArray['description'] . " successfully";
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                }
                            }
                        } else {
                            /**
                             * logs here for failure
                             */
                            $msg = 'Mandatory fields must be filled to sync product(s)';
                            if (isset($_product) && $_product != 0)
                            {
                                if ($syncData['InventoryID'] == '')
                                    $msg = 'Product SKU mapping is not proper';
                            }
                            $productArray['storeId'] = $storeId;
                            $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                            $productArray['description'] = $msg;
                            $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                            if ($autoSync == 'COMPLETE') {
                                $productArray['action'] = "Batch Process";//This needs to be dynamic value
                            } elseif ($autoSync == 'INDIVIDUAL') {
                                $productArray['action'] = "Individual";//This needs to be dynamic value
                            }
                            $productArray['productId'] = '';
                            $productArray['syncAction'] = "Product Not Synced";
                            $productArray['acumaticaStockItem'] = $syncData['sku'];
                            $productArray['syncDirection'] = "syncToMagento";
                            $productArray['messageType'] = "Failure";
                            $txt = "Error: Acumatica Stock Item : " . $syncData['sku'] . " : " . $productArray['description'] . " Please try again";
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->productSyncLogs->productSyncSuccessLogs($productArray);
                            $this->errorCheckInMagento[] = 1;
                        }
                    }
                }
        } catch (Exception $e) {

            /**
             *logs here to print exception
             */
            $productArray['storeId'] = $storeId;
            $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
            $productArray['description'] = $e->getMessage(); //Descripton
            $productArray['runMode'] = $syncType; //Manual/Auto/Individual
            if ($autoSync == 'COMPLETE') {
                $productArray['action'] = "Batch Process";//This needs to be dynamic value
            } elseif ($autoSync == 'INDIVIDUAL') {
                $productArray['action'] = "Individual";//This needs to be dynamic value
            }
            $productArray['syncAction'] = "Product Not Synced";
            $productArray['acumaticaStockItem'] = $syncData['sku'];
            $productArray['syncDirection'] = "syncToMagento";
            $productArray['messageType'] = "Failure";
            $txt = "Error: " . $productArray['syncAction'];
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->productSyncLogs->productSyncSuccessLogs($productArray);
            $this->errorCheckInMagento[] = 1;
        }
        return $this->errorCheckInMagento;
    }

    /**
     * @param $sku
     * @param $mappingAttributes
     * @param $syncType
     * @param $autoSync
     * @param $syncLogID
     * @param $storeId
     * @param $logViewFileName
     * @param $directionFlag
     * @param $url
     * @return array|mixed
     */
    public function createProductInMagento($sku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url)
    {
        $aData = $this->productHelper->getProductBySkuForIndividual($url,$sku,$storeId);
        /**
         * Here we are preparing an array based on the mapping attribute
         */
        $visibilityClass = array("Not Visible Individually" => "1", "Catalog" => "2", "Search" => "3", "Catalog, Search" => "4");
        $syncData = array();
        $quantity = 0;

            foreach ($mappingAttributes as $key => $value) {
                $acumaticaFieldValue = '';
                $mappingData = explode('|', $value);
                if ($directionFlag && $mappingData[1] == 'Bi-Directional (Magento Wins)') {
                    continue;
                }
                if ($mappingData[0] != '') {
                    $acumaticaLabel = $this->productResourceModel->getAcumaticaAttrCode($mappingData[0]);
                }
                $acumaticaAttrCode = explode(" ", $acumaticaLabel); //array[0] will be section and array[1] will be attribute code
                if ($acumaticaAttrCode[0] == "ProductSchema") {
                    if (isset($aData[$acumaticaAttrCode[1]]['Value']))
                        $acumaticaFieldValue = $aData[$acumaticaAttrCode[1]]['Value'];
                    else
                        $acumaticaFieldValue = '';
                } else {
                    if (isset($aData['Attributes']['AttributeValue']) && count($aData['Attributes']['AttributeValue']) > 0) {
                        $aData['Attributes']['AttributeValue'] = json_decode(json_encode($aData['Attributes']['AttributeValue']), 1);
                        $oneAttributeRecordFlag = false;
                        $acumaticaFieldValue = '';
                        foreach ($aData['Attributes']['AttributeValue'] as $attributeKey => $attributeValue) {
                            if (!is_numeric($attributeKey)) {
                                $oneAttributeRecordFlag = true;
                                break;
                            }
                            if (isset($attributeValue['AttributeID']['Value']) && isset($attributeValue['Value']['Value'])) {
                                $magentoAttributeCode = strtolower($attributeValue['AttributeID']['Value']);
                                $magentoAttributeValue = $attributeValue['Value']['Value'];
                                if (strtolower($acumaticaAttrCode[0]) == $magentoAttributeCode) {
                                    $magentoAttributeType = $this->productResourceModel->magentoAttributeType($magentoAttributeCode);
                                    if ($magentoAttributeType == 'boolean') {
                                        if ($magentoAttributeValue == 'True') {
                                            $acumaticaFieldValue = '1';
                                            break;
                                        } else {
                                            $acumaticaFieldValue = '0';
                                            break;
                                        }
                                    } elseif ($magentoAttributeType == 'select') {
                                        $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                        $allOptions = $attributeDetails->getSource()->getAllOptions();
                                        if(isset($allOptions) && !empty($allOptions))
                                        {
                                            foreach($allOptions as $singleOptin)
                                            {
                                                if($singleOptin['label'] == $magentoAttributeValue)
                                                {
                                                    $acumaticaFieldValue = $singleOptin['value'];
                                                    break;
                                                }
                                            }
                                        }
                                    } else if ($magentoAttributeType == 'multiselect') {
                                        if (isset($magentoAttributeValue) && $magentoAttributeValue != '') {
                                            $optinValues = explode(',', $magentoAttributeValue);
                                            $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                            $realIds = array();
                                            foreach($optinValues as $optinValue)
                                            {
                                                $allOptions = $attributeDetails->getSource()->getAllOptions();
                                                foreach($allOptions as $singleOptin)
                                                {
                                                    if($singleOptin['label'] == $optinValue)
                                                    {
                                                        $realIds[] = $singleOptin['value'];
                                                    }
                                                }
                                            }
                                            if (isset($realIds) && !empty($realIds)) {
                                                $acumaticaFieldValue = implode(',', $realIds);
                                                break;
                                            }
                                        }
                                    } else {
                                        if (isset($magentoAttributeValue)) {
                                            $acumaticaFieldValue = $magentoAttributeValue;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if ($oneAttributeRecordFlag) {
                            if (isset($aData['Attributes']['AttributeValue']['AttributeID']['Value']) && isset($aData['Attributes']['AttributeValue']['Value']['Value'])) {
                                $magentoAttributeCode = strtolower($aData['Attributes']['AttributeValue']['AttributeID']['Value']);
                                $magentoAttributeValue = $aData['Attributes']['AttributeValue']['Value']['Value'];
                                if (strtolower($acumaticaAttrCode[0]) == $magentoAttributeCode) {
                                    /**
                                     * Check attribute type
                                     * if boolean then change value from Yes to True and No to False
                                     */
                                    $magentoAttributeType = $this->productResourceModel->magentoAttributeType($magentoAttributeCode);
                                    if ($magentoAttributeType == 'boolean') {
                                        if ($magentoAttributeValue == 'True') {
                                            $acumaticaFieldValue = '1';
                                        } else {
                                            $acumaticaFieldValue = '0';
                                        }
                                    } elseif ($magentoAttributeType == 'select') {
                                        $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                        $allOptions = $attributeDetails->getSource()->getAllOptions();
                                        if(isset($allOptions) && !empty($allOptions))
                                        {
                                            foreach($allOptions as $singleOptin)
                                            {
                                                if($singleOptin['label'] == $magentoAttributeValue)
                                                {
                                                    $acumaticaFieldValue = $singleOptin['value'];
                                                    break;
                                                }
                                            }
                                        }
                                    } else if ($magentoAttributeType == 'multiselect') {
                                        if (isset($magentoAttributeValue) && $magentoAttributeValue != '') {
                                            $optinValues = explode(',', $magentoAttributeValue);
                                            $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                            $realIds = array();
                                            foreach($optinValues as $optinValue)
                                            {
                                                $allOptions = $attributeDetails->getSource()->getAllOptions();
                                                foreach($allOptions as $singleOptin)
                                                {
                                                    if($singleOptin['label'] == $optinValue)
                                                    {
                                                        $realIds[] = $singleOptin['value'];
                                                    }
                                                }
                                            }
                                            if (isset($realIds) && !empty($realIds)) {
                                                $acumaticaFieldValue = implode(',', $realIds);
                                                break;
                                            }
                                        }
                                    } else {
                                        if (isset($magentoAttributeValue))
                                            $acumaticaFieldValue = $magentoAttributeValue;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($key == "visibility") {
                    if ($acumaticaFieldValue != '') {
                        $acumaticaFieldValue = $visibilityClass[$acumaticaFieldValue];
                    } else {
                        $acumaticaFieldValue = "1";
                    }
                }
                if ($key == "description") {
                    if ($acumaticaFieldValue == '') {
                        if (isset($aData['Description']['Value']))
                            $acumaticaFieldValue = $aData['Description']['Value'];

                    } else {

                        $acumaticaFieldValue = $acumaticaFieldValue;
                    }
                }
                if ($key == "status") {
                    if ($acumaticaFieldValue == "true") {
                        $acumaticaFieldValue = 1;
                    } else {
                        $acumaticaFieldValue = 2;
                    }
                }
                $syncData[$key] = $acumaticaFieldValue;
            }

            /**
             * replacing spaces with underscore
             */
            if(isset($syncData['sku']) && $syncData['sku'] != '')
            {
                $syncData['sku'] = str_replace(" ","_",$syncData['sku']);
            }
            /**
             * get attribute set id based on
             */
            if (!empty($syncData)) {
                if (isset($aData['ItemClass']['Value']))
                    $itemClassName = $aData['ItemClass']['Value'];
                else
                    $itemClassName = '';
                if ($itemClassName != '') {
                    $entityTypeId = $this->entityModel->setType('catalog_product')->getTypeId();
                    $attributeSetId = $this->productResourceModel->getAttributeSetId($entityTypeId, $itemClassName);
                    if (isset($attributeSetId) && $attributeSetId != '') {
                        $syncData['attribute_set'] = $attributeSetId;
                    }
                }
                /**
                 * get item status to sync product.if it is in active the product can not be sync
                 * get product status for disable/enable.
                 */
                if (isset($aData['ItemStatus']['Value']))
                    $itemStatus = $aData['ItemStatus']['Value'];
                else
                    $itemStatus = '';
                if (isset($aData['Active']['Value']))
                    $productActive = $aData['Active']['Value'];
                else
                    $productActive = '';

                if (isset($syncData['tax_class_id']) && $syncData['tax_class_id'] != '') {
                    if ($this->productResourceModel->getTaxClassId($syncData['tax_class_id']) != '')
                        $syncData['tax_class_id'] = $this->productResourceModel->getTaxClassId($syncData['tax_class_id']);
                    else
                        $syncData['tax_class_id'] = '';
                }

                try {
                    /**
                     * checking condition that required fields
                     */
                    if (isset($syncData['attribute_set']))
                        if ($syncData['attribute_set'] != '' && $itemStatus == 'Active' && $productActive == "true") {
                            if ($syncData['sku'] != '' && $syncData['name'] != '' && $syncData['price'] != '' && $syncData['description'] != '' && $syncData['short_description'] != '' && $syncData['weight'] != '' && $syncData['status'] != '' && $syncData['tax_class_id'] != '') {
                                if ($storeId > 1) {
                                    $storeInfo = $this->storeManager->load($storeId);
                                    $syncData["store"] = $storeInfo->getCode();
                                    $websiteInfo = $this->storeManager->load($storeInfo->getWebsiteId());
                                    $syncData["websites"] = $websiteInfo->getCode();
                                } else {
                                    $syncData["websites"] = 1;
                                }

                                $syncInMagento = $this->productResourceModel->createSimpleProduct($syncData, $storeId, array(), array(), array());

                                /**
                                 * logs here for sync success
                                 */
                                $productData = $productData = $this->productFactory->create()->loadByAttribute('sku', $syncData['sku']);
                                if (isset($productData) && !empty($productData)) {
                                    $productArray['storeId'] = $storeId;
                                    $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                    $productArray['description'] = "SKU " . $syncData['sku'] . " inserted in Magento"; //Descripton
                                    $productArray['syncAction'] = "Product Inserted Into Magento";
                                    $productArray['productId'] = $productData->getId(); //Manual/Auto/Individual
                                    $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                                    if ($autoSync == 'COMPLETE') {
                                        $productArray['action'] = "Batch Process";//This needs to be dynamic value
                                    } elseif ($autoSync == 'INDIVIDUAL') {
                                        $productArray['action'] = "Individual";//This needs to be dynamic value
                                    }
                                    $productArray['acumaticaStockItem'] = $syncData['sku'];
                                    $productArray['syncDirection'] = "syncToMagento";
                                    $productArray['messageType'] = "Success";
                                    $this->productSyncLogs->productSyncSuccessLogs($productArray);
                                    $txt = "Info : " . $productArray['description'] . " successfully";
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                }
                            } else {
                                /**
                                 * logs here for failure
                                 */
                                $productArray['storeId'] = $storeId;
                                $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                $productArray['description'] = "Mandatory fields must be filled to sync product(s)"; //Descripton
                                $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                                if ($autoSync == 'COMPLETE') {
                                    $productArray['action'] = "Batch Process";//This needs to be dynamic value
                                } elseif ($autoSync == 'INDIVIDUAL') {
                                    $productArray['action'] = "Individual";//This needs to be dynamic value
                                }
                                $productArray['syncAction'] = "Product Not Synced";
                                $productArray['acumaticaStockItem'] = $syncData['sku'];
                                $productArray['productId'] = '';
                                $productArray['syncDirection'] = "syncToMagento";
                                $productArray['messageType'] = "Failure";
                                $txt = "Error: Acumatica Stock Item : " . $syncData['sku'] . " : " . $productArray['description'] . " Please try again";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->productSyncLogs->productSyncSuccessLogs($productArray);
                                $this->errorCheckInMagento[] = 1;
                            }
                        }
                } catch (Exception $e) {
                    /**
                     *logs here to print exception
                     */
                    $productArray['storeId'] = $storeId;
                    $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                    $productArray['description'] = $e->getMessage(); //Descripton
                    $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                    if ($autoSync == 'COMPLETE') {
                        $productArray['action'] = "Batch Process";//This needs to be dynamic value
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $productArray['action'] = "Individual";//This needs to be dynamic value
                    }
                    $productArray['syncAction'] = "Product Not Synced";
                    $productArray['acumaticaStockItem'] = $syncData['sku'];
                    $productArray['syncDirection'] = "syncToMagento";
                    $productArray['messageType'] = "Failure";
                    $txt = "Info : " . $productArray['syncAction'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->productSyncLogs->productSyncSuccessLogs($productArray);
                    $this->errorCheckInMagento[] = 1;
                }
                $productResultData = $this->productFactory->create()->loadByAttribute('sku', $syncData['sku']);
                if (isset($productResultData) && !empty($productResultData)) {
                    return $syncData['sku'];
                } else {
                    return $this->errorCheckInMagento;
                }
            }

    }

    /**
     * @param $aData
     * @param $acumaticaAttributes
     * @param $syncType
     * @param $autoSync
     * @param $syncLogID
     * @param $storeId
     * @param $logViewFileName
     * @param $directionFlag
     * @param $url
     * @param null $configurator
     * @param $scopeType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag,$url, $configurator = NULL,$scopeType)
    {

        $magentoSku = str_replace("_"," ",$aData['magento_sku']);
        $magentoId = $aData['magento_id'];
        if ($magentoId) {
            $visibilityClass = array("1" => "Not Visible Individually", "2" => "Catalog", "3" => "Search", "4" => "Catalog, Search");
            $_product = $this->productFactory->create()->setStoreId($storeId)->load($magentoId);

            $nonStockItemStatus = $this->productResourceModel->getIsNonStock($_product->getRowId());
            $nonStockItemFlg = NULL;
            if($nonStockItemStatus == 1)
            {
                $nonStockItemFlg = 1;
            }

            $upsellCollection = $_product->getUpSellProductCollection();
            $crossellCollection = $_product->getCrossSellProducts();
            $data = $_product->getData();
            $syncData = array();
            $attributeData = array();
            foreach ($acumaticaAttributes as $key => $value) {
                $mappingData = explode('|', $value);
                if ($directionFlag && $mappingData[1] == "Bi-Directional (Acumatica Wins)") {
                    continue;
                }
                if ($mappingData[0] != '') {
                    $acumaticaLabel = $this->productResourceModel->getAcumaticaAttrCode($mappingData[0]);
                }
                $acumaticaAttrCode = explode(" ", $acumaticaLabel); //array[0] will be section and array[1] will be attribute code
                if ($acumaticaAttrCode[0] == "ProductSchema") {
                    if ($acumaticaAttrCode[1] == 'Visibility') {
                        $syncData[$acumaticaAttrCode[1]] = $visibilityClass[$data[$key]];

                    } elseif ($acumaticaAttrCode[1] == 'Active') {
                        if ($data[$key] == 1)
                            $syncData[$acumaticaAttrCode[1]] = "true";
                        else
                            $syncData[$acumaticaAttrCode[1]] = "false";
                    } elseif ($acumaticaAttrCode[1] == 'Description') {
                        $syncData[$acumaticaAttrCode[1]] = $data['name'];
                    } elseif ($acumaticaAttrCode[1] == 'TaxCategory') {
                        $taxClassName = $this->productResourceModel->getTaxClassName($data[$key]);
                        $syncData[$acumaticaAttrCode[1]] = $taxClassName;
                    } elseif ($acumaticaAttrCode[1] == 'Featured' || $acumaticaAttrCode[1] == 'BestSeller' || $acumaticaAttrCode[1] == 'AllowReviews' || $acumaticaAttrCode[1] == 'IsaKit' || $acumaticaAttrCode[1] == 'QuoteItem' || $acumaticaAttrCode[1] == 'ItemStatus' || $acumaticaAttrCode[1] == 'Type' || $acumaticaAttrCode[1] == 'Default') {
                        if (isset($data[$key]) && $data[$key] != '') {
                            $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute($key);
                            $optionValue = $attributeDetails->getSource()->getOptionText($data[$key]);
                            $syncData[$acumaticaAttrCode[1]] = $optionValue;
                        }
                    } else {
                        if (isset($data[$key]))
                            if ($data[$key] != '') {
                                $syncData[$acumaticaAttrCode[1]] = $data[$key];
                            }
                    }
                } else {
                    if (isset($data[strtolower($acumaticaAttrCode[0])])) {
                        /**
                         * get type of a product attribute by code
                         */
                        $_attribute = $this->abstractAttribute->create()->loadByCode('4', strtolower($acumaticaAttrCode[0]));
                        if ($_attribute->getFrontendInput() == 'select') {
                            $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute($_attribute->getAttributeId());
                            $attributeData[$acumaticaAttrCode[0]] = $attributeDetails->getSource()->getOptionText($data[strtolower($acumaticaAttrCode[0])]);
                        } elseif ($_attribute->getFrontendInput() == 'multiselect') {
                            $optinIds = explode(',', $data[strtolower($acumaticaAttrCode[0])]);
                            $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute($_attribute->getAttributeId());
                            $realValues = array();
                            foreach ($optinIds as $optionId) {
                                $realValues[] = $attributeDetails->getSource()->getOptionText($optionId);
                            }
                            $attributeData[$acumaticaAttrCode[0]] = implode(',', $realValues);
                        } elseif ($_attribute->getFrontendInput() == 'text') {
                            $attributeData[$acumaticaAttrCode[0]] = $data[strtolower($acumaticaAttrCode[0])];
                        } elseif ($_attribute->getFrontendInput() == 'boolean') {
                            $acuAttributeValue = $data[strtolower($acumaticaAttrCode[0])];
                            if ($acuAttributeValue == 1) {
                                $attributeData[$acumaticaAttrCode[0]] = 'True';
                            } else {
                                $attributeData[$acumaticaAttrCode[0]] = 'False';
                            }
                        } elseif ($_attribute->getFrontendInput() == 'date') {
                            $attributeData[$acumaticaAttrCode[0]] = $data[strtolower($acumaticaAttrCode[0])];
                        }
                    }
                }
            }
            /**
             * here need to add item class
             */
            $productAvailable = false;
            $createFlag = 0;
            $updateFlag = 0;
            if (!empty($syncData)) {

                /**
                 * need to  check whether sku have any underscore values
                 * if have then we need to replace with the space
                 */
                if(isset($syncData['InventoryID']) && $syncData['InventoryID'] != '')
                {
                    $syncData['InventoryID'] = str_replace("_"," ",$syncData['InventoryID']);
                }

                if ($data['attribute_set_id'] != '') {
                    $syncData['ItemClass'] = $this->productResourceModel->getAttributeSetName('4', $data['attribute_set_id']);
                }
                /**
                 * check condition that required fields should not empty
                 * create envelope with the data
                 */
                $productAvailable = $this->productHelper->getProductBySku($url, $magentoSku, $storeId,$nonStockItemFlg);

            if ($productAvailable) {
                if (isset($syncData['InventoryID']) && $syncData['InventoryID'] != '') {
                    $createFlag = 1;
                    $updateFlag = 1;
                }
            } else {
                if (isset($syncData['InventoryID']) && $syncData['InventoryID'] != '' && isset($syncData['ItemClass']) && $syncData['ItemClass'] != '' && isset($syncData['TaxCategory']) && $syncData['TaxCategory'] != '' && $syncData['Description'] != '') {
                    $createFlag = 1;
                    $updateFlag = 0;
                }
            }
            if ($createFlag) {
                /**
                 * here we need to add category mapping
                 * get categories of product
                 * check that category exist in acumatica
                 * if exist assign to product
                 * if not exist create category and assign to product
                 */

                if ($configurator == NULL) {
                    $getCategories = $_product->getCategoryIds();
                    if (!empty($getCategories)) {
                        $acumaticaCatIds = array();
                        $categoryRequestData = array();
                        $webServiceUrl = $this->urlHelper->getNewWebserviceUrl($scopeType, $storeId);
                        foreach ($getCategories as $categoryKey => $category) {
                            $categoryData = $this->categoryFactory->create()->load($category);
                            $acumaticaCategoryId = $categoryData->getAcumaticaCategoryId();
                            if ($acumaticaCategoryId != '') {
                                $acumaticaCatIds[] = $acumaticaCategoryId;

                            } else {
                                $magentoPath = $categoryData->getPath();
                                $acumaticaPath = $this->categoryResourceModel->getIndividualAcumaticaTreePath($scopeType, $storeId, $magentoPath);
                                $categoryRequestData['magento_category_id'] = $category;
                                $categoryRequestData['acumatica_category_id'] = '';
                                $categoryRequestData['acumatica_category_path'] = $acumaticaPath;
                                $categoryMappingAttributes = $this->categoryResourceModel->getAcumaticaAttributes($storeId);
                                $result = $this->categoryModel->syncToAcumatica($categoryRequestData, $categoryMappingAttributes, $syncType, $autoSync, $syncLogID, $scopeType, $storeId, $logViewFileName, NULL, $url, $webServiceUrl);
                                $categoryDataAfterSync = $this->categoryFactory->create()->load($categoryRequestData['magento_category_id']);
                                if (isset($categoryDataAfterSync) && $categoryDataAfterSync->getAcumaticaCategoryId() != '') {
                                    $acumaticaCatIds[] = $categoryDataAfterSync->getAcumaticaCategoryId();
                                }
                            }
                        }
                    }
                    /**
                     * get upsell & crosssell products
                     * assign if products are already in acumatica otherwise create them in acumatica and assign
                     * Upsell
                     */
                    if (count($upsellCollection)) {
                        $upsellArray = array();
                        foreach ($upsellCollection as $upsell) {
                            $upsellId = $upsell->getId();
                            $upsellProduct = $this->productFactory->create()->load($upsellId);
                            $upsellSku = str_replace("_"," ",$upsellProduct->getSku());
                            /**
                             * check availability of product in acumatca
                             */
                            $upsellProductAvailable = $this->productHelper->getProductBySku($url, $upsellSku, $storeId);
                            if ($upsellProductAvailable) {
                                $upsellArray[] = $upsellSku;
                            } /*else {
                                $upsellArray[] = $this->createProductInAcumatica($upsellProduct->getSku(), $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                            }*/
                        }
                    }
                    /**
                     * crossell
                     */
                    if (count($crossellCollection)) {
                        $crossellArray = array();
                        foreach ($crossellCollection as $crossell) {
                            $crossellId = $crossell->getId();
                            $crossellProduct = $this->productFactory->create()->load($crossellId);
                            $crossellSku = str_replace("_"," ",$crossellProduct->getSku());
                            /**
                             * check availability of product in acumatca
                             */
                            $crossellProductAvailable = $this->productHelper->getProductBySku($url, $crossellSku, $storeId);
                            if ($crossellProductAvailable) {
                                $crossellArray[] = $crossellSku;
                            }/* else {
                                $crossellArray[] = $this->createProductInAcumatica($crossellProduct->getSku(), $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                            }*/
                        }
                    }
                }
                $createProductEnvelope = '';
                foreach ($syncData as $key => $_value) {
                    if ($_value != '') {
                        $_value = str_replace("'", "=&apos;", $_value);
                        $createProductEnvelope .= '<' . $key . '><Value><![CDATA[' . htmlspecialchars(trim($_value)) . ']]</Value><HasError>false</HasError></' . $key . '>';
                    }
                }

                /* For custom attribute */
                if (count($attributeData)) {
                    $createProductEnvelope .= '<Attributes>';
                    foreach ($attributeData as $attKey => $attValue) {
                        if ($attValue != '') {
                            $createProductEnvelope .= '<AttributeValue><ID xsi:nil="true" /><Delete>false</Delete><ReturnBehavior>All</ReturnBehavior><AttributeID><Value><![CDATA[' . trim($attKey) . ']]</Value></AttributeID><Value><Value><![CDATA[' . htmlspecialchars(trim($attValue)) . ']]</Value></Value></AttributeValue>';
                        }
                    }
                    $createProductEnvelope .= '</Attributes>';

                }

                /**
                 * Here mapping is done for category
                 * need to be remove 0 after envelope works
                 */
                if (!empty($acumaticaCatIds) && $configurator == NULL) {
                    $implodeCategories = implode(",", $acumaticaCatIds);
                    $createProductEnvelope .= '<CategoryID>';
                    $createProductEnvelope .= '<Value><![CDATA[' . trim($implodeCategories) . ']]</Value><HasError>false</HasError>';
                    $createProductEnvelope .= '</CategoryID>';
                }

                if (!empty($crossellArray) && count($crossellArray) > 0 && $configurator == NULL) {
                    $implodeCrossell = implode(",", $crossellArray);
                    $createProductEnvelope .= '<CrossSellID>';
                    $createProductEnvelope .= '<Value><![CDATA[' . trim($implodeCrossell) . ']]</Value><HasError>false</HasError>';
                    $createProductEnvelope .= '</CrossSellID>';
                }
                if (!empty($upsellArray) && count($upsellArray) > 0 && $configurator == NULL) {
                    $implodeUpsell = implode(",", $upsellArray);
                    $createProductEnvelope .= '<UpSellID>';
                    $createProductEnvelope .= '<Value><![CDATA[' . trim($implodeUpsell) . ']]</Value><HasError>false</HasError>';
                    $createProductEnvelope .= '</UpSellID>';
                }

                if($nonStockItemFlg == 1)
                {
                    $createProductEnvelope .= '<ItemType><Value>Non-Stock Item</Value></ItemType>';
                    $csvCreateProduct = $this->common->getEnvelopeData('CREATENONSTOCKPRODUCT');
                }else {
                    $csvCreateProduct = $this->common->getEnvelopeData('CREATEPRODUCT');
                }
                $envCreateProduct = $csvCreateProduct['envelope'];
                $createProductEnvelope = str_replace('{{CREATEPRODUCT}}', $createProductEnvelope, $envCreateProduct);
                $createAction = $csvCreateProduct['envName'] . '/' . $csvCreateProduct['envVersion'] . '/' . $csvCreateProduct['methodName'];

                /**
                 * check the product exist or not in acumatica
                 */
                try {
                    $XMLGetRequest = $createProductEnvelope;
		    $configParameters = $this->dataHelper->getConfigParameters($storeId);
                    $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $createAction);
                    if(isset($xml->Body->PutResponse->PutResult) && is_object($xml->Body->PutResponse->PutResult))
                    {
                        $data = $xml->Body->PutResponse->PutResult;
                        $totalData = $this->xmlHelper->xml2array($data);
                        $inventoryID = $totalData['InventoryID']['Value'];
                        if ($updateFlag) {
                            /**
                             *update product logs
                             */
                            if ($inventoryID != '') {
                                $productArray['storeId'] = $storeId;
                                $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                $productArray['productId'] = $magentoId; //Current Magento Product Id
                                $productArray['acumaticaStockItem'] = $inventoryID;
                                $productArray['description'] = "Product Id:" . $inventoryID . " updated in Acumatica"; //Descripton
                                $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                                if ($autoSync == 'COMPLETE') {
                                    $productArray['action'] = "Batch Process";//This needs to be dynamic value
                                } elseif ($autoSync == 'INDIVIDUAL') {
                                    $productArray['action'] = "Individual";//This needs to be dynamic value
                                }
                                $productArray['syncAction'] = "Product Synced To Acumatica";
                                $productArray['syncDirection'] = "syncToAcumatica";
                                $productArray['messageType'] = "Success";
                                $this->productSyncLogs->productSyncSuccessLogs($productArray);
                                $txt = "Info : " . $productArray['description'] . " successfully!";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            } else {
                                $productArray['storeId'] = $storeId;
                                $productArray['schedule_id'] = $syncLogID;
                                $productArray['description'] = json_encode($xml);
                                $productArray['productId'] = $magentoId;
                                $productArray['acumaticaStockItem'] = '';
                                $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                                if ($autoSync == 'COMPLETE') {
                                    $productArray['action'] = "Batch Process";//This needs to be dynamic value
                                } elseif ($autoSync == 'INDIVIDUAL') {
                                    $productArray['action'] = "Individual";//This needs to be dynamic value
                                }
                                $productArray['syncAction'] = "Product Not Synced To Acumatica";
                                $productArray['syncDirection'] = "syncToAcumatica";
                                $productArray['messageType'] = "Failure";
                                $this->productSyncLogs->productSyncSuccessLogs($productArray);
                                $txt = "Error: " . $productArray['description'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->errorCheckInAcumatica[] = 1;
                            }
                        } else {
                            /**
                             * create product logs
                             */
                            if ($inventoryID != '') {
                                $productArray['storeId'] = $storeId;
                                $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                $productArray['productId'] = $magentoId; //Current Magento Product Id
                                $productArray['acumaticaStockItem'] = $inventoryID;
                                $productArray['description'] = "Product Id:" . $inventoryID . " created in Acumatica"; //Descripton
                                $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                                if ($autoSync == 'COMPLETE') {
                                    $productArray['action'] = "Batch Process";//This needs to be dynamic value
                                } elseif ($autoSync == 'INDIVIDUAL') {
                                    $productArray['action'] = "Individual";//This needs to be dynamic value
                                }
                                $productArray['syncAction'] = "Product Synced To Acumatica";
                                $productArray['syncDirection'] = "syncToAcumatica";
                                $productArray['messageType'] = "Success";
                                $this->productSyncLogs->productSyncSuccessLogs($productArray);
                                $txt = "Info : " . $productArray['description'] . " successfully!";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            } else {
                                $productArray['storeId'] = $storeId;
                                $productArray['schedule_id'] = $syncLogID;
                                $productArray['description'] = json_encode($xml);
                                $productArray['productId'] = $magentoId;
                                $productArray['acumaticaStockItem'] = $syncData['InventoryID'];
                                $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                                if ($autoSync == 'COMPLETE') {
                                    $productArray['action'] = "Batch Process";//This needs to be dynamic value
                                } elseif ($autoSync == 'INDIVIDUAL') {
                                    $productArray['action'] = "Individual";//This needs to be dynamic value
                                }
                                $productArray['syncAction'] = "Product Not Synced To Acumatica";
                                $productArray['syncDirection'] = "syncToAcumatica";
                                $productArray['messageType'] = "Failure";
                                $this->productSyncLogs->productSyncSuccessLogs($productArray);
                                $txt = "Error: " . $productArray['description'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->errorCheckInAcumatica[] = 1;
                            }
                        }
                    }else{
                        $productArray['storeId'] = $storeId;
                        $productArray['schedule_id'] = $syncLogID;
                        $productArray['description'] = json_encode($xml);
                        $productArray['productId'] = $magentoId;
                        $productArray['acumaticaStockItem'] = $syncData['InventoryID'];
                        $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                        if ($autoSync == 'COMPLETE') {
                            $productArray['action'] = "Batch Process";//This needs to be dynamic value
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $productArray['action'] = "Individual";//This needs to be dynamic value
                        }
                        $productArray['syncAction'] = "Product Not Synced To Acumatica";
                        $productArray['syncDirection'] = "syncToAcumatica";
                        $productArray['messageType'] = "Failure";
                        $this->productSyncLogs->productSyncSuccessLogs($productArray);
                        $txt = "Error: Acumatica Stock Item : " . $syncData['InventoryID'] . " : " . $productArray['description'] . " Please try again";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $this->errorCheckInAcumatica[] = 1;
                    }
                } catch (SoapFault $e) {
                    $productArray['storeId'] = $storeId;
                    $productArray['schedule_id'] = $syncLogID;
                    $productArray['description'] = $e->getMessage();
                    $productArray['productId'] = $magentoId;
                    $productArray['acumaticaStockItem'] = $syncData['InventoryID'];
                    $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                    if ($autoSync == 'COMPLETE') {
                        $productArray['action'] = "Batch Process";//This needs to be dynamic value
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $productArray['action'] = "Individual";//This needs to be dynamic value
                    }
                    $productArray['syncAction'] = "Product Not Synced To Acumatica";
                    $productArray['syncDirection'] = "syncToAcumatica";
                    $productArray['messageType'] = "Failure";
                    $this->productSyncLogs->productSyncSuccessLogs($productArray);
                    $txt = "Info : " . $productArray['description'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->errorCheckInAcumatica[] = 1;
                }
            } else {
                $msg = "Mandatory fields must be filled to sync product(s)";
                if ($productAvailable) {
                    if ($syncData['InventoryID'] == '')
                        $msg = 'Product SKU mapping is not proper';
                }
                $productArray['storeId'] = $storeId;
                $productArray['schedule_id'] = $syncLogID;
                $productArray['description'] = $msg;
                $productArray['productId'] = $magentoId;
                $productArray['acumaticaStockItem'] = '';
                $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                if ($autoSync == 'COMPLETE') {
                    $productArray['action'] = "Batch Process";//This needs to be dynamic value
                } elseif ($autoSync == 'INDIVIDUAL') {
                    $productArray['action'] = "Individual";//This needs to be dynamic value
                }
                $productArray['syncAction'] = "Product Not Synced To Acumatica";
                $productArray['syncDirection'] = "syncToAcumatica";
                $productArray['messageType'] = "Failure";
                $this->productSyncLogs->productSyncSuccessLogs($productArray);
                $txt = "Error: " . $productArray['description'] . "  Please try again";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->errorCheckInAcumatica[] = 1;
            }
          }
        }
        return $this->errorCheckInAcumatica;
    }

    /**
     * @param $sku
     * @param $acumaticaAttributes
     * @param $syncType
     * @param $autoSync
     * @param $syncLogID
     * @param $storeId
     * @param $logViewFileName
     * @param $directionFlag
     * @param $url
     * @return string
     */
    public function createProductInAcumatica($sku, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag,$url)
    {
	$configParameters = $this->dataHelper->getConfigParameters($storeId);
        $magentoSku = $sku;
        $_product = $this->productFactory->create()->loadByAttribute('sku', $magentoSku);
        if (!empty($_product)) {
            $magentoId = $_product->getId();
        }
        if ($magentoId) {
            $visibilityClass = array("1" => "Not Visible Individually", "2" => "Catalog", "3" => "Search", "4" => "Catalog, Search");
            $product = $this->productFactory->create()->load($magentoId);
            $data = $product->getData();
            $syncData = array();

            foreach ($acumaticaAttributes as $key => $value) {
                $mappingData = explode('|', $value);
                if ($directionFlag && $mappingData[1] == "Bi-Directional (Acumatica Wins)") {
                    continue;
                }
                if($mappingData[0] != '') {
                    $acumaticaLabel = $this->productResourceModel->getAcumaticaAttrCode($mappingData[0]);
                }
                $acumaticaAttrCode = explode(" ", $acumaticaLabel); //array[0] will be section and array[1] will be attribute code
                if ($acumaticaAttrCode[0] == "ProductSchema") {
                    if ($acumaticaAttrCode[1] == 'Visibility') {
                        $syncData[$acumaticaAttrCode[1]] = $visibilityClass[$data[$key]];

                    } elseif ($acumaticaAttrCode[1] == 'Active') {
                        if ($data[$key] == 1)
                            $syncData[$acumaticaAttrCode[1]] = "true";
                        else
                            $syncData[$acumaticaAttrCode[1]] = "false";
                    } elseif ($acumaticaAttrCode[1] == 'Description') {
                        $syncData[$acumaticaAttrCode[1]] = $data['name'];
                    } elseif ($acumaticaAttrCode[1] == 'TaxCategory') {
                        $taxClassName = $this->productResourceModel->getTaxClassName($data[$key]);
                        $syncData[$acumaticaAttrCode[1]] = $taxClassName;
                    }elseif ($acumaticaAttrCode[1] == 'Featured' || $acumaticaAttrCode[1] == 'BestSeller' || $acumaticaAttrCode[1] == 'AllowReviews' || $acumaticaAttrCode[1] == 'IsaKit' || $acumaticaAttrCode[1] == 'QuoteItem' || $acumaticaAttrCode[1] == 'ItemStatus' || $acumaticaAttrCode[1] == 'Type' || $acumaticaAttrCode[1] == 'Default')
                    {
                        if($data[$key] != '') {
                            $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute($key);
                            $optionValue = $attributeDetails->getSource()->getOptionText($data[$key]);
                            $syncData[$acumaticaAttrCode[1]] = $optionValue;
                        }
                    }else{
                        if($data[$key] != '')
                        {
                            $syncData[$acumaticaAttrCode[1]] = $data[$key];
                        }
                    }
                }
            }
            /**
             * here need to add item class
             */
            if($data['attribute_set_id'] != '')
            {
                $syncData['ItemClass'] = $this->productResourceModel->getAttributeSetName('4',$data['attribute_set_id']);
            }
            if ($syncData['InventoryID'] != '' && $syncData['ItemClass'] != '' && $syncData['TaxCategory'] != '' && $syncData['Description'] != '' && $syncData['Weight'] != '')
            {
                /**
                 * check condition that required fields should not empty
                 * create envelope with the data
                 */
                $syncData['InventoryID'] = str_replace('_',' ',$syncData['InventoryID']);
                $createProductEnvelope = '';
                foreach ($syncData as $key => $_value) {
                    if($_value != '')
                    {
                        $_value = str_replace("'","=&apos;",$_value);
                        $createProductEnvelope .= '<' . $key . '><Value><![CDATA[' . trim($_value) . ']]</Value><HasError>false</HasError></' . $key . '>';
                    }
                }

                $csvCreateProduct = $this->common->getEnvelopeData('CREATEPRODUCT');
                $envCreateProduct = $csvCreateProduct['envelope'];
                $createProductEnvelope = str_replace('{{CREATEPRODUCT}}',$createProductEnvelope,$envCreateProduct);
                $createAction = $csvCreateProduct['envName'].'/'.$csvCreateProduct['envVersion'].'/'.$csvCreateProduct['methodName'];

                try {

                    try{
                        $XMLGetRequest = $createProductEnvelope;
                        $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $createAction);
                        $data = $xml->Body->PutResponse->PutResult;
                        $totalData = $this->xmlHelper->xml2array($data);
                        $inventoryID = $totalData['InventoryID']['Value'];
                        /**
                         * create product logs
                         */
                        if ($inventoryID != '') {
                            $productArray['storeId'] = $storeId;
                            $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                            $productArray['productId'] = $magentoId; //Current Magento Product Id
                            $productArray['acumaticaStockItem'] = $inventoryID;
                            $productArray['description'] = "Product Id:" . $inventoryID . " inserted to Acumatica"; //Descripton
                            $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                            if ($autoSync == 'COMPLETE') {
                                $productArray['action'] = "Batch Process";//This needs to be dynamic value
                            } elseif ($autoSync == 'INDIVIDUAL') {
                                $productArray['action'] = "Individual";//This needs to be dynamic value
                            }
                            $productArray['syncAction'] = "Product Synced To Acumatica";
                            $productArray['syncDirection'] = "syncToAcumatica";
                            $productArray['messageType'] = "Success";
                            $this->productSyncLogs->productSyncSuccessLogs($productArray);
                            $txt = "Info : " . $productArray['description']." successfully!";
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        }
                    }catch (SoapFault $ex){
                        $productArray['storeId'] = $storeId;
                        $productArray['schedule_id'] = $syncLogID;
                        $productArray['description'] = $ex->getMessage();
                        $productArray['productId'] = $magentoId;
                        $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                        if ($autoSync == 'COMPLETE') {
                            $productArray['action'] = "Batch Process";//This needs to be dynamic value
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $productArray['action'] = "Individual";//This needs to be dynamic value
                        }
                        $productArray['syncAction'] = "Product Not Synced";
                        $productArray['syncDirection'] = "syncToAcumatica";
                        $productArray['messageType'] = "Failure";
                        $this->productSyncLogs->productSyncSuccessLogs($productArray);
                        $txt = "Info : " .  $productArray['description'];
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $this->errorCheckInAcumatica[] = 1;
                    }
                } catch (SoapFault $e) {
                    $productArray['storeId'] = $storeId;
                    $productArray['schedule_id'] = $syncLogID;
                    $productArray['description'] = $e->getMessage();
                    $productArray['productId'] = $magentoId;
                    $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                    if ($autoSync == 'COMPLETE') {
                        $productArray['action'] = "Batch Process";//This needs to be dynamic value
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $productArray['action'] = "Individual";//This needs to be dynamic value
                    }
                    $productArray['syncAction'] = "Product Not Synced";
                    $productArray['syncDirection'] = "syncToAcumatica";
                    $productArray['messageType'] = "Failure";
                    $this->productSyncLogs->productSyncSuccessLogs($productArray);
                    $txt = "Info : " .  $productArray['description'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->errorCheckInAcumatica[] = 1;
                }
            }
        }
        if($inventoryID != '')
        {
            return $inventoryID;
        }else{
            return $this->errorCheckInAcumatica;
        }
    }
}
