<?php

namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Webapi\Soap;
use SoapFault;
use SoapVar;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magento\Framework\Stdlib\DateTime\Timezone as TimeZone;
use Kensium\Lib;

class ProductImage extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $errorCheckInMagento = array();
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
     * @var \Kensium\Synclog\Helper\ProductImage
     */
    protected $syncLogProductImage;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $resourceModelSync;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Licensecheck
     */
    protected $licenseCheckResourceModel;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    protected $timezone;

    /**
     * @var \Kensium\Amconnector\Helper\Time
     */
    protected $timeHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $syncResourceModel;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     *
     * @var \Kensium\Amconnector\Helper\Sync
     */
    protected $syncHelper;

    /**
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManagerInterface;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface
     */
    protected $attributeMediaGalleryManagementInterface;

    /**
     * @var \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface
     */
    protected $attributeMediaGalleryEntryInterface;

    /**
     * @var \Magento\Framework\Api\Data\ImageContentInterface
     */
    protected $imageContentInterface;

    /**
     * @var
     */
    protected $resource;

    /**
     * @var
     */

    protected $dataHelper;

    protected $successMsg;
    protected $errorCheck;

    public $screenSchema;
    public $productSchema;
    public $imageSchema;
    public $syncId;
    public $totalTrialRecord;
    public $licenseType;

    const IS_LICENSE_INVALID = "Invalid";
    const IS_LICENSE_VALID = "Valid";
    const IS_TIME_VALID = "Valid";

    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        TimeZone $timezone,
        \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface $attributeMediaGalleryManagementInterface,
        \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $attributeMediaGalleryEntryInterface,
        \Magento\Framework\Api\Data\ImageContentInterface $imageContentInterface,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Synclog\Helper\ProductImage $syncLogProductImage,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseCheckResourceModel,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Lib\Common $common
    )
    {
        parent::__construct($context);
        $this->date = $date;
        $this->attributeMediaGalleryManagementInterface = $attributeMediaGalleryManagementInterface;
        $this->timezone = $timezone;
        $this->urlHelper =  $urlHelper;
        $this->imageContentInterface = $imageContentInterface;
        $this->attributeMediaGalleryEntryInterface = $attributeMediaGalleryEntryInterface;
        $this->clientHelper = $clientHelper;
        $this->dataHelper = $dataHelper;
        $this->timeHelper = $timeHelper;
        $this->xmlHelper = $xmlHelper;
        $this->syncLogProductImage = $syncLogProductImage;
        $this->resourceModelSync = $resourceModelSync;
        $this->licenseCheckResourceModel = $licenseCheckResourceModel;
        $this->syncResourceModel = $syncResourceModel;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->syncHelper= $syncHelper;
        $this->productFactory = $productFactory;
        $this->objectManagerInterface = $objectManagerInterface;
        $this->messageManager = $messageManager;
        $this->_resource = $resource;
        $this->common = $common;
    }


    /**
     * @param $autoSync
     * @param $syncType
     * @param $syncId
     * @param null $scheduleId
     * @param $cronStoreId
     * Product Image Sync
     */
    public function syncProductImage($autoSync, $syncType, $syncId, $scheduleId = NULL, $cronStoreId)
    {
        $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'productimage', NULL);
        $acuSyncedRecord = 0;
        $this->errorMsg = 0;
        $txt = "Info : Sync process started!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
        try {
            if ($syncType != "AUTO") {
                if ($cronStoreId == 0) {
                    $storeId = 1;
                } else {
                    $storeId = $cronStoreId;
                }
            } else {
                $storeId = $cronStoreId;
            }
            if ($cronStoreId == 0) {
                $scopeType = 'default';
            } else {
                $scopeType = 'stores';
            }
            $txt = "Info : License verification is in progress";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

            /**
             * License Verification
             */
            $this->licenseType = $this->licenseCheckResourceModel->checkLicenseTypes($storeId);
            $this->totalTrialRecord = $this->syncHelper->numberOfRecordSyncInTrialLicense();
            $licenseStatus = $this->licenseCheckResourceModel->getLicenseStatus($storeId);
            if ($licenseStatus != self::IS_LICENSE_VALID) {
                if ($scheduleId != '') {
                    $productImageArray['schedule_id'] = $scheduleId;
                } else {
                    $productImageArray['schedule_id'] = "";
                }
                $productImageArray['sync_direction'] = "Acumatica To Magento";
                $productImageArray['job_code'] = "productimage";
                $productImageArray['status'] = "error";
                $productImageArray['messages'] = "Invalid License Key";
                $productImageArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                $productImageArray['store_id'] = $storeId;
                $productImageArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                if ($syncType == 'MANUAL') {
                    $productImageArray['runMode'] = 'Manual';
                } elseif ($syncType == 'AUTO') {
                    $productImageArray['runMode'] = 'Automatic';
                }
                if ($autoSync == 'COMPLETE') {
                    $productImageArray['autoSync'] = 'Complete';
                } elseif ($autoSync == 'INDIVIDUAL') {
                    $productImageArray['autoSync'] = 'Individual';
                }
                $txt = "Error: Invalid License Key. Please verify and try again";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $this->syncLogProductImage->productImageManualSync($productImageArray);
                $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
                $this->messageManager->addError('Invalid license key.');
                $this->errorMsg = 1;
            } else {
                $txt = "Info : License verified successfully!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                /**
                 * Server Time verification
                 */
                $timeSyncCheck = $this->timeHelper->timeSyncCheck($storeId);
                if ($timeSyncCheck != self::IS_TIME_VALID) {
                    if ($scheduleId != '') {
                        $productImageArray['schedule_id'] = $scheduleId;
                    } else {
                        $productImageArray['schedule_id'] = "";
                    }
                    $productImageArray['sync_direction'] = "Acumatica To Magento";
                    $productImageArray['job_code'] = "productimage";
                    $productImageArray['status'] = "error";
                    $productImageArray['messages'] = "Server time is not in sync";
                    $productImageArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                    if ($syncType == 'MANUAL') {
                        $productImageArray['runMode'] = 'Manual';
                    } elseif ($syncType == 'AUTO') {
                        $productImageArray['runMode'] = 'Automatic';
                    }
                    if ($autoSync == 'COMPLETE') {
                        $productImageArray['autoSync'] = 'Complete';
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $productImageArray['autoSync'] = 'Individual';
                    }
                    $productImageArray['storeId'] = $storeId;
                    $productImageArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                    $txt = " : Error: Server time is not in sync.";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->syncLogProductImage->productImageManualSync($productImageArray);
                    $$this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
                    $this->messageManager->addError('Server time is not in sync.');
                    $this->errorMsg = 1;
                } else {
                    /**
                     * Start Image Sync
                     */
                    $txt = "Info : Server time is in sync";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl', $scopeType, $storeId);
                    if (!isset($serverUrl))
                        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
                    $loginUrl = $this->urlHelper->getBasicConfigUrl($serverUrl);
                        if ($scheduleId != '') {
                            $productImageArray['schedule_id'] = $scheduleId;
                        } else {
                            $productImageArray['schedule_id'] = "";
                        }
                        $productImageArray['job_code'] = "productimage";
                        $productImageArray['status'] = "success";
                        $productImageArray['messages'] = "Product Image manual sync initiated";
                        $productImageArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                        $productImageArray['storeId'] = $storeId;
                        $productImageArray['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                        if ($syncType == 'MANUAL') {
                            $productImageArray['runMode'] = 'Manual';
                        } elseif ($syncType == 'AUTO') {
                            $productImageArray['runMode'] = 'Automatic';
                        }
                        if ($autoSync == 'COMPLETE') {
                            $productImageArray['autoSync'] = 'Complete';
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $productImageArray['autoSync'] = 'Individual';
                        }
                        $syncLogID = $this->syncLogProductImage->productImageManualSync($productImageArray);

                        $txt = "Info : Product Image manual sync initiated";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $this->resourceModelSync->updateSyncAttribute($syncId, 'STARTED', $storeId);
                        /**
                         * There are two type of Sync
                         * 1. Complete Sync
                         * 2. Individual Sync
                         */
                        if ($autoSync == 'COMPLETE') {
                            $insertedId = $this->resourceModelSync->checkConnectionFlag($syncId, 'productimage', $storeId);
                            if ($insertedId == NULL) {
                                $productImageArray['storeId'] = $storeId;
                                $productImageArray['id'] = $syncLogID;
                                $productImageArray['job_code'] = "productimage";
                                $productImageArray['status'] = "error";
                                $productImageArray['messages'] = "Another Sync is already executing";
                                $productImageArray['acumatica_attribute_code'] = "";
                                $productImageArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                $productImageArray['finished_at'] = $this->date->date('Y-m-d H:i:s');

                                $txt = "Info : Sync in Progress - please wait for the current sync to finish";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->syncLogProductImage->productImageManualSync($productImageArray);
                                $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
                                $this->errorMsg = 1;
                            } else {
                                $this->resourceModelSync->updateConnection($insertedId, 'PROCESS', $storeId);
                                $this->resourceModelSync->updateSyncAttribute($syncId, 'PROCESSING', $storeId);

                                if ($storeId == 0) {
                                    $scopeType = 'default';
                                } else {
                                    $scopeType = 'stores';
                                }
                                $result = array();
                                $enableImageSync = $this->scopeConfigInterface->getValue('amconnectorsync/productimagesync/productimagesync', $scopeType, $storeId);
                                if ($enableImageSync)
                                {
                                    if($this->stopSyncValue() == 1)
                                    {
                                        $this->resourceModelSync->updateSyncAttribute($syncId, 'PROCESSING', $storeId);
                                        $acumaticaData = $this->getDataFromAcumatica($loginUrl, $syncId, $storeId);
                                        if (!empty($acumaticaData['Entity']['InventoryList']['InventoryResults']) && isset($acumaticaData['Entity']['InventoryList']['InventoryResults']))
                                        {
                                            $allProducts = $this->xmlHelper->xml2array($acumaticaData['Entity']['InventoryList']['InventoryResults']);
                                            $oneRecordFlag = false;
                                            foreach ($allProducts as $_key => $aData)
                                            {
                                                if($this->stopSyncValue() == 1)
                                                {
                                                    if (!is_numeric($_key)) {
                                                        $oneRecordFlag = true;
                                                        break;
                                                    }
                                                    if (isset($aData['InventoryID']['Value'])) {
                                                        $magentoSku = str_replace(" ", "_", $aData['InventoryID']['Value']);
                                                        $productData = $this->productFactory->create()->loadByAttribute('sku', $magentoSku);
                                                        /**
                                                         * Check Product Existence in magento
                                                         */
                                                        if (isset($productData) && !empty($productData)) {
                                                            $this->removeImages($magentoSku, $logViewFileName, $storeId, $syncLogID, $syncType);
                                                            if (isset($aData['CompositeItemType']['Value']) && $aData['CompositeItemType']['Value'] != '') {
                                                                $type = "composite";
                                                            } else {
                                                                $type = "simple";
                                                            }
                                                            $productImages = $this->getIndividualProductImages($aData['InventoryID']['Value'], $loginUrl, $storeId, $type);
                                                            if (isset($productImages['File']) && !empty($productImages['File'])) {
                                                                if ($this->licenseType == 'trial' && $acuSyncedRecord < $this->totalTrialRecord) {
                                                                    $result = $this->syncToMagento($productImages, $magentoSku, $syncType, $syncLogID, $storeId, $logViewFileName);
                                                                    $acuSyncedRecord++;
                                                                }
                                                                if ($this->licenseType != 'trial') {
                                                                    $result = $this->syncToMagento($productImages, $magentoSku, $syncType, $syncLogID, $storeId, $logViewFileName);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }else{
                                                    /**
                                                     * Stop Sync logs
                                                     */
                                                    $this->messageManager->addError("Product image sync stopped");
                                                    $productImageArray['id'] = $syncLogID;
                                                    $productImageArray['job_code'] = "productimage";
                                                    $productImageArray['status'] = "notice";
                                                    $productImageArray['messages'] = "Product image sync stopped";
                                                    $productImageArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                                    $productImageArray['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                                    $this->syncLogProductImage->productImageManualSyncUpdate($productImageArray);
                                                    $this->resourceModelSync->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                                    break;
                                                }
                                            }
                                            if ($oneRecordFlag) {
                                                if (isset($allProducts['InventoryID']['Value'])) {
                                                    $magentoSku = str_replace(" ", "_", $allProducts['InventoryID']['Value']);
                                                    $productData = $this->productFactory->create()->loadByAttribute('sku', $magentoSku);
                                                    /**
                                                     * Check Product Existence in magento
                                                     */
                                                    if (isset($productData) && !empty($productData)) {
                                                        $this->removeImages($magentoSku, $logViewFileName, $storeId, $syncLogID, $syncType);
                                                        if (isset($allProducts['CompositeItemType']['Value']) && $allProducts['CompositeItemType']['Value'] != '') {
                                                            $type = "composite";
                                                        } else {
                                                            $type = "simple";
                                                        }
                                                        $productImages = $this->getIndividualProductImages($allProducts['InventoryID']['Value'], $loginUrl, $storeId, $type);
                                                        if (isset($productImages['File']) && !empty($productImages['File'])) {
                                                            $result = $this->syncToMagento($productImages, $magentoSku, $syncType, $syncLogID, $storeId, $logViewFileName);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }else{
                                        /**
                                         * Stop Sync logs
                                         */
                                        $this->messageManager->addError("Product image sync stopped");
                                        $productImageArray['id'] = $syncLogID;
                                        $productImageArray['job_code'] = "productimage";
                                        $productImageArray['status'] = "notice";
                                        $productImageArray['messages'] = "Product image sync stopped";
                                        $productImageArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                        $productImageArray['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                        $this->syncLogProductImage->productImageManualSyncUpdate($productImageArray);
                                        $this->resourceModelSync->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                    }
                                    if (count($result) > 0)
                                    {
                                        $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
                                    }else
                                    {
                                        $this->resourceModelSync->updateSyncAttribute($syncId, 'SUCCESS', $storeId);
                                    }

                                    if ($this->stopSyncValue() == 0)
                                    {
                                        $txt = "Notice: Product Image sync stopped";
                                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                        $this->resourceModelSync->updateSyncAttribute($syncId, 'NOTICE', $storeId);
                                    }
                                    if ($this->stopSyncValue() == 1)
                                    {
                                        $productImageArray['id'] = $syncLogID;
                                        if (count($result) > 0)
                                        {
                                            $productImageArray['status'] = "error";
                                        }else{
                                            $productImageArray['status'] = "success";
                                        }
                                        if ($this->licenseType == 'trial' && $acuSyncedRecord == $this->totalTrialRecord) {
                                            $productImageArray['messages'] = "Trial license allow only " . $this->totalTrialRecord . " records per sync!";
                                            $txt = "Info : Trial license allow only " . $this->totalTrialRecord . " records per sync!";
                                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                                        } else {
                                            $productImageArray['messages'] = "Product image sync executed successfully!";
                                            $txt = "Info : ".$productImageArray['messages'];
                                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                        }
                                        $productImageArray['job_code'] = "productimage";
                                        $productImageArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                        $productImageArray['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                        $this->syncLogProductImage->productImageManualSyncUpdate($productImageArray);
                                        if ($syncType == 'MANUAL') {
                                            $this->messageManager->addSuccess("ProductImage sync completed successfully!");
                                        }
                                    }
                                    if($this->stopSyncValue() == 0)
                                    {
                                        $this->resourceModelSync->updateConnection($insertedId, 'NOTICE','',$storeId);
                                    }else{
                                        if (count($result) > 0)
                                        {
                                            $this->resourceModelSync->updateConnection($insertedId, 'SUCCESS','',$storeId);
                                        }else
                                        {
                                            $this->resourceModelSync->updateConnection($insertedId, 'SUCCESS','',$storeId);
                                        }
                                    }
                                } else {
                                    $this->resourceModelSync->updateConnection($insertedId, 'ERROR', $storeId);
                                    $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
                                    $productImageArray['id'] = $syncLogID;
                                    $productImageArray['status'] = "failure";
                                    $productImageArray['messages'] = "Image sync is disabled";
                                    $productImageArray['job_code'] = "productimage";
                                    $productImageArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
                                    $productImageArray['finished_at'] = $this->date->date('Y-m-d H:i:s');
                                    $this->syncLogProductImage->productImageManualSyncUpdate($productImageArray);
                                    $txt = "Info : Sync process Disabled!";
                                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                }
                            }
                        }
                }
            }
            if ($this->errorMsg == 1) {
                $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
            } else {
                if($this->stopSyncValue() == 1)
                {
                    $this->resourceModelSync->updateSyncAttribute($syncId, 'SUCCESS', $storeId);
                }
            }
        } catch (Exception $e) {
            if ($scheduleId != '') {
                $productImageArray['id'] = $scheduleId;
            } else {
                $productImageArray['id'] = $syncLogID;
            }
            $productImageArray['job_code'] = "productimage";
            $productImageArray['status'] = "error";
            $productImageArray['messages'] = $e->getMessage();
            $productImageArray['executed_at'] = $this->date->date('Y-m-d H:i:s');
            $productImageArray['finished_at'] = $this->date->date('Y-m-d H:i:s');

            $txt = "Error: " . $productImageArray['messages'];
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

            $this->syncLogProductImage->productImageManualSyncUpdate($productImageArray);
            $this->messageManager->addError("Something Went Wrong...");
            $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
        }
        /**
         * enable Syncstop
         */
        $this->enableSync();
        $txt = "Info : Sync process completed!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
    }

    public function stopSyncValue()
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $value = '';
        $query = "SELECT value FROM " . $connection->getTableName("core_config_data") . " WHERE path ='amconnectorsync/productimagesync/syncstopflg' ";
        try{
            $value = $connection->fetchOne($query);
        }catch(Exception $e){
            echo $e->getMessage();
        }
        return $value;
    }

    public function enableSync()
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $path = 'amconnectorsync/productimagesync/syncstopflg';
        $query = "update ".$connection->getTableName('core_config_data')." set value = 1 where path ='".$path."'";
        $connection->query($query);
    }

    /**
     * @param $url
     * @param $syncId
     * @param $storeId
     */
    public function getDataFromAcumatica($url, $syncId,$storeId)
    {
        try {
            $csvProductData = $this->syncHelper->getEnvelopeData('GETALLPRODUCTLIST');
            $XMLGetRequest = $csvProductData['envelope'];
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId,$storeId);
            $getlastSyncDateByTimezone =  $this->timezone->date($lastSyncDate,null,true);
            $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
            $toDate = $this->date->date('Y-m-d H:i:s',strtotime("+1 day"));
            $XMLGetRequest = str_replace('{{FROMDATE}}',$fromDate,$XMLGetRequest);
            $XMLGetRequest = str_replace('{{TODATE}}',$toDate,$XMLGetRequest);
            $productAction = $csvProductData['envName'] . "/" . $csvProductData['envVersion'] . "/" . $csvProductData['methodName'];
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $response = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $productAction);
            $xml = $response;
            if(isset($xml->Body->GetListResponse->GetListResult))
            {
                $data = $xml->Body->GetListResponse->GetListResult;
                $totalData = $this->xmlHelper->xml2array($data);
                return $totalData;
            }else{
                $totalData = '';
                return $totalData;
            }
        }catch (SoapFault $e) {
            echo "Last request:<pre>" . $e->getMessage() . "</pre>";
        }
    }

    /**
     * @param $inventoryId
     * @param $loginUrl
     * @param $storeId
     * @param $type
     */
    public function getIndividualProductImages($inventoryId, $loginUrl, $storeId, $type)
    {
        try {
            if ($type == "composite") {
                $csvProductData = $this->syncHelper->getEnvelopeData('GETCOMPOSITEIMAGES');
            } else {
                $csvProductData = $this->syncHelper->getEnvelopeData('GETSIMPLEPRODUCTIMAGES');
            }
            $XMLGetRequest = $csvProductData['envelope'];
            $XMLGetRequest = str_replace('{{INVENTORYID}}', $inventoryId, $XMLGetRequest);
            $productAction = $csvProductData['envName'] . "/" . $csvProductData['envVersion'] . "/" . $csvProductData['methodName'];
            $configParameters = $this->dataHelper->getConfigParameters($storeId);
            $response = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $loginUrl, $productAction);
            $xml = $response;
            if(isset($xml->Body->GetFilesResponse->GetFilesResult))
            {
                $data = $xml->Body->GetFilesResponse->GetFilesResult;
                $totalData = $this->xmlHelper->xml2array($data);
                return $totalData;
            }else{
                $totalData = '';
                return $totalData;
            }
        } catch (Exception $ep)
        {
            echo "Last request:<pre>" . $ep->getMessage() . "</pre>";
        }
    }

    /**
     * @param $productImages
     * @param $magentoSku
     * @param $syncType
     * @param $syncLogID
     * @param $storeId
     * @param $logViewFileName
     * @return array|string
     */
    public function syncToMagento($productImages,$magentoSku,$syncType,$syncLogID, $storeId, $logViewFileName)
    {
        $result = '';
        if(isset($productImages['File']['Content']))
        {
            $result = $this->createImageInMagento($productImages['File'],$magentoSku,$syncType,$syncLogID, $storeId, $logViewFileName,0);
        }else if(isset($productImages['File'][0]))
        {
            $i = 0;
            foreach($productImages['File'] as $image)
            {
                $image = $this->xmlHelper->xml2array($image);
                $result = $this->createImageInMagento($image,$magentoSku,$syncType,$syncLogID, $storeId, $logViewFileName,$i);
                $i++;
            }
        }
        return $result;
    }

    /**
     * @param $productImages
     * @param $magentoSku
     * @param $syncType
     * @param $syncLogID
     * @param $storeId
     * @param $logViewFileName
     * @param $i
     * @return array
     */
    public function createImageInMagento($productImages,$magentoSku,$syncType,$syncLogID, $storeId, $logViewFileName,$i)
    {
        try {
            $mediaObj = $this->attributeMediaGalleryEntryInterface;
            $name = preg_replace('/[^A-Za-z0-9\-]/', '', $productImages['Name']);
            $contentObj = $this->imageContentInterface;
            if (strstr($name, 'png')) {
                $mimeType = 'image/png';
            } elseif (strstr($name, 'gif')) {
                $mimeType = 'image/gif';
            } elseif (strstr($name, 'jpg')) {
                $mimeType = 'image/jpeg';
            } else {
                $mimeType = 'image/jpeg';
            }
            $contentObj->setName($name);
            $mediaObj->setTypes(array('image', 'small_image', 'thumbnail'));
            $mediaObj->setMediaType(\Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter::MEDIA_TYPE_CODE);
            $mediaObj->setContent($contentObj->setBase64EncodedData($productImages['Content'])->setType($mimeType));
            $entry = $mediaObj->setLabel($name)->setPosition($i)->setDisabled(0);
            $prodId = $this->productFactory->create()->getIdBySku($magentoSku);
            if ($prodId) {
                $prodIdObj = $this->productFactory->create()->load($prodId);
                $prodImgRes = $this->attributeMediaGalleryManagementInterface->create($magentoSku, $entry);
                $prodIdObj->save();
                $rowId = $prodIdObj->getRowId();
                if ($i == 0) {
                    $this->setBaseImage($rowId, $prodImgRes);
                }
                if ($prodImgRes) {
                    $name . " image uploaded successfully for " . $magentoSku . "\n";
                }
                $msg = "Image '" . trim($name) . "' import  completed for " . $magentoSku;
                $productImageArray['storeId'] = $storeId;
                $productImageArray['schedule_id'] = $syncLogID;
                $productImageArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                $productImageArray['acumatica_attribute_code'] = trim($magentoSku);
                $productImageArray['description'] = $msg;
                $productImageArray['runMode'] = $syncType;
                $productImageArray['messageType'] = "Success";
                $productImageArray['syncDirection'] = 'syncToMagento';

                $txt = "Info : " . $msg;
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->syncLogProductImage->productImageSyncSuccessLogs($productImageArray);
            } else {
                $msg = trim($magentoSku) . " Product not exists in Magento";
                $productImageArray['storeId'] = $storeId;
                $productImageArray['schedule_id'] = $syncLogID;
                $productImageArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                $productImageArray['acumatica_attribute_code'] = trim($magentoSku);
                $productImageArray['description'] = $msg;
                $productImageArray['runMode'] = $syncType;
                $productImageArray['messageType'] = "Error";
                $productImageArray['syncDirection'] = 'syncToMagento';
                $productImageArray['longMessage'] = $msg;

                $this->syncLogProductImage->productImageSyncSuccessLogs($productImageArray);
                $productImageArray['description'] = $msg;
                $txt = "Error: " . $msg;
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $this->errorCheckInMagento[] = 1;
            }
        } catch (\Magento\Framework\Exception\InputException $en) {
            $msg = trim($magentoSku) .$en->getMessage();
            $productImageArray['storeId'] = $storeId;
            $productImageArray['schedule_id'] = $syncLogID;
            $productImageArray['created_at'] = $this->date->date('Y-m-d H:i:s');
            $productImageArray['acumatica_attribute_code'] = trim($magentoSku);
            $productImageArray['description'] = $en->getMessage();
            $productImageArray['runMode'] = $syncType;
            $productImageArray['messageType'] = "Error";
            $productImageArray['syncDirection'] = 'syncToMagento';
            $productImageArray['longMessage'] = $msg;
            $this->syncLogProductImage->productImageSyncSuccessLogs($productImageArray);
            $productImageArray['description'] = $msg;
            $txt = "Error: " . $msg;
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->errorCheckInMagento[] = 1;
        } catch (\Magento\Framework\Exception\StateException $ex) {
            $msg = trim($magentoSku) .$ex->getMessage();
            $productImageArray['storeId'] = $storeId;
            $productImageArray['schedule_id'] = $syncLogID;
            $productImageArray['created_at'] = $this->date->date('Y-m-d H:i:s');
            $productImageArray['acumatica_attribute_code'] = trim($magentoSku);
            $productImageArray['description'] = $ex->getMessage();
            $productImageArray['runMode'] = $syncType;
            $productImageArray['messageType'] = "Error";
            $productImageArray['syncDirection'] = 'syncToMagento';
            $productImageArray['longMessage'] = $msg;
            $this->syncLogProductImage->productImageSyncSuccessLogs($productImageArray);
            $productImageArray['description'] = $msg;
            $txt = "Error: " . $msg;
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->errorCheckInMagento[] = 1;
        } catch (Exception $ep)
        {
            $msg = trim($magentoSku) .$ep->getMessage();
            $productImageArray['storeId'] = $storeId;
            $productImageArray['schedule_id'] = $syncLogID;
            $productImageArray['created_at'] = $this->date->date('Y-m-d H:i:s');
            $productImageArray['acumatica_attribute_code'] = trim($magentoSku);
            $productImageArray['description'] = $ep->getMessage();
            $productImageArray['runMode'] = $syncType;
            $productImageArray['messageType'] = "Error";
            $productImageArray['syncDirection'] = 'syncToMagento';
            $productImageArray['longMessage'] = $msg;
            $this->syncLogProductImage->productImageSyncSuccessLogs($productImageArray);
            $productImageArray['description'] = $msg;
            $txt = "Error: " . $msg;
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $this->errorCheckInMagento[] = 1;
        }
        return $this->errorCheckInMagento;
    }
    /**
     * @param $rowId
     * @param $prodImgRes
     */
    public function setBaseImage($rowId,$prodImgRes)
    {
        try {
            $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

            $query = "SELECT value FROM " . $connection->getTableName("catalog_product_entity_media_gallery") . " where `value_id`= " . $prodImgRes;
            $imageName = $connection->fetchOne($query);

            $query = "SELECT value_id FROM " . $connection->getTableName("catalog_product_entity_varchar") . " WHERE`attribute_id` =87 and row_id='" . $rowId . "' ";
            $imageValue = $connection->fetchOne($query);
            if ($imageValue) {
                $query = "UPDATE " . $connection->getTableName("catalog_product_entity_varchar") . " set value='" . $imageName . "' where `attribute_id` IN (87,88,89) and row_id='" . $rowId . "'";
                $connection->query($query);
            } else {
                $query = "INSERT INTO " . $connection->getTableName("catalog_product_entity_varchar") . " (attribute_id,store_id,row_id,value) VALUES
                            (87,0,$rowId,'$imageName'),
                            (88,0,$rowId,'$imageName'),
                            (89,0,$rowId,'$imageName')";
                $connection->query($query);
            }
        } catch (Exception $ex)
        {
            echo $ex->getMessage();
        }
    }

    /**
     * @param $inventoryId
     * @param $logViewFileName
     * @param $storeId
     * @param $syncLogID
     * @param $syncType
     */
    public function removeImages($inventoryId,$logViewFileName,$storeId,$syncLogID,$syncType)
    {
        try {
            $prodId = $this->productFactory->create()->getIdBySku($inventoryId);
            if($prodId){
                $mediaApi = $this->attributeMediaGalleryManagementInterface;
                $items = $mediaApi->getList(trim($inventoryId));
                $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
                foreach ($items as $item) {
                    if ($item->getId())
                    {
                        try {
                            $res = $this->attributeMediaGalleryManagementInterface->remove(trim($inventoryId), $item->getId());
                            if($res){
                                $delete = $connection->fetchRow("SELECT *  FROM ".$connection->getTableName("catalog_product_entity_media_gallery_value") ." WHERE value_id = ".$item->getId());
                                if($delete)
                                    $connection->query("DELETE FROM ".$connection->getTableName("catalog_product_entity_media_gallery_value") ." WHERE value_id = ".$item->getId());
                            }
                        } catch (\Magento\Eav\Model\Entity\Attribute\Exception $ep)
                        {
                            $msg = 'Error in removing product image for '.$inventoryId;
                            $productImageArray['storeId'] = $storeId;
                            $productImageArray['schedule_id'] = $syncLogID;
                            $productImageArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productImageArray['acumatica_attribute_code'] = $inventoryId;
                            $productImageArray['description'] = $msg;
                            $productImageArray['longMessage'] = $ep->getMessage();
                            $productImageArray['runMode'] = $syncType;
                            $productImageArray['messageType'] = "Error";
                            $productImageArray['syncDirection'] = 'syncToMagento';
                            $txt = "Error: " . $productImageArray['longMessage'];
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->syncLogProductImage->productImageSyncSuccessLogs($productImageArray);
                        } catch (Exception $e) {
                            echo $e->getMessage();
                            $msg = 'Error in removing product image for '.$inventoryId;
                            $productImageArray['storeId'] = $storeId;
                            $productImageArray['schedule_id'] = $syncLogID;
                            $productImageArray['created_at'] = $this->date->date('Y-m-d H:i:s');
                            $productImageArray['acumatica_attribute_code'] = $inventoryId;
                            $productImageArray['description'] = $msg;
                            $productImageArray['longMessage'] = $e->getMessage();
                            $productImageArray['runMode'] = $syncType;
                            $productImageArray['messageType'] = "Error";
                            $productImageArray['syncDirection'] = 'syncToMagento';
                            $txt = "Error: " . $productImageArray['longMessage'];
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->syncLogProductImage->productImageSyncSuccessLogs($productImageArray);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }
}