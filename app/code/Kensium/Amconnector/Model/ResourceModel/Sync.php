<?php

/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Symfony\Component\Config\Definition\Exception\Exception;

class Sync extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var int
     */
    protected $_syncEnable;

    /**
     * @var int
     */
    protected $_syncId;

    /**
     * @var int
     */
    protected $_syncAutoCron;

    /**
     * @var
     */
    protected $_connection;

    /**
     * @var string
     */
    protected $_fields;

    /**
     * @var
     */
    protected $_where;

    /**
     * @var
     */
    protected $_title;

    /**
     * @var
     */
    protected $_coll;

    protected $date;

    protected $scopeConfigInterface;

    protected $urlHelper;
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        DateTime $date,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface ,
        $connectionName = null
    )
    {
	parent::__construct($context, $connectionName);
        $this->date = $date;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->urlHelper = $urlHelper;
    }

    /**
     * constructor
     */
    public function _construct()
    {
        $this->_init('amconnector_attribute_sync', 'id');
    }


    /**
     * @param $jobCode
     * @param null $storeId
     * @return string
     */
    public function getSyncId($jobCode, $storeId = NULL)
    {
        $connection = $this->getConnection();
        $syncId = $connection->fetchOne("SELECT id FROM " . $this->getTable('amconnector_attribute_sync') . " where code = '" . $jobCode . "' and store_id =" . $storeId);
        return $syncId;
    }

    /**
     * @return mixed
     */
    public function getAttributes($entity = NULL,$storeId)
    {
	$storeId = 1;
        $connection = $this->getConnection();
        if ($entity == 'category')
            $results = $connection->fetchAll("SELECT * FROM " . $this->getTable('amconnector_acumatica_category_attributes') .  " where store_id =".$storeId);
        if ($entity == 'product')
            $results = $connection->fetchAll("SELECT * FROM " . $this->getTable('amconnector_acumatica_product_attributes') .  " where store_id =".$storeId);
        if ($entity == 'customer')
            $results = $connection->fetchAll("SELECT * FROM " . $this->getTable('amconnector_acumatica_customer_attributes') .  " where store_id =".$storeId);
        if ($entity == 'order')
            $results = $connection->fetchAll("SELECT * FROM " . $this->getTable('amconnector_acumatica_order_attributes') .  " where store_id =".$storeId);
        if ($entity == 'shipping')
            $results = $connection->fetchAll("SELECT * FROM " . $this->getTable('amconnector_acumatica_shipping_attributes') . " where store_id =".$storeId);
        if ($entity == 'ship')
            $results = $connection->fetchAll("SELECT * FROM " . $this->getTable('amconnector_acumatica_ship_attributes') . " where store_id =".$storeId);
        if ($entity == 'payment')
            $results = $connection->fetchAll("SELECT * FROM " . $this->getTable('amconnector_acumatica_payment_attributes') . " where store_id =".$storeId);
        if ($entity == 'customproductattributes')
            $results = $connection->fetchAll("SELECT * FROM " . $this->getTable('amconnector_custom_product_attributes') . " where store_id =".$storeId);

        return $results;
    }


    /**
     * To get Region code by region name
     **/

    public function getStateCode($defaultName)
    {
        $statecode = $this->getConnection()->fetchOne("SELECT code from  " . Mage::getConfig()->getTablePrefix() . "directory_country_region where default_name ='" . $defaultName . "'");
        return $statecode;
    }

    /**
     * Getting State Id
     * @param $code
     * @return mixed
     */

    public function getStateId($code)
    {
        $stateid = $this->getConnection()->fetchOne("SELECT region_id  from  " . $this->getTable("directory_country_region") . " where code='" . $code . "'");
        return $stateid;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getStateCodeById($id)
    {
        $stateCode = $this->getConnection()->fetchOne("SELECT code  from  " . $this->getTable("directory_country_region") . " where region_id='" . $id . "'");
        return $stateCode;
    }

    /**
     * @param $syncType
     * @param $storeId
     * @return string
     */
    public function getAutoSyncId($syncType, $storeId)
    {
        $syncId = $this->getConnection()->fetchOne("SELECT id  from  " . $this->getTable("amconnector_attribute_sync") . " where sync_enable='1' and code='" . $syncType . "' and store_id=" . $storeId);
        return $syncId;
    }


    /**
     * @param $insertedId
     * @param $status
     * @param null $date
     */
    public function updateConnection($insertedId, $status, $storeId = NULL)
    {

        $serverTime = $this->date->date('Y-m-d H:i:s');
        if ($storeId == NULL)
            $storeId = 1;
        if ($status == 'SUCCESS')
            $this->getConnection()->query('UPDATE ' . $this->getTable("amconnector_syncstatus") . ' set status="' . $status . '",finished_at="' . $serverTime . '"  where id = ' . $insertedId . ' and store_id = ' . $storeId . '');
        else if ($status == 'PROCESS')
            $this->getConnection()->query('UPDATE ' . $this->getTable("amconnector_syncstatus") . ' set status="' . $status . '",executed_at="' . $serverTime . '"  where id = ' . $insertedId . ' and store_id = ' . $storeId . '');
        else
            $this->getConnection()->query('UPDATE ' . $this->getTable("amconnector_syncstatus") . ' set status="' . $status . '" where id = ' . $insertedId . ' and store_id = ' . $storeId . '');
    }

    /**
     * @param null $syncId
     */
    public function getLastSyncDate($syncId = NULL, $storeId = NULL)
    {
        if ($storeId == NULL)
            $storeId = 1;
        $sql = "SELECT  DATE_FORMAT(max(`finished_at`),'%Y-%m-%d %H:%i:%s') as syncdate FROM " . $this->getTable('amconnector_syncstatus') . " WHERE `id` = ( SELECT MAX(`id`) FROM " . $this->getTable('amconnector_syncstatus') . "  WHERE `status`='SUCCESS' and  `sync_id` = " . $syncId . " and `store_id` = " . $storeId . ")";
        try {
            $lastsyncDate = $this->getConnection()->fetchOne($sql);
            if ($lastsyncDate == NULL || $lastsyncDate == '') {
                $lastsyncDate = $this->date->date('Y-m-d H:i:s', strtotime('-5 days'));
            }

            return $lastsyncDate;

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * @param $syncId
     * @param $status
     * @param $storeId
     */
    public function updateSyncAttribute($syncId, $status, $storeId)
    {
        $updatedDate = $this->date->date('Y-m-d H:i:s', time());
        $this->getConnection()->query("UPDATE " . $this->getTable("amconnector_attribute_sync") . " set status='" . $status . "', last_sync_date='" . $updatedDate . "' where id='" . $syncId . "' and store_id= '" . $storeId . "' ");
    }

    public function saveSyncConfig($syncEnable, $syncAutoCron, $attributeName, $storeId)
    {
        $tableName = $this->getTable('amconnector_attribute_sync');
            foreach ($syncEnable as $key => $sync) {
                //check is specific-sync exist in table or not
                $selectQry = $this->getConnection()->select()->from($tableName, 'id')->where('code=?', $key)->where('store_id=?', $storeId);
                $resultArray = $this->getConnection()->fetchAll($selectQry);
                //if exist, update the auto-cron status
                if (!empty($resultArray) and is_array($resultArray)) {
                    $syncId = $resultArray[0]['id'];
                    $bind = ['sync_auto_cron' => $syncAutoCron[$key], 'sync_enable' => $syncEnable[$key],'title' => $attributeName[$key]];
                    $this->getConnection()->update($tableName, $bind, ['id=?' => $syncId, 'store_id=?' => $storeId]);
                } elseif ($sync == 1) {
                    // add new record for sync
                    $data = [
                        [
                            'attribute_id' => $key,
                            'title' => $attributeName[$key],
                            'store_id' => $storeId,
                            'sync_enable' => $sync,
                            'sync_auto_cron' => $syncAutoCron[$key],
                            'code'=>$key
                        ]
                    ];
                    // Insert data to table
                    foreach ($data as $item) {
                        $this->getConnection()->insert($tableName, $item);
                    }
                }
            }

            $custSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/syncdirection',\Kensium\Amconnector\Helper\Url::SCOPE_TYPE,$storeId);

            if ($custSyncDirection == 1)
            {
                $custSyncDirectionVal = "Acumatica to Magento";
            }
            elseif($custSyncDirection == 2)
            {
                $custSyncDirectionVal = "Magento to Acumatica";
            }
            elseif($custSyncDirection == 3)
            {
                $custSyncDirectionVal = "Bi-Directional (Last Update Wins)";
            }

            $this->getConnection()->query("UPDATE " . $this->getTable("amconnector_customer_mapping") . "  SET sync_direction='".$custSyncDirectionVal."' WHERE magento_attr_code != 'acumatica_customer_id' AND store_id = $storeId  ");

        $productSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/syncdirection',\Kensium\Amconnector\Helper\Url::SCOPE_TYPE,$storeId);
        if(!isset($productSyncDirection))
            $productSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/syncdirection');

        if ($productSyncDirection == 1)
        {
            $productSyncDirectionVal = "Acumatica to Magento";
        }
        elseif($productSyncDirection == 2)
        {
            $productSyncDirectionVal = "Magento to Acumatica";
        }
        elseif($productSyncDirection == 3)
        {
            $productSyncDirectionVal = "Bi-Directional (Last Update Wins)";
        }

        $this->getConnection()->query("UPDATE " . $this->getTable("amconnector_product_mapping") . "  SET sync_direction='".$productSyncDirectionVal."' WHERE magento_attr_code != 'acumatica_customer_id' AND store_id = $storeId");

    }


    /**
     * @param null $syncId
     * @param $jobCode
     * @return bool|null|string
     */
    public function checkConnectionFlag($syncId = NULL, $jobCode, $storeId = NULL)
    {

        if ($storeId == NULL)
            $storeId = 1;
        $connection = $this->getConnection();
        $results = $connection->fetchAll('SELECT * from ' . $this->getTable("amconnector_syncstatus") .' where id = (SELECT MAX(id) FROM ' . $this->getTable("amconnector_syncstatus") .' where job_code="' . $jobCode . '" order by id DESC limit 100 )');
        $status = $updateIndividualAt = '';
        $startSync = 0;
        foreach ($results as $result) {
            $status = $result['status'];
            $updateIndividualAt = $result['executed_at'];

            if ($status == 'PROCESS' || $status == 'STARTED' || $status == 'IN PROGRESS') {
                $magentoTime = $this->date->date('Y-m-d H:i:s');
                $currentTime = new \DateTime($magentoTime);
                $currentTime->format('Y-m-d H:i:s');
                $datetime2 = new \DateTime($updateIndividualAt);

                $interval = $currentTime->diff($datetime2);
                $elapsedMin = $interval->format('%i');
                $elapsedDays = $interval->format('%a');
                $elapsedHours = $interval->format('%h');

                if ($elapsedDays > 0) {
                    $startSync = 1;
                } elseif ($elapsedHours > 0) {
                    $startSync = 1;
                } elseif ($elapsedMin > 2) {
                    $startSync = 1;
                }
            }
        }

        if (($status != 'PROCESS' && $status != 'STARTED' && $status != 'IN PROGRESS') || $startSync) {
            $serverTime = $this->date->date('Y-m-d H:i:s');
            $message = '';

            $query = " INSERT INTO " . $this->getTable("amconnector_syncstatus") ." (`sync_id`,`job_code`,`status`,`messages`,`created_at`,`scheduled_at`,`store_id`)
            VALUES ('" . $syncId . "','" . $jobCode . "', 'STARTED', '" . $message . "', '" . $serverTime . "', '" . $serverTime . "', '" . $storeId . "')";

            $connection->query($query);

            $lastInsertId = $connection->fetchOne('SELECT last_insert_id()');

            return $lastInsertId;
        } else {
            return NULL;
        }
    }

    /**
     * @param null $syncId
     * @param $jobCode
     * @return bool|null|string
     */
    public function beforeCheckConnectionFlag($syncId = NULL, $jobCode)
    {
        $results = $this->getConnection()->fetchAll('SELECT * from '. $this->getTable("amconnector_syncstatus") .' where id = (SELECT MAX(id) FROM ' . $this->getTable("amconnector_syncstatus") .' where job_code="' . $jobCode . '" order by id DESC limit 100 )');
        $status = $updateIndividualAt = '';
        $startSync = 0;
        foreach ($results as $result) {
            $status = $result['status'];
            $updateIndividualAt = $result['executed_at'];

            if ($status == 'PROCESS' || $status == 'STARTED' || $status == 'IN PROGRESS') {
                $tobeInsertedId = $result['id'];
                $magentoTime = $this->date->date('Y-m-d H:i:s');
                $currentTime = new \DateTime($magentoTime);
                $currentTime->format('Y-m-d H:i:s');
                $datetime2 = new \DateTime($updateIndividualAt);

                $interval = $currentTime->diff($datetime2);
                $elapsedMin = $interval->format('%i');
                $elapsedDays = $interval->format('%a');
                $elapsedHours = $interval->format('%h');

                if ($elapsedDays > 0) {
                    $startSync = 1;
                } elseif ($elapsedHours > 0) {
                    $startSync = 1;
                } elseif ($elapsedMin > 2) {
                    $startSync = 1;
                }
            }
        }

        if (($status != 'PROCESS' && $status != 'STARTED' && $status != 'IN PROGRESS') || $startSync) {
            $latestInsertedId = $this->getConnection()->fetchOne('SELECT max(id)  from ' . $this->getTable("amconnector_syncstatus") .'');
            $tobeInsertedId = $latestInsertedId + 1;
            return $tobeInsertedId;
        } else {
            return $tobeInsertedId;
        }
    }

    /**
     * @param $storeId
     */
    public function deleteWarehouseData($storeId)
    {
        if($storeId == 0){
            $storeId = 1;
        }
        $connection = $this->getConnection();
        $warehouseSql = " DELETE FROM " . $this->getTable("amconnector_warehouse_details"). " WHERE  store_id = ".$storeId;
        try{
            $connection->query($warehouseSql);
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }

    public function insertWarehouseData($warehouseName , $storeId)
    {
        if($storeId == 0){
            $storeId = 1;
        }
        $connection = $this->getConnection();
        $sql = " SELECT * FROM " . $this->getTable("amconnector_warehouse_details") . " WHERE warehouse_name = '". $warehouseName ."' and store_id = ".$storeId;
        try{
            $result = $connection->fetchOne($sql);
        }catch(Exception $e){
            echo $e->getMessage();
        }
        if(!empty($result)){
            $query = " UPDATE " .  $this->getTable("amconnector_warehouse_details") . " SET warehouse_name='".$warehouseName."' WHERE id = '".$result[0]['id']."' and store_id =".$storeId;
        }else {
            $query = " INSERT INTO " . $this->getTable("amconnector_warehouse_details") . " (id,warehouse_name,store_id) VALUES ('','".$warehouseName."','".$storeId."')";
        }
        try{
            $connection->query($query);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function insertCustomerClassData($customerClassName , $storeId)
    {
        if($storeId == 0){
            $storeId = 1;
        }
        $connection = $this->getConnection();
        $sql = "SELECT * FROM " . $this->getTable("amconnector_customerclass_details") . " WHERE customer_class = '". $customerClassName ."' and store_id = ".$storeId;
        try{
            $result = $connection->fetchOne($sql);
        }catch(Exception $e){
            echo $e->getMessage();
        }
        if(!empty($result)){
            $query = " UPDATE " . $this->getTable("amconnector_customerclass_details") . " set customer_class='".$customerClassName."' WHERE id = '".$result[0]['id']."' and store_id =".$storeId;
        }else {
            $query = " INSERT INTO " .$this->getTable("amconnector_customerclass_details") . " (id,customer_class,store_id) VALUES ('','".$customerClassName."','".$storeId."')";
        }
        try{
            $connection->query($query);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * @param $storeId
     * Delete customer Class Data
     */
    public function deleteCustomerClassData($storeId)
    {
        if($storeId == 0){
            $storeId = 1;
        }
        $sql = " DELETE FROM " . $this->getTable("amconnector_customerclass_details") . " WHERE  store_id = ".$storeId;
        $connection = $this->getConnection();
        try{
            $connection->query($sql);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }


    public function insertCustomerTermData($customerTermName , $storeId)
    {
        if($storeId == 0){
            $storeId = 1;
        }
        $connection = $this->getConnection();
        $sql = "SELECT * FROM " . $this->getTable("amconnector_customerterms_details") . " WHERE customer_term = '". $customerTermName ."' and store_id = ".$storeId;
        try{
            $result = $connection->fetchOne($sql);
        }catch(Exception $e){
            echo $e->getMessage();
        }
        if(!empty($result)){
            $query = " UPDATE " . $this->getTable("amconnector_customerterms_details") . " set customer_term='".$customerTermName."' WHERE id = '".$result[0]['id']."' and store_id =".$storeId;
        }else {
            $query = " INSERT INTO " .$this->getTable("amconnector_customerterms_details") . " (id,customer_term,store_id) VALUES ('','".$customerTermName."','".$storeId."')";
        }
        try{
            $connection->query($query);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * @param $storeId
     * Delete customer term Data
     */
    public function deleteCustomerTermData($storeId)
    {
        if($storeId == 0){
            $storeId = 1;
        }
        $sql = " DELETE FROM " . $this->getTable("amconnector_customerterms_details") . " WHERE  store_id = ".$storeId;
        $connection = $this->getConnection();
        try{
            $connection->query($sql);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function insertCustomerCycleData($customerCycleName , $storeId)
    {
        if($storeId == 0){
            $storeId = 1;
        }
        $connection = $this->getConnection();
        $sql = "SELECT * FROM " . $this->getTable("amconnector_customercycle_details") . " WHERE customer_cycle = '". $customerCycleName ."' and store_id = ".$storeId;
        try{
            $result = $connection->fetchOne($sql);
        }catch(Exception $e){
            echo $e->getMessage();
        }
        if(!empty($result)){
            $query = " UPDATE " . $this->getTable("amconnector_customercycle_details") . " set customer_cycle='".$customerCycleName."' WHERE id = '".$result[0]['id']."' and store_id =".$storeId;
        }else {
            $query = " INSERT INTO " .$this->getTable("amconnector_customercycle_details") . " (id,customer_cycle,store_id) VALUES ('','".$customerCycleName."','".$storeId."')";
        }
        try{
            $connection->query($query);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * @param $storeId
     * Delete customer cycle Data
     */
    public function deleteCustomerCycleData($storeId)
    {
        if($storeId == 0){
            $storeId = 1;
        }
        $sql = " DELETE FROM " . $this->getTable("amconnector_customercycle_details") . " WHERE  store_id = ".$storeId;
        $connection = $this->getConnection();
        try{
            $connection->query($sql);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function insertSalesAccountData($salesAccountName , $storeId)
    {
        if($storeId == 0){
            $storeId = 1;
        }
        $connection = $this->getConnection();
        $sql = "SELECT * FROM " . $this->getTable("amconnector_salesaccount_details") . " WHERE sales_account = '". $salesAccountName ."' and store_id = ".$storeId;
        try{
            $result = $connection->fetchOne($sql);
        }catch(Exception $e){
            echo $e->getMessage();
        }
        if(!empty($result)){
            $query = " UPDATE " . $this->getTable("amconnector_salesaccount_details") . " set sales_account='".$salesAccountName."' WHERE id = '".$result[0]['id']."' and store_id =".$storeId;
        }else {
            $query = " INSERT INTO " .$this->getTable("amconnector_salesaccount_details") . " (id,sales_account,store_id) VALUES ('','".$salesAccountName."','".$storeId."')";
        }
        try{
            $connection->query($query);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * @param $storeId
     * Delete Sales Account Data
     */
    public function deleteSalesAccountData($storeId)
    {
        if($storeId == 0){
            $storeId = 1;
        }
        $sql = " DELETE FROM " . $this->getTable("amconnector_salesaccount_details") . " WHERE  store_id = ".$storeId;
        $connection = $this->getConnection();
        try{
            $connection->query($sql);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }


    public function insertPaymentMethodData($paymentMethodTotalData,$storeId)
    {
        $status = 0;
        $connection = $this->getConnection();
        try{
            $truncateQuery = "DELETE FROM " . $this->getTable("amconnector_acumatica_payment_attributes")." WHERE store_id = ".$storeId;
            $connection->query($truncateQuery);
            $truncateQuery = "DELETE FROM " . $this->getTable("amconnector_acumatica_cashaccount_attribute")." WHERE store_id = ".$storeId;
            $connection->query($truncateQuery);
        }catch (Exception $e){
            $status = 1;
        }
        foreach($paymentMethodTotalData->Entity as $dataValue) {
            /* If payment method is active in acumatica */
            if($dataValue->Active->Value == 'true'){
                $finalValue = $dataValue->PaymentMethodID->Value;
                try{
                    $connection->query("INSERT INTO " . $this->getTable("amconnector_acumatica_payment_attributes") . " VALUES ('','".$finalValue."','".$finalValue."','".$storeId."') ");
                }catch(Exception $e){
                    $status = 1;
                }
            }
        }


        $cashAccountArray = array();
        foreach($paymentMethodTotalData->Entity as $dataValue) {
            if($dataValue->CashAccounts->CashAccounts){
                foreach($dataValue->CashAccounts->CashAccounts as $cashData){
                    $value = trim($cashData->CashAccount->Value);
                    if(!in_array($value,$cashAccountArray)){
                        $cashAccountArray[] = $value;
                        try{
                            $connection->query("INSERT INTO " . $this->getTable("amconnector_acumatica_cashaccount_attribute") . " VALUES ('','".$value."', '".$storeId."')");
                        }catch(Exception $e){
                            $status = 1;
                        }
                    }
                }
            }
        }

        return $status;
    }

    /**
     * @param array $data
     */
    public function updateCategorySchema($data = array(),$storeId)
    {
        $storeId = 1;
        $this->getConnection()->query("DELETE FROM " . $this->getTable("amconnector_acumatica_category_attributes") ." WHERE store_id =".$storeId);
        if(isset($data['FIELDS'][0]['FIELD'])){
            $objectName = $data['NAME'];
            foreach ($data['FIELDS'][0]['FIELD'] as $newXmlData) {
                $this->getConnection()->query("INSERT INTO " . $this->getTable("amconnector_acumatica_category_attributes") . " set label='".$objectName.' '.$newXmlData['NAME']."', code='" . $newXmlData['NAME'] . "',field_type='" . $newXmlData['TYPE'] . "' ,store_id = '".$storeId."' ");
            }
        }
    }
    /**
     * @param $path
     * @param null $scope
     * @param null $scopeId
     * Get Data from core_config_data Table
     */
    public function getDataFromCoreConfig($path,$scope=NULL,$scopeId=NULL)
    {
        if($scope == '' || $scopeId == ''){
            $query = "SELECT value FROM ".$this->getTable("core_config_data")." WHERE path= '".$path."'";
        }else{
            $query = "SELECT value FROM ".$this->getTable("core_config_data")." WHERE path= '".$path."' AND scope='".$scope."' AND scope_id= '".$scopeId."'";
        }
        try{
            return $this->getConnection()->fetchOne($query);
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * @return string
     * Get all Store Ids by License
     */
    public function getAllStoreIdsByLicence()
    {
        $query = "SELECT store_id  from  " .$this->getTable("amconnector_license_check")." where license_status='VmFsaWQ='";
        $storeIds =  $this->getConnection()->fetchAll($query);
        $stores = array();
        foreach($storeIds as $storeId){
            $stores[] = $storeId['store_id'];
    }
        return $stores;
    }

    /**
     * @param $syncEnable
     * @param $syncAutoCron
     * @param $syncId
     * @param null $storeId
     * @return $this
     */
    public function updateSync($syncEnable , $syncAutoCron , $syncId, $storeId= NULL)
    {
        $this->_syncEnable    = $syncEnable;
        $this->_syncId        = $syncId ;
        $this->_syncAutoCron  = $syncAutoCron ;
        $query = "UPDATE ".$this->getTable("amconnector_attribute_sync")." SET sync_auto_cron = '".$this->_syncAutoCron."' , sync_enable = '".$this->_syncEnable."' where id = '".$this->_syncId."' and store_id = ".$storeId;
        $this->getConnection()->query($query);
        return $this ;
    }
}
