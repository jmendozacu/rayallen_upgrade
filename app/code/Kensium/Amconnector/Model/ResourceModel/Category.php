<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Symfony\Component\Config\Definition\Exception\Exception;
use Kensium\Lib;

/**
 * Class Category
 * @package Kensium\Amconnector\Model\ResourceModel
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
     * @var
     */
    protected $amconnectorCategoryHelper;
    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $amconnectorHelper;
    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;
    /**
     * @var Sync
     */
    protected $resourceModelSync;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Kensium\Amconnector\Helper\Client
     */
    protected $clientHelper;
    /**
     * @var \Kensium\Amconnector\Helper\Sync
     */
    protected $syncHelper;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    protected $dataHelper;
    protected $common;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param DateTime $date
     * @param \Kensium\Amconnector\Helper\Data $amconnectorHelper
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param Sync $resourceModelSync
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Kensium\Amconnector\Helper\Client $clientHelper
     * @param \Kensium\Amconnector\Helper\Sync $syncHelper
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        DateTime $date,
        //\Kensium\Amconnector\Helper\Category $amconnectorCategoryHelper,
        \Kensium\Amconnector\Helper\Data $amconnectorHelper,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Kensium\Amconnector\Helper\Data $dataHelper,
         Lib\Common $common,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->date = $date;
        $this->storeRepository = $storeRepository;
        //$this->amconnectorCategoryHelper = $amconnectorCategoryHelper;
        $this->amconnectorHelper = $amconnectorHelper;
        $this->resourceModelSync = $resourceModelSync;
        $this->_storeManager = $storeManager;
        $this->clientHelper = $clientHelper;
        $this->syncHelper = $syncHelper;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->dataHelper = $dataHelper;
        $this->common = $common;
    }

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amconnector_category_mapping', 'id');
    }

    /**
     * @return string
     */
    public function getAcumaticaAttrCount(){
        $acumaticaCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_acumatica_category_attributes'));
        return $acumaticaCount;
    }

    /**
     *Checking count of the mapping table
     */
    public function checkCategoryMapping($storeId)
    {
        $orderAttributes = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_category_mapping') . " WHERE store_id =" . $storeId);
        return $orderAttributes;
    }

    /**
     * @param $storeId
     * delete category mapping table data based on current store
     */
    public function deleteMappingTableData($storeId)
    {
        $this->getConnection()->query("DELETE FROM " . $this->getTable("amconnector_category_mapping")." WHERE store_id = $storeId ");
    }

    /**
     *update sync attribute table
     */
    public function updateSyncAttribute($syncId, $status, $storeId)
    {
        $updatedDate = $this->date->date('Y-m-d H:i:s', time());
        $this->getConnection()->query("UPDATE " . $this->getTable("amconnector_attribute_sync") . " set status='" . $status . "', last_sync_date='" . $updatedDate . "' where id='" . $syncId . "' and store_id= '" . $storeId . "' ");
    }

    /**
     * @return mixed
     * Get stop sync value for database
     */
    public function stopSyncValue()
    {
        $query = "SELECT value FROM " .  $this->getTable("core_config_data")." WHERE path ='amconnectorsync/categorysync/syncstopflg' ";
        $value = $this->getConnection()->fetchOne($query);
        return $value;
    }

    /**
     * Enable stop sync flag at end of sync
     */
    public function enableSync()
    {
        $path = 'amconnectorsync/categorysync/syncstopflg';
        $query = "update " . $this->getTable("core_config_data")." set value = 1 where path ='" . $path . "'";
        $this->getConnection()->query($query);
    }

    /**
     * Truncate temp table
     */
    public function truncateDataFromTempTables()
    {
        $query = "TRUNCATE table " . $this->getTable("amconnector_category_sync_temp");
        $this->getConnection()->query($query);
    }

    /**
     * Fetching the data based on last sync date
     * and Inserting into the temp table
     *
     * First fetching from Acumatica and if same record is updated in Magento
     * then updating the same record with category detail and updated date in temp table
     *
     * @param $acumaticaData
     * @param $syncId
     * @param $storeId
     * @return mixed
     */

    public function insertDataIntoTempTables($acumaticaData, $syncId,$scopeType,$storeId)
    {
        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();

        /**
         * Based on the last sync date get the data from Acumatica and insert into temporary table
         */
        $oneRecordFlag=false;

        $categorySyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/categorysync/syncdirection',$scopeType,$storeId);
        if ($categorySyncDirection == 1 || $categorySyncDirection == 3)
        {
            if (isset($acumaticaData['Entity'])) {
                $parentCategoryId = array();
                foreach ($acumaticaData['Entity'] as $key => $value) {
                    if (!is_numeric($key)) {
                        $oneRecordFlag = true;
                        break;
                    }
                    if(!in_array($value->ParentCategoryID->Value, array_map('current', $parentCategoryId))){
                        $acumaticaCategoryId = $value->CategoryID->Value;
                        $acumaticaCategoryName = $value->Description->Value;
                        $acumaticaCategoryName = addslashes($acumaticaCategoryName);
                        $acumaticaParentCategoryId = $value->ParentCategoryID->Value;
                        $acumaticaParentCategoryName = '';
                        $acumaticaCategoryPath = $value->Path->Value;
                        $acumaticaCategoryPath = addslashes($acumaticaCategoryPath);
                        $acumaticaModifiedDate = date('Y-m-d H:i:s', strtotime($value->LastModifiedDateTime->Value));
			if(isset($value->CategoryInfo->SyncStatus->Value) && $value->CategoryInfo->SyncStatus->Value == 'Active'){
                        if ($acumaticaCategoryId < 1) continue;
                        $this->getConnection()->query("INSERT INTO `" . $this->getTable("amconnector_category_sync_temp") . "`
                (`id`, `magento_category_id`,`acumatica_category_id`, `acumatica_category_description`,`acumatica_parent_category_id`,`acumatica_parent_category_name`,`acumatica_category_path`, `acumatica_category_skus`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `store_id`, `flg`,`entity_ref`)
                VALUES (NULL, NULL,'" . $acumaticaCategoryId . "', '" . $acumaticaCategoryName . "','" . $acumaticaParentCategoryId . "','" . $acumaticaParentCategoryName . "', '" . $acumaticaCategoryPath . "',NULL,NULL, '" . $acumaticaModifiedDate . "', '" . $storeId . "', '0','" . $key . "')");
		   }
                    }else{
                        $parentCategoryId[] = $value->CategoryID->Value;
                        $parentCatStatus = $value->CategoryInfo->SyncStatus->Value;
                    }
                }
                if ($oneRecordFlag) {
                    if($acumaticaData['Entity']['CategoryInfo']['SyncStatus']['Value'] == "Active"){
                        $acumaticaCategoryId = $acumaticaData['Entity']['CategoryID']['Value'];
                        $acumaticaCategoryName = $acumaticaData['Entity']['Description']['Value'];
                        $acumaticaCategoryName = addslashes($acumaticaCategoryName);
                        $acumaticaParentCategoryId = $acumaticaData['Entity']['ParentCategoryID']['Value'];
                        $acumaticaParentCategoryName = '';
                        $acumaticaModifiedDate = date('Y-m-d H:i:s', strtotime($acumaticaData['Entity']['LastModifiedDateTime']['Value']));
                        $acumaticaCategoryPath = $acumaticaData['Entity']['Path']['Value'];
                        $acumaticaCategoryPath = addslashes($acumaticaCategoryPath);
                        if ($acumaticaCategoryId >= 1)
                            $this->getConnection()->query("INSERT INTO `" . $this->getTable("amconnector_category_sync_temp") . "`
                (`id`, `magento_category_id`,`acumatica_category_id`, `acumatica_category_description`,`acumatica_parent_category_id`,`acumatica_parent_category_name`,`acumatica_category_path`, `acumatica_category_skus`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `store_id`, `flg`,`entity_ref`)
                VALUES (NULL, NULL,'" . $acumaticaCategoryId . "', '" . $acumaticaCategoryName . "','" . $acumaticaParentCategoryId . "','" . $acumaticaParentCategoryName . "',  '" . $acumaticaCategoryPath . "',NULL,NULL, '" . $acumaticaModifiedDate . "', '" . $storeId . "', '0',NULL)");
                    }
                }
            }
        }

        /**
         * Get store id based on store id
         * Based on the last sync date get the data from Magento and insert/update into temporary table
         */
        if ($categorySyncDirection == 2 || $categorySyncDirection == 3) {
            $attrIds = array();
            $categoryAttributeId = $this->getConnection()->fetchOne("SELECT attribute_id FROM `" . $this->getTable('eav_attribute') . "` WHERE `attribute_code` in ('acumatica_category_id') AND `entity_type_id` = 3");
            $categoryNameAttrId = $this->getConnection()->fetchOne("SELECT attribute_id FROM `" . $this->getTable('eav_attribute') . "` WHERE `attribute_code` in ('name') AND `entity_type_id` = 3");

            $lastSyncDate = $this->date->gmtDate('Y-m-d H:i:s', $this->resourceModelSync->getLastSyncDate($syncId, $storeId));
            $magentoData = $this->getConnection()->fetchAll("SELECT entity_id,path,updated_at FROM " . $this->getTable('catalog_category_entity') . "  WHERE updated_at >='" . $lastSyncDate . "' and entity_id > 2 ");

            foreach ($magentoData as $mData) {
                $updatedDate = $this->date->date('Y-m-d H:i:s', strtotime($mData['updated_at']));
                $categoryPath = $mData['path'];
                $magentoCatId = $mData['entity_id'];
                if ($storeId == 1) {
                    $_storeId = 0;
                } else {
                    $_storeId = $storeId;
                }
                $acumaticaCatId = $this->getConnection()->fetchOne("SELECT value FROM " . $this->getTable('catalog_category_entity_varchar') . "  WHERE row_id='" . $magentoCatId . "' and attribute_id ='" . $categoryAttributeId . "' and store_id =" . $_storeId . "  ");
                $categoryName = $this->getConnection()->fetchOne("SELECT value FROM " . $this->getTable('catalog_category_entity_varchar') . "  WHERE row_id='" . $magentoCatId . "' and attribute_id ='" . $categoryNameAttrId . "' and store_id =" . $_storeId . "  ");
                $acuCategoryPath = $this->getAcumaticaTreePath($categoryPath, $scopeType, $storeId, $categoryNameAttrId);
                $categoryName = addslashes($categoryName);
                $acuCategoryPath = addslashes($acuCategoryPath);
                if ($acumaticaCatId != '') {
                    $recordCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_category_sync_temp') . "
                WHERE acumatica_category_id ='" . $acumaticaCatId . "' and store_id = '" . $storeId . "'  ");
                } else {
                    $recordCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_category_sync_temp') . "
                WHERE acumatica_category_path ='" . $acuCategoryPath . "' and store_id = '" . $storeId . "'  ");
                }
                if ($recordCount) {
                    $this->getConnection()->query("UPDATE " . $this->getTable('amconnector_category_sync_temp') . "
                set magento_category_id='" . $magentoCatId . "',acumatica_category_description='" . $categoryName . "',acumatica_category_path='" . $acuCategoryPath . "', magento_lastsyncdate='" . $updatedDate . "'
                where acumatica_category_id='" . $acumaticaCatId . "' and store_id= '" . $storeId . "' ");
                } else {
                    $this->getConnection()->query("INSERT INTO `" . $this->getTable('amconnector_category_sync_temp') . "`
                (`id`, `magento_category_id`,`acumatica_category_id`, `acumatica_category_description`,`acumatica_parent_category_id`,`acumatica_parent_category_name`,`acumatica_category_path`, `acumatica_category_skus`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `store_id`, `flg`,`entity_ref`)
                VALUES (NULL, '" . $magentoCatId . "','" . $acumaticaCatId . "', '" . $categoryName . "',NULL,NULL,  '" . $acuCategoryPath . "',NULL,'" . $updatedDate . "', NULL, '" . $storeId . "', '0',NULL)");
                }
            }
        }
        try{
            $records = $this->getConnection()->fetchAll("SELECT * FROM `".$this->getTable('amconnector_category_sync_temp')."` WHERE store_id = '".$storeId."' ORDER BY acumatica_category_id ");
            $data = array();
            $results = $this->getConnection()->fetchAll("SELECT magento_attr_code,acumatica_attr_code,sync_direction FROM `".$this->getTable('amconnector_category_mapping')."` WHERE store_id =".$storeId);
            foreach($results as $result){
                $attrCode = $result['magento_attr_code'];

                $mappingAttributes[$attrCode] = $result['acumatica_attr_code'] ."|". $result['sync_direction'];
            }
            $biDirectional = array();
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
                    if(count(array_unique($biDirectional)) === 2 && in_array('Acumatica to Magento', $biDirectional)){
                        if($record['magento_lastsyncdate'] > $record['acumatica_lastsyncdate'])
                        {
                            $acuFlag = 1;
                            $magFlag = 0;
                        }else {
                            $magFlag = 1;
                            $acuFlag = 0;
                        }
                    }else{
                        if($record['magento_lastsyncdate'] > $record['acumatica_lastsyncdate'])
                            $acuFlag = 1;
                        else
                            $magFlag = 1;
                    }
                }

                if($magFlag){
                    $data['magento'][] = array(
                        "id" => $record['id'],
                        "magento_category_id" => $record['magento_category_id'],
                        "acumatica_category_id" => $record['acumatica_category_id'],
                        "acumatica_category_description" => $record['acumatica_category_description'],
                        "acumatica_parent_category_id" => $record['acumatica_parent_category_id'],
                        "acumatica_parent_category_name" => $record['acumatica_parent_category_name'],
                        "acumatica_category_path" => $record['acumatica_category_path'],
                        "acumatica_category_skus" => $record['acumatica_category_skus'],
                        "acumatica_lastsyncdate" => $record['acumatica_lastsyncdate'],
                        "magento_lastsyncdate" => $record['magento_lastsyncdate'],
                        "store_id" => $record['store_id'],
                        "entity_ref" => $record['entity_ref'],
                        "flg" => $record['flg']
                    );
                }

                if($acuFlag){
                    $data['acumatica'][] = array(
                        "id" => $record['id'],
                        "magento_category_id" => $record['magento_category_id'],
                        "acumatica_category_id" => $record['acumatica_category_id'],
                        "acumatica_category_description" => $record['acumatica_category_description'],
                        "acumatica_parent_category_id" => $record['acumatica_parent_category_id'],
                        "acumatica_parent_category_name" => $record['acumatica_parent_category_name'],
                        "acumatica_category_path" => $record['acumatica_category_path'],
                        "acumatica_category_skus" => $record['acumatica_category_skus'],
                        "acumatica_lastsyncdate" => $record['acumatica_lastsyncdate'],
                        "magento_lastsyncdate" => $record['magento_lastsyncdate'],
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
        $results = $this->getConnection()->fetchAll("SELECT magento_attr_code,acumatica_attr_code,sync_direction FROM `".$this->getTable('amconnector_category_mapping')."`
            where sync_direction in('Bi-Directional (Last Update Wins)', 'Acumatica to Magento', 'Bi-Directional (Acumatica Wins)','Bi-Directional (Magento Wins)') and store_id =".$storeId);

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
        $results = $this->getConnection()->fetchAll("SELECT acumatica_attr_code,magento_attr_code,sync_direction FROM `".$this->getTable('amconnector_category_mapping')."`
            WHERE sync_direction IN('Bi-Directional (Last Update Wins)', 'Magento to Acumatica', 'Bi-Directional (Magento Wins)','Bi-Directional (Acumatica Wins)') AND store_id =".$storeId);

        $attributes = array();
        foreach($results as $result){
            $attrCode = $result['magento_attr_code'];
            $attributes[$attrCode] = $result['acumatica_attr_code'].'|'.$result['sync_direction'];
        }
        return $attributes;
    }

    /**
     * Get Individual category path
     * @param $storeId
     * @param $path
     * @return string
     */
    public function getIndividualAcumaticaTreePath($scopeType,$storeId,$path)
    {
        $categoryNameAttrId = $this->getConnection()->fetchOne("SELECT attribute_id FROM `".$this->getTable('eav_attribute')."` WHERE `attribute_code` in ('name') AND `entity_type_id` = 3");
        $acuCategoryPath = $this->getAcumaticaTreePath($path,$scopeType ,$storeId,$categoryNameAttrId);
        return $acuCategoryPath;
    }

    /**
     * @param $url
     * @param $acumaticaCategoryId
     * Get Acumatica Category By Id
     */
    public function getAcumaticaCategoryById($url,$acumaticaCategoryId,$storeId)
    {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvGetCategoryById = $this->common->getEnvelopeData('GETCATEGORYBYID');
            $XMLGetRequest = $csvGetCategoryById['envelope'];
            $XMLGetRequest = str_replace('{{CATEGORYID}}', $acumaticaCategoryId, $XMLGetRequest);
            $catgeoryAction = $csvGetCategoryById['envName'] . '/' . $csvGetCategoryById['envVersion'] . '/' . $csvGetCategoryById['methodName'];
            $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $catgeoryAction);
            $data = trim($xml->Body->GetResponse->GetResult->CategoryID->Value);
            return $data;
        } catch (SoapFault $e) {
            echo "Last request:<pre>" . $e->getMessage() . "</pre>";
        }
    }

    /**
     * @param $path
     * @param $storeId
     * @param $categoryNameAttrId
     * @return string
     */
    public function getAcumaticaTreePath($path, $scopeType,$storeId,$categoryNameAttrId)
    {
        if($storeId == 1)
            $storeId = 0;
        $company = $this->scopeConfigInterface->getValue('amconnectorsync/categorysync/company',$scopeType,$storeId);
        $catIds = explode('/',$path);
        $acumaticaPath = $company.'/';
        foreach($catIds as $id){
            if($id <=2) continue;
            $categoryName = $this->getConnection()->fetchOne("SELECT value FROM ".$this->getTable('catalog_category_entity_varchar')." WHERE row_id='".$id."' AND attribute_id ='".$categoryNameAttrId."' AND store_id =".$storeId."  " );
            $acumaticaPath.= $categoryName."/";
        }
        $acumaticaPath = substr($acumaticaPath,0,-1);
        return $acumaticaPath;
    }
}
