<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Webapi\Soap;
use SoapClient;
use SoapFault;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Kensium\Amconnector\Helper\Sync;
use Kensium\Amconnector\Helper\AmconnectorSoap;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Locale\ListsInterface;
use Magento\Config\Model\ResourceModel\Config;
use Kensium\Lib;

class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected  $scopeConfigInterface;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var ResourceConnection|\Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $resourceModelSync;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Kensium\Amconnector\Model\Category
     */
    protected $amconnectorCategoryFactory;

    /**
     * @var \Magento\Category\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Category\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Data
     */
    protected $xmlHelper;

    /**
     * @var Data
     */
    protected $timeHelper;

    /**
     * @var Url
     */
    protected $urlHelper;

    /**
     * @var Sync
     */
    protected $syncHelper;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var ListsInterface
     */
    protected $localeLists;
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Category
     */
    protected $categoryResourceModel;
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
    protected $categoryLogHelper;
    /**
     * @var Client
     */
    protected $clientHelper;
    /**
     * @var \Kensium\Amconnector\Model\Category
     */
    protected $categoryModel;
    /**
     * @var
     */
    protected $licenseResourceModel;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var
     */
    protected  $licensecheck;

    const IS_LICENSE_INVALID = "Invalid";
    const IS_LICENSE_VALID = "Valid";
    const IS_TIME_VALID = "Valid";


    /**
     * @param Context $context
     * @param DateTime $date
     * @param Sync $syncHelper
     * @param Timezone $timezone
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Xml $xmlHelper
     * @param Time $timeHelper
     * @param Url $urlHelper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Kensium\Amconnector\Model\ResourceModel\Category $categoryResourceModel
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync
     * @param Data $dataHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Kensium\Synclog\Helper\Category $categoryLogHelper
     * @param \Kensium\Amconnector\Model\Category $categoryModel
     * @param Client $clientHelper
     * @param \Kensium\Amconnector\Model\CategoryFactory $amconnectorCategoryFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Model\CategoryFactory $category
     * @param ModuleDataSetupInterface $setup
     * @param ListsInterface $localeLists
     * @param Config $config
     */
    public function __construct(
        Context $context,
        DateTime $date,
        Sync $syncHelper,
        Timezone $timezone,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Kensium\Amconnector\Model\ResourceModel\Category $categoryResourceModel,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Synclog\Helper\Category $categoryLogHelper,
        \Kensium\Amconnector\Model\Category $categoryModel,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Model\CategoryFactory $amconnectorCategoryFactory,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\CategoryFactory $category,
        ListsInterface $localeLists,
		Lib\Common $common,
        Config $config
    )
    {
        parent::__construct($context);
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->date = $date;
        $this->syncHelper = $syncHelper;
        $this->urlHelper = $urlHelper;
        $this->xmlHelper = $xmlHelper;
        $this->timeHelper = $timeHelper;
        $this->resourceConnection = $resourceConnection;
        $this->dir = $dir;
        $this->resourceModelSync = $resourceModelSync;
        $this->logger = $context->getLogger();
        $this->messageManager = $messageManager;
        $this->localeLists = $localeLists;
        $this->config = $config;
        $this->amconnectorCategoryFactory = $amconnectorCategoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $category;
        $this->clientHelper = $clientHelper;
        $this->categoryModel = $categoryModel;
        $this->categoryLogHelper = $categoryLogHelper;
        $this->dataHelper = $dataHelper;
        $this->syncResourceModel = $syncResourceModel;
        $this->categoryResourceModel = $categoryResourceModel;
        $this->timezone = $timezone;
        $this->_messageManager = $messageManager;
        $this->licenseResourceModel = $licenseResourceModel;
        $this->common = $common;
    }

    /**
     * @param $url
     */
    public function getCategorySchema($url, $storeId) {
        try {
            if ($storeId == 0 || $storeId == NULL) {
                $storeId = 1;
            }
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvCategorySchemaData = $this->common->getEnvelopeData('GETCATEGORYSCHEMA');
            $XMLGetRequest = $csvCategorySchemaData['envelope'];
            $action = $csvCategorySchemaData['methodName'];
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $action);
            $schemaData = array();
            if ($xmlResponse->Body->GetSchemaResponse->GetSchemaResult) {
                $data = $xmlResponse->Body->GetSchemaResponse->GetSchemaResult;
                $arrayData = $this->xmlHelper->XMLtoArray($data);
                $schemaData = $arrayData['ENDPOINT']['TOPLEVELENTITY'];
            }
            return $schemaData;
        } catch (SoapFault $e) {
            echo "Last request:<pre>" . htmlentities($e->getMessage()) . "</pre>";
        }
    }

    /**
     * @param $autoSync
     * @param $syncType
     * @param $syncId
     * @param null $scheduleId
     * @param $cronStoreId
     * @param null $individualCategoryId
     *
     * Category Sync
     */
    public function getCategorySync($autoSync, $syncType, $syncId, $scheduleId = NULL, $cronStoreId, $individualCategoryId = NULL)
    {
        $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'category', NULL);
        $this->totalTrialRecord = $this->common->numberOfRecordSyncInTrialLicense();
        try {
            $txt = "Info : Sync process started!";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            if ($syncType != "AUTO") {
                if ($cronStoreId == 0) {
                    $storeId = 1;
                } else {
                    $storeId = $cronStoreId;
                }
            } else {
                $storeId = $cronStoreId;
            }

            if($cronStoreId == 0){
                $scopeType = 'default';
            }else{
                $scopeType = 'stores';
            }
            $this->licenseType = $this->licenseResourceModel->checkLicenseTypes($storeId);
            if ($this->categoryResourceModel->stopSyncValue() == 1) {
                /**
                 * License status check
                 */
                $txt = "Info : License verification is in progress";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $licenseStatus = $this->licenseResourceModel->getLicenseStatus($storeId);

                if ($licenseStatus != self::IS_LICENSE_VALID) {
                    /**
                     * logs here for Invalid License
                     */
                    if ($scheduleId != '') {
                        $categoryLog['schedule_id'] = $scheduleId;
                    } else {
                        $categoryLog['schedule_id'] = "";
                    }
                    $categoryLog['store_id'] = $storeId;
                    $categoryLog['job_code'] = "category";
                    $categoryLog['status'] = "error";
                    $categoryLog['messages'] = "Invalid License Key";
                    $categoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                    $categoryLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                    if ($syncType  == 'MANUAL') {
                        $categoryLog['runMode'] = 'Manual';
                    } elseif ($syncType == 'AUTO') {
                        $categoryLog['runMode'] = 'Automatic';
                    }
                    if ($autoSync == 'COMPLETE') {
                        $categoryLog['autoSync'] = 'Complete';
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $categoryLog['autoSync'] = 'Individual';
                    }
                    $txt = "Error: " . $categoryLog['messages'];
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                    $this->categoryLogHelper->categoryManualSync($categoryLog);
                } else {

                    $txt = "Info : License verified successfully!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    $txt = "Info : Server time verification is in progress";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    /**
                     * Server time check
                     */
                    $timeSyncCheck = $this->timeHelper->timeSyncCheck($storeId);
                    if ($timeSyncCheck != self::IS_TIME_VALID) {
                        /**
                         * logs here for Time Not Synced
                         */
                        if ($scheduleId != '') {
                            $categoryLog['schedule_id'] = $scheduleId;
                        } else {
                            $categoryLog['schedule_id'] = "";
                        }
                        $categoryLog['store_id'] = $storeId;
                        $categoryLog['job_code'] = "category"; //job code
                        $categoryLog['status'] = "error"; //status
                        $categoryLog['messages'] = "Server time is not in sync"; //messages
                        $categoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                        $categoryLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                        if ($syncType  == 'MANUAL') {
                            $categoryLog['runMode'] = 'Manual';
                        } elseif ($syncType == 'AUTO') {
                            $categoryLog['runMode'] = 'Automatic';
                        }
                        if ($autoSync == 'COMPLETE') {
                            $categoryLog['autoSync'] = 'Complete';
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $categoryLog['autoSync'] = 'Individual';
                        }
                        $txt = "Error: " . $categoryLog['messages'];
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                        $this->categoryLogHelper->categoryManualSync($categoryLog);
                    } else {
                        /**
                         * Log here for starting Sync
                         * If $scheduleId == NULL; it means Manual Sync ( Individual or COMPLETE)
                         * If $scheduleId != NULL; AUTO Sync via Cron
                         */
                        $txt = "Info : Server time is in sync.";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                        if ($scheduleId != '') {
                            $categoryLog['schedule_id'] = $scheduleId;
                        } else {
                            $categoryLog['schedule_id'] = "";
                        }
                        $categoryLog['store_id'] = $storeId;
                        $categoryLog['job_code'] = "category";
                        $categoryLog['status'] = "success";
                        $categoryLog['messages'] = "Category manual sync initiated";
                        $categoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                        $categoryLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                        if ($syncType  == 'MANUAL') {
                            $categoryLog['runMode'] = 'Manual';
                        } elseif ($syncType == 'AUTO') {
                            $categoryLog['runMode'] = 'Automatic';
                        }
                        if ($autoSync == 'COMPLETE') {
                            $categoryLog['autoSync'] = 'Complete';
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $categoryLog['autoSync'] = 'Individual';
                        }
                        $txt = "Info : " . $categoryLog['messages'];
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $syncLogID = $this->categoryLogHelper->categoryManualSync($categoryLog);
                        $this->syncResourceModel->updateSyncAttribute($syncId, 'STARTED', $storeId);


                        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl', $scopeType, $storeId);
                        $loginUrl = $this->common->getBasicConfigUrl($serverUrl);
                        if ($autoSync == 'COMPLETE') {
                            /**
                             * Hold the connection flag for category sync
                             */
                            $insertedId = $this->syncResourceModel->checkConnectionFlag($syncId, 'category', $storeId);
                            if ($insertedId == NULL) {
                                $categoryLog['id'] = $syncLogID;
                                $categoryLog['job_code'] = "catgeory";
                                $categoryLog['status'] = "error";
                                $categoryLog['messages'] = "Another Sync is already executing"; //messages
                                $categoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                $categoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                $txt = "Info : Sync in Progress - please wait for the current sync to finish";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);
                            } else {
                                $this->syncResourceModel->updateConnection($insertedId, 'PROCESS','',$storeId);
                                $this->syncResourceModel->updateSyncAttribute($syncId, 'PROCESSING', $storeId);
                                $this->categoryResourceModel->truncateDataFromTempTables();

                                /**
                                 * Fetch the category data based on lastsyncdate and store
                                 * in temporary tables (both Magento & Acumatica)
                                 * starting execution of sync
                                 */
                                $categoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                $categoryLog['id'] = $syncLogID;

                                $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);

                                $acumaticaData = $this->getDataFromAcumatica($loginUrl, $syncId, $storeId);
                                $insertedData = $this->categoryResourceModel->insertDataIntoTempTables($acumaticaData, $syncId, $scopeType, $storeId);
                                /**
                                 * Sync to Magento
                                 */
                                $mappingAttributes = $this->categoryResourceModel->getMagentoAttributes($storeId);
                                $categoryMappingCheck = $this->categoryResourceModel->checkCategoryMapping($storeId);
                                if ($categoryMappingCheck == 0) {
                                    $txt = "Notice: Attributes not mapped. Please try again";
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                    $categoryLog['id'] = $syncLogID;
                                    $categoryLog['job_code'] = "category";
                                    $categoryLog['status'] = "error";
                                    $categoryLog['messages'] = "Attributes are not mapped";
                                    $categoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                    $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);
                                    $this->syncResourceModel->updateConnection($insertedId, 'ERROR','', $storeId);
                                    $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                                }else{
                                    /**
                                     * 1 means Acumatica to Magento
                                     * 3 means Bi-Directional
                                     */

                                    /**
                                     *  1. Check license Type
                                     *  2. If license is Trial then need to restrict code to sync only 100 records
                                     */
                                    $default_company = $this->scopeConfigInterface->getValue('amconnectorsync/categorysync/company',$scopeType,$storeId);
                                    if(isset($default_company) && $default_company != ''){

                                        $trialSyncRecordCount = 0 ;
                                        $syncToMagentoResult = array();

                                        $categorySyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/categorysync/syncdirection',$scopeType,$storeId);

                                        if ($this->categoryResourceModel->stopSyncValue() == 1) {
                                            if ($categorySyncDirection == 1 || $categorySyncDirection == 3) {
                                                if(isset($insertedData['magento'])){
                                                    foreach ($insertedData['magento'] as $aData) {
                                                        if ($this->categoryResourceModel->stopSyncValue() == 1) {
                                                            if ($aData['magento_category_id'] != '' && $aData['acumatica_category_id'] != '') {
                                                                $directionFlg = 1;
                                                            } else {
                                                                $directionFlg = 0;
                                                            }

                                                            if ($aData['entity_ref'] == NULL) {
                                                                if (isset($acumaticaData['Entity']['CategoryID']['Value']) && $aData['acumatica_category_id'] == $acumaticaData['Entity']['CategoryID']['Value']) {
                                                                    if ($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord) {
                                                                        $syncToMagentoResult = $this->categoryModel->syncToMagento($acumaticaData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg);
                                                                        $trialSyncRecordCount++;
                                                                    }
                                                                    if ($this->licenseType != 'trial') {
                                                                        $syncToMagentoResult = $this->categoryModel->syncToMagento($acumaticaData['Entity'], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg);
                                                                    }
                                                                }
                                                            } else {
                                                                if ($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord) {
                                                                    $syncToMagentoResult = $this->categoryModel->syncToMagento($acumaticaData['Entity'][$aData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg);
                                                                    $trialSyncRecordCount++;
                                                                }
                                                                if ($this->licenseType != 'trial') {
                                                                    $syncToMagentoResult = $this->categoryModel->syncToMagento($acumaticaData['Entity'][$aData['entity_ref']], $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, $directionFlg);
                                                                }
                                                            }
                                                        } else {
                                                            $categoryLog['id'] = $syncLogID;
                                                            $categoryLog['job_code'] = "category"; //job code
                                                            $categoryLog['status'] = "notice"; //status
                                                            $categoryLog['messages'] = "Category sync stopped"; //messages
                                                            $categoryLog['finished_at'] = '';
                                                            $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);
                                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                                            break;
                                                        }
                                                    }
                                                }
                                            }

                                            /**
                                             * Sync to Acumatica
                                             */
                                            if ($this->categoryResourceModel->stopSyncValue() == 1) {
                                                /**
                                                 * 2 means Magento to Acumatica
                                                 * 3 means Bi-Directional
                                                 */
                                                $syncToAcumaticaResult = array();
                                                $webServiceUrl = $this->urlHelper->getNewWebserviceUrl($scopeType, $storeId);
                                                if ($categorySyncDirection == 2 || $categorySyncDirection == 3) {
                                                    $acumaticaAttributes = $this->categoryResourceModel->getAcumaticaAttributes($storeId);
                                                    if(isset($insertedData['acumatica'])){
                                                        foreach ($insertedData['acumatica'] as $aData) {
                                                            if ($this->categoryResourceModel->stopSyncValue() == 1) {
                                                                if ($aData['magento_category_id'] != '' && $aData['acumatica_category_id'] != '') {
                                                                    $directionFlg = 1;
                                                                } else {
                                                                    $directionFlg = 0;
                                                                }
                                                                if ($this->licenseType == 'trial' && $trialSyncRecordCount < $this->totalTrialRecord) {
                                                                    $syncToAcumaticaResult = $this->categoryModel->syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $scopeType, $storeId, $logViewFileName, $directionFlg, $loginUrl, $webServiceUrl);
                                                                    $trialSyncRecordCount++;
                                                                }
                                                                if ($this->licenseType != 'trial') {
                                                                    $syncToAcumaticaResult = $this->categoryModel->syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $scopeType, $storeId, $logViewFileName, $directionFlg, $loginUrl, $webServiceUrl);
                                                                }
                                                            } else {
                                                                $categoryLog['id'] = $syncLogID;
                                                                $categoryLog['job_code'] = "category"; //job code
                                                                $categoryLog['status'] = "notice"; //status
                                                                $categoryLog['messages'] = "Category sync stopped"; //messages
                                                                $categoryLog['finished_at'] = '';
                                                                $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);
                                                                $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            if (count($syncToMagentoResult) >= 1 || count($syncToAcumaticaResult) >= 1) {
                                                $this->syncResourceModel->updateSyncAttribute($syncId, 'ERROR', $storeId);
                                            } else {
                                                $this->syncResourceModel->updateSyncAttribute($syncId, 'SUCCESS', $storeId);
                                            }

                                            if ($this->categoryResourceModel->stopSyncValue() == 0) {
                                                $txt = "Notice: Category sync stopped";
                                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                                $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                            }
                                            /**
                                             * need to put 3 second difference for next time sync
                                             */
                                            sleep(3);
                                            if ($this->categoryResourceModel->stopSyncValue() == 1) {
                                                /**
                                                 *logs here for Sync Success
                                                 */
                                                $categoryLog['id'] = $syncLogID;
                                                $categoryLog['job_code'] = "category"; //job code
                                                if (count($syncToMagentoResult) >= 1 || count($syncToAcumaticaResult) >= 1) {
                                                    $categoryLog['status'] = "error"; //status
                                                } else {
                                                    $categoryLog['status'] = "success"; //status
                                                }
                                                if($this->licenseType == 'trial' && $trialSyncRecordCount == $this->totalTrialRecord){
                                                    $categoryLog['messages'] = "Trial license allow only ".$this->totalTrialRecord." records per sync!"; //messages
                                                    $txt = "Info : " . $categoryLog['messages'];
                                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                                }else{
                                                    $categoryLog['messages'] = "Category sync executed successfully!"; //messages
                                                    $txt = "Info : " . $categoryLog['messages'];
                                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                                }
                                                $categoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                                $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);
                                            }
                                        } else {
                                            $categoryLog['id'] = $syncLogID;
                                            $categoryLog['job_code'] = "category";
                                            $categoryLog['status'] = "notice";
                                            $categoryLog['messages'] = "Category sync stopped";
                                            $categoryLog['finished_at'] = '';
                                            $txt = "Notice: " . $categoryLog['messages'];
                                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                            $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);
                                            $this->syncResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                        }
                                        if($this->categoryResourceModel->stopSyncValue() == 0){
                                            $this->syncResourceModel->updateConnection($insertedId, 'NOTICE','', $storeId);
                                        }else{
                                            $this->syncResourceModel->updateConnection($insertedId, 'SUCCESS','', $storeId);
                                        }
                                    }else{
                                        $categoryLog['id'] = $syncLogID;
                                        $categoryLog['job_code'] = "catgeory";
                                        $categoryLog['status'] = "error";
                                        $categoryLog['messages'] = "Default company not defined"; //messages
                                        $categoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                        $categoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                        $txt = "Error : Default company not defined";
                                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                        $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);
                                    }
                                }
                            }
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            /**
                             * Individual sync
                             */
                            if ($individualCategoryId != '' || $individualCategoryId != NULL) {
                                $categoryId = $individualCategoryId;
                                $magentoCategoryData = $this->categoryFactory->create()->setStoreId($storeId)->load($categoryId);
                                $acumaticaCategoryId = $magentoCategoryData->getAcumaticaCategoryId();
                                $mappingAttributes = $this->categoryResourceModel->getMagentoAttributes($storeId);
                                $kemsUrl = $this->urlHelper->getNewWebserviceUrl($scopeType, $storeId); // Soap/KEMS.asmx?wsdl
                                if ($acumaticaCategoryId != '') {
                                    $getCategory = $this->getIndividualDataFromAcumatica($loginUrl, $acumaticaCategoryId, $storeId);

                                    if ($getCategory['CategoryID'] != '') {
                                        $acumaticaUpdatedDate = $getCategory['LastModifiedDateTime']['Value'];
                                        $magentoUpdatedDate = $magentoCategoryData->getUpdatedAt();
                                        $updatedDate = $this->timezone->date(strtotime($magentoUpdatedDate))->format('Y-m-d H:i:s');
                                        /**
                                         * Checking Updated Date
                                         */
                                        if (strtotime($acumaticaUpdatedDate) > strtotime($updatedDate)) {
                                            /**
                                             * Sync To Magento
                                             */
                                            if($getCategory['CategoryInfo']['SyncStatus']['Value'] == "Active"){

                                                $categoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                                $result = $this->categoryModel->syncToMagento($getCategory, $mappingAttributes, $syncType, $autoSync, $syncLogID, $storeId, $logViewFileName, NULL);
                                                $categoryLog['id'] = $syncLogID;
                                                $categoryLog['job_code'] = "category";
                                                $categoryLog['status'] = "success";
                                                $categoryLog['messages'] = "Category sync executed successfully!";
                                                $categoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                                $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);
                                                $this->_messageManager->addSuccess(__("Category sync executed successfully!"));
                                            }
                                        }else{
                                            /**
                                             * Sync To acumatica
                                             */
                                            $categoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                            $acumaticaAttributes = $this->categoryResourceModel->getAcumaticaAttributes($storeId);
                                            $aData['magento_category_id'] = $categoryId;
                                            $aData['acumatica_category_id'] = $acumaticaCategoryId;
                                            /**
                                             * Need to get acumatica path
                                             */
                                            $magentoPath = $magentoCategoryData->getPath();
                                            $acumaticaPath = $this->categoryResourceModel->getIndividualAcumaticaTreePath($scopeType,$storeId, $magentoPath);
                                            $aData['acumatica_category_path'] = $acumaticaPath;
                                            $result = $this->categoryModel->syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $scopeType, $storeId, $logViewFileName, NULL, $loginUrl, $kemsUrl);
                                            $categoryLog['id'] = $syncLogID;
                                            $categoryLog['job_code'] = "category";
                                            $categoryLog['status'] = "success";
                                            $categoryLog['messages'] = "Category sync executed successfully!";
                                            $categoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                            $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);
                                            $this->_messageManager->addSuccess(__("Category sync executed successfully!"));
                                        }
                                    }
                                }else{
                                    /**
                                     * Sync to acumatica
                                     */
                                    $categoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                    $acumaticaAttributes = $this->categoryResourceModel->getAcumaticaAttributes($storeId);
                                    $aData['magento_category_id'] = $categoryId;
                                    $aData['acumatica_category_id'] = $acumaticaCategoryId;
                                    /**
                                     * Need to get acumatica path
                                     */
                                    $magentoPath = $magentoCategoryData->getPath();
                                    $acumaticaPath = $this->categoryResourceModel->getIndividualAcumaticaTreePath($scopeType,$storeId, $magentoPath);
                                    $aData['acumatica_category_path'] = $acumaticaPath;
                                    $result = $this->categoryModel->syncToAcumatica($aData, $acumaticaAttributes, $syncType, $autoSync, $syncLogID, $scopeType, $storeId, $logViewFileName, NULL, $loginUrl, $kemsUrl);
                                    $categoryLog['id'] = $syncLogID;
                                    $categoryLog['job_code'] = "category";
                                    $categoryLog['status'] = "success";
                                    $categoryLog['messages'] = "Category sync executed successfully!";
                                    $categoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                    $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);
                                    $this->_messageManager->addSuccess(__("Category sync executed successfully!"));
                                }
                            }
                        }
                    }
                }
            }else{
                if ($scheduleId != '') {
                    $categoryLog['schedule_id'] = $scheduleId;
                } else {
                    $categoryLog['schedule_id'] = "";
                }
                $categoryLog['store_id'] = $storeId;
                $categoryLog['job_code'] = "category";
                $categoryLog['status'] = "notice";
                $categoryLog['messages'] = "Category sync stopped";
                $categoryLog['created_at'] = $this->date->date('Y-m-d H:i:s');
                $categoryLog['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                if ($syncType == 'MANUAL') {
                    $categoryLog['runMode'] = 'Manual';
                } elseif ($syncType == 'AUTO') {
                    $categoryLog['runMode'] = 'Automatic';
                }
                if ($autoSync == 'COMPLETE') {
                    $categoryLog['autoSync'] = 'Complete';
                } elseif ($autoSync == 'INDIVIDUAL') {
                    $categoryLog['autoSync'] = 'Individual';
                }

                $txt = "Notice: Category sync stopped";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $this->categoryLogHelper->categoryManualSync($categoryLog);
                $this->categoryResourceModel->updateSyncAttribute($syncId, 'NOTICE', $storeId);
            }
        } catch (Exception $e) {
            if($scheduleId != ''){
                $categoryLog['id'] = $scheduleId;
            }else{
                $categoryLog['id'] = $syncLogID;
            }
            $categoryLog['job_code'] = "category"; //job code
            $categoryLog['status'] = "error"; //status
            $categoryLog['messages'] = $e->getMessage(); //message
            $categoryLog['executed_at'] = $this->date->date('Y-m-d H:i:s');
            $categoryLog['finished_at'] = $this->date->date('Y-m-d H:i:s');
            $this->categoryLogHelper->categoryManualSyncUpdate($categoryLog);
        }
        $this->categoryResourceModel->enableSync();
        $txt = "Info : Sync process completed!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
    }

    /**
     * @param $url
     * @param $syncId
     * @param $storeId
     *
     * Get category from acumatica by date
     */
    public function getDataFromAcumatica($url, $syncId, $storeId) {
        try {
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvCategoryData = $this->common->getEnvelopeData('CATGETDATA');
            $XMLGetRequest = $csvCategoryData['envelope'];
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId, $storeId);
            $getlastSyncDateByTimezone = $this->timezone->date($lastSyncDate, null, true);
            $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
            $toDate = $this->date->date('Y-m-d H:i:s', strtotime("+1 day"));
            $XMLGetRequest = str_replace('{{FROMDATE}}', $fromDate, $XMLGetRequest);
            $XMLGetRequest = str_replace('{{TODATE}}', $toDate, $XMLGetRequest);
            $catgeoryAction = $csvCategoryData['envName'] . "/" . $csvCategoryData['envVersion'] . "/" . $csvCategoryData['methodName'];
            $xmlResponse = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $catgeoryAction);
            $totalData = array();
            if($xmlResponse->Body->GetListResponse->GetListResult){
                $data = $xmlResponse->Body->GetListResponse->GetListResult;
                $totalData = $this->xmlHelper->xml2array($data);
            }
            return $totalData;
        } catch (SoapFault $e) {
            echo "Last request:<pre>" . $e->getMessage() . "</pre>";
        }
    }

    /**
     * Get Individual Category data from acumatica by using id
     * @param $url
     * @param $categoryId
     */
    public function getIndividualDataFromAcumatica($url, $categoryId, $storeId) {
        try {
	    $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $csvGetIndividualCategory = $this->common->getEnvelopeData('GETCATEGORYBYID');
            $XMLGetRequest = $csvGetIndividualCategory['envelope'];
            $XMLGetRequest = str_replace('{{CATEGORYID}}', $categoryId, $XMLGetRequest);
            $action = $csvGetIndividualCategory['envName'] . '/' . $csvGetIndividualCategory['envVersion'] . '/' . $csvGetIndividualCategory['methodName'];
            $xml = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $action);
            $data = $xml->Body->GetResponse->GetResult;
            $totalData = $this->xmlHelper->xml2array($data);
            return $totalData;
        } catch (SoapFault $e) {
            echo "Last request:<pre>" . htmlentities($client->__getLastRequest()) . "</pre>";
        }
    }

}