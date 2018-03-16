<?php
/**
 *
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Symfony\Component\Config\Definition\Exception\Exception;
use Psr\Log\LoggerInterface as Logger;
use Kensium\Lib;

class CustomerAttribute extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var
     */
    public $arrData;

    /**
     * @var
     */
    public $sucessMsg;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var
     */
    public $errorCheck;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Kensium\Amconnector\Helper\Time
     */
    protected $timeHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $attribute;

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var \Magento\Customer\Model\AttributeFactory
     */
    protected $_attrFactory;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;

    protected $syncResourceModel;

    /**
     * @var
     */
    protected $customerAttributeHelper;

    /**
     * @var
     */
    protected $licenseResourceModel;
    protected $common;

    const IS_TIME_VALID = "Valid";

    const IS_LICENSE_VALID = "Valid";

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Kensium\Amconnector\Helper\Data $dataHelper
     * @param \Kensium\Amconnector\Helper\Time $timeHelper
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @param CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param AttributeSetFactory $attributeSetFactory
     * @param \Magento\Customer\Model\AttributeFactory $attrFactory
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param Sync $syncResourceModel
     * @param \Kensium\Synclog\Helper\CustomerAttribute $customerAttributeHelper
     * @param Logger $logger
     * @param \Kensium\Amconnector\Helper\Sync $syncHelper
     * @param Licensecheck $licenseResourceModel
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Magento\Eav\Model\Entity\Attribute $attribute,
        CustomerSetupFactory $customerSetupFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Customer\Model\AttributeFactory $attrFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Synclog\Helper\CustomerAttribute $customerAttributeHelper,
        Logger $logger,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel,
        Lib\Common $common,
        $connectionName = null
    )
    {
        $this->date = $date;
        $this->syncHelper = $syncHelper;
        $this->dataHelper = $dataHelper;
        $this->timeHelper = $timeHelper;
        $this->attribute = $attribute;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->messageManager = $messageManager;
        $this->_attrFactory = $attrFactory;
        $this->websiteFactory = $websiteFactory;
        $this->syncResourceModel=$syncResourceModel;
        $this->customerAttributeHelper = $customerAttributeHelper;
        $this->logger = $logger;
        $this->licenseCheck = $licenseResourceModel;
        $this->common = $common;
	parent::__construct($context, $connectionName);
    }


    protected function _construct()
    {
	$this->time = $this->date->date('Y-m-d H:i:s');
        $this->sucessMsg[] = array();
        $this->_init('amconnector_customerattribute', 'id');

    }

    /***
     * Fetching attributes from Array
     * ***/
    public function getDetailsFromArray()
    {
        $data = array();
        $data[0] = array('s.no', 'Field name', 'Field Code', 'Tab Name', 'is_common', 'Magento Attribute', 'Field Value', 'Field Type', 'Field Length', 'Is Required');
        $data[1] = array(1, 'Customer Summary Customerid', 'custsum_customerid', 'Customer Summary', 0, null, null, 'text', null, 0);
        $data[2] = array(2, 'Company Info Businessname', 'compinfo_businessname', 'General Info', 0, null, null, 'text', null, 0);
        $data[3] = array(3, 'Mainaddress Addressline1', 'mainaddr_addressline1', 'General Info', 0, null, null, 'text', null, 0);
        $data[4] = array(4, 'Accountsettings Customerclass', 'acstng_custclass', 'General Info', 0, null, null, 'text', null, 0);
        $data[5] = array(5, 'Shipping Branch', 'shippstr_shpbrch', 'Delivery Settings', 0, null, null, 'text', null, 0);
        $data[6] = array(6, 'Billto Info Attention', 'billtoinfo_attention', 'Payment Methods', 1, null, null, 'text', null, 0);
        $data[7] = array(7, 'ACU ID', 'acumatica_customer_id', 'General Info', 0, null, null, 'text', null, 0);
        $data[8] = array(8, 'wwss', 'wwss', 'Payment Methods', 1, null, null, 'text', null, 0);
        return $this->arrData = $data;
    }

    /**
     * Inserting attribute values in mapping table
     */

    public function syncMappingTable($syncLogID, $logViewFileName, $storeId)
    {
        $i = 0;
        $sync = 0;
        $k = 0;
        foreach ($this->arrData as $data) {

            try {
                if ($k >= 1) {
                    $fieldname = str_replace(" ", "", $data[1]);
                    $fieldcode = $data[2];
                    $tabdname = $data[3];
                    $is_comon = $data[4];
                    $magento_attribute = $data[5];
                    $fieldvalue = $data[6];
                    $fieldtype = $data[7];
                    $fieldlength = $data[8];
                    $is_required = $data[9];
                    if (!empty($fieldvalue)) {
                        $option_values = explode(",", $fieldvalue);
                        $options = json_encode($option_values);

                    } else {
                        $options = "";
                    }
                    if (strlen($fieldcode) <= 30) {
                        $Id = $this->getConnection()->fetchOne("select id from ".$this->getTable('amconnector_customer_attribute_mapping')." where entity_type=1 and acumatica_attribute_code='" . $fieldname . "'");
                        if (!empty($Id)) {
                            $checkvalue = $this->getConnection()->fetchOne("select count(*) from  ".$this->getTable('amconnector_customer_attribute_mapping')." where entity_type=1 and magento_attribute_code='" . $fieldname . "'");
                            if ($checkvalue == 1) {
                                $flag = 1;
                            } else {
                                $flag = 0;
                            }
                            $sql = $this->getConnection()->query("update   ". $this->getTable('amconnector_customer_attribute_mapping')." set acumatica_attribute_code='" . $fieldname . "',magento_attribute_code='" . $fieldcode . "',is_common='" . $is_comon . "',field_type='" . $fieldtype . "',is_required='" . $is_required . "',field_values='" . $options . "',flag='" . $flag . "' where id='" . $Id . "' ");
                        } else {

                            $sql = $this->getConnection()->query("INSERT INTO ".$this->getTable('amconnector_customer_attribute_mapping')." (`entity_type`, `magento_attribute_code`, `acumatica_attribute_code`, `field_type`, `is_common`, `is_required`, `is_unique`, `field_values`, `magento_field_values`, `flag`, `store_id`) VALUES(1,'" . $fieldcode . "','" . $fieldname . "','" . $fieldtype . "','" . $is_comon . "','" . $is_required . "','','','" . $options . "',0,0)");
                            if ($sql) {
                                $msg = 'Successfully inserted in Mapping table. Attribute Code::' . $fieldname;
                                $this->sucessMsg++;
                            }
                        }
                    } else {
                        $msg = "Maximum length of attribute code must be less then 30 symbols. [Attribute code: " . $fieldcode . " , Length: " . strlen($fieldcode)."]";

                        $errorMsg = $msg;
                        $syncName = 'Customer Attribute Sync';
                        $this->dataHelper->errorLogEmail($syncName, $errorMsg);

                        $txt = "Info : " . $msg;
                        $this->dataHelper->writeLogToFile($logViewFileName,$txt);
                        $customerArray['store_id'] = $storeId;
                        $customerArray['schedule_id'] = $syncLogID;
                        $customerArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                        $customerArray['acumatica_attribute_code'] = $fieldname;
                        $customerArray['magento_attribute_code'] = $fieldcode;
                        $customerArray['longMessage'] = $msg;
                        $customerArray['runMode'] = "Manual";
                        $customerArray['messageType'] = "Failure";
                        $customerArray['syncDirection'] = 'syncToMagento';
                        $this->customerAttributeHelper->customerAttributeSyncSuccessLogs($customerArray, $storeId);
                        $this->errorCheck = 1;
                    }
                }
            } catch (Exception $e) {

                $errorMsg = $e->getMessage()."/n".$e->getTraceAsString();
                $syncName = 'Customer Attribute Sync';
                $this->dataHelper->errorLogEmail($syncName, $errorMsg);

                $msg = $e->getMessage();
                $txt = "Error: " . $msg;
                $this->dataHelper->writeLogToFile($logViewFileName,$txt);
                $customerArray['store_id'] = $storeId;
                $customerArray['schedule_id'] = $syncLogID;
                $customerArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                $customerArray['acumatica_attribute_code'] = $fieldname;
                $customerArray['magento_attribute_code'] = $fieldcode;
                $customerArray['longMessage'] = $msg;
                $customerArray['runMode'] = "Manual";
                $customerArray['messageType'] = "Failure";
                $customerArray['syncDirection'] = 'syncToMagento';
                $this->customerAttributeHelper->customerAttributeSyncSuccessLogs($customerArray, $storeId);
                $this->errorCheck = 1;
            }
            $k++;
        }
    }

    /**
     * Importing Acumatica and Magento values from mapping table to magento.
     * @access public
     * @return string
     * */
    public function importIntoMagento($syncLogID, $logViewFileName, $storeId)
    {
        $adminInfo = $this->getConnection()->fetchAll("SELECT * FROM ".$this->getTable('amconnector_customer_attribute_mapping')." where entity_type=1 order by id asc");
        foreach ($adminInfo as $row) {
            $attributeDetails = $this->attribute->getCollection()
                ->setCodeFilter($row['magento_attribute_code'])
                ->load()
                ->getFirstItem();
            $attribute_id = $attributeDetails['attribute_id'];
            if ($row['is_common'] == 0 && !$attribute_id) {
                try {
                    $data = array();
                    $attributeObject = $this->_attrFactory->create();
                    $attributeSet = $this->attributeSetFactory->create();
                    //$customerSetup = $this->customerSetupFactory->create();
                    //$customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
                    //$attributeSetId = $customerEntity->getDefaultAttributeSetId();
		    $attributeSetId = $this->getConnection()->fetchOne("SELECT default_attribute_set_id FROM ".$this->getTable('eav_entity_type')." WHERE entity_type_code='customer'");
                    $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
                    $website = $this->websiteFactory->create();
                    $attributeObject->setWebsite($website);
                    $data['attribute_code'] = $row['magento_attribute_code'];
                    $data['label'] = $row['acumatica_attribute_code'];
                    $data['is_user_defined'] = true;
                    $data['frontend_input'] = $row['field_type'];
                    $data['is_system'] = 0;
                    $data['used_in_forms'][] = 'adminhtml_customer';
                    $data['attribute_set_id'] = $attributeSetId;
                    $data['input'] = $row['field_type'];
                    $data['unique'] = $row['is_unique'];
                    $data['visible'] = 1;
                    $data['frontend_class'] = '';
                    $data['frontend_label']['0'] = $row['acumatica_attribute_code'];
                    $field_values = $row['field_values'];
                    if (!empty($field_values) && $field_values != "") {
                        $options = json_decode($field_values);
                        foreach ($options as $key => $val) {
                            $arropt = explode("\t", $val);
                            foreach ($arropt as $kk => $vv) {
                                $attributes['option']['values'][] = trim($vv);
                            }
                        }

                        $data['source'] = "eav/entity_attribute_source_table";
                        $data['backend_type'] = 'varchar';
                        $data['input'] = 'select';
                    }
                    $data['backend_type'] = 'varchar';
                    /** @var $attrSet \Magento\Eav\Model\Entity\Attribute\Set */
                    $attrSet = $this->attributeSetFactory->create();
                    $data['attribute_group_id'] = $attributeGroupId;
                    $data['entity_type_id'] = 1;
                    $attributeObject->addData($data);
                    $attributeObject->save();
                    $this->getConnection()->query("update ".$this->getTable('amconnector_customer_attribute_mapping')." set flag=0 where id='" . $row['id'] . "' ");
                    $msg = "Attribute " . $row['acumatica_attribute_code'] . " created successfully in Magento!";
                    $txt = "Info : " . $msg;
                    $this->dataHelper->writeLogToFile($logViewFileName,$txt);

                    $customerAttributeArray['schedule_id'] = $syncLogID;
                    $customerAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                    $customerAttributeArray['acumatica_attribute_code'] = $row['acumatica_attribute_code'];
                    $customerAttributeArray['description'] = $msg;
                    $customerAttributeArray['runMode'] = "Manual";
                    $customerAttributeArray['messageType'] = "Success";
                    $customerAttributeArray['syncDirection'] = 'syncToMagento';
                    $this->customerAttributeHelper->customerAttributeSyncSuccessLogs($customerAttributeArray, $storeId);

                    $this->sucessMsg++;

                } catch (Exception $e) {
                    $errorMsg = $e->getMessage()."/n".$e->getTraceAsString();
                    $syncName = 'Customer Attribute Sync';
                    $this->dataHelper->errorLogEmail($syncName, $errorMsg);
                }
            }


        }
    }

    /*
     * Customer Attribute sync (one time)
     */
    public function syncCustomerAttributes($autoSync, $syncType, $syncId, $scheduleId = NULL, $sessionStoreId)
    {
        $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'customerAttribute', NULL);
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
                    $customerAttributeArray['schedule_id'] = $scheduleId;
                } else {
                    $customerAttributeArray['schedule_id'] = "";
                }
                $customerAttributeArray['store_id'] = $storeId;
                $customerAttributeArray['job_code'] = "customerattribute";
                $customerAttributeArray['status'] = "error";
                $customerAttributeArray['messages'] = "Invalid License Key";
                $customerAttributeArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                $customerAttributeArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                if($syncType == 'MANUAL'){
                    $customerAttributeArray['runMode'] = 'Manual';
                }elseif($syncType == 'AUTO'){
                    $customerAttributeArray['runMode'] = 'Automatic';
                }
                if($autoSync == 'COMPLETE'){
                    $customerAttributeArray['autoSync'] = 'Complete';
                }elseif($autoSync == 'INDIVIDUAL'){
                    $customerAttributeArray['autoSync'] = 'Individual';
                }
                $txt = "Error: Invalid License Key. Please verify and try again";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->customerAttributeHelper->customerAttributeManualSync($customerAttributeArray);
                $this->syncResourceModel->updateSyncAttribute($syncId,'ERROR',$storeId);
                $session->addError('Invalid license key.');
                $this->errorCheck = 1;
            }else{
                $txt = "Info : License verified successfully!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $txt = "Info : Server time verification is in progress";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $timeSyncCheck = $this->timeHelper->timeSyncCheck($storeId);

                if ($timeSyncCheck != self::IS_TIME_VALID ) {
                    if ($scheduleId != '') {
                        $customerArray['schedule_id'] = $scheduleId;
                    } else {
                        $customerArray['schedule_id'] = "";
                    }
                    $customerArray['status'] = "error";
                    $customerArray['messages'] = "Server time is not in sync.";
                    $customerArray['store_id'] = $storeId;
                    $customerArray['job_code'] = "customerattribute";
                    $customerArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                    $customerArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                    if ($syncType == 'MANUAL') {
                        $customerArray['runMode'] = 'Manual';
                    } elseif ($syncType == 'AUTO') {
                        $customerArray['runMode'] = 'Automatic';
                    }
                    if ($autoSync == 'COMPLETE') {
                        $customerArray['autoSync'] = 'Complete';
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $customerArray['autoSync'] = 'Individual';
                    }
                    $txt = "Error: " . $customerArray['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName,$txt);

                    $this->customerAttributeHelper->customerAttributeManualSync($customerArray);
                    $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                    $this->messageManager->addError("Server time is not in sync.");
                    $this->errorCheck = 1;
                } else {

                    $txt = "Info : Server time is in sync.";
                    $this->dataHelper->writeLogToFile($logViewFileName,$txt);
                    if ($scheduleId != '') {
                        $customerArray['schedule_id'] = $scheduleId;
                    } else {
                        $customerArray['schedule_id'] = "";
                    }
                    $customerArray['store_id'] = $storeId;
                    $customerArray['job_code'] = "customerattribute";
                    $customerArray['status'] = "success";
                    $customerArray['messages'] = "Customer Attribute manual sync initiated.";
                    if ($syncType == 'MANUAL') {
                        $customerArray['runMode'] = 'Manual';
                    } elseif ($syncType == 'AUTO') {
                        $customerArray['runMode'] = 'Automatic';
                    }
                    if ($autoSync == 'COMPLETE') {
                        $customerArray['autoSync'] = 'Complete';
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $customerArray['autoSync'] = 'Individual';
                    }
                    $customerArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                    $customerArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                    $txt = "Info : " . $customerArray['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName,$txt);

                    $syncLogID = $this->customerAttributeHelper->customerAttributeManualSync($customerArray);
                    $this->syncResourceModel->updateSyncAttribute($syncId, 'STARTED', $storeId);

                    if ($autoSync == 'COMPLETE') {
                        $insertedId = $this->syncResourceModel->checkConnectionFlag($syncId, 'CUSTOMER_ATTRIBUTE_SYNC',$storeId);
                        if ($insertedId == NULL) {
                            $this->messageManager->addError("Sync in Progress - please wait for the current sync to finish.");
                            $customerArray['id'] = $syncLogID;
                            $customerArray['job_code'] = "customerattribute";
                            $customerArray['status'] = "error"; //status
                            $customerArray['messages'] = "Another Sync is already executing"; //messages
                            $txt = "Info : Sync in Progress - please wait for the current sync to finish.";
                            $this->dataHelper->writeLogToFile($logViewFileName,$txt);
                            $this->customerAttributeHelper->customerAttributeManualSync($customerArray);
                            $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                            $this->syncResourceModel->updateConnection($insertedId, 'ERROR');
                        } else {
                            $this->syncResourceModel->updateConnection($insertedId, 'PROCESS');
                            $this->syncResourceModel->updateSyncAttribute($syncId, 'PROCESSING', $storeId);
                            $this->arrData = $this->getDetailsFromArray();
                            $this->syncMappingTable($syncLogID, $logViewFileName, $storeId);
                            $this->importIntoMagento($syncLogID, $logViewFileName, $storeId);

                            if ($this->sucessMsg == 0) {
                                $customerArray['id'] = $syncLogID;
                                if ($this->errorCheck == 1) {
                                    $customerArray['status'] = "error";
                                } else {
                                    $customerArray['status'] = "success";
                                }
                                $customerArray['messages'] = "Attributes already synced, no attribute has been updated.";
                                $customerArray['job_code'] = "customerattribute";
                                $customerArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                $customerArray['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                $this->customerAttributeHelper->customerAttributeManualSync($customerArray);

                                $txt = "Info : " . $customerArray['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName,$txt);
                                $this->messageManager->addSuccess("Customer attributes synced successfully!");

                            } else {
                                $customerArray['id'] = $syncLogID;
                                if ($this->errorCheck == 1) {
                                    $customerArray['status'] = "error";
                                } else {
                                    $customerArray['status'] = "success";
                                }
                                $customerArray['messages'] = "Attributes created successfully!";
                                $customerArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                $customerArray['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                $this->customerAttributeHelper->customerAttributeManualSync($customerArray);

                                $txt = "Info : " . $customerArray['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName,$txt);
                                $this->messageManager->addSuccess('Customer attributes synced successfully');
                            }

                        }
                    }
                }
            }



            if ($this->errorCheck == 1) {
                $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
            } else {
                $this->syncResourceModel->updateConnection($insertedId, 'SUCCESS');
                $this->syncResourceModel->updateSyncAttribute($syncId, 'SUCCESS', $storeId);
            }
        } catch (Exception $e) {
            if ($scheduleId != '') {
                $customerArray['id'] = $scheduleId;
            } else {
                $customerArray['id'] = $syncLogID;
            }
            $customerArray['messages'] = $e->getMessage();
            $txt = "Error: " . $customerArray['messages'];
            $this->dataHelper->writeLogToFile($logViewFileName,$txt);
            $this->customerAttributeHelper->customerAttributeManualSync($customerArray);
            $this->messageManager->addError("Something Went Wrong...");
        }

        $txt = "Info : Sync process completed!";
        $this->dataHelper->writeLogToFile($logViewFileName,$txt);

    }

}
