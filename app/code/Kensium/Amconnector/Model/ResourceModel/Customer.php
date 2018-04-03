<?php
/**
 *
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Framework\Stdlib\DateTime\Timezone as TimeZone;

use Magento\Framework\Stdlib\DateTime\DateTime as DateTime;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Customer resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Customer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var DateTime
     */
    protected $date;
    protected $timezone;

    protected $timeZoneInterface;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var Sync
     */
    protected $syncResourceModel;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param DateTime $date
     * @param TimeZone $timezone
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param Sync $syncResourceModel
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        DateTime $date,
        TimeZone $timezone,
        ScopeConfigInterface $scopeConfigInterface,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->date = $date;
        $this->timezone = $timezone;
        $this->storeRepository = $storeRepository;
        $this->syncResourceModel = $syncResourceModel;
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amconnector_customer_mapping', 'id');
    }

    /**
     * @return string
     */
    public function getAcumaticaAttrCount()
    {
        $acumaticaCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_acumatica_customer_attributes')."");
        return $acumaticaCount;
    }

    /**
     *Checking count of the mapping table
     */
    public function checkCustomerMapping($storeId)
    {
        $customerAttributes = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_customer_mapping') . " WHERE store_id =" . $storeId);
        return $customerAttributes;
    }

    /**
     * truncate customer mapping table
     * @param $storeId
     */
    public function truncateMappingTable($storeId = null)
    {
        $this->getConnection()->query("DELETE FROM  " . $this->getTable("amconnector_customer_mapping")." WHERE store_id = $storeId");
    }

    /**
     * @param $storeId
     */
    public function truncateDataFromTempTables($storeId)
    {
        $this->getConnection()->query("TRUNCATE table " . $this->getTable("amconnector_customer_sync_temp"));
    }

    /**
     * Fetching the data based on last sync date
     * and Inserting into the temp table
     *
     * First fetching from Acumatica and if same record is updated in Magento
     * then updating the same record with customer detail and updated date in temp table
     *
     * @param $getCustomerUrl
     * @param $syncId
     * @param $storeId
     * @return mixed
     */
    public function insertDataIntoTempTables($acumaticaData, $syncId, $storeId)
    {
        $websiteId = $this->storeRepository->getById($storeId)->getWebsiteId();
        /**
         * Based on the last sync date get the data from Acumatica and insert into temporary table
         */
        $oneRecordFlag = false;
        if ($this->scopeConfigInterface->getValue('amconnectorsync/customersync/syncdirection') == 1 || $this->scopeConfigInterface->getValue('amconnectorsync/customersync/syncdirection') == 3) {
            if (isset($acumaticaData['Entity'])) {
                foreach ($acumaticaData['Entity'] as $key => $value) {
                    if (!is_numeric($key)) {
                        $oneRecordFlag = true;
                        break;
                    }
                    $emailValue = $value->MainContact->Email->Value;
                    $acumaticaId = $value->CustomerID->Value;
                    $acumaticaModifiedDate = $this->date->date('Y-m-d H:i:s', strtotime($value->LastModified->Value));
                    $this->getConnection()->query("INSERT INTO `" . $this->getTable("amconnector_customer_sync_temp") . "`(`id`, `email`, `acumatica_id`, `magento_id`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `website_id`, `flg`,`entity_ref`)
            VALUES (NULL, '" . $emailValue . "', '" . $acumaticaId . "', NULL, NULL, '" . $acumaticaModifiedDate . "', '" . $websiteId . "', '0','" . $key . "')");

                }
                if ($oneRecordFlag) {
                    $emailValue = $acumaticaData['Entity']['MainContact']['Email']['Value'];
                    $acumaticaId = $acumaticaData['Entity']['CustomerID']['Value'];
                    $acumaticaModifiedDate = $this->date->date('Y-m-d H:i:s', strtotime($acumaticaData['Entity']['LastModified']['Value']));

                    $this->getConnection()->query("INSERT INTO `" . $this->getTable("amconnector_customer_sync_temp") . "`(`id`, `email`, `acumatica_id`, `magento_id`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `website_id`, `flg`,`entity_ref`)
            VALUES (NULL, '" . $emailValue . "', '" . $acumaticaId . "', NULL, NULL, '" . $acumaticaModifiedDate . "', '" . $websiteId . "', '0',NULL)");

                }
            }
        }
        /**
         * Get website id based on store id
         * Based on the last sync date get the data from Magento and insert/update into temporary table
         */
        if ($this->scopeConfigInterface->getValue('amconnectorsync/customersync/syncdirection') == 2 || $this->scopeConfigInterface->getValue('amconnectorsync/customersync/syncdirection') == 3)
        {
            $toTimezone = $this->timezone->getDefaultTimezone();
            $lastSyncDate = $this->timezone->date($this->syncResourceModel->getLastSyncDate($syncId, $storeId));
            $lastSyncDate->setTimezone(new \DateTimeZone($toTimezone));
            $lastSyncDate = $lastSyncDate->format('Y-m-d H:i:s');
            $magentoData = $this->getConnection()->fetchAll("SELECT entity_id, email, updated_at FROM " . $this->getTable("customer_entity") . " WHERE updated_at >='" . $lastSyncDate . "' and website_id =" . $websiteId);
            foreach ($magentoData as $mData) {
                $updatedDate = $this->timezone->date($mData['updated_at'], null, true);
                $updatedDate = $updatedDate->format('Y-m-d H:i:s');
                $email = trim($mData['email']);
                $recordCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable("amconnector_customer_sync_temp") . "
                WHERE email ='" . $email . "' and website_id = '" . $websiteId . "'  ");
                if ($recordCount) {
                    $this->getConnection()->query("UPDATE " . $this->getTable("amconnector_customer_sync_temp") . "
                set magento_id='" . $mData['entity_id'] . "', magento_lastsyncdate='" . $updatedDate . "'
                where email='" . $email . "' and website_id= '" . $websiteId . "' ");
                } else {
                    $this->getConnection()->query("INSERT INTO `" . $this->getTable("amconnector_customer_sync_temp") . "`(`id`, `email`, `acumatica_id`, `magento_id`, `magento_lastsyncdate`, `acumatica_lastsyncdate`, `website_id`, `flg`)
                VALUES (NULL, '" . $email . "', NULL, '" . $mData['entity_id'] . "', '" . $updatedDate . "', NULL, '" . $websiteId . "', '0')");
                }
            }
        }
        try {
            $records = $this->getConnection()->fetchAll("SELECT * FROM " . $this->getTable('amconnector_customer_sync_temp') . " WHERE website_id = '" . $websiteId . "'  ");
            $data = array();
            $results = $this->getConnection()->fetchAll("SELECT magento_attr_code,acumatica_attr_code,sync_direction FROM " . $this->getTable('amconnector_customer_mapping'));
            foreach ($results as $result) {
                $attrCode = str_replace('BILLADD_', '', $result['magento_attr_code']);

                $mappingAttributes[$attrCode] = $result['acumatica_attr_code'] . "|" . $result['sync_direction'];
            }
            $biDirectional = array();
            if(isset($mappingAttributes) && !empty($mappingAttributes))
            {
                foreach ($records as $record) {
                    $magFlag = 0;
                    $acuFlag = 0;
                    foreach ($mappingAttributes as $attributeStr) {
                        $attrArray = explode('|', $attributeStr);
                        if (is_numeric($attrArray[1])) {
                            continue;
                        }
                        $biDirectional[] = $attrArray[1];
                        if ($attrArray[1] == 'Bi-Directional (Acumatica Wins)')
                            $attrArray[1] = "Acumatica to Magento";
                        if ($attrArray[1] == 'Bi-Directional (Magento Wins)')
                            $attrArray[1] = "Magento to Acumatica";
                        $direction[] = $attrArray[1];
                    }
                    $direction = array_unique($direction);

                    if (in_array('Acumatica to Magento', $direction))
                        $magFlag = 1;

                    if (in_array('Magento to Acumatica', $direction))
                        $acuFlag = 1;

                    if (in_array('Bi-Directional (Last Update Wins)', $direction)) {
                        if (count(array_unique($biDirectional)) === 2 && in_array('Acumatica to Magento', $biDirectional)) {
                            if ($record['magento_lastsyncdate'] > $record['acumatica_lastsyncdate']) {
                                $acuFlag = 1;
                                $magFlag = 0;
                            } else {
                                $magFlag = 1;
                                $acuFlag = 0;
                            }
                        } else {
                            if ($record['magento_lastsyncdate'] > $record['acumatica_lastsyncdate'])
                                $acuFlag = 1;
                            else
                                $magFlag = 1;
                        }
                    }
                    if (count(array_unique($biDirectional)) === 1 && in_array('Bi-Directional (Magento Wins)', $biDirectional)) {
                        $magFlag = 1;
                    }

                    if (count(array_unique($biDirectional)) === 1 && in_array('Bi-Directional (Acumatica Wins)', $biDirectional)) {
                        $acuFlag = 1;
                    }
                    if (count(array_unique($biDirectional)) === 2 && in_array('Bi-Directional (Acumatica Wins)', $biDirectional) && in_array('Acumatica to Magento', $biDirectional)) {
                        $acuFlag = 1;
                    }
                    if (count(array_unique($biDirectional)) === 2 && in_array('Bi-Directional (Magento Wins)', $biDirectional) && in_array('Magento to Acumatica', $biDirectional)) {
                        $magFlag = 1;
                    }
                    if ($magFlag) {
                        $data['magento'][] = array(
                            "id" => $record['id'],
                            "email" => $record['email'],
                            "acumatica_id" => $record['acumatica_id'],
                            "magento_id" => $record['magento_id'],
                            "magento_lastsyncdate" => $record['magento_lastsyncdate'],
                            "acumatica_lastsyncdate" => $record['acumatica_lastsyncdate'],
                            "website_id" => $record['website_id'],
                            "entity_ref" => $record['entity_ref'],
                            "flg" => $record['flg']
                        );
                    }
                    if ($acuFlag) {
                        $data['acumatica'][] = array(
                            "id" => $record['id'],
                            "email" => $record['email'],
                            "acumatica_id" => $record['acumatica_id'],
                            "magento_id" => $record['magento_id'],
                            "magento_lastsyncdate" => $record['magento_lastsyncdate'],
                            "acumatica_lastsyncdate" => $record['acumatica_lastsyncdate'],
                            "website_id" => $record['website_id'],
                            "entity_ref" => $record['entity_ref'],
                            "flg" => $record['flg']
                        );
                    }

                }
            }
        } catch (Exception $e) {
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
        $websiteId = $this->storeRepository->getById($storeId)->getWebsiteId();
        $results = $this->getConnection()->fetchAll("SELECT magento_attr_code,acumatica_attr_code,sync_direction FROM " . $this->getTable('amconnector_customer_mapping') . "
            where sync_direction in('Bi-Directional (Last Update Wins)', 'Acumatica to Magento', 'Bi-Directional (Acumatica Wins)','Bi-Directional (Magento Wins)') and store_id =" . $storeId);

        $attributes = array();
        foreach ($results as $result) {
            $attrCode = str_replace('BILLADD_', '', $result['magento_attr_code']);

            $attributes[$attrCode] = $result['acumatica_attr_code'] . '|' . $result['sync_direction'];
        }
        return $attributes;
    }

    /**
     * @TODO to change the attribute array
     * @param $storeId
     * @return array
     */
    public function getAcumaticaAttributes($storeId)
    {
        $websiteId = $this->storeRepository->getById($storeId)->getWebsiteId();
        $results = $this->getConnection()->fetchAll("SELECT acumatica_attr_code,magento_attr_code,sync_direction FROM " . $this->getTable('amconnector_customer_mapping') . "
            where sync_direction in('Bi-Directional (Last Update Wins)', 'Magento to Acumatica', 'Bi-Directional (Magento Wins)','Bi-Directional (Acumatica Wins)') and store_id =" . $storeId);

        $attributes = array();
        foreach ($results as $result) {
            $attrCode = str_replace('BILLADD_', '', $result['magento_attr_code']);

            $attributes[$attrCode] = $result['acumatica_attr_code'] . '|' . $result['sync_direction'];
        }
        return $attributes;
    }

    /**
     * @TODO to change the attribute array
     * @param $storeId
     * @return array
     */
    public function getAcumaticaAttributesForOrder($storeId)
    {
        $websiteId = $this->storeRepository->getById($storeId)->getWebsiteId();
        $results = $this->getConnection()->fetchAll("SELECT acumatica_attr_code,magento_attr_code,sync_direction FROM " . $this->getTable('amconnector_customer_mapping') . "
            where sync_direction in('Bi-Directional (Last Update Wins)','Acumatica to Magento','Magento to Acumatica', 'Bi-Directional (Magento Wins)','Bi-Directional (Acumatica Wins)') and store_id =" . $storeId);

        $attributes = array();
        foreach ($results as $result) {
            $attrCode = str_replace('BILLADD_', '', $result['magento_attr_code']);

            $attributes[$attrCode] = $result['acumatica_attr_code'] . '|' . $result['sync_direction'];
        }
        return $attributes;
    }

    /**
     * @param $id
     * @return string
     */
    public function getAcumaticaAttrCode($id)
    {

        $acumaticaLabel = $this->getConnection()->fetchOne("SELECT label FROM " . $this->getTable('amconnector_acumatica_customer_attributes') . " where code = '".$id."'");

        return trim($acumaticaLabel);
    }

    /**
     * @param $email
     * @param $websiteId
     * @return mixed
     */
    public function getCustomerIdByEmail($email, $websiteId)
    {
        $email = addslashes($email);
        $customerId = $this->getConnection()->fetchOne("SELECT entity_id FROM " . $this->getTable('customer_entity') . " where email = '" . $email . "' and website_id = $websiteId ");

        return $customerId;
    }

    /**
     * @return mixed
     */
    public function getAcumaticaMappedAttributes()
    {

        $customerAttributes = $this->getConnection()->fetchAll("SELECT id,label FROM " . $this->getTable('amconnector_acumatica_customer_attributes') . " where id IN(SELECT acumatica_attr_code FROM `" . $this->getTable("amconnector_customer_mapping"));
        return $customerAttributes;
    }

    /**
     * Saving deleted customer in custom table
     * @param $getCustomer
     */
    public function updateCustomerDataInCustomTable($getCustomer)
    {
        $magentoCustomerId = $getCustomer->getId();
        $email = $getCustomer->getEmail();
        $acumaticaCustomerId = $getCustomer->getAcumaticaCustomerId();
        $deletedDate = $this->date->date('Y-m-d H:i:s', time());
        if ($acumaticaCustomerId != '') {
            $this->getConnection()->query("INSERT INTO " . $this->getTable("amconnector_customer_deleted_data") . "
                (`id`, `email`, `acumatica_id`, `magento_id`, `deleted_date`)
                VALUES (NULL, '" . $email . "', '" . $acumaticaCustomerId . "', '" . $magentoCustomerId . "', '" . $deletedDate . "')");
        }
    }

    /**
     *getDeleted customers from magento based on last sync date to delete in acumatica
     * @param $syncId
     */
    public function getDeletedCustomers($syncId)
    {
        $lastSyncDate = date('Y-m-d H:i:s', strtotime($this->syncResourceModel->getLastSyncDate($syncId)));
        $magentoDeletedCustomers = $this->getConnection()->fetchAll("SELECT magento_id,acumatica_id FROM " . $this->getTable('amconnector_customer_deleted_data') . " WHERE deleted_date >='" . $lastSyncDate . "'");
        return $magentoDeletedCustomers;
    }

    /**
     * @param $emailOfCustomer
     * @param $AcumaticaCustomerId
     */
    public function sendDataToMapping($emailOfCustomer, $AcumaticaCustomerId, $storeId)
    {
        $updateddate = $this->date->date('Y-m-d H:i:s', time());
        $this->getConnection()->query("INSERT INTO " . $this->getTable("amconnector_customer_order_mapping") . "
                (`id`, `email`, `acumatica_customer_id`, `store_id`,`updated_date`)
                VALUES (NULL, '" . $emailOfCustomer . "', '" . $AcumaticaCustomerId . "', '" . $storeId . "', '" . $updateddate . "')");
    }

    /** To check guest customer existence in mapping table
     * @param $emailOfCustomer
     * @param $storeId
     */
    public function getCustomerAcumaticaId($emailOfCustomer, $storeId)
    {
        $customerId = $this->getConnection()->fetchOne("SELECT `acumatica_customer_id` FROM  " . $this->getTable('amconnector_customer_order_mapping') . "
                WHERE `email` = '" . $emailOfCustomer . "' and `store_id` = $storeId ");
        return $customerId;
    }


    /**
     * @param array $data
     */
    public function updateCustomerSchema($data = array(),$storeId)
    {
	$storeId = 1;
        $this->getConnection()->query("DELETE FROM " . $this->getTable("amconnector_acumatica_customer_attributes") ." WHERE store_id = '".$storeId."' " );

        if(isset($data['ENDPOINT']['TOPLEVELENTITY']) && !empty($data['ENDPOINT']['TOPLEVELENTITY']))
        {
            $customerSchemaLable = "CustomerSchema";
            $fieldType = "StringValue";
            foreach($data['ENDPOINT']['TOPLEVELENTITY']['FIELDS'][0]['FIELD'] as $key => $value)
            {
                $this->getConnection()->query("INSERT INTO " . $this->getTable("amconnector_acumatica_customer_attributes") . " set label='" . $customerSchemaLable . ' ' . $value['NAME'] . "',code='" . $value['NAME'] . "',field_type='" . $value['TYPE'] . "' ,store_id = '" . $storeId . "' ");
            }
        }

        if(isset($data['ENDPOINT']['LINKEDENTITY']) && !empty($data['ENDPOINT']['LINKEDENTITY']))
        {
            $k = 1;
            foreach($data['ENDPOINT']['LINKEDENTITY'] as $addressInfo)
            {
                $customerSchemaLable = $addressInfo['NAME'];
                foreach ($addressInfo['FIELDS'][$k]['FIELD'] as $entityField)
                {
                    if($customerSchemaLable == "BillToContact" || $customerSchemaLable == "BillToAddress")
                        continue;

                    $this->getConnection()->query("INSERT INTO " . $this->getTable("amconnector_acumatica_customer_attributes") . " set label='" . $customerSchemaLable . ' ' . $entityField['NAME'] . "',code='" . $entityField['NAME'] . "',field_type='" . $entityField['TYPE'] . "' ,store_id = '" . $storeId . "' ");
                }
                $k++;
            }
        }
    }
    /**
     * Get Id of Acumatica Code for customer schema
     * Array key relates to following attribute in magento
     * 0  -> First Name
     * 1  -> Last Name
     * 2  -> Email
     * 3  -> Fist Name
     * 4  -> Last Name
     * 5  -> Company
     * 6  -> Street Address
     * 7  -> City
     * 8  -> Country
     * 9  -> Other than US State/Province
     * 10 -> US State/Province
     * 11 -> Zip/Postal Code
     * 12 -> Telephone
     * 13 -> Fax
     * 14 -> ACUID
     */

    public function getCustomerAttributeLabelId($storeId)
    {
        $acumaticaCodes = array(
            0 => 'CustomerName|CustomerSchema CustomerName',
            1 => 'CustomerName|CustomerSchema CustomerName',
            2 => 'Email|MainContact Email',
            3 => 'CustomerName|CustomerSchema CustomerName',
            4 => 'CustomerName|CustomerSchema CustomerName',
            5 => 'CompanyName|MainContact CompanyName',
            6 => 'AddressLine1|MainAddress AddressLine1',
            7 => 'City|MainAddress City',
            8 => 'Country|MainAddress Country',
            9 => 'State|MainAddress State',
            10 => 'State|MainAddress State',
            11 => 'PostalCode|MainAddress PostalCode',
            12 => 'Phone1|MainContact Phone1',
            13 => 'Fax|MainContact Fax',
            14 => 'CustomerID|CustomerSchema CustomerID',
        );

        foreach ($acumaticaCodes as $key => $label) {
            $mappingData = explode('|', $label);
            $query = "SELECT id FROM " . $this->getTable("amconnector_acumatica_customer_attributes") . " WHERE store_id = '".$storeId."' AND code = '" . $mappingData['0'] . "' AND label = '" . $mappingData['1'] . "'";
            $result[] = $this->getConnection()->fetchOne($query);
        }
        return $result;
    }


    /**
     * @param array $syncData
     */
    public function updateAcumaticaCustomerData($syncData = array())
    {

        $this->getConnection()->query("TRUNCATE table  " . $this->getTable("amconnector_acumatica_customer_temp"));
        foreach ($syncData as $dataValue) {
            $insertQry = "INSERT INTO " . $this->getTable("amconnector_acumatica_customer_temp") . " (acumatica_id,magento_id,email,magento_lastsyncdate,acumatica_lastsyncdate,flag)
          VALUES ('" . $dataValue['CustomerID']->Value . "','','" . $dataValue['CustomerID']->Value . "','','" . date('Y-m-d h:i:s', strtotime($dataValue['LastModifiedDate']->Value)) . "',1)";
            $this->getConnection()->query($insertQry);

        }


    }

    /**
     * @param array $syncData
     */
    public function updateMagentoCustomerData($syncData = array())
    {


        foreach ($syncData as $dataValue) {
            $emailCheck = $this->getConnection()->fetchOne("select email from " . $this->getTable("amconnector_acumatica_customer_temp") . " where email='" . $dataValue['email'] . "'");
            if (!$emailCheck) {

                echo $insertQry = "INSERT INTO " . $this->getTable("amconnector_acumatica_customer_temp") . " (acumatica_id,magento_id,email,magento_lastsyncdate,acumatica_lastsyncdate,flag)
          VALUES ('','" . $dataValue['entity_id'] . "','" . $dataValue['email'] . "','" . $dataValue['updated_at'] . "','',1)";

                $this->getConnection()->query($insertQry);
            }


        }


    }


    /**
     * Inserting customer details in magento temporary table
     * @param $data
     */
    public function insertCustomerDataIntoTemporaryLocation($data)
    {
        foreach ($data->Entity as $value) {
            $emailValue = $value->MainContact->Email->Value;
            $acumaticaId = $value->CustomerID->Value;
            $acumaticaModifiedDate = $value->LastModifiedDateTime->Value;

            $this->getConnection()->query("INSERT INTO " . Mage::getConfig()->getTablePrefix() . "`​amconnector_acumatica_customer_sync_temp​`
            (`​id​`, `​acumatica_id​`, `​magento_id​`, `​email​`, `​magento_lastsyncdate​`, `​acumatica_lastsyncdate​`, `​flag​`)
            VALUES (NULL, '" . $acumaticaId . "', '', '" . $emailValue . "', NULL, '" . $acumaticaModifiedDate . "', '0')");

        }
    }

    /**
     * Insert Customer Sync Data In Mapping Table
     * @param array $data
     */
    public function insertCustomerSyncDataInMappingTable($acumaticaCustomerID, $email, $magentoCustomerId)
    {
        $acumaticaId = $acumaticaCustomerID;
        $magentoId = $magentoCustomerId;
        $email = $email;
        $count = $this->getConnection()->fetchOne("SELECT count(*) from " . Mage::getConfig()->getTablePrefix() . "amconnector_acumatica_customer_sync_data  where magento_customer_id ='" . $magentoId . "' ");
        if ($count) {
            $this->getConnection()->query("UPDATE " . Mage::getConfig()->getTablePrefix() . "amconnector_acumatica_customer_sync_data set acumatica_customer_id='" . $acumaticaId . "',email_id='" . $email . "' where magento_customer_id= '" . $magentoId . "'   ");
        } else {
            $this->getConnection()->query("INSERT INTO " . Mage::getConfig()->getTablePrefix() . "amconnector_acumatica_customer_sync_data (acumatica_customer_id,magento_customer_id,email_id) VALUES('" . $acumaticaId . "','" . $magentoId . "','" . $email . "' ) ");
        }
    }


    public function getAcumaticaCustomerId()
    {

        $custId = Mage::app()->getRequest()->getParam('id');

        $acumaticaCustomerId = $this->getConnection()->fetchOne("SELECT acumatica_customer_id from  " . Mage::getConfig()->getTablePrefix() . "amconnector_acumatica_customer_sync_data where magento_customer_id ='" . $custId . "'");
        return $acumaticaCustomerId;

    }

    public function updateCustomerDataIntoTemporaryLocation()
    {

        $custId = Mage::app()->getRequest()->getParam('id');
        $customerData = Mage::getModel('customer/customer')->load($custId);

        $email = $customerData->getEmail();
        $updatedDate = $customerData->getUpdatedAt();

        $updateQry = "UPDATE  " . $this->getTable('amconnector_acumatica_customer_sync_temp​') . " SET `​magento_id​` ='" . $custId . "',
                        `​magento_lastsyncdate​` = '" . $updatedDate . "' WHERE `​email​` = '" . $email . "' ";


        $this->getConnection()->query($updateQry);

    }

    public function truncateCustomerDataIntoTemporaryLocation()
    {
        $this->getConnection()->query("TRUNCATE table " . Mage::getConfig()->getTablePrefix() . "​amconnector_acumatica_customer_sync_temp​");

    }

    /**
     * @param $attributeCode
     * @return string
     */
    public function getCustomerAttributeId($attributeCode)
    {
        if (strpos($attributeCode, 'BILLADD_') !== false) {
            $entityTypeId = 2;
            $attributeCode = str_replace('BILLADD_','',$attributeCode);
        }else{
            $entityTypeId = 1;
        }
        $attributeId = $this->getConnection()->fetchOne("SELECT attribute_id FROM ".$this->getTable('eav_attribute')." where attribute_code = '".$attributeCode."' and entity_type_id = $entityTypeId ");
        if($attributeId){
            return $attributeId;
        }else{
            return '';
        }
    }

    /**
     * @param $attributeCode
     * @param $value
     * @param $entityId
     */
    public function updateCustomerAttribute($attributeCode,$value,$entityId)
    {
        $attributeId = $this->getConnection()->fetchOne("SELECT magento_attribute_id FROM ".$this->getTable('amconnector_customer_mapping')." where magento_attr_code = '".$attributeCode."'");
        if($attributeId)
        {
            $checkAttributeValue = $this->getConnection()->fetchOne("SELECT value_id FROM ".$this->getTable('customer_entity_varchar')." where attribute_id = '".$attributeId."' and entity_id = $entityId ");
            if($checkAttributeValue)
            {
                $this->getConnection()->query("UPDATE ".$this->getTable("customer_entity_varchar")." set value='".$value."' where attribute_id='".$attributeId."' and entity_id= '".$entityId."' ");
            }else
            {
                $this->getConnection()->query("INSERT INTO `".$this->getTable("customer_entity_varchar")."`(`value_id`, `attribute_id`, `entity_id`, `value`)
                VALUES (NULL, '" . $attributeId . "', '" . $entityId . "', '" . $value . "')" );
            }
        }
    }




    /**
     * @return mixed
     * Get stop sync value for database
     */

    public function StopSyncValue()
    {
        $query = "select value from " .  $this->getTable("core_config_data")." where path ='amconnectorsync/customersync/syncstopflg' ";
        $value = $this->getConnection()->fetchOne($query);
        return $value;
    }



    public function enableSync()
    {

        $path = 'amconnectorsync/customersync/syncstopflg';
        $query = "update " . $this->getTable("core_config_data")." set value = 1 where path ='" . $path . "'";
        $this->getConnection()->query($query);
    }

    /**
     * @param $acmId
     * @param $websiteId
     * @return string
     */
    public function getcustomerIdByAcmId($acmId, $attributeCode, $websiteId)
    {
        $customerEmail = $this->getConnection()->fetchOne("SELECT ce.entity_id FROM ". $this->getTable('customer_entity') ." as ce inner join ". $this->getTable('customer_entity_varchar') ." as cev on cev.entity_id=ce.entity_id inner join ". $this->getTable('eav_attribute') ." as ea on ea.attribute_id=cev.attribute_id where ea.attribute_code='".$attributeCode."' and cev.value='".$acmId."' and ce.website_id='".$websiteId."'");
        return $customerEmail;
    }

    public function getCustomerById($customerId){
        $customerData = $this->getConnection()->fetchAll("SELECT * FROM ". $this->getTable('customer_entity') ." WHERE entity_id=".$customerId);
        return $customerData[0];
    }

    public function getCustomerPrimaryAddress($addresId){
        $customerAddressData = $this->getConnection()->fetchAll("SELECT * FROM ". $this->getTable('customer_address_entity') ." WHERE entity_id=".$addresId."");
        return $customerAddressData[0];
    }

    public function getAcmCustomerId($customerId){
        $attributeId = $this->getCustomerAttributeId('acumatica_customer_id');
        $acmId = $this->getConnection()->fetchOne("SELECT value FROM ". $this->getTable('customer_entity_varchar') ." WHERE entity_id=".$customerId." AND attribute_id=".$attributeId);
        return $acmId;
    }
}
