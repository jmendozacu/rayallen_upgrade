<?php
/**
 *
 * @category   Product Inventory Sync
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */


namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Webapi\Soap;
use SoapFault;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magento\Framework\Stdlib\DateTime\Timezone as TimeZone;

class Inventory extends \Magento\Framework\App\Helper\AbstractHelper
{
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
     * @var \Kensium\Amconnector\Model\ResourceModel\Product
     */
    protected $resourceModelProduct;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    /**
     * @var
     */
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
     * @var \Kensium\Synclog\Helper\Data
     */

    protected $dataHelper;

    /**
     * @var
     */
    protected $successMsg;
    
    /**
     * @var 
     */
    protected $errorCheck;
    
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Licensecheck
     */
    protected $licenseCheck;

    const IS_TIME_VALID = "Valid";


    /**
     * @var \Kensium\Synclog\Helper\ProductInventory
     */
    protected $prodInvtHelper;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $syncResourceModel;
    /**
     *
     * @var \Kensium\Amconnector\Helper\Sync
     */
    protected $syncHelper;
    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface 
     */
    protected $scopeConfigInterface;
    /**
     *
     * @var \Magento\Catalog\Model\ProductFactory 
     */
    protected $productFactory;
    /**
     * @var
     */
    protected $licensecheck;

    /**
     * @var
     */
    protected $licenseResourceModel;

    const IS_LICENSE_INVALID = "Invalid";
    const IS_LICENSE_VALID = "Valid";

    /**
     * @param Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param TimeZone $timezone
     * @param Client $clientHelper
     * @param Data $dataHelper
     * @param Time $timeHelper
     * @param Xml $xmlHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct
     * @param Url $urlHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Kensium\Synclog\Helper\ProductInventory $prodInvtHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param Sync $syncHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param Licensecheck $licensecheck
     * @param \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
	    TimeZone $timezone,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Kensium\Synclog\Helper\ProductInventory $prodInvtHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel

    )
    {
        $this->context = $context;
        $this->date = $date;
        $this->timezone = $timezone;
        $this->urlHelper =  $urlHelper;
        $this->clientHelper = $clientHelper;
        $this->dataHelper = $dataHelper;
        $this->timeHelper = $timeHelper;
        $this->xmlHelper = $xmlHelper;
        $this->resourceModelProduct = $resourceModelProduct;
        $this->resourceModelSync = $resourceModelSync;
        $this->messageManager = $messageManager;
        $this->prodInvtHelper = $prodInvtHelper;
        $this->syncResourceModel = $syncResourceModel;
        $this->syncHelper= $syncHelper;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->productFactory = $productFactory;
        $this->licenseCheck = $licenseResourceModel;

    }


    /**
     * @param $autoSync
     * @param $syncType
     * @param $syncId
     * @param null $scheduleId
     * @param $gridSessionStoreId
     * @description : Method to start the product inventory sync, we get all the products array from acumatica and sync the relevant products 
     */
    public function inventorySync($autoSync, $syncType, $syncId, $scheduleId = NULL, $gridSessionStoreId)
    {
        $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'productInventory', NULL);
        $txt = "Product Inventory Sync process started.";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
       
