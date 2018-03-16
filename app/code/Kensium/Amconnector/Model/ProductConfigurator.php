<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Config\Definition\Exception\Exception;

class ProductConfigurator extends \Magento\Framework\Model\AbstractModel
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
     * @var ResourceModel\ProductConfigurator
     */
    protected $productConfiguratorResourceModel;
    /**
     * @var \Kensium\Amconnector\Helper\Url
     */
    protected $urlHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var
     */
    protected $productOptionRepositoryInterface;

    /**
     * @var
     */
    protected $linkInterfaceFactory;

    /**
     * @var
     */
    protected $optionInterfaceFactory;

    /**
     * @var \Magento\Catalog\Api\Data\ProductLinkInterface
     */
    protected $productLinkInterface;

    /**
     * @var \Kensium\Amconnector\Helper\ProductConfigurator
     *
     */
    protected $productConfigurator;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    protected $attributeRepositoryInterface;

    /**
     * @var \Magento\ConfigurableProduct\Helper\Product\Options\Factory
     */
    protected $optionFactory;

    /**
     * @var
     */
    protected $processorFactory;

    /**
     * @var string
     */
    protected $acumaticaGender;

    /**
     * @param Logger $logger
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Product $productResourceModel
     * @param \Kensium\Amconnector\Helper\Data $dataHelper
     * @param \Kensium\Amconnector\Helper\Url $urlHelper
     * @param \Magento\Bundle\Api\ProductOptionRepositoryInterface $productOptionRepositoryInterface
     * @param \Magento\Bundle\Api\Data\OptionInterfaceFactory $optionInterfaceFactory
     * @param \Magento\Bundle\Api\Data\LinkInterfaceFactory $linkInterfaceFactory
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Kensium\Amconnector\Helper\Category $categoryHelper
     * @param \Kensium\Amconnector\Helper\ProductConfigurator $productConfigurator
     * @param \Kensium\Amconnector\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\ConfigurableProduct\Helper\Product\Options\Factory $optionFactory
     * @param \Kensium\Synclog\Helper\ProductConfigurator $productConfiguratorSyncLogs
     * @param ResourceModel\Product\Collection $resourceCollection
     * @param ResourceModel\Category $categoryResourceModel
     * @param Category $categoryModel
     * @param \Magento\Eav\Model\Entity $entityModel
     * @param ResourceModel\ProductConfigurator $productConfiguratorResourceModel
     * @param \Kensium\Amconnector\Helper\Xml $xmlHelper
     * @param \Magento\Eav\Model\Entity\AttributeFactory $abstractAttribute
     * @param \Magento\Store\Model\Website $storeManager
     * @param \Magento\Indexer\Model\Processor $processorFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface $productLinkInterface
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepositoryInterface
     * @param array $data
     */
    public function __construct(

        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Kensium\Amconnector\Model\ResourceModel\Product $productResourceModel,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Magento\Bundle\Api\ProductOptionRepositoryInterface $productOptionRepositoryInterface,
        \Magento\Bundle\Api\Data\OptionInterfaceFactory $optionInterfaceFactory,
        \Magento\Bundle\Api\Data\LinkInterfaceFactory $linkInterfaceFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Kensium\Amconnector\Helper\Category $categoryHelper,
        \Kensium\Amconnector\Helper\ProductConfigurator $productConfigurator,
        \Kensium\Amconnector\Helper\Product $productHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\ConfigurableProduct\Helper\Product\Options\Factory $optionFactory,
        \Kensium\Synclog\Helper\ProductConfigurator $productConfiguratorSyncLogs,
        \Kensium\Amconnector\Model\ResourceModel\Product\Collection $resourceCollection = null,
        \Kensium\Amconnector\Model\ResourceModel\Category $categoryResourceModel,
        \Kensium\Amconnector\Model\Category $categoryModel,
        \Magento\Eav\Model\Entity $entityModel,
        \Kensium\Amconnector\Model\ResourceModel\ProductConfigurator $productConfiguratorResourceModel,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Magento\Eav\Model\Entity\AttributeFactory $abstractAttribute,
        \Magento\Store\Model\Website $storeManager,
        \Magento\Indexer\Model\Processor $processorFactory,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection,
        \Magento\Catalog\Api\Data\ProductLinkInterface $productLinkInterface,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepositoryInterface,
        $data = []
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
        $this->ConfiguratorSyncLogs = $productConfiguratorSyncLogs;
        $this->urlHelper = $urlHelper;
        $this->xmlHelper = $xmlHelper;
        $this->abstractAttribute = $abstractAttribute;
        $this->objectManager = $objectManager;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->optionInterfaceFactory = $optionInterfaceFactory;
        $this->linkInterfaceFactory = $linkInterfaceFactory;
        $this->productOptionRepositoryInterface = $productOptionRepositoryInterface;
        $this->productConfiguratorResourceModel = $productConfiguratorResourceModel;
        $this->productConfigurator = $productConfigurator;
        $this->productLinkInterface = $productLinkInterface;
        $this->attributeFactory = $attributeFactory;
        $this->optionFactory = $optionFactory;
        $this->processor = $processorFactory;
        $this->attributeRepositoryInterface = $attributeRepositoryInterface;
        parent::__construct($context, $registry, $productResourceModel, $resourceCollection, []);
    }

    /**
     * constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Kensium\Amconnector\Model\ResourceModel\ProductConfigurator');
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
     
     * @return array
     */
    public function groupedSyncToMagento($aData, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag,$url)
    {

        if (!is_array($aData)) {
            $aData = json_decode(json_encode($aData), 1);
        }
        /**
         * Here we are preparing an array based on the mapping attribute
         */
        $visibilityClass = array("Not Visible Individually" => "1", "Catalog" => "2", "Search" => "3", "Catalog, Search" => "4");
        $syncData = array();
        $quantity = 0;
        foreach ($mappingAttributes as $key => $value) {
            $mappingData = explode('|', $value);
            if ($directionFlag && $mappingData[1] == 'Bi-Directional (Magento Wins)') {
                continue;
            }
            if ($mappingData[0] != '') {
                $acumaticaLabel = $this->productResourceModel->getAcumaticaAttrCode($mappingData[0]);
            }
            $acumaticaAttrCode = explode(" ", $acumaticaLabel); //array[0] will be section and array[1] will be attribute code
            if ($acumaticaAttrCode[0] == "ProductSchema") {
                if ($acumaticaAttrCode[1] == "Weight") {
                    if (isset($aData['GrpPackaging']['Weight']['Value']))
                        $acumaticaFieldValue = $aData['GrpPackaging']['Weight']['Value'];
                    else
                        $acumaticaFieldValue = '';
                } else {
                    if (isset($aData[$acumaticaAttrCode[1]]['Value']))
                        $acumaticaFieldValue = $aData[$acumaticaAttrCode[1]]['Value'];
                    else
                        $acumaticaFieldValue = '';
                }
            } else {
                if (isset($aData['GrpAttributes']['GrpAttributes']) && count($aData['GrpAttributes']['GrpAttributes']) > 0) {
                    $aData['GrpAttributes']['GrpAttributes'] = json_decode(json_encode($aData['GrpAttributes']['GrpAttributes']), 1);
                    $oneAttributeRecordFlag = false;
                    $acumaticaFieldValue = '';
                    foreach ($aData['GrpAttributes']['GrpAttributes'] as $attributeKey => $attributeValue) {
                        if (!is_numeric($attributeKey)) {
                            $oneAttributeRecordFlag = true;
                            break;
                        }
                        if (isset($attributeValue['Attribute']['Value']) && isset($attributeValue['Value']['Value'])) {
                            $magentoAttributeCode = strtolower($attributeValue['Attribute']['Value']);
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
                                }elseif($magentoAttributeType == 'select') {
                                    $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                    $optionId = $attributeDetails->getSource()->getOptionId($magentoAttributeValue);
                                    if (isset($optionId) && $optionId != '') {
                                        $acumaticaFieldValue = $optionId;
                                        break;
                                    }
                                }else if($magentoAttributeType == 'multiselect') {
                                    if(isset($magentoAttributeValue) && $magentoAttributeValue != '') {
                                        $optinValues = explode(',', $magentoAttributeValue);
                                        $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                        $realIds = array();
                                        foreach($optinValues as $optinValue)
                                        {
                                            $realIds[] =$attributeDetails->getSource()->getOptionId($optinValue);
                                        }
                                        if(isset($realIds) && !empty($realIds))
                                        {
                                            $acumaticaFieldValue = implode(',',$realIds);
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
                        if (isset($aData['GrpAttributes']['GrpAttributes']['Attribute']['Value'])) {
                            $magentoAttributeCode = strtolower($aData['GrpAttributes']['GrpAttributes']['Attribute']['Value']);
                            $magentoAttributeValue = $aData['GrpAttributes']['GrpAttributes']['Value']['Value'];
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
                                    $optionId = $attributeDetails->getSource()->getOptionId($magentoAttributeValue);
                                    if (isset($optionId) && $optionId != '') {
                                        $acumaticaFieldValue = $optionId;
                                        break;
                                    }
                                }else if($magentoAttributeType == 'multiselect') {
                                    if(isset($magentoAttributeValue) && $magentoAttributeValue != '') {
                                        $optinValues = explode(',', $magentoAttributeValue);
                                        $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                        $realIds = array();
                                        foreach($optinValues as $optinValue)
                                        {
                                            $realIds[] =$attributeDetails->getSource()->getOptionId($optinValue);
                                        }
                                        if(isset($realIds) && !empty($realIds))
                                        {
                                            $acumaticaFieldValue = implode(',',$realIds);
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
         * Check the child products are assigned to the product or not
         * if yes create grouped product with assigning child
         * if no skip the product to sync
         */
        if (isset($aData['GroupedProducts']['GroupedProducts']) && count($aData['GroupedProducts']['GroupedProducts']) > 0) {
            $childProducts = json_decode(json_encode($aData['GroupedProducts']['GroupedProducts']), 1);
            $selectedChild = array();
            $oneChildRecordFlag = false;
            $position = 0;
            foreach ($childProducts as $childKey => $childValue) {
                if (!is_numeric($childKey)) {
                    $oneChildRecordFlag = true;
                    break;
                }
                if (isset($childValue['InventoryID']['Value'])) {
                    $acumaticaChildSkuReal = str_replace(" ","_",$childValue['InventoryID']['Value']);
                    $qty = trim($childValue['Quantity']['Value']);
                } else {
                    $acumaticaChildSkuReal = str_replace(" ","_",$childValue->InventoryID->Value);
                    $qty = trim($childValue->Quantity->Value);
                }
                $childProduct = $this->productResourceModel->getProductBySku($acumaticaChildSkuReal);
                if (isset($childProduct) && $childProduct != 0) {
                    $childData = $this->productFactory->create()->loadByAttribute('sku', $acumaticaChildSkuReal);
                    $childPrice = $childData->getPrice();
                    $selectedChild[] = array('sku'=>$acumaticaChildSkuReal,'position'=>$position,'qty'=>$qty,'price'=>$childPrice);
                } //else {
                    /* Create child product */
                    /*$selectedChildSku = $this->createProductInMagento($acumaticaChildSkuReal, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url, $cookies);
                    if(isset($selectedChildSku) && $selectedChildSku != ''  && $selectedChildSku == $acumaticaChildSkuReal)
                    {
                        $childData = $this->productFactory->create()->loadByAttribute('sku', $selectedChildSku);
                        $childPrice = $childData->getPrice();
                        $selectedChild[] = array('sku'=>$selectedChildSku,'position'=>$position,'qty'=>$qty,'price'=>$childPrice);

                    }
                }*/
                $position++;
            }
            if ($oneChildRecordFlag) {
                $acumaticaChildSkuReal = str_replace(" ","_",$childProducts['InventoryID']['Value']);
                $qty = trim($childProducts['Quantity']['Value']);
                $childProduct = $this->productResourceModel->getProductBySku($acumaticaChildSkuReal);
                if (isset($childProduct) && $childProduct != 0) {
                    $selectedChildSku = $acumaticaChildSkuReal;
                }/*else {
                    $selectedChildSku = $this->createProductInMagento($acumaticaChildSkuReal, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                }*/
                if(isset($selectedChildSku) && $selectedChildSku != '' && $selectedChildSku == $acumaticaChildSkuReal)
                {
                    $childData = $this->productFactory->create()->loadByAttribute('sku', $selectedChildSku);
                    $childPrice = $childData->getPrice();
                    $selectedChild[] = array('sku'=>$selectedChildSku,'position'=>$position,'qty'=>$qty,'price'=>$childPrice);
                }
            }
        }
        $groupedProductCreateFlag = 0;
        if (!empty($selectedChild) && count($selectedChild) > 0) {
            $groupedProductCreateFlag = 1;
        }

        if ($groupedProductCreateFlag) {
            /**
             * get Item class and check that item class is exist or not
             */
            if (!empty($syncData)) {
                if(isset($aData['ItemClass']['Value']))
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
                 * here we need to add category mapping
                 * get categories of product
                 * check that product exist in magento
                 * if exist assign to product
                 * if not exist create category and assign to product
                 */
                $oneCategoryRecordFlag = false;
                $realCategories = array();
                if (isset($aData['GrpCategories']['GrpCategories']))
                    $getCategories = json_decode(json_encode($aData['GrpCategories']['GrpCategories']), 1);
                if (!empty($getCategories)) {
                    foreach ($getCategories as $catkey => $catValue) {
                        if (!is_numeric($catkey)) {
                            $oneCategoryRecordFlag = true;
                            break;
                        }
                        $categoryId = $catValue['CategoryID']['Value'];
                        /**
                         * Check category exist in magento
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
            /**
             * Get item status to sync product.if it is in active the product can not be sync
             * Get product status for disable/enable.
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
                    if ($syncData['attribute_set'] != '') {
                        if ($aData['InventoryID']['Value'] != '') {
                            $acumaticaInventoryId = str_replace(" ","_",$aData['InventoryID']['Value']);
                        }
                        $_product = $this->productResourceModel->getProductBySku($acumaticaInventoryId);
                        $createFlag = 0;
                        if (isset($_product) && $_product != 0) {
                            if ($syncData['sku'] != '')
                                $createFlag = 1;
                        } else {
                            if ($syncData['sku'] != '' && $syncData['name'] != '' && $syncData['description'] != '' && $syncData['short_description'] != '' && $syncData['weight'] != '' && $syncData['status'] != '' && $syncData['tax_class_id'] != '')
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
                                    $productBeforeData = $this->productFactory->create()->loadByAttribute('sku', $syncData['sku']);
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
                                    $upsellData = array();
                                    $upsell = array();
                                    if (isset($aData['GrpUpSells']['GrpUpSells']))
                                        $upsellData = json_decode(json_encode($aData['GrpUpSells']['GrpUpSells']), 1);
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
                                                $upsell[] = $this->createProductInMagento($acumaticaUpSellSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                            }*/
                                        }
                                        if ($oneUpsellRecordFlag) {
                                            $acumaticaUpSellSku = str_replace(" ","_",$upsellData['InventoryID']['Value']);
                                            $upSellProduct = $this->productResourceModel->getProductBySku($acumaticaUpSellSku);
                                            if ($upSellProduct) {
                                                $upsell[] = $acumaticaUpSellSku;
                                            }/* else {

                                                $upsell[] = $this->createProductInMagento($acumaticaUpSellSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                            }*/
                                        }
                                    }
                                    $crosSellData = array();
                                    $csell = array();
                                    if (isset($aData['GrpCrossSells']['GrpCrossSells']))
                                        $crosSellData = json_decode(json_encode($aData['GrpCrossSells']['GrpCrossSells']), 1);
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
                                                $csell[] = $this->createProductInMagento($acumaticaCrosSellSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                            }*/
                                        }
                                        if ($oneRecordFlag) {
                                            $acumaticaCrosSellSku = str_replace(" ","_",$crosSellData['InventoryID']['Value']);
                                            $crosSellProduct = $this->productResourceModel->getProductBySku($acumaticaCrosSellSku);
                                            if ($crosSellProduct) {
                                                $csell[] = $acumaticaCrosSellSku;
                                            } /*else {
                                                $csell[] = $this->createProductInMagento($acumaticaCrosSellSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                            }*/
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
                                    $syncData["type"] = 'grouped';
                                    if (isset($_product) && $_product != 0)
                                    {
                                        $syncInMagento = $this->productConfiguratorResourceModel->updateGroupedProduct($syncData, $storeId, $realCategories, $upsell, $csell,$selectedChild);
                                    } else {
                                        if(isset($syncData["name"]) && $syncData["name"] != '' && isset($syncData['sku']) && $syncData['sku'] != '')
                                        {
                                            $urlKey = str_replace(" ","-",$syncData["name"])."-".str_replace("_","-",$syncData['sku']);
                                            $syncData["url_key"] = strtolower($urlKey);
                                        }
                                        $syncInMagento = $this->productConfiguratorResourceModel->createGroupedProduct($syncData, $storeId, $realCategories, $upsell, $csell,$selectedChild);
                                    }
                                    /**
                                     * logs here for sync success
                                     */
                                    $productData = $this->productFactory->create()->loadByAttribute('sku', $syncData['sku']);
                                    if (isset($productData) && !empty($productData)) {
                                        $productArray['storeId'] = $storeId;
                                        $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                        if (isset($_product) && $_product != 0) {
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
                                        $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
                                        $txt = "Info : " . $productArray['description'] . " successfully";
                                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                    }
                                }
                            } else {
                                /**
                                 * logs here for failure
                                 */
                                $msg = 'Mandatory fields must be filled to sync product(s)';
                                if (isset($_product) && $_product != 0) {
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
                                $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
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
                $productArray['productId'] = "";
                $productArray['syncAction'] = "Product Not Synced";
                $productArray['acumaticaStockItem'] = $syncData['sku'];
                $productArray['syncDirection'] = "syncToMagento";
                $productArray['messageType'] = "Failure";
                $txt = "Error: " . $productArray['syncAction'];
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
                $this->errorCheckInMagento[] = 1;
            }
        }else{

            /**
             * Log error when group product have no child assigned
             * No need to create group product without assigned child product
             */
            $msg = 'No Child product(s) for '. $syncData['sku'];
            $productSieArray['storeId'] = $storeId;
            $productSieArray['schedule_id'] = $syncLogID;
            $productSieArray['description'] = $msg;
            $productSieArray['runMode'] = $syncType;
            if ($autoSync == 'COMPLETE') {
                $productSieArray['action'] = "Batch Process";
            } elseif ($autoSync == 'INDIVIDUAL') {
                $productSieArray['action'] = "Individual";
            }
            $productSieArray['productId'] = "";
            $productSieArray['syncAction'] = "Product Not Synced";
            $productSieArray['acumaticaStockItem'] = $syncData['sku'];
            $productSieArray['syncDirection'] = "syncToMagento";
            $productSieArray['messageType'] = "Failure";
            $txt = "Error: " . $productSieArray['description'] ." . Please try again";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productSieArray);
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
            $aData = $this->productHelper->getProductBySkuForIndividual($url, $sku,$storeId);
            $visibilityClass = array("Not Visible Individually" => "1", "Catalog" => "2", "Search" => "3", "Catalog, Search" => "4");
            $syncData = array();
            $productData = array();
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
                        if ($syncData['attribute_set'] != '') {
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
                                    $productData = $this->productFactory->create()->loadByAttribute('sku', $syncData['sku']);
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
                                        $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
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
                                    $productArray['productId'] = "";
                                    $productArray['syncAction'] = "Product Not Synced";
                                    $productArray['acumaticaStockItem'] = $syncData['sku'];
                                    $productArray['syncDirection'] = "syncToMagento";
                                    $productArray['messageType'] = "Failure";
                                    $txt = "Error: Acumatica Stock Item : " . $syncData['sku'] . " : " . $productArray['description'] . " Please try again";
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                    $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
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
                    $productArray['productId'] = "";
                    $productArray['syncAction'] = "Product Not Synced";
                    $productArray['acumaticaStockItem'] = $syncData['sku'];
                    $productArray['syncDirection'] = "syncToMagento";
                    $productArray['messageType'] = "Failure";
                    $txt = "Info : " . $productArray['syncAction'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
                    $this->errorCheckInMagento[] = 1;
                }
                if (isset($productData) && !empty($productData)) {
                    return $syncData['sku'];
                }
            }
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
     
     * @return array
     */
    public function bundleSyncToMagento($aData, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag,$url)
    {
        if (!is_array($aData)) {
            $aData = json_decode(json_encode($aData), 1);
        }
        /**
         * Here we are preparing an array based on the mapping attribute
         */
        $visibilityClass = array("Not Visible Individually" => "1", "Catalog" => "2", "Search" => "3", "Catalog, Search" => "4");
        $syncData = array();
        foreach ($mappingAttributes as $key => $value) {
            $mappingData = explode('|', $value);
            if ($directionFlag && $mappingData[1] == 'Bi-Directional (Magento Wins)') {
                continue;
            }
            if ($mappingData[0] != '') {
                $acumaticaLabel = $this->productResourceModel->getAcumaticaAttrCode($mappingData[0]);
            }

            $acumaticaAttrCode = explode(" ", $acumaticaLabel); //array[0] will be section and array[1] will be attribute code
            if ($acumaticaAttrCode[0] == "ProductSchema") {
                if($acumaticaAttrCode[1] == "Weight"){
                    if(isset($aData['BunPackaging']['Weight']['Value']))
                        $acumaticaFieldValue = $aData['BunPackaging']['Weight']['Value'];
                    else
                        $acumaticaFieldValue = '';
                }else {
                    if (isset($aData[$acumaticaAttrCode[1]]['Value']))
                        $acumaticaFieldValue = $aData[$acumaticaAttrCode[1]]['Value'];
                    else
                        $acumaticaFieldValue = '';
                }
            } else{
                if (isset($aData['BunAttributes']['BunAttributes']) && count($aData['BunAttributes']['BunAttributes']) > 0) {
                    $aData['BunAttributes']['BunAttributes'] = json_decode(json_encode($aData['BunAttributes']['BunAttributes']), 1);
                    $oneAttributeRecordFlag = false;
                    $acumaticaFieldValue = '';
                    foreach ($aData['BunAttributes']['BunAttributes'] as $attributeKey => $attributeValue) {
                        if (!is_numeric($attributeKey)) {
                            $oneAttributeRecordFlag = true;
                            break;
                        }
                        if (isset($attributeValue['Attribute']['Value']) && isset($attributeValue['Value']['Value'])) {
                            $magentoAttributeCode = strtolower($attributeValue['Attribute']['Value']);
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
                                } elseif($magentoAttributeType == 'select')
                                {
                                    $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                    $optionId = $attributeDetails->getSource()->getOptionId($magentoAttributeValue);
                                    if (isset($optionId) && $optionId != '') {
                                        $acumaticaFieldValue = $optionId;
                                        break;
                                    }
                                }else if($magentoAttributeType == 'multiselect')
                                {
                                    if(isset($magentoAttributeValue) && $magentoAttributeValue != '')
                                    {
                                        $optinValues = explode(',', $magentoAttributeValue);
                                        $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                        $realIds = array();
                                        foreach($optinValues as $optinValue)
                                        {
                                            $realIds[] =$attributeDetails->getSource()->getOptionId($optinValue);
                                        }
                                        if(isset($realIds) && !empty($realIds))
                                        {
                                            $acumaticaFieldValue = implode(',',$realIds);
                                            break;
                                        }
                                    }
                                }else{
                                    $acumaticaFieldValue = $magentoAttributeValue;
                                    break;
                                }
                            }
                        }
                    }
                    if ($oneAttributeRecordFlag) {
                        if (isset($aData['BunAttributes']['BunAttributes']['Attribute']['Value']) && isset($aData['BunAttributes']['BunAttributes']['Value']['Value']))
                        {
                            $magentoAttributeCode = strtolower($aData['BunAttributes']['BunAttributes']['Attribute']['Value']);
                            $magentoAttributeValue = $aData['BunAttributes']['BunAttributes']['Value']['Value'];
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
                                    $optionId = $attributeDetails->getSource()->getOptionId($magentoAttributeValue);
                                    if (isset($optionId) && $optionId != '') {
                                        $acumaticaFieldValue = $optionId;
                                        break;
                                    }
                                }else if($magentoAttributeType == 'multiselect')
                                {
                                    if(isset($magentoAttributeValue) && $magentoAttributeValue != '') {
                                        $optinValues = explode(',', $magentoAttributeValue);
                                        $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                        $realIds = array();
                                        foreach($optinValues as $optinValue)
                                        {
                                            $realIds[] =$attributeDetails->getSource()->getOptionId($optinValue);
                                        }
                                        if(isset($realIds) && !empty($realIds))
                                        {
                                            $acumaticaFieldValue = implode(',',$realIds);
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
         * first we need to check composite item has any options or not
         * here we need to check composite item has select any options with inventory or not
         * if yes we need to map the option with inventory
         * if not then don't consider that option to create bundle product
         */
        if (isset($aData['BunDefinedOptions']['BunDefinedOptions']) && count($aData['BunDefinedOptions']['BunDefinedOptions']) > 0)
        {
            $selectedBundleOptionsWithInventories = array();
            $selectedBundleOptions = array();
            $bundleOptions = json_decode(json_encode($aData['BunDefinedOptions']['BunDefinedOptions']), 1);
            $bundleInventories = json_decode(json_encode($aData['BunMappedInventories']['BunMappedInventories']), 1);
            $oneBundleOptionFlag = false;
            $i = 0;
            foreach ($bundleOptions as $bundleOptionKey => $bundleOptionValue)
            {
                if (!is_numeric($bundleOptionKey)) {
                    $oneBundleOptionFlag = true;
                    break;
                }
                if(count($bundleInventories) > 0)
                {
                    $oneBundleInventoryFlag = false;
                    $position = 0;
                    foreach($bundleInventories as $bundleInventoryKey => $bundleInventoryValue)
                    {
                        if (!is_numeric($bundleInventoryKey)) {
                            $oneBundleInventoryFlag = true;
                            break;
                        }
                        if(isset($bundleInventoryValue['OptionID']['Value']) && $bundleOptionValue['OptionID']['Value'] == $bundleInventoryValue['OptionID']['Value'])
                        {
                            /**
                             * multiple Records
                             * check bundle simple is exist in magento or not
                             * if exist then map bundle_sku data
                             * if not create that simple product then map bundle_sku data
                             */
                            $acumaticaBundleSimpleSku = str_replace(" ","_",$bundleInventoryValue['InventoryID']['Value']);
                            $bundleSimpleProduct = $this->productFactory->create()->loadByAttribute('sku', $acumaticaBundleSimpleSku);
                            $simpleBundleFlag = false;
                            if(isset($bundleSimpleProduct) && !empty($bundleSimpleProduct))
                            {
                                $simpleBundleFlag = true;
                                $bundleSimpleInventory = $bundleSimpleProduct->getId();
                            }/*else{
                                $bundleSkuAfterCreateMagento = $this->createProductInMagento($acumaticaBundleSimpleSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                if(isset($bundleSkuAfterCreateMagento) && $bundleSkuAfterCreateMagento == $acumaticaBundleSimpleSku)
                                {
                                    $bundleSimpleProduct = $this->productFactory->create()->loadByAttribute('sku', $acumaticaBundleSimpleSku);
                                    $simpleBundleFlag = true;
                                    $bundleSimpleInventory = $bundleSimpleProduct->getId();
                                }
                            }*/
                            if($simpleBundleFlag)
                            {
                                if (isset($bundleInventoryValue['InventoryID']['Value']))
                                    $selectedBundleOptionsWithInventories[$i][$position]['product_id'] = $bundleSimpleInventory;
                                if (isset($bundleInventoryValue['Quantity']['Value']))
                                {
                                    $selectedBundleOptionsWithInventories[$i][$position]['selection_qty'] = $bundleInventoryValue['Quantity']['Value'];
                                }else{
                                    $selectedBundleOptionsWithInventories[$i][$position]['selection_qty'] = '0';
                                }
                                if (isset($bundleInventoryValue['UserCanDefineQty']['Value'])) {
                                    if ($bundleInventoryValue['UserCanDefineQty']['Value'] == "true")
                                        $selectedBundleOptionsWithInventories[$i][$position]['selection_can_change_qty'] = '1'; //User Defined Qty 1 = Yes
                                    else
                                        $selectedBundleOptionsWithInventories[$i][$position]['selection_can_change_qty'] = '0';//User Defined Qty 0 = No
                                } else {
                                    $selectedBundleOptionsWithInventories[$i][$position]['selection_can_change_qty'] = '0';//User Defined Qty 0 = No
                                }
                            }
                        }
                        $position++;
                    }
                    if($oneBundleInventoryFlag)
                    {
                        if($bundleOptionValue['OptionID']['Value'] == $bundleInventories['OptionID']['Value'])
                        {
                            /**
                             * single record
                             * check bundle simple is exist in magento or not
                             * if exist then map bundle_sku data
                             * if not create that simple product then map bundle_sku data
                             */
                            $acumaticaBundleSimpleSku = str_replace(" ","_",$bundleInventories['InventoryID']['Value']);
                            $bundleSimpleProduct = $this->productFactory->create()->loadByAttribute('sku', $acumaticaBundleSimpleSku);
                            $simpleBundleFlag = false;
                            if ($bundleSimpleProduct)
                            {
                                $simpleBundleFlag = true;
                                $bundleSimpleInventory = $bundleSimpleProduct->getId();
                            }/*else{
                                $bundleSkuAfterCreateMagento = $this->createProductInMagento($acumaticaBundleSimpleSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                if(isset($bundleSkuAfterCreateMagento) && $bundleSkuAfterCreateMagento == $acumaticaBundleSimpleSku)
                                {
                                    $bundleSimpleProduct = $this->productFactory->create()->loadByAttribute('sku', $acumaticaBundleSimpleSku);
                                    $simpleBundleFlag = true;
                                    $bundleSimpleInventory = $bundleSimpleProduct->getId();
                                }
                            }*/
                            if($simpleBundleFlag)
                            {
                                $position = 0;
                                if (isset($bundleInventories['InventoryID']['Value']))
                                    $selectedBundleOptionsWithInventories[$bundleOptionValue['OptionID']['Value']][$position]['product_id'] = $bundleSimpleInventory;
                                if (isset($bundleInventories['Quantity']['Value']))
                                {
                                    $selectedBundleOptionsWithInventories[$bundleOptionValue['OptionID']['Value']][$position]['selection_qty'] = $bundleInventories['Quantity']['Value'];
                                }else{
                                    $selectedBundleOptionsWithInventories[$bundleOptionValue['OptionID']['Value']][$position]['selection_qty'] = '0';
                                }
                                if (isset($bundleInventories['UserCanDefineQty']['Value'])) {
                                    if ($bundleInventories['UserCanDefineQty']['Value'] == "true")
                                        $selectedBundleOptionsWithInventories[$bundleOptionValue['OptionID']['Value']][$position]['selection_can_change_qty'] = '1'; //User Defined Qty 1 = Yes
                                    else
                                        $selectedBundleOptionsWithInventories[$bundleOptionValue['OptionID']['Value']][$position]['selection_can_change_qty'] = '0';//User Defined Qty 0 = No
                                } else {
                                    $selectedBundleOptionsWithInventories[$bundleOptionValue['OptionID']['Value']][$position]['selection_can_change_qty'] = '0';//User Defined Qty 0 = No
                                }
                            }
                        }
                    }
                }
                $selectedBundleOptions[$i]['title'] = $bundleOptionValue['OptionTitle']['Value'];
                $selectedBundleOptions[$i]['default_title'] = $bundleOptionValue['OptionTitle']['Value'];
                if(isset($bundleOptionValue['ControlType']['Value']))
                {
                    if ($bundleOptionValue['ControlType']['Value'] == "Drop Down")
                        $selectedBundleOptions[$i]['type'] = "select";
                    else
                        $selectedBundleOptions[$i]['type'] = "multi"; //Input Type Multiple Select
                }else
                {
                    $selectedBundleOptions[$i]['type'] = "select";
                }
                if(isset($bundleOptionValue['IsMandatory']['Value']))
                {
                    if ($bundleOptionValue['IsMandatory']['Value'] == "true")
                        $selectedBundleOptions[$i]['required'] = "1"; //Is Required 1 = Yes
                    else
                        $selectedBundleOptions[$i]['required'] = "0"; //Is Required 0 = No
                }else{
                    $selectedBundleOptions[$i]['required'] = "0"; //Is Required 0 = No
                }
                $i++;
            }
            if ($oneBundleOptionFlag)
            {
                $position = 0;
                if(count($bundleInventories) > 0)
                {
                    $oneBundleInventoryFlag = false;
                    foreach($bundleInventories as $bundleInventoryKey => $bundleInventoryValue)
                    {
                        if (!is_numeric($bundleInventoryKey)) {
                            $oneBundleInventoryFlag = true;
                            break;
                        }
                        if($bundleOptions['OptionID']['Value'] == $bundleInventoryValue['OptionID']['Value'])
                        {
                            /**
                             * multiple Records
                             * check bundle simple is exist in magento or not
                             * if exist then map bundle_sku data
                             * if not create that simple product then map bundle_sku data
                             */
                            $acumaticaBundleSimpleSku = str_replace(" ","_",$bundleInventoryValue['InventoryID']['Value']);
                            $bundleSimpleProduct = $this->productFactory->create()->loadByAttribute('sku', $acumaticaBundleSimpleSku);
                            $simpleBundleFlag = false;
                            if ($bundleSimpleProduct)
                            {
                                $simpleBundleFlag = true;
                                $bundleSimpleInventory = $bundleSimpleProduct->getId();
                            }/*else{
                                $bundleSkuAfterCreateMagento = $this->createProductInMagento($acumaticaBundleSimpleSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                if(isset($bundleSkuAfterCreateMagento) && $bundleSkuAfterCreateMagento == $acumaticaBundleSimpleSku)
                                {
                                    $bundleSimpleProduct = $this->productFactory->create()->loadByAttribute('sku', $acumaticaBundleSimpleSku);
                                    $simpleBundleFlag = true;
                                    $bundleSimpleInventory = $bundleSimpleProduct->getId();
                                }
                            }*/
                            if($simpleBundleFlag)
                            {
                                if (isset($bundleInventoryValue['InventoryID']['Value']))
                                    $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['product_id'] = $bundleSimpleInventory;
                                if (isset($bundleInventoryValue['Quantity']['Value']))
                                {
                                    $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['selection_qty'] = $bundleInventoryValue['Quantity']['Value'];
                                }else{
                                    $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['selection_qty'] = '0';
                                }
                                if (isset($bundleInventoryValue['UserCanDefineQty']['Value'])) {
                                    if ($bundleInventoryValue['UserCanDefineQty']['Value'] == "true")
                                        $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['selection_can_change_qty'] = '1'; //User Defined Qty 1 = Yes
                                    else
                                        $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['selection_can_change_qty'] = '0';//User Defined Qty 0 = No
                                } else {
                                    $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['selection_can_change_qty'] = '0';//User Defined Qty 0 = No
                                }
                            }
                        }
                        $position++;
                    }
                    if($oneBundleInventoryFlag)
                    {
                        if($bundleOptions['OptionID']['Value'] == $bundleInventories['OptionID']['Value'])
                        {
                            /**
                             * single record
                             * check bundle simple is exist in magento or not
                             * if exist then map bundle_sku data
                             * if not create that simple product then map bundle_sku data
                             */
                            $acumaticaBundleSimpleSku = str_replace(" ","_",$bundleInventories['InventoryID']['Value']);
                            $bundleSimpleProduct = $this->productFactory->create()->loadByAttribute('sku', $acumaticaBundleSimpleSku);
                            $simpleBundleFlag = false;
                            if ($bundleSimpleProduct)
                            {
                                $simpleBundleFlag = true;
                                $bundleSimpleInventory = $bundleSimpleProduct->getId();
                            }/*else{
                                $bundleSkuAfterCreateMagento = $this->createProductInMagento($acumaticaBundleSimpleSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                if(isset($bundleSkuAfterCreateMagento) && $bundleSkuAfterCreateMagento == $acumaticaBundleSimpleSku)
                                {
                                    $bundleSimpleProduct = $this->productFactory->create()->loadByAttribute('sku', $acumaticaBundleSimpleSku);
                                    $simpleBundleFlag = true;
                                    $bundleSimpleInventory = $bundleSimpleProduct->getId();
                                }
                            }*/
                            if($simpleBundleFlag)
                            {
                                if (isset($bundleInventories['InventoryID']['Value']))
                                    $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['product_id'] = $bundleSimpleInventory;
                                if (isset($bundleInventories['Quantity']['Value']))
                                {
                                    $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['selection_qty'] = $bundleInventories['Quantity']['Value'];
                                }else{
                                    $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['selection_qty'] = '0';
                                }
                                if (isset($bundleInventories['UserCanDefineQty']['Value'])) {
                                    if ($bundleInventories['UserCanDefineQty']['Value'] == "true")
                                        $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['selection_can_change_qty'] = '1'; //User Defined Qty 1 = Yes
                                    else
                                        $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['selection_can_change_qty'] = '0';//User Defined Qty 0 = No
                                } else {
                                    $selectedBundleOptionsWithInventories[$bundleOptions['OptionID']['Value']][$position]['selection_can_change_qty'] = '0';//User Defined Qty 0 = No
                                }
                            }
                        }
                    }
                }
                $selectedBundleOptions[$position]['title'] = $bundleOptions['OptionTitle']['Value'];
                $selectedBundleOptions[$position]['default_title'] = $bundleOptions['OptionTitle']['Value'];
                if(isset($bundleOptions['ControlType']['Value']))
                {
                    if ($bundleOptions['ControlType']['Value'] == "Drop Down")
                        $selectedBundleOptions[$position]['type'] = "select";
                    else
                        $selectedBundleOptions[$position]['type'] = "multi"; //Input Type Multiple Select
                }else
                {
                    $selectedBundleOptions[$position]['type'] = "select";
                }
                if(isset($bundleOptions['IsMandatory']['Value']))
                {
                    if ($bundleOptions['IsMandatory']['Value'] == "true")
                        $selectedBundleOptions[$position]['required'] = "1"; //Is Required 1 = Yes
                    else
                        $selectedBundleOptions[$position]['required'] = "0"; //Is Required 0 = No
                }else{
                    $selectedBundleOptions[$position]['required'] = "0"; //Is Required 0 = No
                }
            }
        }

        /**
         * get Item class and check that item class is exist or not
         *
         */
        if (!empty($syncData)) {
            if(isset($aData['ItemClass']['Value']))
                $itemClassName = $aData['ItemClass']['Value'];
            else
                $itemClassName = '';
            if ($itemClassName != '') {
                $entityTypeId = $this->entityModel->setType('catalog_product')->getTypeId();
                $attributeSetId = $this->productResourceModel->getAttributeSetId($entityTypeId, $itemClassName);
                if (isset($attributeSetId) && $attributeSetId != '') {
                    $syncData['attribute_set_id'] = $attributeSetId;
                }
            }

            /**
             * here we need to add category mapping
             * get categories of product
             * check that product exist in magento
             * if exist assign to product
             * if not exist create category and assign to product
             */
            $oneCategoryRecordFlag = false;
            $realCategories = array();
            if(isset($aData['BunCategories']['BunCategories']))
                $getCategories = json_decode(json_encode($aData['BunCategories']['BunCategories']), 1);;
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
                    if ($checkCategoryInMagento != '') {
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
            if (isset($syncData['attribute_set_id']))
                if ($syncData['attribute_set_id'] != '') {
                    if ($aData['InventoryID']['Value'] != '') {
                        $acumaticaInventoryId = str_replace(" ","_",$aData['InventoryID']['Value']);
                    }
                    $_product = $this->productResourceModel->getProductBySku($acumaticaInventoryId);
                    $createFlag = 0;
                    if (isset($_product) && $_product != 0) {
                        if ($syncData['sku'] != '')
                            $createFlag = 1;
                    } else {
                        if ($syncData['sku'] != '' && $syncData['name'] != '' && $syncData['description'] != '' && $syncData['short_description'] != '' && $syncData['weight'] != '' && $syncData['status'] != '' && $syncData['tax_class_id'] != '' && !empty($selectedBundleOptions) && !empty($selectedBundleOptionsWithInventories))
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
                            if (isset($_product) && $_product != 0)
                            {
                                $productBeforeData =  $this->productFactory->create()->loadByAttribute('sku',$syncData['sku']);
                                $productSieArray['before_change'] = json_encode($productBeforeData->getData());
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
                                 * if upsell/crossell product is not exist in magennto need to create
                                 */
                                $upsell = array();
                                if (isset($aData['BunUpSells']['BunUpSells']))
                                    $upsellData = json_decode(json_encode($aData['BunUpSells']['BunUpSells']), 1);
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
                                        }/* else {

                                            $upsell[] = $this->createProductInMagento($acumaticaUpSellSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                        }*/
                                    }
                                    if ($oneUpsellRecordFlag) {
                                        $acumaticaUpSellSku = str_replace(" ","_",$upsellData['InventoryID']['Value']);
                                        $upSellProduct = $this->productResourceModel->getProductBySku($acumaticaUpSellSku);
                                        if (isset($upSellProduct) && $upSellProduct != ''){
                                            $upsell[] = $acumaticaUpSellSku;
                                        } /*else {
                                            $upsell[] = $this->createProductInMagento($acumaticaUpSellSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                        }*/
                                    }
                                }

                                $csell = array();
                                if (isset($aData['BunCrossSells']['BunCrossSells']))
                                    $crosSellData = json_decode(json_encode($aData['BunCrossSells']['BunCrossSells']), 1);
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

                                            $csell[] = $this->createProductInMagento($acumaticaCrosSellSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                                        }*/
                                    }
                                    if ($oneRecordFlag) {
                                        $acumaticaCrosSellSku = str_replace(" ","_",$crosSellData['InventoryID']['Value']);
                                        $crosSellProduct = $this->productResourceModel->getProductBySku($acumaticaCrosSellSku);
                                        if ($crosSellProduct) {
                                            $csell[] = $acumaticaCrosSellSku;
                                        } /*else {

                                            $csell[] = $this->createProductInMagento($acumaticaCrosSellSku, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);

                                        }*/
                                    }
                                }
                                $syncData["type_id"] = \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE;//Product Type
                                $syncData["price_type"] = 1;//price type is 0 = Dynamic
                                $syncData["weight_type"] = 0;//weight type is 0 = Dynamic
                                $syncData["is_in_stock"] = 1;//is in stock is 1 = Yes
                                $syncData["price"] = 0;
                                if (!empty($realCategories))
                                {
                                    $syncData["category_ids"] = implode(",", $realCategories);
                                }
                                if ($storeId > 1)
                                {
                                    $storeInfo = $this->storeManager->load($storeId);
                                    $syncData["store_id"] = $storeId;
                                    $syncData["website_ids"] = array($storeInfo->getWebsiteId());
                                }else
                                {
                                    $syncData["store_id"] = 0;
                                    $syncData["website_ids"] = array(1);
                                }
                                $product = $this->productFactory->create();
                                if(isset($_product) && $_product != 0)
                                {
                                    $productUpdateCollection = $this->productFactory->create()->loadByAttribute('sku', $acumaticaInventoryId);
                                    $product->load($productUpdateCollection->getId());
                                    $syncData['entity_id'] = $productUpdateCollection->getId();
                                    /**
                                     * Here we are deleting already existed bundle product options while updating the product
                                     */
                                    $typeInstance = $product->getTypeInstance();
                                    $typeInstance->setStoreFilter($product->getStoreId(), $product);
                                    $optionCollection = $typeInstance->getOptionsCollection($product);
                                    if(isset($optionCollection) && !empty($optionCollection))
                                    {
                                        foreach ($optionCollection as $_option)
                                        {
                                            /** @var $selection \Magento\Bundle\Model\Selection */
                                            $this->productOptionRepositoryInterface->deleteById($acumaticaInventoryId,$_option->getOptionId());
                                        }
                                    }
                                }else{
                                    if(isset($syncData["name"]) && $syncData["name"] != '' && isset($syncData['sku']) && $syncData['sku'] != '')
                                    {
                                        $urlKey = str_replace(" ","-",$syncData["name"])."-".str_replace("_","-",$syncData['sku']);
                                        $syncData["url_key"] = strtolower($urlKey);
                                    }
                                }

                                $product->setData($syncData);
                                /**
                                 * creating bundle product options and assigning the corresponding products to the option
                                 */
                                $product->setBundleOptionsData($selectedBundleOptions);
                                $product->setBundleSelectionsData($selectedBundleOptionsWithInventories);
                                $bundleLinks = $product->getBundleSelectionsData();
                                if ($product->getBundleOptionsData())
                                {
                                    $options = array();
                                    foreach ($product->getBundleOptionsData() as $key => $optionData)
                                    {
                                        $bundleOption = $this->optionInterfaceFactory->create(['data' => $optionData]);
                                        $bundleOption->setSku($product->getSku());
                                        $bundleOption->setOptionId(null);

                                        $links = array();
                                        if (!empty($bundleLinks[$key]))
                                        {
                                            foreach ($bundleLinks[$key] as $linkData)
                                            {
                                                $link = $this->linkInterfaceFactory->create(['data' => $linkData]);
                                                $linkProduct = $this->productFactory->create()->load($linkData['product_id']);
                                                $link->setSku($linkProduct->getSku());
                                                $link->setQty($linkData['selection_qty']);
                                                $link->setPrice($linkProduct['price']);
                                                $links[] = $link;
                                            }
                                            $bundleOption->setProductLinks($links);
                                            $options[] = $bundleOption;
                                        }
                                    }
                                    $extension = $product->getExtensionAttributes();
                                    $extension->setBundleProductOptions($options);
                                    $product->setExtensionAttributes($extension);
                                    if ($storeId > 1)
                                    {
                                        $product->setStoreId($storeId);
                                    }else{
                                        $product->setStoreId(0);
                                    }
                                    $product->setPrice(0);
                                    $product->setSkuType(0);
                                    $product->setPriceView(0);
                                    $product->setShipmentType(0);
                                }
                                $product->setStockData(['use_config_manage_stock' => 1, 'qty' => 0, 'is_qty_decimal' => 0, 'is_in_stock' => 1]);
                                $product->save();

                                /**
                                 * cross sell and upsell products assignment
                                 */
                                if($product->getId())
                                {
                                    if($syncData['status'] == 1)
                                        $this->productConfiguratorResourceModel->updateStatus(97,$product->getRowId(),$storeId);

                                    $this->productConfiguratorResourceModel->linkedProducts($upsell,$csell,$product->getRowId());
                                }
                                /**
                                 * logs here for sync success
                                 */
                                if($product->getId())
                                {
                                    $productData = $this->productFactory->create()->loadByAttribute('sku', $syncData['sku']);
                                    $productSieArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                    if (!empty($_product) && $_product != 0) {
                                        $productSieArray['description'] = "SKU " . $syncData['sku'] . " updated in Magento"; //Descripton
                                        $productSieArray['syncAction'] = "Product Updated Into Magento";
                                    } else {
                                        $productSieArray['description'] = "SKU " . $syncData['sku'] . " inserted in Magento"; //Descripton
                                        $productSieArray['syncAction'] = "Product Inserted Into Magento";
                                    }
                                    if ($productData) {
                                        $productSieArray['productId'] = $productData->getId(); //Manual/Auto/Individual
                                    }
                                    $productSieArray['storeId'] = $storeId;
                                    $productSieArray['runMode'] = $syncType; //Manual/Auto/Individual
                                    if ($autoSync == 'COMPLETE') {
                                        $productSieArray['action'] = "Batch Process";//This needs to be dynamic value
                                    } elseif ($autoSync == 'INDIVIDUAL') {
                                        $productSieArray['action'] = "Individual";//This needs to be dynamic value
                                    }
                                    $productSieArray['acumaticaStockItem'] = $syncData['sku'];
                                    $productSieArray['syncDirection'] = "syncToMagento";
                                    $productSieArray['messageType'] = "Success";
                                    $txt = "Info : " . $productSieArray['description'] . " successfully";
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                    $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productSieArray);
                                }
                            }
                        } else {
                            /**
                             * logs here for failure
                             */
                            $msg = 'Mandatory fields must be filled to sync product(s)';
                            if (!empty($_product) && $_product != 0)
                            {
                                if ($syncData['InventoryID'] == '')
                                    $msg = 'Product SKU mapping is not proper';

                            }
                            $productSieArray['storeId'] = $storeId;
                            $productSieArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                            $productSieArray['description'] = $msg;
                            $productSieArray['runMode'] = $syncType; //Manual/Auto/Individual
                            if ($autoSync == 'COMPLETE') {
                                $productSieArray['action'] = "Batch Process";//This needs to be dynamic value
                            } elseif ($autoSync == 'INDIVIDUAL') {
                                $productSieArray['action'] = "Individual";//This needs to be dynamic value
                            }
                            $productSieArray['productId'] = "";
                            $productSieArray['syncAction'] = "Product Not Synced";
                            $productSieArray['acumaticaStockItem'] = $syncData['sku'];
                            $productSieArray['syncDirection'] = "syncToMagento";
                            $productSieArray['messageType'] = "Failure";
                            $txt = "Error: Acumatica Stock Item : " . $syncData['sku'] . " : " . $productSieArray['description'] . " Please try again";
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productSieArray);
                            $this->errorCheckInMagento[] = 1;
                        }
                    }
                }
        } catch (\Magento\Framework\Exception\CouldNotSaveException $e) {

            /**
             *logs here to print exception
             */
            $productSieArray['storeId'] = $storeId;
            $productSieArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
            $productSieArray['description'] = $e->getMessage(); //Descripton
            $productSieArray['runMode'] = $syncType; //Manual/Auto/Individual
            if ($autoSync == 'COMPLETE') {
                $productSieArray['action'] = "Batch Process";//This needs to be dynamic value
            } elseif ($autoSync == 'INDIVIDUAL') {
                $productSieArray['action'] = "Individual";//This needs to be dynamic value
            }
            $productSieArray['productId'] = "";
            $productSieArray['syncAction'] = "Product Not Synced";
            $productSieArray['acumaticaStockItem'] = $syncData['sku'];
            $productSieArray['syncDirection'] = "syncToMagento";
            $productSieArray['messageType'] = "Failure";
            $txt = "Error: " . $productSieArray['syncAction'];
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productSieArray);
            $this->errorCheckInMagento[] = 1;
        } catch (Exception $e) {

            /**
             *logs here to print exception
             */
            $productSieArray['storeId'] = $storeId;
            $productSieArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
            $productSieArray['description'] = $e->getMessage(); //Descripton
            $productSieArray['runMode'] = $syncType; //Manual/Auto/Individual
            if ($autoSync == 'COMPLETE') {
                $productSieArray['action'] = "Batch Process";//This needs to be dynamic value
            } elseif ($autoSync == 'INDIVIDUAL') {
                $productSieArray['action'] = "Individual";//This needs to be dynamic value
            }
            $productSieArray['productId'] = "";
            $productSieArray['syncAction'] = "Product Not Synced";
            $productSieArray['acumaticaStockItem'] = $syncData['sku'];
            $productSieArray['syncDirection'] = "syncToMagento";
            $productSieArray['messageType'] = "Failure";
            $txt = "Error: " . $productSieArray['syncAction'];
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productSieArray);
            $this->errorCheckInMagento[] = 1;
        }
        return $this->errorCheckInMagento;
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
     * @return array
     */
    public function configurableSyncToMagento($aData, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag,$url)
    {
        $productRepository = $this->objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');
        if (!is_array($aData))
        {
            $aData = json_decode(json_encode($aData), 1);
        }

        /**
         * Get attributes by inventoryId to map
         */
        if(isset($aData['InventoryID']['Value']))
        {
            $configurableAttributes = $this->productConfigurator->getConfigurableAttributesFromAcumatica($url,$aData['InventoryID']['Value'],$storeId);
        }
        /**
         * Here we are preparing an array based on the mapping attribute
         */
        $visibilityClass = array("Not Visible Individually" => "1", "Catalog" => "2", "Search" => "3", "Catalog, Search" => "4");
        $syncData = array();
        $configurableAttributesData = array();
        $associatedProductIds = array();
        $quantity = 0;
        foreach ($mappingAttributes as $key => $value)
        {
            $mappingData = explode('|', $value);
            if ($directionFlag && $mappingData[1] == 'Bi-Directional (Magento Wins)') {
                continue;
            }
            if ($mappingData[0] != '') {
                $acumaticaLabel = $this->productResourceModel->getAcumaticaAttrCode($mappingData[0]);
            }

            $acumaticaAttrCode = explode(" ", $acumaticaLabel); //array[0] will be section and array[1] will be attribute code
            if ($acumaticaAttrCode[0] == "ProductSchema") {
                if($acumaticaAttrCode[1] == "Weight"){
                    if(isset($aData['ConPackaging']['Weight']['Value']))
                        $acumaticaFieldValue = $aData['ConPackaging']['Weight']['Value'];
                    else
                        $acumaticaFieldValue = '';
                }else if($acumaticaAttrCode[1] == "DefaultPrice")
                {
                    if(isset($aData['PriceCostInfo']['DefaultPrice']['Value']))
                        $acumaticaFieldValue = $aData['PriceCostInfo']['DefaultPrice']['Value'];
                    else
                        $acumaticaFieldValue = '';
                }else {
                    if (isset($aData[$acumaticaAttrCode[1]]['Value']))
                        $acumaticaFieldValue = $aData[$acumaticaAttrCode[1]]['Value'];
                    else
                        $acumaticaFieldValue = '';
                }
            } else{
                if(count($configurableAttributes['Entity']) > 0 && !empty($configurableAttributes['Entity']) && isset($acumaticaAttrCode[0]))
                {
                    if (isset($configurableAttributes['Entity']['AttributesList']['AttributesList']) && count($configurableAttributes['Entity']['AttributesList']['AttributesList']) > 0)
                    {
                        $configurableAttributes['Entity']['AttributesList']['AttributesList'] = json_decode(json_encode($configurableAttributes['Entity']['AttributesList']['AttributesList']), 1);
                        $oneAttributeRecordFlag = false;
                        $acumaticaFieldValue = '';
                        foreach ($configurableAttributes['Entity']['AttributesList']['AttributesList'] as $attributeKey => $attributeValue)
                        {
                            if (!is_numeric($attributeKey)) {
                                $oneAttributeRecordFlag = true;
                                break;
                            }
                            if (isset($attributeValue['Attribute']['Value']) && isset($attributeValue['Value']['Value']))
                            {

                                $magentoAttributeCode = strtolower($attributeValue['Attribute']['Value']);
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
                                    } elseif($magentoAttributeType == 'select')
                                    {
                                        $attributeDetails = $this->entityModel->setType("catalog_product")->getAttribute(strtolower($magentoAttributeCode));
                                        $allOptions = $attributeDetails->getSource()->getAllOptions();
                                        if(isset($allOptions) && !empty($allOptions))
                                        {
                                            foreach($allOptions as $singleOptin)
                                            {
                                                if(strtolower($singleOptin['label']) == strtolower($magentoAttributeValue))
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
                                                    if(strtolower($singleOptin['label']) == strtolower($optinValue))
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
                            if (isset($configurableAttributes['Entity']['AttributesList']['AttributesList']['Attribute']['Value']) && isset($configurableAttributes['Entity']['AttributesList']['AttributesList']['Value']['Value']))
                            {
                                $magentoAttributeCode = strtolower($configurableAttributes['Entity']['AttributesList']['AttributesList']['Attribute']['Value']);
                                $magentoAttributeValue = $configurableAttributes['Entity']['AttributesList']['AttributesList']['Value']['Value'];
                                if (strtolower($acumaticaAttrCode[0]) == $magentoAttributeCode)
                                {
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
                                                if(strtolower($singleOptin['label']) == strtolower($magentoAttributeValue))
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
                                                    if(strtolower($singleOptin['label']) == strtolower($optinValue))
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
                                        if ($magentoAttributeType != '')
                                        {
                                            if (isset($magentoAttributeValue))
                                                $acumaticaFieldValue = $magentoAttributeValue;
                                        }
                                    }
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
            {
                $syncData[$key] = $acumaticaFieldValue;
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
         * here we need to check composite item has select any attribute or not
         * if yes we need to un assign that attribute from $syncData array
         * if not just throw error like configurabele attributes are not selected to create product in magento
         */
        if (isset($configurableAttributes['Entity']['ConAttributes']['ConAttributes']) && count($configurableAttributes['Entity']['ConAttributes']['ConAttributes']) > 0)
        {
            $selectedConfigurableAttributes = array();
            $configurableCustomAttributes = json_decode(json_encode($configurableAttributes['Entity']['ConAttributes']['ConAttributes']), 1);
            $oneConfigAttributeFlag = false;
            foreach ($configurableCustomAttributes as $confiAttributeKey => $configAttributeValue)
            {
                if (!is_numeric($confiAttributeKey)) {
                    $oneConfigAttributeFlag = true;
                    break;
                }
                if (isset($configAttributeValue['Selected']['Value']) && $configAttributeValue['Selected']['Value'] == 'true')
                {
                    /**
                     * check attribute exist in magento or not
                     * if exist then create select the attribute otherwise skip it
                     */
                    $entity = 'catalog_product';
                    $code = strtolower($configAttributeValue['AttributeID']['Value']);
                    $attributeCollection = $this->abstractAttribute->create()->loadByCode($entity,$code);
                    if ($attributeCollection->getId())
                    {
                        $selectedConfigurableAttributes[] = strtolower($configAttributeValue['AttributeID']['Value']);
                    }
                }
            }
            if ($oneConfigAttributeFlag) {
                if (isset($configurableCustomAttributes['Selected']['Value']) && $configurableCustomAttributes['Selected']['Value'] == 'true')
                {
                    /**
                     * check attribute exist in magento or not
                     * if exist then create select the attribute otherwise skip it
                     */
                    $entity = 'catalog_product';
                    $code = strtolower($configurableCustomAttributes['AttributeID']['Value']);
                    $attributeCollection = $this->abstractAttribute->create()->loadByCode($entity,$code);
                    if ($attributeCollection->getId())
                    {
                        $selectedConfigurableAttributes[] = strtolower($configurableCustomAttributes['AttributeID']['Value']);
                    }
                }
            }
        }
        if (isset($selectedConfigurableAttributes) && count($selectedConfigurableAttributes) > 0)
        {
            foreach ($selectedConfigurableAttributes as $selectedKey => $selectedConfigurableAttribute)
            {
                unset($syncData[$selectedConfigurableAttribute]);
            }
        }
        /**
         * here we need to check the simple product is mapped or not
         * if mapped check that the product exist in magento
         * if exist take that sku otherwise skip
         */
        if(isset($aData['ConMapSimpleInventory']['ConMapSimpleInventory']) && count($aData['ConMapSimpleInventory']['ConMapSimpleInventory']) > 0)
        {
            $simpleSkus = array();
            $simpleOneRecordFlag = false;
            $simpleAcumaticaData = json_decode(json_encode($aData['ConMapSimpleInventory']['ConMapSimpleInventory']), 1);
            $count = 0;
            $attributeValues = array();
            foreach($simpleAcumaticaData as $simpleSkuKey => $simpleSkuValue)
            {
                if (!is_numeric($simpleSkuKey))
                {
                    $simpleOneRecordFlag = true;
                    break;
                }
                $simpleSkuCheck = str_replace(" ","_",$simpleSkuValue['InventoryID']['Value']);
                $simpleSkuCollection = $this->productResourceModel->getProductBySku($simpleSkuCheck);
                if(isset($simpleSkuCollection) && $simpleSkuCollection != 0)
                {
                    $simpleSkus[] = $simpleSkuCheck;
                }/*else{
                    $simpleSkus[] = $this->createProductInMagento($simpleSkuValue['InventoryID']['Value'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                }*/
                /**
                 * Here we need to set associated products and configurable attribute option accordingly
                 */
                $associatedProduct = $this->productResourceModel->getProductBySku($simpleSkuCheck);
                if(isset($associatedProduct) && $associatedProduct != 0)
                {
                    $associateProductCollection =  $this->productFactory->create()->loadByAttribute('sku',$simpleSkuCheck);
                    if(isset($selectedConfigurableAttributes) && count($selectedConfigurableAttributes) > 0)
                    {
                        $kount = 0;
                        foreach($selectedConfigurableAttributes as $selectedConfigurableAttribute)
                        {
                            if(isset($associateProductCollection[strtolower($selectedConfigurableAttribute)]))
                            {
                                $selectedAttributeDetails = $this->attributeRepositoryInterface->get('catalog_product', strtolower($selectedConfigurableAttribute));
                                foreach ($selectedAttributeDetails->getOptions() as $attributeOption)
                                {
                                    $optionValue = $attributeOption->getValue();
                                    if (isset($optionValue) && $associateProductCollection[strtolower($selectedConfigurableAttribute)] == $optionValue)
                                    {
                                        $attributeValues[$selectedAttributeDetails->getId()][$count]['label'] = $attributeOption->getLabel();
                                        $attributeValues[$selectedAttributeDetails->getId()][$count]['attribute_id'] = $selectedAttributeDetails->getId();
                                        $attributeValues[$selectedAttributeDetails->getId()][$count]['value_index'] = $optionValue;
                                    }
                                }
                            }
                            if(isset($attributeValues) && !empty($attributeValues))
                            {
                                $attributeValuesUnique = array_map("unserialize", array_unique(array_map("serialize", $attributeValues[$selectedAttributeDetails->getId()])));
                                $configurableAttributesData[$selectedAttributeDetails->getId()]['attribute_id'] = $selectedAttributeDetails->getId();
                                $configurableAttributesData[$selectedAttributeDetails->getId()]['code'] = $selectedAttributeDetails->getAttributeCode();
                                $configurableAttributesData[$selectedAttributeDetails->getId()]['label'] = $selectedAttributeDetails->getAttributeCode();
                                $configurableAttributesData[$selectedAttributeDetails->getId()]['position'] = $kount;
                                $configurableAttributesData[$selectedAttributeDetails->getId()]['values'] = $attributeValuesUnique;
                            }
                            //$kount++;
                        }
                    }
                    $associatedProductIds[] = $associateProductCollection->getId();
                }
                $count++;
            }
            if($simpleOneRecordFlag)
            {
                $simpleSkuCheck = str_replace(" ","_",$simpleAcumaticaData['InventoryID']['Value']);
                $simpleSkuCollection = $this->productResourceModel->getProductBySku($simpleSkuCheck);
                if(isset($simpleSkuCollection) && $simpleSkuCollection != 0)
                {
                    $simpleSkus[] = $simpleSkuCheck;
                }/*else
                {
                    $simpleSkus[] = $this->createProductInMagento($simpleAcumaticaData['InventoryID']['Value'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlag, $url);
                }*/
                /**
                 * Here we need to set associated products and configurable attribute option accordingly
                 */
                $associatedProduct = $this->productResourceModel->getProductBySku($simpleSkuCheck);
                if(isset($associatedProduct) && $associatedProduct != 0)
                {
                    $associateProductCollection =  $this->productFactory->create()->loadByAttribute('sku',$simpleSkuCheck);

                    if(isset($selectedConfigurableAttributes) && count($selectedConfigurableAttributes) > 0)
                    {
                        $kount = 0;
                        foreach($selectedConfigurableAttributes as $selectedConfigurableAttribute)
                        {
                            if(isset($associateProductCollection[strtolower($selectedConfigurableAttribute)]))
                            {
                                $selectedAttributeDetails = $this->attributeRepositoryInterface->get('catalog_product', strtolower($selectedConfigurableAttribute));
                                foreach ($selectedAttributeDetails->getOptions() as $attributeOption)
                                {
                                    $optionValue = $attributeOption->getValue();
                                    if (isset($optionValue) && $associateProductCollection[strtolower($selectedConfigurableAttribute)] == $optionValue)
                                    {
                                        $attributeValues[$selectedAttributeDetails->getId()][$count]['label'] = $attributeOption->getLabel();
                                        $attributeValues[$selectedAttributeDetails->getId()][$count]['attribute_id'] = $selectedAttributeDetails->getId();
                                        $attributeValues[$selectedAttributeDetails->getId()][$count]['value_index'] = $optionValue;
                                    }
                                }
                            }
                            if(isset($attributeValues) && !empty($attributeValues))
                            {
                                $attributeValuesUnique = array_map("unserialize", array_unique(array_map("serialize", $attributeValues[$selectedAttributeDetails->getId()])));
                                $configurableAttributesData[$selectedAttributeDetails->getId()]['attribute_id'] =  $selectedAttributeDetails->getId();
                                $configurableAttributesData[$selectedAttributeDetails->getId()]['code'] =  $selectedAttributeDetails->getAttributeCode();
                                $configurableAttributesData[$selectedAttributeDetails->getId()]['label'] =  $selectedAttributeDetails->getAttributeCode();
                                $configurableAttributesData[$selectedAttributeDetails->getId()]['position'] =  $kount;
                                $configurableAttributesData[$selectedAttributeDetails->getId()]['values'] =  $attributeValuesUnique;
                            }
                            //$kount++;
                        }
                    }
                    $associatedProductIds[] = $associateProductCollection->getId();
                }

            }
        }
        /**
         * get Item class and check that item class is exist or not
         *
         */
        if (!empty($syncData))
        {
            if(isset($aData['ItemClass']['Value']))
                $itemClassName = $aData['ItemClass']['Value'];
            else
                $itemClassName = '';
            if ($itemClassName != '') {
                $entityTypeId = $this->entityModel->setType('catalog_product')->getTypeId();
                $attributeSetId = $this->productResourceModel->getAttributeSetId($entityTypeId, $itemClassName);
                if (isset($attributeSetId) && $attributeSetId != '') {
                    $syncData['attribute_set_id'] = $attributeSetId;
                }
            }
            /**
             * here we need to add category mapping
             * get categories of product
             * check that product exist in magento
             * if exist assign to product
             * if not exist create category and assign to product
             */
            $oneCategoryRecordFlag = false;
            $realCategories = array();
            if(isset($aData['ConCategories']['ConCategories']))
                $getCategories = json_decode(json_encode($aData['ConCategories']['ConCategories']), 1);
            if (isset($getCategories) && !empty($getCategories))
            {
                foreach ($getCategories as $catkey => $catValue)
                {
                    if (!is_numeric($catkey)) {
                        $oneCategoryRecordFlag = true;
                        break;
                    }
                    $categoryId = $catValue['CategoryID']['Value'];
                    /**
                     * check category exist in magento
                     */
                    $checkCategoryInMagento = $this->productResourceModel->checkCategoryInMagento($categoryId,$storeId);
                    if (isset($checkCategoryInMagento) && $checkCategoryInMagento != '')
                    {
                        $realCategories[] = $checkCategoryInMagento;
                    }
                }
                if ($oneCategoryRecordFlag)
                {
                    $categoryId = $getCategories['CategoryID']['Value'];
                    /**
                     * check category exist in magento
                     */
                    $checkCategoryInMagento = $this->productResourceModel->checkCategoryInMagento($categoryId,$storeId);
                    if ($checkCategoryInMagento != '')
                    {
                        $realCategories[] = $checkCategoryInMagento;
                    }
                }
            }
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
            if (isset($syncData['attribute_set_id']) && $syncData['attribute_set_id'] != '') {
                if ($aData['InventoryID']['Value'] != '') {
                    $acumaticaInventoryId = str_replace(" ","_",$aData['InventoryID']['Value']);
                }
                $_product = $this->productResourceModel->getProductBySku($acumaticaInventoryId);
                $createFlag = 0;
                if (isset($_product) && $_product != 0)
                {
                    if ($syncData['sku'] != '')
                        $createFlag = 1;
                }else {
                    if ($syncData['sku'] != '' && $syncData['name'] != '' && $syncData['price'] != '' && $syncData['description'] != '' && $syncData['short_description'] != '' && $syncData['status'] != '' && $syncData['tax_class_id'] != '')
                        $createFlag = 1;

                    if ($itemStatus != "Active" || $productActive != "true") {
                        $productDisableFlag = 1;
                    }
                }

                if (!$productDisableFlag) {
                    if ($createFlag && isset($configurableAttributesData) && !empty($configurableAttributesData))
                    {

                        /**
                         * Here need to set upsell and cross sell product
                         * if upsell/crossell product is not exist in magennto need to create
                         */
                        $upsell = array();
                        if (isset($aData['ConUpSells']['ConUpSells']))
                            $upsellData = json_decode(json_encode($aData['ConUpSells']['ConUpSells']), 1);
                        if (!empty($upsellData)) {
                            $oneUpsellRecordFlag = false;
                            foreach ($upsellData as $_key => $_value) {
                                if (!is_numeric($_key)) {
                                    $oneUpsellRecordFlag = true;
                                    break;
                                }
                                $acumaticaUpSellSku = $_value['InventoryID']['Value'];
                                $upSellProduct = $this->productResourceModel->getProductBySku($acumaticaUpSellSku);
                                if (isset($upSellProduct) && $upSellProduct != '') {
                                    $upsell[] = $acumaticaUpSellSku;
                                }
                            }
                            if ($oneUpsellRecordFlag) {
                                $acumaticaUpSellSku = $upsellData['InventoryID']['Value'];
                                $upSellProduct = $this->productResourceModel->getProductBySku($acumaticaUpSellSku);
                                if (isset($upSellProduct) && $upSellProduct != ''){
                                    $upsell[] = $acumaticaUpSellSku;
                                }
                            }
                        }

                        $csell = array();
                        if (isset($aData['ConCrossSells']['ConCrossSells']))
                            $crosSellData = json_decode(json_encode($aData['ConCrossSells']['ConCrossSells']), 1);
                        if (!empty($crosSellData)) {
                            $oneRecordFlag = false;
                            foreach ($crosSellData as $crossellkey => $crossellvalue) {
                                if (!is_numeric($crossellkey)) {
                                    $oneRecordFlag = true;
                                    break;
                                }
                                $acumaticaCrosSellSku = $crossellvalue['InventoryID']['Value'];
                                $crosSellProduct = $this->productResourceModel->getProductBySku($acumaticaCrosSellSku);
                                if ($crosSellProduct) {
                                    $csell[] = $acumaticaCrosSellSku;
                                }
                            }
                            if ($oneRecordFlag) {
                                $acumaticaCrosSellSku = $crosSellData['InventoryID']['Value'];
                                $crosSellProduct = $this->productResourceModel->getProductBySku($acumaticaCrosSellSku);
                                if ($crosSellProduct) {
                                    $csell[] = $acumaticaCrosSellSku;
                                }
                            }
                        }

                        /**
                         * Here we are checking that the item status & Active field are Enabled
                         * if both fields are enabled then we need to enable the product in magento
                         * if any field is disabled then while updating product disable the product in magento
                         * while creating product don't sync to magento
                         */
                        if (isset($_product) && $_product != 0)
                        {
                            $productUpdateCollection = $this->productFactory->create()->loadByAttribute('sku', $acumaticaInventoryId);
                            $productArray['before_change'] = json_encode($productUpdateCollection->getData());
                        }
                        if ($itemStatus == "Active" && $productActive == "true")
                        {
                            $syncData['status'] = "1"; //if both are active we are enabled the product
                        } else {
                            $syncData['status'] = "2"; //else disabled the product
                        }
                        if ($storeId > 1)
                        {
                            $storeInfo = $this->storeManager->load($storeId);
                            $syncData["store_id"] = $storeId;
                            $syncData["website_ids"] = array($storeInfo->getWebsiteId());
                        }else
                        {
                            $syncData["store_id"] = 0;
                            $syncData["website_ids"] = array(1);
                        }

                        $syncData["type_id"] = "configurable";//Product Type

                        try {
                            $product = $this->objectManager->create('Magento\Catalog\Model\Product');
                            $configurableProduct = $this->productResourceModel->getProductBySku($acumaticaInventoryId);
                            if (isset($configurableProduct) && $configurableProduct != 0)
                            {
                                $productUpdateCollection = $this->productFactory->create()->loadByAttribute('sku', $acumaticaInventoryId);
                                $product->load($productUpdateCollection->getId());
                                $syncData['entity_id'] = $productUpdateCollection->getId();
                            }else{
                                /**
                                 * url key generation
                                 */
                                if(isset($syncData["name"]) && $syncData["name"] != '' && isset($syncData['sku']) && $syncData['sku'] != '')
                                {
                                    $urlKey = str_replace(" ","-",$syncData["name"])."-".str_replace("_","-",$syncData['sku']);
                                    $syncData["url_key"] = strtolower($urlKey);
                                }
                            }
                            $optionsFactory = $this->objectManager->create('Magento\ConfigurableProduct\Helper\Product\Options\Factory');
                            $configurableOptions = $optionsFactory->create($configurableAttributesData);
                            $extensionConfigurableAttributes = $product->getExtensionAttributes();
                            $extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
                            $extensionConfigurableAttributes->setConfigurableProductLinks($associatedProductIds);
                            $product->setExtensionAttributes($extensionConfigurableAttributes);

                            foreach ($syncData as $_key => $_value) {
                                if ($_value != '') {
                                    $_keyset = 'set' . ucfirst($_key);
                                    $product->$_keyset($_value);
                                }
                            }
                            $product->setStockData(['use_config_manage_stock' => 1, 'is_in_stock' => 1]);

                            $productRepository->save($product);

                            $productData = $this->productFactory->create()->loadByAttribute('sku', $acumaticaInventoryId);

                            if ($productData->getId())
                            {
			                    /**
                                *@todo updating status through query as it is not working with magento object
                                */
                                if($syncData['status'] == 1)
                                        $this->productConfiguratorResourceModel->updateStatus(97,$productData->getRowId(),$storeId);

                                $productSetStore = $this->productFactory->create()->load($productData->getId());
                                $productSetStore->setStoreId(0);
                                $productSetStore->save();
                                /**
                                 * assign categories to product
                                 */
                                if (isset($realCategories) && !empty($realCategories))
                                {

                                    $this->productResourceModel->assignCategories($realCategories, $productData->getId());
                                }
                                /**
                                 * assign upsell & crossell
                                 */
                                $this->productConfiguratorResourceModel->linkedProducts($upsell,$csell,$productData->getRowId());

                                /**
                                 * logs here for sync success
                                 */
                                $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                if (isset($_product) && $_product != 0) {
                                    $productArray['description'] = "SKU " . $syncData['sku'] . " updated in Magento"; //Descripton
                                    $productArray['syncAction'] = "Product Updated Into Magento";
                                } else {
                                    $productArray['description'] = "SKU " . $syncData['sku'] . " inserted in Magento"; //Descripton
                                    $productArray['syncAction'] = "Product Inserted Into Magento";
                                }
                                $productArray['productId'] = $productData->getId(); //Manual/Auto/Individual
                                $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                                if ($autoSync == 'COMPLETE') {
                                    $productArray['action'] = "Batch Process";//This needs to be dynamic value
                                } elseif ($autoSync == 'INDIVIDUAL') {
                                    $productArray['action'] = "Individual";//This needs to be dynamic value
                                }
                                $productArray['storeId'] = $storeId;
                                $productArray['acumaticaStockItem'] = $syncData['sku'];
                                $productArray['syncDirection'] = "syncToMagento";
                                $productArray['messageType'] = "Success";
                                $txt = "Info : " . $productArray['description'] . " successfully";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
                            }
                        } catch (\Magento\Framework\Exception\InputException $en) {
                            /**
                             *logs here to print exception
                             */
                            $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                            $productArray['description'] = $en->getMessage(); //Descripton
                            $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                            if ($autoSync == 'COMPLETE') {
                                $productArray['action'] = "Batch Process";//This needs to be dynamic value
                            } elseif ($autoSync == 'INDIVIDUAL') {
                                $productArray['action'] = "Individual";//This needs to be dynamic value
                            }
                            $productArray['productId'] = "";
                            $productArray['storeId'] = $storeId;
                            $productArray['syncAction'] = "Product Not Synced";
                            $productArray['acumaticaStockItem'] = $syncData['sku'];
                            $productArray['syncDirection'] = "syncToMagento";
                            $productArray['messageType'] = "Failure";
                            $txt = "Error: " . $en->getMessage();
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
                            $this->errorCheckInMagento[] = 1;
                        } catch (\Magento\Framework\Exception\CouldNotSaveException $en) {
                            /**
                             *logs here to print exception
                             */
                            $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                            $productArray['description'] = $en->getMessage(); //Descripton
                            $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                            if ($autoSync == 'COMPLETE') {
                                $productArray['action'] = "Batch Process";//This needs to be dynamic value
                            } elseif ($autoSync == 'INDIVIDUAL') {
                                $productArray['action'] = "Individual";//This needs to be dynamic value
                            }
                            $productArray['productId'] = "";
                            $productArray['storeId'] = $storeId;
                            $productArray['syncAction'] = "Product Not Synced";
                            $productArray['acumaticaStockItem'] = $syncData['sku'];
                            $productArray['syncDirection'] = "syncToMagento";
                            $productArray['messageType'] = "Failure";
                            $txt = "Error: " . $en->getMessage();
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
                            $this->errorCheckInMagento[] = 1;
                        } catch (Exception $en) {
                            /**
                             *logs here to print exception
                             */
                            $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                            $productArray['description'] = $en->getMessage(); //Descripton
                            $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                            if ($autoSync == 'COMPLETE') {
                                $productArray['action'] = "Batch Process";//This needs to be dynamic value
                            } elseif ($autoSync == 'INDIVIDUAL') {
                                $productArray['action'] = "Individual";//This needs to be dynamic value
                            }
                            $productArray['productId'] = "";
                            $productArray['storeId'] = $storeId;
                            $productArray['syncAction'] = "Product Not Synced";
                            $productArray['acumaticaStockItem'] = $syncData['sku'];
                            $productArray['syncDirection'] = "syncToMagento";
                            $productArray['messageType'] = "Failure";
                            $txt = "Error: " . $en->getMessage();
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
                            $this->errorCheckInMagento[] = 1;
                        }
                    } else {
                        /**
                         * logs here for failure
                         */
                        $msg = 'Mandatory fields must be filled to sync product(s).';
                        if (isset($_product) && $_product != 0)
                        {
                            if ($syncData['sku'] == '')
                            {
                                $msg = 'Product SKU mapping is not proper';
                            }else if(empty($configurableAttributesData) || empty($associatedProductIds))
                            {
                                $msg = 'No associated products found';
                            }
                        }
                        $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                        $productArray['description'] = $msg;
                        $productArray['runMode'] = $syncType; //Manual/Auto/Individual
                        if ($autoSync == 'COMPLETE') {
                            $productArray['action'] = "Batch Process";//This needs to be dynamic value
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $productArray['action'] = "Individual";//This needs to be dynamic value
                        }
                        $productArray['storeId'] = $storeId;
                        $productArray['syncAction'] = "Product Not Synced";
                        $productArray['acumaticaStockItem'] = $syncData['sku'];
                        $productArray['productId'] = "";
                        $productArray['syncDirection'] = "syncToMagento";
                        $productArray['messageType'] = "Failure";
                        $txt = "Error: Acumatica Stock Item : " . $syncData['sku'] . " : " . $productArray['description'] . " Please try again.";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
                        $this->errorCheckInMagento[] = 1;
                    }
                }
            }
        } catch (Exception $e) {

            /**
             *logs here to print exception
             */
            $productArray['schedule_id'] = $syncLogID; //It will dynamic Cron ID
            $productArray['description'] = $e->getMessage(); //Descripton
            $productArray['runMode'] = $syncType; //Manual/Auto/Individual
            if ($autoSync == 'COMPLETE') {
                $productArray['action'] = "Batch Process";//This needs to be dynamic value
            } elseif ($autoSync == 'INDIVIDUAL') {
                $productArray['action'] = "Individual";//This needs to be dynamic value
            }
            $productArray['productId'] = "";
            $productArray['storeId'] = $storeId;
            $productArray['syncAction'] = "Product Not Synced";
            $productArray['acumaticaStockItem'] = $syncData['sku'];
            $productArray['syncDirection'] = "syncToMagento";
            $productArray['messageType'] = "Failure";
            $txt = "Error: " . $productArray['syncAction'];
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->ConfiguratorSyncLogs->productConfiguratorSyncSuccessLogs($productArray);
            $this->errorCheckInMagento[] = 1;
        }
        return $this->errorCheckInMagento;
    }
}
