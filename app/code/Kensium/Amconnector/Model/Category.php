<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Symfony\Component\Config\Definition\Exception\Exception;

class Category extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var Timezone
     */
    protected $timeZone;

    /**
     * @var errorCheckInMagento
     */
    protected $errorCheckInMagento = array();

    /**
     * @var Sync
     */
    protected $helper;

    /**
     * @var ResourceConnection|\Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;


    /**
     * @var \Kensium\Amconnector\Model\TimeFactory
     */
    protected $timeFactory;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Category
     */
    protected $resource;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $resourceModelSync;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryFactory;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $dataHelper;
    /**
     * @var
     */
    protected $categoryLogHelper;
    /**
     * @var \Kensium\Amconnector\Helper\Category
     */
    protected $categoryResourceModel;
    /**
     * @var
     */
    protected $urlHelper;
    /**
     * @var \Kensium\Amconnector\Helper\Client
     */
    protected $clientHelper;
    /**
     * @var \Kensium\Amconnector\Helper\Sync
     */
    protected $syncHelper;

    protected $productHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Category\Collection $resourceCollection
     * @param DateTime $date
     * @param Sync $helper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param TimeFactory $timeFactory
     * @param ResourceModel\Category $resource
     * @param ResourceModel\Sync $resourceModelSync
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFact
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Kensium\Synclog\Helper\Category $categoryLogHelper
     * @param \Kensium\Amconnector\Helper\Url $urlHelper
     * @param ResourceModel\Category $categoryResourceModel
     * @param \Kensium\Amconnector\Helper\Data $dataHelper
     * @param \Kensium\Amconnector\Helper\Client $clientHelper
     * @param \Kensium\Amconnector\Helper\Sync $syncHelper
     * @param \Kensium\Amconnector\Helper\Product $productHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Kensium\Amconnector\Model\ResourceModel\Category\Collection $resourceCollection = null,
        DateTime $date,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Kensium\Amconnector\Model\TimeFactory $timeFactory,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Category $resource,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFact,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Kensium\Synclog\Helper\Category $categoryLogHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Category $categoryResourceModel,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Kensium\Amconnector\Helper\Product $productHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $data = []
    )
    {
        parent::__construct($context,$registry,$resource,$resourceCollection,[]);
        $this->date = $date;
        $this->resourceConnection = $resourceConnection;
        $this->timeFactory = $timeFactory;
        $this->resource = $resource;
        $this->resourceModelSync = $resourceModelSync;
        $this->messageManager = $messageManager;
        $this->categoryRepository=$categoryRepository;
        $this->categoryFactory=$categoryFactory;
        $this->categoryFact=$categoryFact;
        $this->xmlHelper = $xmlHelper;
        $this->productFactory=$productFactory;
        $this->storeRepository = $storeRepository;
        $this->_storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
        $this->urlHelper = $urlHelper;
        $this->categoryResourceModel = $categoryResourceModel;
        $this->categoryLogHelper = $categoryLogHelper;
        $this->clientHelper = $clientHelper;
        $this->productHelper = $productHelper;
        $this->syncHelper = $syncHelper;
    }

    /**
     * constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Kensium\Amconnector\Model\ResourceModel\Category');
    }

    /**
     * @param $aData
     * @param $mappingAttributes
     * @param $syncType
     * @param $autoSync
     * @param $syncLogID
     * @param $storeId
     * @param null $logViewFileName
     * @param $directionFlag
     * @return array|errorCheckInMagento
     * Sync to Magento
     */
    public function syncToMagento($aData, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName = NULL,$directionFlag)
    {
        /**
         * Here we are preparing an array based on the mapping attribute
         */
        if (!is_array($aData)) {
            $aData = json_decode(json_encode($aData), 1);
        }
	$aData['CategoryInfo']['SyncStatus']['Value'] = 'Active';
        /**
         * get Acumatica Category ID For Logs
         */

        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
        $syncData = array();
        $errorCheckInMagento = array();
        foreach ($mappingAttributes as $key => $value) {
            $mappingData = explode('|',$value);
            if($directionFlag && $mappingData[1] == 'Bi-Directional (Magento Wins)'){
                continue;
            }
            if($mappingData[0] == 'Category') $mappingData[0] = 'CategoryID';
            if($mappingData[0] == 'ParentCategory') $mappingData[0] = 'ParentCategoryID';
            if (isset($aData[$mappingData[0]]['Value'])) {
                $acumaticaFieldValue = $aData[$mappingData[0]]['Value'];
            } else if(isset($aData['CategoryInfo'][$mappingData[0]]['Value']))
            {
                $acumaticaFieldValue = $aData['CategoryInfo'][$mappingData[0]]['Value'];
            } else {
                $acumaticaFieldValue = '';
            }

            $syncData[$key] = $acumaticaFieldValue;

        }

        $syncData['path'] = $aData['Path']['Value'];
        $_categoryName = '';
        if(array_key_exists('name',$syncData)){
            $_categoryName = $syncData['name'];
        }
        $acumaticaCategoryId = trim($syncData['acumatica_category_id']);
        if($acumaticaCategoryId != '' && $_categoryName != '') {
            if ($_categoryName == '' || $syncData['path'] == '') {
                /**
                 * logs here for category required fields
                 */
                $categoryLog['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                $categoryLog['description'] = "Mandatory fields must be filled to sync category(s)"; //Descripton
                $categoryLog['runMode'] = $syncType; //Manual/Auto/Individual
                if ($autoSync == 'COMPLETE') {
                    $categoryLog['action'] = "Batch Process";//This needs to be dynamic value
                } elseif ($autoSync == 'INDIVIDUAL') {
                    $categoryLog['action'] = "Individual";//This needs to be dynamic value
                }
                $categoryLog['syncAction'] = "Category Not Synced";
                $categoryLog['acumaticaCategoryId'] = $acumaticaCategoryId;
                $categoryLog['syncDirection'] = "syncToMagento";
                $categoryLog['messageType'] = "Failure";
                $categoryLog['storeId'] = $storeId;
                $txt = "Error: Acumatica Category ID : " . $categoryLog['acumaticaCategoryId'] . " : Mandatory fields must be filled to sync category(s). Please try again";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->categoryLogHelper->categorySyncLogs($categoryLog);
                $errorCheckInMagento[] = 1;
            } else {
                /**
                 * Required data present
                 */
                try {

                    if($storeId == 0 || $storeId == 1){
                        $syncData['store_id'] = 0;
                    }else{
                        $syncData['store_id'] = $storeId;
                    }
                    $rootCategoryId = $this->_storeManager->getStore($storeId)->getRootCategoryId();
                    $rootCategory = $this->categoryFact->create()->load($rootCategoryId);
                    $parentCategoryPath = $rootCategory->getPath();
                    $categoryCollection = $this->categoryFactory->create()->addFieldToFilter('acumatica_category_id', $syncData['acumatica_category_id'])->getFirstItem();
                    if ($categoryCollection->getId() != '') {
                        /**
                         * Update Category
                         */
                        $catId = $categoryCollection->getId();
                        $categoryLog['before_change'] = json_encode($categoryCollection->getData());
                        $categoryUpdate = $this->categoryFact->create()->load($catId);
                        $categoryUpdate->setName($syncData['name']);
                        if ($syncData['acumatica_parent_category_id'] != 0) {
                            $parentCategory = $this->categoryFactory->create()->addFieldToFilter('acumatica_category_id', $syncData['acumatica_parent_category_id'])->getFirstItem();
                            if ($parentCategory->getId() != '') {
                                $categoryUpdate->setPath($parentCategory->getPath() . '/' . $catId);
                            }
                            $categoryUpdate->setAcumaticaParentCategoryId($syncData['acumatica_parent_category_id']);
                        } else {
                            $categoryUpdate->setPath($parentCategoryPath . '/' . $catId);
                            $categoryUpdate->setAcumaticaParentCategoryId($syncData['acumatica_parent_category_id']);
                        }
                        if(isset($syncData['default_sort_by']))
                        {
                            $availableSortBy = array('Best Value'=>'position','Name'=>'name','Price'=>'price');
                            if(isset($availableSortBy[$syncData['default_sort_by']]))
                                $categoryUpdate->setDefaultSortBy($availableSortBy[$syncData['default_sort_by']]);
                        }
                        if(isset($syncData['description']))
                        {
                            $categoryUpdate->setDescription($syncData['description']);
                        }
                        if(isset($syncData['meta_description']))
                        {
                            $categoryUpdate->setMetaDescription($syncData['meta_description']);
                        }
                        if(isset($syncData['meta_keywords']))
                        {
                            $categoryUpdate->setMetaKeywords($syncData['meta_keywords']);
                        }
                        if(isset($syncData['meta_title']))
                        {
                            $categoryUpdate->setMetaTitle($syncData['meta_title']);
                        }
                        if(isset($syncData['url_key']))
                        {
                            $categoryUpdate->setUrlKey($syncData['url_key']);
                        }
                        if(isset($syncData['include_in_menu']))
                        {
                            $includeInMenu = array('true'=>'1','false'=>'0');
                            if(isset($includeInMenu[$syncData['include_in_menu']]))
                                $categoryUpdate->setIncludeInMenu($includeInMenu[$syncData['include_in_menu']]);
                        }
                        if(isset($aData['CategoryInfo']['SyncStatus']['Value']) &&  $aData['CategoryInfo']['SyncStatus']['Value'] == 'Active')
                        {
                            $categoryUpdate->setIsActive(1);
                        }else
                        {
                            $categoryUpdate->setIsActive(0);
                        }

                        /**
                         * here need to check category is having products
                         * if exist then need to check product exist in magento
                         * if available then assign otherwise skip
                         */
                        if(!empty($aData['Members']))
                        {
                            $categoryMembers = $this->xmlHelper->xml2array($aData['Members']['ItemSalesCategoryMember']);
                            $oneRecordFlag=false;
                            $proIds = array();
                            $position = '';
                            foreach($categoryMembers as $_key => $_value)
                            {
                                if (!is_numeric($_key)) {
                                    $oneRecordFlag = true;
                                    break;
                                }
                                $inventoryId = $_value['InventoryID']['Value'];
                                $inventoryId = str_replace(' ', '_', $inventoryId);
                                $productId = $this->productFactory->create()->getIdBySku($inventoryId);
                                if($productId)
                                {
                                    if(isset($_value['RowNumber']['Value']))
                                        $position =  $_value['RowNumber']['Value'];
                                    else
                                        $position = $_key;

                                    $proIds[$productId] = $position;
                                }
                            }
                            if($oneRecordFlag)
                            {
                                $inventoryId = $categoryMembers['InventoryID']['Value'];
                                $inventoryId = str_replace(' ', '_', $inventoryId);
                                $productId = $this->productFactory->create()->getIdBySku($inventoryId);
                                if($productId)
                                {
                                    $position =  $categoryUpdate->getProductsPosition();
                                    if(isset($categoryMembers['RowNumber']['Value']))
                                        $position =  $categoryMembers['RowNumber']['Value'];
                                    else
                                        $position = 0;

                                    $proIds[$productId] = $position;
                                }
                            }
                        }
                        if(isset($proIds))
                        {
                            $categoryUpdate->setPostedProducts($proIds);
                        }
                        if($storeId == 0 || $storeId == 1){
                            $categoryUpdate->setStoreId(0);
                        }else{
                            $categoryUpdate->setStoreId($storeId);
                        }
                        $categoryUpdate->save();
                        /**
                         * logs here for update category
                         */
                        $categoryLog['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                        $categoryLog['catId'] = $catId;
                        $categoryLog['acumaticaCategoryName'] = $syncData['name'];
                        $categoryLog['description'] = "Category : ".$syncData['name']." updated in Magento"; //Descripton
                        $categoryLog['runMode'] = $syncType; //Manual/Auto/Individual
                        if ($autoSync == 'COMPLETE') {
                            $categoryLog['action'] = "Batch Process";//This needs to be dynamic value
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $categoryLog['action'] = "Individual";//This needs to be dynamic value
                        }
                        $categoryLog['syncAction'] = "Category Synced To Magento";
                        $categoryLog['acumaticaCategoryId'] = $acumaticaCategoryId;
                        $categoryLog['syncDirection'] = "syncToMagento";
                        $categoryLog['storeId'] = $storeId;
                        $categoryLog['messageType'] = "Success";
                        $txt = "Info : Category : '".$syncData['name']."' updated in Magento successfully!";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $this->categoryLogHelper->categorySyncLogs($categoryLog);
                    } else {

                        /**
                         * Create Category
                         */
                        if($storeId == 0  || $storeId == 1){
                            $syncData['store_id'] = 0;
                        }else{
                            $syncData['store_id'] = $storeId;
                        }
                        $categoryStatusInAcumatica = "";
                        $parentCategoryId = $acumatiucaParentCategoryId = 0;
                        if(isset($aData['CategoryInfo']['SyncStatus']['Value']))
                            $categoryStatusInAcumatica = $aData['CategoryInfo']['SyncStatus']['Value'];
                        if ($syncData['path'] && ($categoryStatusInAcumatica != '' && $categoryStatusInAcumatica == 'Active' )) {
                            $category = $this->categoryFact->create();
                            if ($syncData['acumatica_parent_category_id'] != 0) {
                                $parentCategory = $this->categoryFactory->create()->addFieldToFilter('acumatica_category_id', $syncData['acumatica_parent_category_id'])->getFirstItem();
                                if ($parentCategory->getId() != '') {
                                    $parentCategoryPath = $parentCategory->getPath();
                                    $parentCategoryId = $parentCategory->getId();
                                    $acumatiucaParentCategoryId = $parentCategory->getAcumaticaCategoryId();
                                }
                            }
                            $syncData['display_mode'] = 'PRODUCTS';
                            if(isset($aData['CategoryInfo']['SyncStatus']['Value']) &&  $aData['CategoryInfo']['SyncStatus']['Value'] == 'Active')
                            {
                                $syncData['is_active'] = 1;
                            }else{
                                $syncData['is_active'] = 0;
                            }
                            $syncData['path'] = $parentCategoryPath;
                            $syncData['parent_id'] = $parentCategoryId;
                            $syncData['acumatica_parent_category_id'] = $acumatiucaParentCategoryId;
                            if(isset($syncData['include_in_menu']))
                            {
                                if($syncData['include_in_menu'] == 'true')
                                    $syncData['include_in_menu'] = 1;
                                else
                                    $syncData['include_in_menu'] = 0;
                            }
                            if(isset($syncData['default_sort_by']))
                            {
                                $availableSortBy = array('Best Value' => 'position', 'Name' => 'name', 'Price' => 'price');
                                if (isset($availableSortBy[$syncData['default_sort_by']]))
                                    $syncData['default_sort_by'] = $availableSortBy[$syncData['default_sort_by']];
                            }
                            foreach ($syncData as $_key => $_value) {
                                if($_value != '' || $_key == 'acumatica_parent_category_id'){
                                    $_keyset = 'set' . ucfirst($_key);
                                    $category->$_keyset($_value);
                                }
                            }
                            $category->save();
                            $syncData['parent_id'] = $parentCategoryId = $category->getId();
                            $syncData['path'] = $parentCategoryPath = $parentCategoryPath . '/' . $parentCategoryId;
                            if ($category->getId() != '') {

                                /**
                                 * here need to check category is having products
                                 * if exist then need to check product exist in magento
                                 * if available then assign otherwise skip
                                 */
                                if(!empty($aData['Members']))
                                {
                                    $categoryMembers = $this->xmlHelper->xml2array($aData['Members']['ItemSalesCategoryMember']);
                                    $oneRecordFlag=false;
                                    $proIds = array();
                                    $position = '';
                                    foreach($categoryMembers as $_key => $_value)
                                    {
                                        if (!is_numeric($_key)) {
                                            $oneRecordFlag = true;
                                            break;
                                        }
                                        $inventoryId = $_value['InventoryID']['Value'];
                                        $inventoryId = str_replace(' ', '_', $inventoryId);
                                        $productId = $this->productFactory->create()->getIdBySku($inventoryId);
                                        if($productId)
                                        {
                                            if(isset($_value['RowNumber']['Value']))
                                                $position =  $_value['RowNumber']['Value'];
                                            else
                                                $position = $_key;

                                            $proIds[$productId] = $position;
                                        }
                                    }
                                    if($oneRecordFlag)
                                    {
                                        $inventoryId = $categoryMembers['InventoryID']['Value'];
                                        $productId = $this->productFactory->create()->getIdBySku($inventoryId);
                                        if($productId)
                                        {
                                            if(isset($categoryMembers['RowNumber']['Value']))
                                                $position =  $categoryMembers['RowNumber']['Value'];
                                            else
                                                $position = $_key;

                                            $proIds[$productId] = $position;
                                        }
                                    }
                                    if(isset($proIds))
                                    {
                                        $category->setPostedProducts($proIds);
                                        $category->save();
                                    }
                                }

                                /**
                                 * logs here for create category
                                 */
                                $categoryLog['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                $categoryLog['catId'] = $category->getId();
                                $categoryLog['acumaticaCategoryName'] = $syncData['name'];
                                $categoryLog['description'] = "Category : " .$syncData['name']. " created in Magento"; //Descripton
                                $categoryLog['runMode'] = $syncType; //Manual/Auto/Individual
                                if ($autoSync == 'COMPLETE') {
                                    $categoryLog['action'] = "Batch Process";//This needs to be dynamic value
                                } elseif ($autoSync == 'INDIVIDUAL') {
                                    $categoryLog['action'] = "Individual";//This needs to be dynamic value
                                }
                                $categoryLog['syncAction'] = "Category Synced To Magento";
                                $categoryLog['acumaticaCategoryId'] = trim($category->getAcumaticaCategoryId());
                                $categoryLog['syncDirection'] = "syncToMagento";
                                $categoryLog['storeId'] = $storeId;
                                $categoryLog['messageType'] = "Success";
                                $txt = "Info : Category : '".$syncData['name']."' created in Magento successfully!";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->categoryLogHelper->categorySyncLogs($categoryLog);
                            }
                        }
                    }
                } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
                    /**
                     *logs here to print exception
                     */
                    $categoryLog['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                    $categoryLog['catId'] = '';
                    $categoryLog['description'] = "Category : " . $syncData['name'] . " sync to Magento failed";
                    $categoryLog['longMessage'] = $e->getMessage();
                    $categoryLog['runMode'] = $syncType; //Manual/Auto/Individual
                    if ($autoSync == 'COMPLETE') {
                        $categoryLog['action'] = "Batch Process";//This needs to be dynamic value
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $categoryLog['action'] = "Individual";//This needs to be dynamic value
                    }
                    $categoryLog['acumaticaCategoryName'] = $syncData['name'];
                    $categoryLog['syncAction'] = "Category Not Synced";
                    $categoryLog['acumaticaCategoryId'] = $acumaticaCategoryId;
                    $categoryLog['syncDirection'] = "syncToMagento";
                    $categoryLog['storeId'] = $storeId;
                    $categoryLog['messageType'] = "Failure";
                    $txt = "Error: Category : '" . $syncData['name'] . "' sync to Magento failed";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->categoryLogHelper->categorySyncLogs($categoryLog);
                    $errorCheckInMagento[] = 1;
                } catch (Exception $e) {
                    /**
                     *logs here to print exception
                     */
                    $categoryLog['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                    $categoryLog['catId'] = '';
                    $categoryLog['acumaticaCategoryName'] = $syncData['name'];
                    $categoryLog['description'] = "Category : " . $syncData['name'] . " sync to Magento failed";
                    $categoryLog['longMessage'] = $e->getMessage();
                    $categoryLog['runMode'] = $syncType; //Manual/Auto/Individual
                    if ($autoSync == 'COMPLETE') {
                        $categoryLog['action'] = "Batch Process";//This needs to be dynamic value
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $categoryLog['action'] = "Individual";//This needs to be dynamic value
                    }
                    $categoryLog['syncAction'] = "Category Not Synced";
                    $categoryLog['acumaticaCategoryId'] = $acumaticaCategoryId;
                    $categoryLog['syncDirection'] = "syncToMagento";
                    $categoryLog['storeId'] = $storeId;
                    $categoryLog['messageType'] = "Failure";
                    $txt = "Error: Category : '" . $syncData['name'] . "' sync to Magento failed";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->categoryLogHelper->categorySyncLogs($categoryLog);
                    $errorCheckInMagento[] = 1;
                }
            }
        }
        return $this->errorCheckInMagento;
    }

    public function syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $scopeType,$storeId, $logViewFileName,$directionFlag,$url,$webServiceUrl)
    {
        $categoryId = $aData['magento_category_id'];
        if (isset($categoryId) && $categoryId != $this->_storeManager->getStore($storeId)->getRootCategoryId())
        {
            $acumaticaCategoryId = trim($aData['acumatica_category_id']);

            if ($categoryId)
            {
                if($storeId > 1)
                    $categoryCollection = $this->categoryFact->create()->setStoreId($storeId)->load($categoryId);
                else
                    $categoryCollection = $this->categoryFact->create()->setStoreId(0)->load($categoryId);
            }
            $availableSortBy = array('position' => 'Best Value', 'name' => 'Name', 'price' => 'Price');
            $includeInMenu = array('1' => 'true', '0' => 'false');
            /**
             * check that category having products
             * if exist then need to check those product are exist in acumatica
             * if exist then assign to category otherwise skip
             */
            if (isset($categoryCollection)) {
                foreach ($categoryCollection->getProductCollection() as $product) {
                    $inventoryId = str_replace('_', ' ', $product->getSku());
                    $productAvailable = $this->productHelper->getProductBySku($url, $inventoryId,$storeId);
                    if ($productAvailable) {
                        $productSkus[] = $inventoryId;
                    }
                }
            }
            $syncData = array();
            $errorCheckInAcumatica = array();
            foreach ($acumaticaAttributes as $key => $value) {
                if (isset($categoryCollection)) {
                    $mappingData = explode('|', $value);
                    if ($directionFlag && $mappingData[1] == "Bi-Directional (Acumatica Wins)") {
                        continue;
                    }
                    if ($mappingData[0] == 'ProductSortBy') {
                        if (isset($availableSortBy[$categoryCollection[$key]]))
                            $syncData[$mappingData[0]] = $availableSortBy[$categoryCollection[$key]];
                        else
                            $syncData[$mappingData[0]] = "Best Value";
                    } else if ($mappingData[0] == 'IncludeinNavigationMenu') {
                        if (isset($includeInMenu[$categoryCollection[$key]]))
                            $syncData[$mappingData[0]] = $includeInMenu[$categoryCollection[$key]];
                    } else {
                        if (isset($categoryCollection[$key]))
                            $syncData[$mappingData[0]] = strip_tags($categoryCollection[$key]);
                    }
                }
            }
            $syncData['Path'] = $aData['acumatica_category_path'];
            /**
             * Retrieving actual acumatica path
             */
            if (isset($categoryCollection)) {
                $categoryActiveStatus = $categoryCollection->getIsActive();
            } else {
                $categoryActiveStatus = 0;;
            }
            if ($categoryId != '' && $syncData['Description'] != '' && $categoryActiveStatus != 0) {
                if ($syncData['Path'] != '' && $syncData['Description'] != '') {
                    $pathArray = explode('/', $syncData['Path']);
                    if (count($pathArray) > 1) {
                        $arrayCount = count($pathArray) - 1;
                        unset($pathArray[$arrayCount]);
                        $actualAcumaticaPath = implode('/', $pathArray);
                    }
                    $parentId = $this->categoryFact->create()->setStoreId($storeId)->load($categoryId)->getParentId();
                    $parentCategoryFlag = false;
                    if ($parentId != $this->_storeManager->getStore($storeId)->getRootCategoryId()) {
                        $parentCategoryCollection = $this->categoryFact->create()->setStoreId($storeId)->load($parentId);
                        if ($parentCategoryCollection->getIsActive() != 0) {
                            $parentCategoryId = $parentCategoryCollection->getAcumaticaCategoryId();
                            $parentCategoryFlag = true;
                        }
                    } else {
                        $parentCategoryId = 0;
                        $parentCategoryFlag = true;
                    }

                    /**
                     * need to check category have parent or not in magento
                     * if category have parent then need to verify that the category is active or not
                     * if inactive then don't consider this category in sync
                     * need check category exist in acumatica or not
                     * getCategoryById
                     */

                    if ($parentCategoryFlag) {
                        /**
                         * This is because of using new acumatica version in order to get category by id
                         */
                        $getCategory = '';
                        if ($acumaticaCategoryId != '') {
                            $getCategory = $this->categoryResourceModel->getAcumaticaCategoryById($url, $acumaticaCategoryId, $storeId);
                        }
                        if ($getCategory == '') {
                            /**
                             * Creating Category in Acumatica
                             */
                            try {
                                /*if ($autoSync == 'INDIVIDUAL') {
                                    $webServiceUrl = $this->urlHelper->getNewWebserviceUrl($scopeType, $storeId);
                                    $webServiceCookies = $this->clientHelper->login(array(), $webServiceUrl, $storeId);
                                }*/

                                $XMLGetRequest = '';
                                $XMLGetRequest .= '<CategoryID xsi:nil="true" /><CategoryInfo><ID xsi:nil="true" /><Delete>false</Delete><ReturnBehavior>All</ReturnBehavior>';

                                if (isset($syncData['IncludeinNavigationMenu'])) {
                                    $XMLGetRequest .= '<IncludeinNavigationMenu><Value>' . $syncData['IncludeinNavigationMenu'] . '</Value></IncludeinNavigationMenu>';
                                }
                                if (isset($syncData['ProductSortBy'])) {
                                    $XMLGetRequest .= '<ProductSortBy><Value>' . $syncData['ProductSortBy'] . '</Value></ProductSortBy>';
                                }
                                if (isset($syncData['URLKey'])) {
                                    $XMLGetRequest .= '<URLKey><Value>' . $syncData['URLKey'] . '</Value></URLKey>';
                                }
                                if (isset($syncData['DescriptionLong'])) {
                                    $XMLGetRequest .= '<DescriptionLong><Value>' . strip_tags($syncData['DescriptionLong']) . '</Value></DescriptionLong>';
                                }
                                if (isset($syncData['MetaTitle'])) {
                                    $XMLGetRequest .= '<MetaTitle><Value>' . $syncData['MetaTitle'] . '</Value></MetaTitle>';
                                }
                                if (isset($syncData['MetaDescription'])) {
                                    $XMLGetRequest .= '<MetaDescription><Value>' . $syncData['MetaDescription'] . '</Value></MetaDescription>';
                                }
                                if (isset($syncData['MetaKeywords'])) {
                                    $XMLGetRequest .= '<MetaKeywords><Value>' . $syncData['MetaKeywords'] . '</Value></MetaKeywords>';
                                }
                                if ($categoryCollection->getIsActive() != 0) {
                                    $XMLGetRequest .= '<SyncStatus><Value>Active</Value></SyncStatus>';
                                } else {
                                    $XMLGetRequest .= '<SyncStatus><Value>InActive</Value></SyncStatus>';
                                }

                                $XMLGetRequest .= '</CategoryInfo>';

                                if (isset($syncData['Description'])) {
                                    $XMLGetRequest .= '<Description><Value>' . $syncData['Description'] . '</Value></Description>';
                                }
                                /**
                                 * Here need to do category and product mapping
                                 */
                                if (!empty($productSkus) && count($productSkus) > 0) {
                                    $XMLGetRequest .= '<Members>';
                                    foreach($productSkus as $productSku){
                                        $XMLGetRequest .= '<ItemSalesCategoryMember><ID xsi:nil="true" /><Delete>false</Delete><ReturnBehavior>All</ReturnBehavior><InventoryID><Value>'.$productSku.'</Value></InventoryID></ItemSalesCategoryMember>';
                                    }
                                    $XMLGetRequest .= '</Members>';
                                }

                                $csvCreateCategory = $this->syncHelper->getEnvelopeData('CREATECATEGORY');
                                $csvCreateCategoryEnvelope = $csvCreateCategory['envelope'];
                                $csvCreateCategoryEnvelope = str_replace('{{PARENTCATEGORYID}}', $parentCategoryId, $csvCreateCategoryEnvelope);
                                $XMLGetRequest = str_replace('{{CREATECATEGORY}}', $XMLGetRequest, $csvCreateCategoryEnvelope);
                                $action = $csvCreateCategory['envName'] . "/" . $csvCreateCategory['envVersion'] . "/" . $csvCreateCategory['methodName'];
                                $createXml = $this->clientHelper->getAcumaticaResponse($XMLGetRequest, $url, $action, $storeId);
                                if (isset($createXml->Body->Fault->faultstring) && is_object($createXml->Body->Fault->faultstring))
                                {
                                    $categoryLog['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                    $categoryLog['description'] = "Category : " . $syncData['Description'] . " sync to Acumatica failed"; //Descripton
                                    $categoryLog['longMessage'] = json_encode($createXml);
                                    $categoryLog['runMode'] = $syncType; //Manual/Auto/Individual
                                    if ($autoSync == 'COMPLETE') {
                                        $categoryLog['action'] = "Batch Process";//This needs to be dynamic value
                                    } elseif ($autoSync == 'INDIVIDUAL') {
                                        $categoryLog['action'] = "Individual";//This needs to be dynamic value
                                    }
                                    $categoryLog['syncAction'] = "Category Not Synced";
                                    $categoryLog['acumaticaCategoryId'] = '';
                                    $categoryLog['syncDirection'] = "syncToAcumatica";
                                    $categoryLog['storeId'] = $storeId;
                                    $categoryLog['messageType'] = "Failure";
                                    $txt = "Error: Category : '" . $syncData['Description'] . "' sync to Acumatica failed";
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                    $this->categoryLogHelper->categorySyncLogs($categoryLog);
                                    $errorCheckInAcumatica[] = 1;
                                }else {
                                    if(isset($createXml->Body->PutResponse->PutResult->CategoryID->Value))
                                    {
                                        $getCreateAcumaticaCategoryId = trim($createXml->Body->PutResponse->PutResult->CategoryID->Value);
                                        $getCreateAcumaticaParentCategoryId = trim($createXml->Body->PutResponse->PutResult->ParentCategoryID->Value);
                                        if ($getCreateAcumaticaCategoryId != '' && $getCreateAcumaticaParentCategoryId != '') {
                                            $categoryUpdate = $this->categoryFact->create();
                                            $categoryUpdate->load($categoryId);
                                            $categoryUpdate->setAcumaticaCategoryId($getCreateAcumaticaCategoryId);
                                            $categoryUpdate->setAcumaticaParentCategoryId($getCreateAcumaticaParentCategoryId);
                                            $categoryUpdate->save();
                                            $categoryLog['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                            $categoryLog['catId'] = $categoryId;
                                            $categoryLog['acumaticaCategoryName'] = $syncData['Description'];
                                            $categoryLog['description'] = "Category : " . $syncData['Description'] . " created in Acumatica"; //Descripton
                                            $categoryLog['runMode'] = $syncType; //Manual/Auto/Individual
                                            if ($autoSync == 'COMPLETE') {
                                                $categoryLog['action'] = "Batch Process";//This needs to be dynamic value
                                            } elseif ($autoSync == 'INDIVIDUAL') {
                                                $categoryLog['action'] = "Individual";//This needs to be dynamic value
                                            }
                                            $categoryLog['syncAction'] = "Category Synced To Acumatica";
                                            $categoryLog['acumaticaCategoryId'] = $getCreateAcumaticaCategoryId;
                                            $categoryLog['syncDirection'] = "syncToAcumatica";
                                            $categoryLog['storeId'] = $storeId;
                                            $categoryLog['messageType'] = "Success";
                                            $txt = "Info : Category : '" . $syncData['Description'] . "' created in Acumatica successfully!";
                                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                            $this->categoryLogHelper->categorySyncLogs($categoryLog);
                                        }
                                    }
                                }
                            } catch (SoapFault $e) {

                                $categoryLog['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                $categoryLog['description'] = "Category : " . $syncData['Description'] . " sync to Acumatica failed"; //Descripton
                                $categoryLog['longMessage'] = $e->getMessage();
                                $categoryLog['runMode'] = $syncType; //Manual/Auto/Individual
                                if ($autoSync == 'COMPLETE') {
                                    $categoryLog['action'] = "Batch Process";//This needs to be dynamic value
                                } elseif ($autoSync == 'INDIVIDUAL') {
                                    $categoryLog['action'] = "Individual";//This needs to be dynamic value
                                }
                                $categoryLog['syncAction'] = "Category Not Synced";
                                $categoryLog['acumaticaCategoryId'] = '';
                                $categoryLog['syncDirection'] = "syncToAcumatica";
                                $categoryLog['storeId'] = $storeId;
                                $categoryLog['messageType'] = "Failure";
                                $txt = "Error: Category : '" . $syncData['Description'] . "' sync to Acumatica failed";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->categoryLogHelper->categorySyncLogs($categoryLog);
                                $errorCheckInAcumatica[] = 1;
                            }
                        } else {
                            /**
                             * update Category By Id in Acumatica
                             */
                            try {
                                /*if ($autoSync == 'INDIVIDUAL') {
                                    $webServiceUrl = $this->urlHelper->getNewWebserviceUrl($scopeType, $storeId);
                                    $webServiceCookies = $this->clientHelper->login(array(), $webServiceUrl, $storeId);
                                }*/
                                $parentCategory = $parentCategoryId; //set path first while creating

                                $XMLGetRequest = '';
                                if (isset($syncData['Description'])) {
                                    $XMLGetRequest .= '<Command xsi:type="Value"><Value>' . $syncData['Description'] . '</Value><LinkedCommand xsi:type="Field"><FieldName>Description</FieldName><ObjectName>CategoryInfo</ObjectName><Value>Description</Value><Commit>true</Commit></LinkedCommand></Command>';
                                }
                                if (isset($syncData['IncludeinNavigationMenu'])) {
                                    $XMLGetRequest .= '<Command xsi:type="Value"><Value>' . $syncData['IncludeinNavigationMenu'] . '</Value><LinkedCommand xsi:type="Field"><FieldName>IncludeInNavigationMenu</FieldName><ObjectName>CategoryInfo: 1</ObjectName><Value>IncludeInNavigationMenu</Value></LinkedCommand></Command>';
                                }
                                if (isset($syncData['ProductSortBy'])) {
                                    $XMLGetRequest .= '<Command xsi:type="Value"><Value>' . $syncData['ProductSortBy'] . '</Value><LinkedCommand xsi:type="Field"><FieldName>ProductSortBy</FieldName><ObjectName>CategoryInfo: 1</ObjectName><Value>ProductSortBy</Value></LinkedCommand></Command>';
                                }
                                if (isset($syncData['URLKey'])) {
                                    $XMLGetRequest .= '<Command xsi:type="Value"><Value>' . $syncData['URLKey'] . '</Value><LinkedCommand xsi:type="Field"><FieldName>URLKey</FieldName><ObjectName>CategoryInfo: 1</ObjectName><Value>URLKey</Value></LinkedCommand></Command>';
                                }
                                if (isset($syncData['DescriptionLong'])) {
                                    $XMLGetRequest .= '<Command xsi:type="Value"><Value>' . strip_tags($syncData['DescriptionLong']) . '</Value><LinkedCommand xsi:type="Field"><FieldName>DescriptionLong</FieldName><ObjectName>CategoryInfo: 1</ObjectName><Value>DescriptionLong</Value></LinkedCommand></Command>';
                                }
                                if (isset($syncData['MetaTitle'])) {
                                    $XMLGetRequest .= '<Command xsi:type="Value"><Value>' . $syncData['MetaTitle'] . '</Value><LinkedCommand xsi:type="Field"><FieldName>MetaTitle</FieldName><ObjectName>CategoryInfo: 1</ObjectName><Value>MetaTitle</Value></LinkedCommand></Command>';
                                }
                                if (isset($syncData['MetaDescription'])) {
                                    $XMLGetRequest .= '<Command xsi:type="Value"><Value>' . $syncData['MetaDescription'] . '</Value><LinkedCommand xsi:type="Field"><FieldName>MetaDescription</FieldName><ObjectName>CategoryInfo: 1</ObjectName><Value>MetaDescription</Value></LinkedCommand></Command>';
                                }
                                if (isset($syncData['MetaKeywords'])) {
                                    $XMLGetRequest .= '<Command xsi:type="Value"><Value>' . $syncData['MetaKeywords'] . '</Value><LinkedCommand xsi:type="Field"><FieldName>MetaKeywords</FieldName><ObjectName>CategoryInfo: 1</ObjectName><Value>MetaKeywords</Value></LinkedCommand></Command>';
                                }
                                if ($categoryCollection->getIsActive() != 0) {
                                    $XMLGetRequest .= '<Command xsi:type="Value"><Value>Active</Value><LinkedCommand xsi:type="Field"><FieldName>Status</FieldName><ObjectName>CategoryInfo: 1</ObjectName><Value>Status</Value></LinkedCommand></Command>';
                                } else {
                                    $XMLGetRequest .= '<Command xsi:type="Value"><Value>InActive</Value><LinkedCommand xsi:type="Field"><FieldName>Status</FieldName><ObjectName>CategoryInfo: 1</ObjectName><Value>Status</Value></LinkedCommand></Command>';
                                }

                                /**
                                 * Here need to do category and product mapping
                                 */
                                if (!empty($productSkus) && count($productSkus) > 0) {
                                    $XMLGetRequest .= '<Command xsi:type="Value"><Value>' . implode(',', $productSkus) . '</Value><LinkedCommand xsi:type="Field"><FieldName>CategoryMembers</FieldName><ObjectName>CategoryInfo: 2</ObjectName><Value>CategoryMembers</Value><Commit>true</Commit></LinkedCommand></Command>';
                                }

                                $csvUpdateCategory = $this->syncHelper->getEnvelopeData('UPDATECATEGORY');
                                $csvUpdateCategoryEnvelope = $csvUpdateCategory['envelope'];
                                $csvUpdateCategoryEnvelope = str_replace('{{ACUMATICACATEGORYID}}', $acumaticaCategoryId, $csvUpdateCategoryEnvelope);
                                $csvUpdateCategoryEnvelope = str_replace('{{PARENTCATEGORYID}}', $parentCategory, $csvUpdateCategoryEnvelope);
                                $XMLGetRequest = str_replace('{{UPDATECATEGORY}}', $XMLGetRequest, $csvUpdateCategoryEnvelope);
                                $action = 'KN300080/Submit'; //$csvUpdateCategory['envName'] . "/" . $csvUpdateCategory['envVersion'] . "/" . $csvUpdateCategory['methodName'];                               
                                $xml = $this->clientHelper->getAcumaticaResponse($XMLGetRequest, $webServiceUrl, $action, $storeId,NULL,1);
                                $getAcumaticaCategoryId = trim($xml->Body->KN300080SubmitResponse->SubmitResult->Content->CategoryInfo->ResCategoryID->Value);
                                $getAcumaticaParentCategoryId = trim($xml->Body->KN300080SubmitResponse->SubmitResult->Content->CategoryInfo->ParentCategoryID->Value);
                                if ($getAcumaticaCategoryId != '' && $getAcumaticaParentCategoryId != '') {
                                    $categoryUpdate = $this->categoryFact->create()->setStoreId($storeId);
                                    $categoryUpdate->load($categoryId);
                                    $categoryUpdate->setAcumaticaCategoryId($getAcumaticaCategoryId);
                                    $categoryUpdate->setAcumaticaParentCategoryId($getAcumaticaParentCategoryId);
                                    $categoryUpdate->save();
                                    $categoryLog['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                    $categoryLog['catId'] = $categoryId;
                                    $categoryLog['acumaticaCategoryName'] = $syncData['Description'];
                                    $categoryLog['description'] = "Category : " . $syncData['Description'] . " updated in Acumatica"; //Descripton
                                    $categoryLog['runMode'] = $syncType; //Manual/Auto/Individual
                                    if ($autoSync == 'COMPLETE') {
                                        $categoryLog['action'] = "Batch Process";//This needs to be dynamic value
                                    } elseif ($autoSync == 'INDIVIDUAL') {
                                        $categoryLog['action'] = "Individual";//This needs to be dynamic value
                                    }
                                    $categoryLog['syncAction'] = "Category Synced To Acumatica";
                                    $categoryLog['acumaticaCategoryId'] = $getAcumaticaCategoryId;
                                    $categoryLog['syncDirection'] = "syncToAcumatica";
                                    $categoryLog['messageType'] = "Success";
                                    $categoryLog['storeId'] = $storeId;
                                    $txt = "Info : Category : '" . $syncData['Description'] . "' updated in Acumatica successfully!";
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                    $this->categoryLogHelper->categorySyncLogs($categoryLog);
                                }
                            } catch (SoapFault $e) {

                                /**
                                 *logs here to print exception
                                 */
                                $categoryLog['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                                $categoryLog['description'] = "Category : " . $syncData['Description'] . " Failed To Sync In Acumatica"; //Descripton
                                $categoryLog['longMessage'] = $e->getMessage();
                                $categoryLog['runMode'] = $syncType; //Manual/Auto/Individual
                                if ($autoSync == 'COMPLETE') {
                                    $categoryLog['action'] = "Batch Process";//This needs to be dynamic value
                                } elseif ($autoSync == 'INDIVIDUAL') {
                                    $categoryLog['action'] = "Individual";//This needs to be dynamic value
                                }
                                $categoryLog['syncAction'] = "Category Not Synced";
                                $categoryLog['acumaticaCategoryId'] = '';
                                $categoryLog['syncDirection'] = "syncToAcumatica";
                                $categoryLog['messageType'] = "Failure";
                                $categoryLog['storeId'] = $storeId;
                                $txt = "Error: Category : '" . $syncData['Description'] . "' sync to Acumatica failed";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->categoryLogHelper->categorySyncLogs($categoryLog);
                                $errorCheckInAcumatica[] = 1;
                            }
                        }
                    }
                } else {
                    /**
                     * logs here for category required fields
                     */
                    $categoryLog['schedule_id'] = $syncLogID; //It will dynamic Cron ID
                    $categoryLog['description'] = "Mandatory fields must be filled to sync category(s)"; //Descripton
                    $categoryLog['runMode'] = $syncType; //Manual/Auto/Individual
                    if ($autoSync == 'COMPLETE') {
                        $categoryLog['action'] = "Batch Process";//This needs to be dynamic value
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $categoryLog['action'] = "Individual";//This needs to be dynamic value
                    }
                    $categoryLog['syncAction'] = "Category Not Synced";
                    $categoryLog['acumaticaCategoryId'] = '';
                    $categoryLog['syncDirection'] = "syncToAcumatica";
                    $categoryLog['messageType'] = "Failure";
                    $categoryLog['storeId'] = $storeId;
                    $txt = "Error: Category ID : " . $categoryId . " : Mandatory fields must be filled to sync category(s). Please try again";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->categoryLogHelper->categorySyncLogs($categoryLog);
                    $errorCheckInAcumatica[] = 1;
                }
            }

            return $errorCheckInAcumatica;
        }
    }
}
