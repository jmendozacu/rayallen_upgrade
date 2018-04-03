<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Framework\Stdlib\DateTime\Timezone as TimeZone;

use Magento\Framework\Stdlib\DateTime\DateTime as DateTime;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magento\Tax\Model\ClassModel;
use Kensium\Lib;

/**
 * Class TaxCategory
 * @package Kensium\Amconnector\Model\ResourceModel
 */
class TaxCategory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var
     */
    public $totalTrialRecord;
    /**
     * @var
     */
    public $licenseType;
    /**
     * @var
     */
    public $sucessMsg;
    /**
     * @var
     */
    public $errorCheck;
    /**
     * @var
     */
    protected  $arrData;
    /**
     * @var DateTime
     */
    protected $date;
    /**
     * @var TimeZone
     */
    protected $timezone;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var Sync
     */
    protected $syncResourceModel;


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Kensium\Amconnector\Helper\Xml
     */
    protected $xmlHelper;

    /**
     * @var \Magento\Tax\Model\ResourceModel\TaxClass\CollectionFactory
     */
    protected $taxClassFactory;

    /**
     * @var \Magento\Tax\Model\ClassModelFactory
     */
    protected $taxClassModelFactory;

    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Kensium\Amconnector\Helper\Time
     */
    protected $timeHelper;

    /**
     * @var \Kensium\Synclog\Helper\TaxCategory
     */
    protected $taxCategoryLogHelper;

    /**
     * @var
     */
    protected $licenseResourceModel;

    /**
     * @var \Kensium\Amconnector\Helper\Licensecheck
     */
    protected $licenseHelper;
    protected $common;

    const IS_TIME_VALID = "Valid";

    const IS_LICENSE_VALID = "Valid";

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param DateTime $date
     * @param TimeZone $timezone
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param Sync $syncResourceModel
     * @param \Kensium\Amconnector\Helper\Data $dataHelper
     * @param \Kensium\Amconnector\Helper\Time $timeHelper
     * @param \Kensium\Synclog\Helper\TaxCategory $taxCategoryLogHelper
     * @param \Magento\Tax\Model\ResourceModel\TaxClass\CollectionFactory $taxClassFactory
     * @param \Magento\Tax\Model\ClassModelFactory $taxClassModelFactory
     * @param \Kensium\Amconnector\Helper\Xml $xmlHelper
     * @param Licensecheck $licenseResourceModel
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        DateTime $date,
        TimeZone $timezone,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Synclog\Helper\TaxCategory $taxCategoryLogHelper,
        \Magento\Tax\Model\ResourceModel\TaxClass\CollectionFactory $taxClassFactory,
        \Magento\Tax\Model\ClassModelFactory $taxClassModelFactory,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
	Lib\Common $common,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->date = $date;
        $this->timezone = $timezone;
        $this->licenseCheck = $licenseResourceModel;
        $this->xmlHelper = $xmlHelper;
        $this->dataHelper = $dataHelper;
        $this->timeHelper = $timeHelper;
        $this->taxClassFactory = $taxClassFactory;
        $this->taxCategoryLogHelper = $taxCategoryLogHelper;
        $this->taxClassModelFactory = $taxClassModelFactory;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->storeRepository = $storeRepository;
        $this->syncResourceModel = $syncResourceModel;
	$this->common = $common;
    }

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amconnector_taxCategory_mapping', 'id');
    }

    /**
     * @param $autoSync
     * @param $syncType
     * @param $syncId
     * @param null $scheduleId
     * @param $storeId
     */
    public function getTaxCategorySync($autoSync, $syncType, $syncId, $scheduleId = NULL,$storeId)
    {
        $this->totalTrialRecord = $this->common->numberOfRecordSyncInTrialLicense();
        $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'taxCategory', NULL);
        try {
            if ($storeId == 0) {
                $storeId = 1;
            }
            $txt = "Info : Sync process started!";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            /* License check */
            $this->licenseType = $this->licenseCheck->checkLicenseTypes($storeId);
            $licenseStatus = $this->licenseCheck->getLicenseStatus($storeId);

            $txt = "Info : License verification is in progress";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            if ($licenseStatus != self::IS_LICENSE_VALID) {
                /**
                 * logs here for Invalid License
                 */
                if ($scheduleId != '') {
                    $taxCategoryLog['schedule_id'] = $scheduleId;
                } else {
                    $taxCategoryLog['schedule_id'] = "";
                }
                $taxCategoryLog['store_id'] = $storeId;
                $taxCategoryLog['job_code'] = "taxCategory"; //job code
                $taxCategoryLog['status'] = "error"; //status
                $taxCategoryLog['messages'] = "Invalid License Key"; //messages
                $taxCategoryLog['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                $taxCategoryLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s', time());
                if ($syncType == 'MANUAL') {
                    $taxCategoryLog['runMode'] = 'Manual';
                } elseif ($syncType == 'AUTO') {
                    $taxCategoryLog['runMode'] = 'Automatic';
                }
                if ($autoSync == 'COMPLETE') {
                    $taxCategoryLog['autoSync'] = 'Complete';
                }

                $txt = "Error: " . $taxCategoryLog['messages'];
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                $this->taxCategoryLogHelper->taxCategoryManualSync($taxCategoryLog);
                //$this->messageManager->addError('Invalid license key');
                $this->errorCheck = 1;

            } else {

                $txt = "Info : License verified successfully!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $txt = "Info : Server time verification is in progress";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $timeSyncCheck = $this->timeHelper->timeSyncCheck($storeId);
                if ($timeSyncCheck != self::IS_TIME_VALID && 0) {
                    //Check Time is synced or not
                    if ($scheduleId != '') {
                        $taxCategoryLog['schedule_id'] = $scheduleId;
                    } else {
                        $taxCategoryLog['schedule_id'] = "";
                    }
                    $taxCategoryLog['store_id'] = $storeId;
                    $taxCategoryLog['job_code'] = "taxCategory"; //job code
                    $taxCategoryLog['status'] = "error"; //status
                    $taxCategoryLog['messages'] = "Time is not synced"; //messages
                    $taxCategoryLog['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                    $taxCategoryLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s', time());
                    if ($syncType == 'MANUAL') {
                        $taxCategoryLog['runMode'] = 'Manual';
                    } elseif ($syncType == 'AUTO') {
                        $taxCategoryLog['runMode'] = 'Automatic';
                    }
                    if ($autoSync == 'COMPLETE') {
                        $taxCategoryLog['autoSync'] = 'Complete';
                    }

                    /**
                     * logs here for Time Not Synced
                     */
                    $taxCategoryLog['messages'] = "Server time is not in sync"; //messages
                    $txt = "Error: " . $taxCategoryLog['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->taxCategoryLogHelper->taxCategoryManualSync($taxCategoryLog);
                    $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                    //$this->messageManager->addError('Time is not synced.');
                    $this->errorCheck = 1;
                } else {
                    $taxCategoryLog['messages'] = "Server time is in sync"; //messages
                    $txt = "Info : " . $taxCategoryLog['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    /**
                     * Log here for starting Sync
                     * If $scheduleId == NULL; it means Manual Sync ( Individual or COMPLETE)
                     * If $scheduleId != NULL; AUTO Sync via Cron
                     */
                    if ($scheduleId != '') {
                        $taxCategoryLog['schedule_id'] = $scheduleId;
                    } else {
                        $taxCategoryLog['schedule_id'] = "";
                    }
                    $taxCategoryLog['store_id'] = $storeId;
                    $taxCategoryLog['job_code'] = "taxCategory"; //job code
                    $taxCategoryLog['status'] = "success"; //status
                    $taxCategoryLog['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                    $taxCategoryLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s', time());
                    $taxCategoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                    $taxCategoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                    if ($syncType == 'MANUAL') {
                        $taxCategoryLog['runMode'] = 'Manual';
                    } elseif ($syncType == 'AUTO') {
                        $taxCategoryLog['runMode'] = 'Automatic';
                    }
                    if ($autoSync == 'COMPLETE') {
                        $taxCategoryLog['autoSync'] = 'Complete';
                    }
                    // chmod($logViewFileName, 0777);

                    $taxCategoryLog['messages'] = "Tax Category sync initiated";
                    $txt = "Info : " . $taxCategoryLog['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    $syncLogID = $this->taxCategoryLogHelper->taxCategoryManualSync($taxCategoryLog);;
                    $this->syncResourceModel->updateSyncAttribute($syncId, 'STARTED', $storeId);
                    /**
                     * tax category sync
                     */
                    if ($autoSync == 'COMPLETE') {
                        $insertedId = $this->syncResourceModel->checkConnectionFlag($syncId, 'TAX_CATEGORY_SYNC');
                        if ($insertedId == NULL) {
                            //$this->messageManager->addError("Sync in Progress - please wait for the current sync to finish.");
                            $taxArray['id'] = $syncLogID;
                            $taxArray['job_code'] = "taxCategory";
                            $taxArray['status'] = "error";
                            $taxArray['messages'] = "Another Sync is already executing";
                            $taxArray['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                            $taxArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                            $txt = "Info : " . $taxArray['messages'];
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->taxCategoryLogHelper->taxCategoryManualSyncUpdate($taxArray);
                            $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                            $this->errorCheck = 1;
                        } else {
                            $this->syncResourceModel->updateConnection($insertedId, 'PROCESS', $storeId);
                            $this->syncResourceModel->updateSyncAttribute($syncId, 'PROCESSING', $storeId);

                            $this->arrData = $this->getDataFromAcumatica($storeId);
                            $syncedRecord = $this->importIntoMagento($syncLogID,$logViewFileName,$storeId);

                            if ($this->sucessMsg == (count($this->arrData) - 1)) {

                                $taxArray['id'] = $syncLogID;
                                if ($syncType == 'MANUAL') {
                                    $taxArray['runMode'] = 'Manual';
                                } elseif ($syncType == 'AUTO') {
                                    $taxArray['runMode'] = 'Automatic';
                                }
                                if ($autoSync == 'COMPLETE') {
                                    $taxArray['autoSync'] = 'Complete';
                                }
                                if ($this->errorCheck == 1) {
                                    $taxArray['status'] = "error";
                                } else {
                                    $taxArray['status'] = "success";
                                }
                                $taxArray['job_code'] = "taxCategory";
                                $taxArray['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                $taxArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s', time());
                                $taxArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                $taxArray['messages'] = "Tax category(s) already exist, no category has been updated"; //messages
                                $this->taxCategoryLogHelper->taxCategoryManualSyncUpdate($taxArray);

                                $txt = "Info : " . $taxArray['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->syncResourceModel->updateSyncAttribute($syncId, 'SUCCESS', $storeId);
                                //$this->messageManager->addSuccess('Tax category sync completed successfully.');

                            } else {
                                $taxArray['id'] = $syncLogID;
                                if ($syncType == 'MANUAL') {
                                    $taxArray['runMode'] = 'Manual';
                                } elseif ($syncType == 'AUTO') {
                                    $taxArray['runMode'] = 'Automatic';
                                }
                                if ($autoSync == 'COMPLETE') {
                                    $taxArray['autoSync'] = 'Complete';
                                }
                                if ($this->errorCheck == 1) {
                                    $taxArray['status'] = "error";
                                } else {
                                    $taxArray['status'] = "success";
                                }
                                if ($this->licenseType == 'trial' && $syncedRecord == $this->totalTrialRecord) {
                                    $taxArray['messages'] = "Trial license allow only " . $this->totalTrialRecord . " records per sync!"; //messages
                                    $txt = "Trial license allow only " . $this->totalTrialRecord . " records per sync!";
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                } else {
                                    $taxArray['messages'] = "Tax category(s) created/updated successfully!";
                                    $txt = "Info : Tax category(s) created/updated successfully!";
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                }
                                $taxArray['job_code'] = "taxCategory";
                                $taxArray['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
                                $taxArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s', time());
                                $taxArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
                                $this->taxCategoryLogHelper->taxCategoryManualSyncUpdate($taxArray);
                            }
                        }
                    }
                }
            }
            if ($this->errorCheck == 1) {
                $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
            } else {
                $this->syncResourceModel->updateSyncAttribute($syncId, 'SUCCESS', $storeId);
            }
            if ($insertedId) {
                $this->syncResourceModel->updateConnection($insertedId, 'SUCCESS', $storeId);
            }
        }catch (Exception $e){
            if ($scheduleId != '') {
                $taxArray['id'] = $scheduleId;
            } else {
                $taxArray['id'] = $syncLogID;
            }
            $taxArray['store_id'] = $storeId;
            $taxArray['job_code'] = "taxCategory";
            $taxArray['status'] = "error";
            $taxArray['messages'] = $e->getMessage();
            $taxArray['executed_at'] = $this->date->date('Y-m-d H:i:s', time());
            $taxArray['finished_at'] = $this->date->date('Y-m-d H:i:s', time());
            $txt = "Error: " . $taxArray['messages'];
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->taxCategoryLogHelper->taxCategoryManualSync($taxArray);
        }
        $txt = "Info : Sync process completed!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
    }

    /**
     * get acumatica tax categories
     */
    public function getDataFromAcumatica($storeId)
    {
        if($storeId==0){
            $scopeType = 'default';
        }else{
            $scopeType = 'stores';
        }
        try {
	    $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$storeId);
            $url = $this->common->getBasicConfigUrl($serverUrl);

            $csvTaxCategoryClassData = $this->common->getEnvelopeData('TAXCATEGORIES');
            $taxCategoriesXMLGetRequest = $csvTaxCategoryClassData['envelope'];
            $taxCategoriesClassAction = $csvTaxCategoryClassData['envName'] . "/" . $csvTaxCategoryClassData['envVersion'] . "/" . $csvTaxCategoryClassData['methodName'];
            $gettaxCategoriesResponse = $this->common->getAcumaticaResponse($configParameters, $taxCategoriesXMLGetRequest, $url, $taxCategoriesClassAction);
            $data = $gettaxCategoriesResponse->Body->GetListResponse->GetListResult;
            $totalData = $this->xmlHelper->xml2array($data);
            $arrayKey = 1;
            $insertedData = array();
            $insertedData[0] = array('s.no', 'Class Name', 'Class Desc', 'Status');
	    if(isset($totalData['Entity']['RowNumber']['Value'])){
                $sNo = '' . $totalData['Entity']['RowNumber']['Value'];
                $className = '' . $totalData['Entity']['TaxCategoryID']['Value'];
                $classDesc = '' . $totalData['Entity']['Description']['Value'];
                $status = '' . $totalData['Entity']['Active']['Value'];
                if ($status == 'true') {
                    $insertedData[$arrayKey] = array($sNo, $className, $classDesc, $status);
                }
            }else{
                foreach ($totalData['Entity'] as $value) {
	 	    $value = $this->xmlHelper->xml2array($value);
                    $sNo = '' . $value['RowNumber']['Value'];
                    $className = '' . $value['TaxCategoryID']['Value'];
                    $classDesc = '' . $value['Description']['Value'];
                    $status = '' . $value['Active']['Value'];
                    if ($status == 'true') {
                        $insertedData[$arrayKey] = array($sNo, $className, $classDesc, $status);
                        $arrayKey++;
                    }
                }
            }
            return $this->arrData = $insertedData;
        }catch (SoapFault $e) {
            echo "Last request:<pre>" . $e->getMessage() . "</pre>";
        }
    }

    /**
     * @param $syncLogID
     * @param $logViewFileName
     * @param $storeId
     * @return int
     */
    public function importIntoMagento($syncLogID,$logViewFileName,$storeId)
    {

        $i = 0;
        $k = 0 ;
        $trialSyncRecordCount = 0 ;

        foreach ($this->arrData as $row) {
            if($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord) {
                try {
                    if($k>=1) {
                        $taxCategory = array();
                        $taxCategory['class_name'] = $row[1];
                        $taxCategory['class_type'] = 'PRODUCT';
                        $checkTaxCategory = $this->taxClassFactory->create()
                            ->addFieldToFilter('class_type', array('eq' => 'PRODUCT'))
                            ->addFieldToFilter('class_name', array('eq' => $taxCategory['class_name']))
                            ->getFirstItem();
                        if ($row[3] != 'true') {
                            $taxModel = $this->taxClassFactory->create()->load($checkTaxCategory['class_id']);
                            if (empty($checkTaxCategory['class_name'])) {
                                /**
                                 * Tax Category with status Inactive in acumatica
                                 * Not exists in Magento
                                 */

                                $taxCategoryLog['messages'] = "Tax Category : " . $taxCategory['class_name'] . " not exists in Magento"; //messages
                                $txt = "Error : " . $taxCategoryLog['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $taxArray['schedule_id'] = $syncLogID;
                                $taxArray['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                                $taxArray['acumatica_attribute_code'] = $taxCategory['class_name'];
                                $taxArray['magento_attribute_code'] = '';
                                $taxArray['longMessage'] = "Tax Category not exists";
                                $taxArray['runMode'] = "Manual";
                                $taxArray['messageType'] = "Error";
                                $taxArray['syncDirection'] = 'syncToMagento';
                                $this->taxCategoryLogHelper->taxcategorySyncLogs($taxArray,$storeId);
                                $this->errorCheck = 1;
                            } else {
                                $taxCategoryId = $taxModel->getId();
                                $taxModel->delete();

                                $taxCategoryLog['messages'] = "Tax Category " . $taxCategoryId . " is deleted"; //messages
                                $txt = "Info : " . $taxCategoryLog['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $taxArray['schedule_id'] = $syncLogID;
                                $taxArray['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                                $taxArray['acumatica_attribute_code'] = $taxCategory['class_name'];
                                $taxArray['magento_attribute_code'] = $taxCategoryId;
                                $taxArray['description'] = 'Tax category deleted';
                                $taxArray['runMode'] = "Manual";
                                $taxArray['messageType'] = "Success";
                                $taxArray['syncDirection'] = 'syncToMagento';
                                $this->taxCategoryLogHelper->taxcategorySyncLogs($taxArray,$storeId);
                            }
                        } else {
                            if (empty($checkTaxCategory['class_name'])) {
                                /**
                                 * create tax category
                                 */
                                try {
                                    $taxModel = $this->taxClassModelFactory->create();
                                    $taxModel->setClassType(ClassModel::TAX_CLASS_TYPE_PRODUCT);
                                    $taxModel->setClassName($taxCategory['class_name']);
                                    $taxModel->save();

                                    $taxCategoryLog['messages'] = "Tax category " . $taxCategory['class_name'] . " created in Magento successfully!"; //messages
                                    $txt = "Info : " . $taxCategoryLog['messages'];
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                    $taxArray['schedule_id'] = $syncLogID;
                                    $taxArray['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    $taxArray['acumatica_attribute_code'] = $taxCategory['class_name'];
                                    $taxArray['magento_attribute_code'] = $taxModel->getId();
                                    $taxArray['description'] = 'Tax category created / updated';
                                    $taxArray['runMode'] = "Manual";
                                    $taxArray['messageType'] = "Success";
                                    $taxArray['syncDirection'] = 'syncToMagento';
                                    $this->taxCategoryLogHelper->taxcategorySyncLogs($taxArray,$storeId);


                                } catch (Exception $e) {
                                    $taxCategoryLog['messages'] = "Failed to create '" . $taxCategory['class_name'] . "' Tax category"; //messages
                                    $txt = "Error : " . $taxCategoryLog['messages'];
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                    $taxArray['schedule_id'] = $syncLogID;
                                    $taxArray['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    $taxArray['acumatica_attribute_code'] = $taxCategory['class_name'];
                                    $taxArray['magento_attribute_code'] = '';
                                    $taxArray['longMessage'] = $e->getMessage();
                                    $taxArray['runMode'] = "Manual";
                                    $taxArray['messageType'] = "Error";
                                    $taxArray['syncDirection'] = 'syncToMagento';
                                    $this->taxCategoryLogHelper->taxcategorySyncLogs($taxArray,$storeId);
                                    $this->errorCheck = 1;
                                }
                            } else {
                                /*
                                 * If All Tax category exists in magento
                                 * Increment the variable by 1
                                 */
                                $i++;
                            }
                        }
                    }

                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
            $trialSyncRecordCount++ ;
            if($this->licenseType != 'trial'){
                try{
                    if($k>=1) {
                        $taxCategory = array();
                        $taxCategory['class_name'] = $row[1];
                        $taxCategory['class_type'] = 'PRODUCT';
                        $checkTaxCategory = $this->taxClassFactory->create()
                            ->addFieldToFilter('class_type', array('eq' => 'PRODUCT'))
                            ->addFieldToFilter('class_name', array('eq' => $taxCategory['class_name']))
                            ->getFirstItem();
                        if ($row[3] != 'true') {
                            $taxModel = $this->taxClassFactory->create()->load($checkTaxCategory['class_id']);
                            if (empty($checkTaxCategory['class_name'])) {
                                /**
                                 * Tax Category with status Inactive in acumatica
                                 * Not exists in Magento
                                 */

                                $taxCategoryLog['messages'] = "Tax Category : " . $taxCategory['class_name'] . " not exists in Magento"; //messages
                                $txt = "Error : " . $taxCategoryLog['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $taxArray['schedule_id'] = $syncLogID;
                                $taxArray['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                                $taxArray['acumatica_attribute_code'] = $taxCategory['class_name'];
                                $taxArray['magento_attribute_code'] = '';
                                $taxArray['longMessage'] = "Tax Category not exists";
                                $taxArray['runMode'] = "Manual";
                                $taxArray['messageType'] = "Error";
                                $taxArray['syncDirection'] = 'syncToMagento';
                                $this->taxCategoryLogHelper->taxcategorySyncLogs($taxArray,$storeId);
                                $this->errorCheck = 1;
                            } else {
                                $taxCategoryId = $taxModel->getId();
                                $taxModel->delete();

                                $taxCategoryLog['messages'] = "Tax Category " . $taxCategoryId . " is deleted"; //messages
                                $txt = "Info : " . $taxCategoryLog['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                $taxArray['schedule_id'] = $syncLogID;
                                $taxArray['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                                $taxArray['acumatica_attribute_code'] = $taxCategory['class_name'];
                                $taxArray['magento_attribute_code'] = $taxCategoryId;
                                $taxArray['description'] = 'Tax category deleted';
                                $taxArray['runMode'] = "Manual";
                                $taxArray['messageType'] = "Success";
                                $taxArray['syncDirection'] = 'syncToMagento';
                                $this->taxCategoryLogHelper->taxcategorySyncLogs($taxArray,$storeId);
                            }
                        } else {
                            if (empty($checkTaxCategory['class_name'])) {
                                /**
                                 * create tax category
                                 */
                                try {
                                    $taxModel = $this->taxClassModelFactory->create();
                                    $taxModel->setClassType(ClassModel::TAX_CLASS_TYPE_PRODUCT);
                                    $taxModel->setClassName($taxCategory['class_name']);
                                    $taxModel->save();

                                    $taxCategoryLog['messages'] = "Tax category " . $taxCategory['class_name'] . " created in Magento successfully!"; //messages
                                    $txt = "Info : " . $taxCategoryLog['messages'];
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                    $taxArray['schedule_id'] = $syncLogID;
                                    $taxArray['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    $taxArray['acumatica_attribute_code'] = $taxCategory['class_name'];
                                    $taxArray['magento_attribute_code'] = $taxModel->getId();
                                    $taxArray['description'] = 'Tax category created / updated';
                                    $taxArray['runMode'] = "Manual";
                                    $taxArray['messageType'] = "Success";
                                    $taxArray['syncDirection'] = 'syncToMagento';
                                    $this->taxCategoryLogHelper->taxcategorySyncLogs($taxArray,$storeId);


                                } catch (Exception $e) {
                                    $taxCategoryLog['messages'] = "Failed to create '" . $taxCategory['class_name'] . "' Tax category"; //messages
                                    $txt = "Error : " . $taxCategoryLog['messages'];
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                    $taxArray['schedule_id'] = $syncLogID;
                                    $taxArray['created_at'] = $this->date->date('Y-m-d H:i:s', time());
                                    $taxArray['acumatica_attribute_code'] = $taxCategory['class_name'];
                                    $taxArray['magento_attribute_code'] = '';
                                    $taxArray['longMessage'] = $e->getMessage();
                                    $taxArray['runMode'] = "Manual";
                                    $taxArray['messageType'] = "Error";
                                    $taxArray['syncDirection'] = 'syncToMagento';
                                    $this->taxCategoryLogHelper->taxcategorySyncLogs($taxArray,$storeId);
                                    $this->errorCheck = 1;
                                }
                            } else {
                                /*
                                 * If All Tax category exists in magento
                                 * Increment the variable by 1
                                 */
                                $i++;
                            }
                        }
                    }
                }catch (Exception $e){
                    echo $e->getMessage();
                }
            }
            $k++;
        }
        $this->sucessMsg = $i;
        return $trialSyncRecordCount;
    }

}