        $schedulerData = array();
        try {
            if ($gridSessionStoreId == 0)
            {
                $storeId = 1;
            } else 
            {
                $storeId = $gridSessionStoreId;
            }
            
            $insertedId = $this->syncResourceModel->checkConnectionFlag($syncId, 'PRODUCT_INVENTORY_SYNC',$storeId);   // get the insert id only after getting the logviewfile name
            if($insertedId == NULL)
            {
                $schedulerData['schedule_id'] = $scheduleId;
                $schedulerData['syncDirection'] = 'Acumatica To Magento';
                $schedulerData['job_code'] = "inventory";
                $schedulerData['status'] = "error";
                $schedulerData['messages'] = "Another Sync is already running please wait.";
                $schedulerData['created_at'] = $this->date->date('Y-m-d H:i:s');
                $schedulerData['store_id'] = $storeId;
                $schedulerData['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                if ($syncType == 'MANUAL') {
                    $schedulerData['run_mode'] = 'Manual';
                } elseif ($syncType == 'AUTO') {
                    $schedulerData['run_mode'] = 'Automatic';
                }
                if ($autoSync == 'COMPLETE') {
                    $schedulerData['auto_sync'] = 'Complete';
                } elseif ($autoSync == 'INDIVIDUAL') {
                    $schedulerData['auto_sync'] = 'Individual';
                }
                $txt = "Another Sync is already running please wait";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $syncExecId = $this->prodInvtHelper->inventoryManualSync($schedulerData);
                $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
               
                $this->errorCheck = 1;
            }
            else
            {
            $this->licenseType = $this->licenseCheck->checkLicenseTypes($storeId);
            $licenseStatus = $this->licenseCheck->getLicenseStatus($storeId);;
            if ($licenseStatus != self::IS_LICENSE_VALID) {  // if the license is not valid
                
                $schedulerData['schedule_id'] = $scheduleId;
                $schedulerData['syncDirection'] = 'Acumatica To Magento';
                $schedulerData['job_code'] = "inventory";
                $schedulerData['status'] = "error";
                $schedulerData['messages'] = "Invalid License Key";
                $schedulerData['created_at'] = $this->date->date('Y-m-d H:i:s');
                $schedulerData['store_id'] = $storeId;
                $schedulerData['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                if ($syncType == 'MANUAL') {
                    $schedulerData['run_mode'] = 'Manual';
                } elseif ($syncType == 'AUTO') {
                    $schedulerData['run_mode'] = 'Automatic';
                }
                if ($autoSync == 'COMPLETE') {
                    $schedulerData['auto_sync'] = 'Complete';
                } elseif ($autoSync == 'INDIVIDUAL') {
                    $schedulerData['auto_sync'] = 'Individual';
                }
                $txt = "Error: Invalid License Key. Please verify and try again";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $syncExecId = $this->prodInvtHelper->inventoryManualSync($schedulerData);
                $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
               
                $this->errorCheck = 1;

               
            }
            else //if the license if valid
            { 
                $txt = "Sync Process Started";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                $timeSyncCheck = $this->timeHelper->timeSyncCheck($storeId);
                if ($timeSyncCheck == self::IS_TIME_VALID ) // if the time is in sync 
                {
                    $this->syncResourceModel->updateConnection($insertedId, 'STARTED',NULL,$storeId);
                    $this->resourceModelSync->updateSyncAttribute($syncId, 'STARTED', $storeId);
                    $txt = "Time is in sync";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $txt = "Info : Sync process started!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);    
                    $txt = "License Verification Completed ";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $inventoryData = $this->getInventoryIdCollection($syncId,$storeId,$scheduleId);
                    $inventoryData = array_map("unserialize", array_unique(array_map("serialize", $inventoryData))); //this line of code removes the duplicate skus that are coming from acumatica response for each receipt that is being created for that particular sku
                    

                    if (count($inventoryData)<=0)
                    {
                        $txt = "No new product to sync";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                        $schedulerData['schedule_id'] = $scheduleId;
                        $schedulerData['syncDirection'] = 'Acumatica To Magento';
                        $schedulerData['job_code'] = "inventory";
                        $schedulerData['status'] = "success";
                        $schedulerData['messages'] = "Sync completed successfully";
                        $schedulerData['created_at'] = $this->date->date('Y-m-d H:i:s');
                        $schedulerData['store_id'] = $storeId;
                        $schedulerData['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                        if ($syncType == 'MANUAL') {
                            $schedulerData['run_mode'] = 'Manual';
                        } elseif ($syncType == 'AUTO') {
                            $schedulerData['run_mode'] = 'Automatic';
                        }
                        if ($autoSync == 'COMPLETE') {
                            $schedulerData['auto_sync'] = 'Complete';
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $schedulerData['auto_sync'] = 'Individual';
                        }
                        $syncExecId = $this->prodInvtHelper->inventoryManualSync($schedulerData);
                    }
                    else
                    {
                        $trialLicenseCount = $this->syncHelper->numberOfRecordSyncInTrialLicense();
                        $schedulerData['schedule_id'] = $scheduleId;
                        $schedulerData['syncDirection'] = 'Acumatica To Magento';
                        $schedulerData['job_code'] = "inventory";
                        $schedulerData['status'] = "success";
                        $schedulerData['messages'] = "Product Inventory Sync Process Initiaited";
                        $schedulerData['created_at'] = $this->date->date('Y-m-d H:i:s');
                        $schedulerData['store_id'] = $storeId;
                        $schedulerData['scheduled_at'] = $this->date->date('Y-m-d H:i:s');
                        if ($syncType == 'MANUAL') {
                            $schedulerData['run_mode'] = 'Manual';
                        } elseif ($syncType == 'AUTO') {
                            $schedulerData['run_mode'] = 'Automatic';
                        }
                        if ($autoSync == 'COMPLETE') {
                            $schedulerData['auto_sync'] = 'Complete';
                        } elseif ($autoSync == 'INDIVIDUAL') {
                            $schedulerData['auto_sync'] = 'Individual';
                        }
                        $syncExecId = $this->prodInvtHelper->inventoryManualSync($schedulerData);
                        $syncRecCou = 1;
                        
                        foreach($inventoryData as $value)
                        {
                            if ( isset($trialLicenseCount) && $trialLicenseCount != NULL && $syncRecCou == $trialLicenseCount)
                            {
                                $rowCount = $this->resourceModelProduct->updateProdQuantInMagentoByEntId($value);
                                $rowCount = $rowCount;
                                $txt = "Inventory with SKU: ".$value['sku']." synced to magento";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt); 
                                $data = array();
                                $data['sync_exec_id'] = $syncExecId;
                                $data['created_at'] = '';
                                $data['acumatica_attribute_code'] = $value['sku'];
                                $data['description'] = "Inventory with SKU: ".$value['sku']." synced to magento";
                                $data['message_type'] = 'Success';
                                $data['sync_direction'] = 'Acumatica To Magento';
                                $this->prodInvtHelper->inventorySyncSuccessLogs($data);
                                $txt = "Trial license allows only ".$trialLicenseCount." records to be synced";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                                break;
                            }
                            else{
                                $rowCount = $this->resourceModelProduct->updateProdQuantInMagentoByEntId($value);
                                $rowCount = $rowCount;
                                $txt = "Inventory with SKU: ".$value['sku']." synced to magento";
                                $this->dataHelper->writeLogToFile($logViewFileName, $txt);  
                                $data = array();
                                $data['sync_exec_id'] = $syncExecId;
                                $data['created_at'] = '';
                                $data['acumatica_attribute_code'] = $value['sku'];
                                $data['description'] = "Inventory with SKU: ".$value['sku']." synced to magento";
                                $data['message_type'] = 'Success';
                                $data['sync_direction'] = 'Acumatica To Magento';
                                $this->prodInvtHelper->inventorySyncSuccessLogs($data);
                            }
                            $syncRecCou++;
                        }
                    }

                     if (isset($trialLicenseCount) && $trialLicenseCount != NULL)
                     {
                            $schedulerData['messages'] = "Completed the sync successfully. Trial license allows only ".$trialLicenseCount." inventory to be synced.";
                     }
                    else
                    {
                        $schedulerData['messages'] = "Completed the sync successfully";
                    }
                    $this->prodInvtHelper->inventoryManualSyncUpdate($schedulerData,$syncExecId);
                    $this->resourceModelSync->updateSyncAttribute($syncId, 'SUCCESS', $storeId);
                    $this->syncResourceModel->updateConnection($insertedId, 'SUCCESS',NULL,$storeId);
                    $txt = "Completed Inventory Sync Successfully ";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                }
                else // if the time is not in sync
                {
                    $txt = "Error: Time is not sync";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    $this->resourceModelSync->updateSyncAttribute($syncId, 'ERROR', $storeId);
                    $this->syncResourceModel->updateConnection($insertedId, 'ERROR',NULL,$storeId);
                    $txt = "Completed Inventory Sync Successfully ";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                }
            }
        }

            
        }catch (Exception $e) {
            $txt = $e->getMessage();
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $data = array();
            $data['sync_exec_id'] = $syncExecId;
            $data['created_at'] = '';
            $data['acumatica_attribute_code'] = $value['sku'];
            $data['description'] = "Inventory with SKU: ".$value['sku']." sync to magento failed";
            $data['message_type'] = 'Error';
            $data['sync_direction'] = 'Acumatica To Magento';
            $data['long_message'] = $txt;
            $this->prodInvtHelper->inventorySyncSuccessLogs($data);
        }
        
    }
    /**
     * @param $syncId
     * @param $storeId
     * @param $scheduleId
     * 
     * @description : Method to get all the products from acumatica and convert it into array of data
     */
    public function getInventoryIdCollection($syncId,$storeId,$scheduleId)
    {
        $logViewFileName = $this->dataHelper->syncLogFile($syncId, 'productInventory', NULL);
        if($storeId==0){
            $scopeType = 'default';
        }else{
            $scopeType = 'stores';
        }
        try{
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$storeId);
            $warehouse = $this->scopeConfigInterface->getValue('amconnectorsync/defaultwarehouses/defaultwarehouse',$scopeType,$storeId);
            $url = $this->urlHelper->getBasicDefaultConfigUrl($serverUrl); 
            $csvDataForAcumatica = $this->syncHelper->getEnvelopeData('PRODUCTINVENTORYBYDATE');
            $classAction = $csvDataForAcumatica['envVersion'].'/'.$csvDataForAcumatica['envName'].'/'.$csvDataForAcumatica['methodName'];
            $XMLRequest = $csvDataForAcumatica['envelope'];
            
            $lastSyncDate = $this->syncResourceModel->getLastSyncDate($syncId, $storeId);
            $getlastSyncDateByTimezone =  $this->timezone->date($lastSyncDate,null,true);
            $fromDate = $getlastSyncDateByTimezone->format('Y-m-d H:i:s');
            $XMLRequest = str_replace('{{FROMDATE}}', trim($fromDate), $XMLRequest);
            $XMLRequest = str_replace('{{WAREHOUSE}}', trim($warehouse), $XMLRequest);
            $request = $csvDataForAcumatica['methodName'];
            $XMLResponse = $this->clientHelper->getAcumaticaWebserviceResponse($XMLRequest, $classAction,$storeId);
            $XMLData = $XMLResponse->Body->GIKEMS16SubmitResponse->SubmitResult;
            $arrayData = $this->xmlHelper->xml2array($XMLData);
            $inventoryIds = array();
            $productFactory = $this->productFactory->create();
            if (isset($arrayData['Content']['Result']))
            {
                $sku = str_replace(' ','_',trim($arrayData['Content']['Result']['InventoryIDInventoryItemInventoryCD']['Value']));
                $qty = $arrayData['Content']['Result']['QtyAvailable']['Value'];
                $productData = $productFactory->loadByAttribute('sku', $sku);
                $productData = $productData->getData();
                if (isset($productData) )
                {
                    $productEntityId = $productData['entity_id'];
                    $magentoQty = $this->resourceModelProduct->getProdQuantInMagentoByEntId($productEntityId);
                    $magentoQty = $magentoQty->qty;
                    $acumaticaQty = trim($qty);
                    if($magentoQty != $acumaticaQty)
                            $inventoryIds[$sku] = (int)$acumaticaQty;
                }
            }
            else if (isset($arrayData['Content']))
            {
                $counter = 0;
                foreach ($arrayData['Content'] as $acuInventoryData)
                {
                    $sku = str_replace(' ','_',trim($acuInventoryData->Result->InventoryIDInventoryItemInventoryCD->Value));
                    $qty = $acuInventoryData->Result->QtyAvailable->Value;
                    $productFactory = $this->productFactory->create();
                    $productData = $productFactory->loadByAttribute('sku', $sku);
                    
                    if ( $productData != false    )
                    {
                        $productData = $productData->getData();
                        $productEntityId = $productData['entity_id'];
                        $magentoQty = $this->resourceModelProduct->getProdQuantInMagentoByEntId($productEntityId);
                        $magentoQty = $magentoQty->qty;
                        $acumaticaQty = trim($qty);
                        if($magentoQty != $acumaticaQty)
                        {
                            $inventoryIds[$counter]['sku']       = $sku;
                            $inventoryIds[$counter]['qty']       = (int)$acumaticaQty;
                            $inventoryIds[$counter]['product_id'] = $productEntityId;
                            $counter = $counter+1;
                            
                        }
                            
                    }
                }
            }
            return $inventoryIds;
        } catch (SoapFault $e) {
            echo "Last request:<pre>" . $e->getMessage() . "</pre>";
        }
    }
}
