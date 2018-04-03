<?php

/**
 *
 * @category   Product Price Sync
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Webapi\Soap;
use SoapFault;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magento\Framework\Stdlib\DateTime\Timezone as TimeZone;
use Kensium\Amconnector\Helper\Sync;

class ProductPrice extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var
     */
    protected $urlHelper;

    /**
     * @var
     */
    protected $clientHelper;

    /**
     * @var
     */
    protected $xmlHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Product
     */
    protected $resourceModelProduct;

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
    protected $resourceModelSync;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;


    /**
     * @var
     */

    protected $dataHelper;


    const IS_LICENSE_INVALID = "Invalid";
    const IS_LICENSE_VALID = "Valid";//change
    Const SCOPE_TYPE = 'stores';


    /**
     * @var
     */
    protected $successMsg;

    protected $errorCheck;

    /**
     * @var \Kensium\Amconnector\Helper\Sync
     *
     */
    protected $syncHelper;

    protected $productHelper;

    protected $Licensecheck;

    const IS_TIME_VALID = "Valid";

    /**
     * @param Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param TimeZone $timezone
     * @param Client $clientHelper
     * @param Data $dataHelper
     * @param Time $timeHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Licensecheck $Licensecheck
     * @param \Kensium\Synclog\Helper\Productprice $productPriceHelper
     * @param Xml $xmlHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct
     * @param Url $urlHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Kensium\Synclog\Helper\Product $productHelper
     * @param Sync $syncHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        TimeZone $timezone,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $Licensecheck,
        \Kensium\Synclog\Helper\Productprice $productPriceHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Kensium\Synclog\Helper\Product $productHelper,
        Sync $syncHelper

    )
    {
        $this->context = $context;
        $this->date = $date;
        $this->timezone = $timezone;
        $this->Licensecheck = $Licensecheck;
        $this->productPriceHelper = $productPriceHelper;
        $this->urlHelper = $urlHelper;
        $this->clientHelper = $clientHelper;
        $this->dataHelper = $dataHelper;
        $this->timeHelper = $timeHelper;
        $this->xmlHelper = $xmlHelper;
        $this->resourceModelProduct = $resourceModelProduct;
        $this->resourceModelSync = $resourceModelSync;
        $this->messageManager = $messageManager;
        $this->syncHelper = $syncHelper;
        $this->productHelper = $productHelper;

    }


    /**
     * @param $autoSync
     * @param $syncType
     * @param $syncId
     * @param null $scheduleId
     * @param $gridSessionStoreId
     */
    public function priceSync($autoSync, $syncType, $syncId, $scheduleId = NULL, $gridSessionStoreId)
    {
        $this->totalTrialRecord = $this->syncHelper->numberOfRecordSyncInTrialLicense();
        $insertedId = 0;
        $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'productPrice', NULL);
        $this->successMsg = 0;
        $this->errorCheck = 0;
        try {
            $txt = "Info : Sync process started!";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            if ($gridSessionStoreId == 0) {
                $storeId = 1;
            } else {
                $storeId = $gridSessionStoreId;
            }
            $licenseStatus = $this->Licensecheck->getLicenseStatus($storeId);

            $txt = "Info : License verification is in progress";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            if ($licenseStatus != self::IS_LICENSE_VALID) {

                if ($scheduleId != '') {
                    $productPriceArray['schedule_id'] = $scheduleId;
                } else {
                    $productPriceArray['schedule_id'] = "";
                }
                $productPriceArray['sync_direction'] = "Acumatica To Magento";
                $productPriceArray['job_code'] = "productprice";
                $productPriceArray['status'] = "error";
                $productPriceArray['messages'] = "Invalid License Key";
                $productPriceArray['created_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                $productPriceArray['executed_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                $productPriceArray['store_id'] = $storeId;
                $productPriceArray['scheduled_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                $productPriceArray['finished_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                if ($syncType == 'MANUAL') {
                    $productPriceArray['runMode'] = 'Manual';
                } elseif ($syncType == 'AUTO') {
                    $productPriceArray['runMode'] = 'Automatic';
                }
                if ($autoSync == 'COMPLETE') {
                    $productPriceArray['autoSync'] = 'Complete';
                } elseif ($autoSync == 'INDIVIDUAL') {
                    $productPriceArray['autoSync'] = 'Individual';
                }
                $txt = "Error: Invalid License Key. Please verify and try again";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $productPriceArray['schedule_id'] = $this->productPriceHelper->ProductPriceManualSync($productPriceArray);
                $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
                $this->messageManager->addError("Invalid license key.");
                $this->errorCheck = 1;
            } else {

                $txt = "Info : License Verified Successfully";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $productPriceArray['created_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                if ($syncType == 'MANUAL') {
                    $productPriceArray['runMode'] = 'Manual';
                } elseif ($syncType == 'AUTO') {
                    $productPriceArray['runMode'] = 'Automatic';
                }
                if ($autoSync == 'COMPLETE') {
                    $productPriceArray['autoSync'] = 'Complete';
                } elseif ($autoSync == 'INDIVIDUAL') {
                    $productPriceArray['autoSync'] = 'Individual';
                }
                $this->productPriceHelper->ProductPriceManualSync($productPriceArray);
                $txt = "Info : Server time verification is in progress.";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $timeSyncCheck = $this->timeHelper->timeSyncCheck($storeId);
                $this->productPriceHelper->productPriceSyncSuccessLogs($productPriceArray);

                if ($timeSyncCheck != self::IS_TIME_VALID) {
                    if ($scheduleId != '') {
                        $productPriceArray['schedule_id'] = $scheduleId;
                    } else {
                        $productPriceArray['schedule_id'] = "";
                    }
                    $productPriceArray['sync_direction'] = "Acumatica To Magento";
                    $productPriceArray['store_id'] = $storeId;
                    $productPriceArray['job_code'] = "productprice";
                    $productPriceArray['status'] = "error";
                    $productPriceArray['messages'] = "Server time is not in sync";
                    $productPriceArray['created_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                    $productPriceArray['executed_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                    $productPriceArray['finished_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");

                    if ($syncType == 'MANUAL') {
                        $productPriceArray['runMode'] = 'Manual';
                    } elseif ($syncType == 'AUTO') {
                        $productPriceArray['runMode'] = 'Automatic';
                    }
                    if ($autoSync == 'COMPLETE') {
                        $productPriceArray['autoSync'] = 'Complete';
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $productPriceArray['autoSync'] = 'Individual';
                    }
                    $productPriceArray['scheduled_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                    $txt = "Info : Error: Server time is not in sync.";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $productPriceArray['schedule_id'] = $this->productPriceHelper->ProductPriceManualSync($productPriceArray);
                    $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
                    $this->messageManager->addError("Server time is not in sync.");
                    $this->errorCheck = 1;

                } else {

                    $txt = "Info : Server time is in sync.";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                    if ($scheduleId != '') {
                        $productPriceArray['schedule_id'] = $scheduleId;
                    } else {
                        $productPriceArray['schedule_id'] = "";
                    }
                    $productPriceArray['sync_direction'] = "Acumatica To Magento";
                    $productPriceArray['job_code'] = "productprice";
                    $productPriceArray['status'] = "success";
                    $productPriceArray['scheduled_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                    $productPriceArray['store_id'] = $storeId;
                    $productPriceArray['messages'] = "Server time is in sync";
                    $productPriceArray['created_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                    if ($syncType == 'MANUAL') {
                        $productPriceArray['runMode'] = 'Manual';
                    } elseif ($syncType == 'AUTO') {
                        $productPriceArray['runMode'] = 'Automatic';
                    }
                    if ($autoSync == 'COMPLETE') {
                        $productPriceArray['autoSync'] = 'Complete';
                    } elseif ($autoSync == 'INDIVIDUAL') {
                        $productPriceArray['autoSync'] = 'Individual';
                    }
                    $syncLogID = $this->productPriceHelper->productPriceManualSync($productPriceArray);
                    $txt = "Info : Product Price manual sync initiated.";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->resourceModelSync->updateSyncAttribute($syncId, 'STARTED', $storeId);

                    if ($autoSync == 'COMPLETE') {

                        $insertedId = $this->resourceModelSync->checkConnectionFlag($syncId, 'productprice');
                        if ($insertedId == NULL) {

                            $this->messageManager->addError("Sync in Progress - please wait for the current sync to finish.");
                            $productPriceArray['sync_direction'] = "Acumatica To Magento";
                            $productPriceArray['store_id'] = $storeId;
                            $productPriceArray['id'] = $syncLogID;
                            $productPriceArray['scheduled_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                            $productPriceArray['job_code'] = "productprice";
                            $productPriceArray['status'] = "error";
                            $productPriceArray['store_id'] = $storeId;
                            $productPriceArray['messages'] = "Another Sync is already executing";
                            $productPriceArray['executed_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                            $productPriceArray['finished_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                            $txt = "Info : Sync in Progress - please wait for the current sync to finish.";
                            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                            $this->productPriceHelper->ProductPriceManualSync($productPriceArray);
                            $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
                            return;
                        } else {
                            $this->resourceModelSync->updateConnection($insertedId, 'PROCESS');
                            $this->resourceModelSync->updateSyncAttribute($syncId, 'PROCESSING', $storeId);
                            $acumaticaData = $this->getAcumaticaPriceData($syncId, $storeId);
                            $updated = $this->resourceModelProduct->updatePriceDataIntoMagento($acumaticaData, $logViewFileName, $syncLogID, $storeId, $syncId);
                            $this->successMsg = $this->successMsg + $updated;
                            $this->productPriceHelper->productPriceSyncSuccessLogs($productPriceArray);
                            if ($this->successMsg == 0) {
                                $customerArray['messages'] = "Product Prices already synced, no price has been updated.";
                                $txt = "Info : " . $customerArray['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $productPriceArray['sync_direction'] = "Acumatica To Magento";
                                $productPriceArray['schedule_id'] = $syncLogID;
                                $productPriceArray['scheduled_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                                $productPriceArray['status'] = "success";
                                $productPriceArray['store_id'] = $storeId;
                                $productPriceArray['messages'] = "Product price already in sync, no price has been updated";
                                $productPriceArray['job_code'] = "productprice";
                                $productPriceArray['executed_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                                $productPriceArray['finished_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
                                $this->productPriceHelper->ProductPriceManualSync($productPriceArray);
                                $this->productPriceHelper->productPriceSyncSuccessLogs($productPriceArray);
                                $this->messageManager->addSuccess("Product Price synced successfully!");
                            } else {

                                $customerArray['messages'] = "Product Price(s) updated successfully!";
                                $txt = "Info : " . $customerArray['messages'];
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                $this->productPriceHelper->productPriceSyncSuccessLogs($productPriceArray);
                                $this->messageManager->addSuccess('Product Price synced successfully');
                            }
                        }
                    }

                }


            }
            if ($this->errorCheck == 1) {
                $this->resourceModelSync->updateConnection($insertedId, 'ERROR');
                $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
            } else {
                $this->resourceModelSync->updateConnection($insertedId, 'SUCCESS');
                $this->resourceModelSync->updateSyncAttribute($syncId, 'SUCCESS', $storeId);
            }

        } catch (Exception $e) {
            if ($scheduleId != '') {
                $productPriceArray['schedule_id'] = $scheduleId;
            } else {
                $productPriceArray['schedule_id'] = "";
            }
            $productPriceArray['job_code'] = "productprice";
            $productPriceArray['status'] = "error";
            $productPriceArray['long_message'] = $e->getMessage();
            $productPriceArray['executed_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
            $productPriceArray['finished_at'] = $this->timezone->date(time())->format("Y-m-d H:i:s");
            $txt = " : Error: " . $productPriceArray['messages'];
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

            $this->productPriceHelper->ProductPriceManualSync($productPriceArray);
            $this->productPriceHelper->productPriceSyncSuccessLogs($productPriceArray);
            $this->messageManager->addError("Sync error occurred. Please try again.");
            $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
        }
        $txt = "Info : Sync process completed!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);


    }


    /**
     * Get price data from acumatica
     * @param $syncId
     * @param $storeId
     * @return array
     */
    public function getAcumaticaPriceData($syncId, $storeId)
    {
        $inventoryIds = array();
        try {

            $lastSyncDate = $this->resourceModelSync->getLastSyncDate($syncId, $storeId);
            $fromDate = $this->timezone->date($lastSyncDate, null, true);
            $fromDate = $fromDate->format('Y-m-d H:i:s');
            $csvProductPriceSchemaData = $this->syncHelper->getEnvelopeData('PRODUCTWITHPRICE');
            $XMLGetRequest = $csvProductPriceSchemaData['envelope'];
            $productPriceCycleAction = $csvProductPriceSchemaData['methodName'];
            $XMLGetRequest = str_replace('{{FROMDATE}}', $fromDate, $XMLGetRequest);
            $flag = '';
            $requestType = $productPriceCycleAction;
            $xml = $this->clientHelper->getAcumaticaResponsePrice($XMLGetRequest, $requestType,self::SCOPE_TYPE,$storeId);
            $data = $xml->Body->GIKEMS18SubmitResponse->SubmitResult;
            $totalData = $this->xmlHelper->xml2array($data);
            if (isset($totalData['Content'])) {
                $inventoryIds = array();
                $oneRecordFlag = false;
                foreach ($totalData['Content'] as $key => $value) {
                    if (!is_numeric($key)) {
                        $oneRecordFlag = true;
                        break;
                    }
                    $sku = str_replace(' ', '_', trim($value->Result->InventoryID->Value));
                    $price = $value->Result->DefaultPrice->Value;
                    if ($sku != '' && $sku != NULL && isset($price))
                        $inventoryIds[$sku] = trim($price);
                }
                if ($oneRecordFlag) {
                    $sku = str_replace(' ', '_', trim($totalData['Content']['Result']['InventoryID']['Value']));
                    if ($sku != '' && $sku != NULL && isset($totalData['Content']['Result']['DefaultPrice']['Value']))
                        $inventoryIds[$sku] = trim($totalData['Content']['Result']['DefaultPrice']['Value']);
                }
            }
        } catch (SoapFault $e) {
            echo $e->getMessage();
        }

        return $inventoryIds;
    }

}
