<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Kensium\Amconnector\Helper\Sync;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class Observer
 * @package Kensium\Amconnector
 */
class Observer implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Sync
     */
    protected $syncHelper;

    /**
     * @var Model\ResourceModel\Sync
     */
    protected $syncResourceModel;
    /**
     * @var
     */
    protected $resource;
    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $config;
    /**
     * @var DateTime
     */
    protected $date;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $baseDirPath;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $ioFile;
    /**
     * @var Helper\Client
     */
    protected $clientHelper;
    /**
     * @var
     */
    protected $_transportBuilder;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Sync $syncHelper
     * @param Model\ResourceModel\Sync $syncResourceModel
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\App\DeploymentConfig $config
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\App\Filesystem\DirectoryList $baseDirPath
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param Helper\Client $clientHelper
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Sync $syncHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\DeploymentConfig $config,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Filesystem\DirectoryList $baseDirPath,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation

    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->syncHelper = $syncHelper;
        $this->syncResourceModel = $syncResourceModel;
        $this->_resource = $resource;
        $this->config = $config;
        $this->date = $date;
        $this->resourceConfig = $resourceConfig;
        $this->baseDirPath = $baseDirPath;
        $this->ioFile = $ioFile;
        $this->clientHelper = $clientHelper;
        $this->_transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_logger = $logger;

    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $storeId = $this->syncHelper->getCurrentStoreId();
        if ($storeId == 0) {
            $storeId = 1;
        }
        $storeType = 'stores';
        $categoryEnable = $this->scopeConfig->getValue('amconnectorsync/categorysync/categorysync', $storeType, $storeId);
        if($categoryEnable == '' && $storeId == 1){
            $categoryEnable = $this->scopeConfig->getValue('amconnectorsync/categorysync/categorysync');
        }
        $productAttributeEnable = $this->scopeConfig->getValue('amconnectorsync/attributesync/product', $storeType, $storeId);
        if($productAttributeEnable == '' && $storeId == 1){
            $productAttributeEnable = $this->scopeConfig->getValue('amconnectorsync/attributesync/product');
        }
        $productEnable = $this->scopeConfig->getValue('amconnectorsync/productsync/productsync', $storeType, $storeId);
        if($productEnable == '' && $storeId == 1){
            $productEnable = $this->scopeConfig->getValue('amconnectorsync/productsync/productsync');
        }
        $productInventoryEnable = $this->scopeConfig->getValue('amconnectorsync/productinventorysync/productinventorysync', $storeType, $storeId);
        if($productInventoryEnable == '' && $storeId == 1){
            $productInventoryEnable = $this->scopeConfig->getValue('amconnectorsync/productinventorysync/productinventorysync');
        }
        $productImageEnable = $this->scopeConfig->getValue('amconnectorsync/productimagesync/productimagesync', $storeType, $storeId);
        if($productImageEnable == '' && $storeId == 1){
            $productImageEnable = $this->scopeConfig->getValue('amconnectorsync/productimagesync/productimagesync');
        }
        $customerAttributeEnable = $this->scopeConfig->getValue('amconnectorsync/attributesync/customer', $storeType, $storeId);
        if($customerAttributeEnable == '' && $storeId == 1){
            $customerAttributeEnable = $this->scopeConfig->getValue('amconnectorsync/attributesync/customer');
        }
        $customerEnable = $this->scopeConfig->getValue('amconnectorsync/customersync/customersync', $storeType, $storeId);
        if($customerEnable == '' && $storeId == 1){
            $customerEnable = $this->scopeConfig->getValue('amconnectorsync/customersync/customersync');
        }
        $orderEnable = $this->scopeConfig->getValue('amconnectorsync/ordersync/ordersync', $storeType, $storeId);
        if($orderEnable == '' && $storeId == 1){
            $orderEnable = $this->scopeConfig->getValue('amconnectorsync/ordersync/ordersync');
        }
        $shipmentEnable = $this->scopeConfig->getValue('amconnectorsync/shipmentsync/shipmentsync', $storeType, $storeId);
        if($shipmentEnable == '' && $storeId == 1){
            $shipmentEnable = $this->scopeConfig->getValue('amconnectorsync/shipmentsync/shipmentsync');
        }
        $failedOrderEnable = $this->scopeConfig->getValue('amconnectorsync/failedordersync/failedordersync', $storeType, $storeId);
        if($failedOrderEnable == '' && $storeId == 1){
            $failedOrderEnable = $this->scopeConfig->getValue('amconnectorsync/failedordersync/failedordersync');
        }
        $configuratorEnable = $this->scopeConfig->getValue('amconnectorsync/configuratorsync/configuratorsync', $storeType, $storeId);
        if($configuratorEnable == '' && $storeId == 1){
            $configuratorEnable = $this->scopeConfig->getValue('amconnectorsync/configuratorsync/configuratorsync');
        }
        $priceEnable = $this->scopeConfig->getValue('amconnectorsync/productpricesync/productpricesync',$storeType,$storeId);
        if($priceEnable == '' && $storeId == 1){
            $priceEnable = $this->scopeConfig->getValue('amconnectorsync/productpricesync/productpricesync');
        }

        $merchandiseEnable = $this->scopeConfig->getValue('amconnectorsync/merchandise/merchandisesync',$storeType,$storeId);
        if($merchandiseEnable == '' && $storeId == 1){
            $merchandiseEnable = $this->scopeConfig->getValue('amconnectorsync/merchandise/merchandisesync');
        }

        $syncEnable = array(
            '1' => $categoryEnable,
            '2' => $productAttributeEnable,
            '3' => $productEnable,
            '4' => $productInventoryEnable,
            '5' => $productImageEnable,
            '6' => $customerAttributeEnable,
            '7' => $customerEnable,
            '8' => $orderEnable,
            '9' => $priceEnable,
            '10' => $failedOrderEnable,
            '11' => $configuratorEnable,
            '12' => $merchandiseEnable,
            '13' => $shipmentEnable
        );

        $sync_enable = array(
            'category' => $categoryEnable,
            'productAttribute' => $productAttributeEnable,
            'product' => $productEnable,
            'productInventory' => $productInventoryEnable,
            'productimage' => $productImageEnable,
            'customerAttribute' => $customerAttributeEnable,
            'customer' => $customerEnable,
            'order' => $orderEnable,
            'orderShipment' => $shipmentEnable,
            'productPrice' => $priceEnable,
            'failedOrder' => $failedOrderEnable,
            'productConfigurator' => $configuratorEnable,
            'merchandise' => $merchandiseEnable
        );

        $syncAutoCron = array(
            '1' => $this->scopeConfig->getValue('amconnectorsync/categorysync/autocron', $storeType, $storeId),
            '2' => '0',
            '3' => $this->scopeConfig->getValue('amconnectorsync/productsync/autocron', $storeType, $storeId),
            '4' => $this->scopeConfig->getValue('amconnectorsync/productinventorysync/autocron', $storeType, $storeId),
            '5' => $this->scopeConfig->getValue('amconnectorsync/productimagesync/autocron', $storeType, $storeId),
            '6' => '0',
            '7' => $this->scopeConfig->getValue('amconnectorsync/customersync/autocron', $storeType, $storeId),
            '8' => $this->scopeConfig->getValue('amconnectorsync/ordersync/autocron', $storeType, $storeId),
            '9' => $this->scopeConfig->getValue('amconnectorsync/productinventorysync/autocron', $storeType, $storeId),
            '10' => $this->scopeConfig->getValue('amconnectorsync/failedordersync/autocron', $storeType, $storeId),
            '11' => $this->scopeConfig->getValue('amconnectorsync/configuratorsync/autocron', $storeType, $storeId),
            '12' => 0,
            '13' => $this->scopeConfig->getValue('amconnectorsync/shipmentsync/autocron', $storeType, $storeId),
        );

        $sync_autoCron = array(
            'category' => $this->scopeConfig->getValue('amconnectorsync/categorysync/autocron', $storeType, $storeId),
            'productAttribute' => '0',
            'product' => $this->scopeConfig->getValue('amconnectorsync/productsync/autocron', $storeType, $storeId),
            'productInventory' => $this->scopeConfig->getValue('amconnectorsync/productinventorysync/autocron', $storeType, $storeId),
            'productimage' => $this->scopeConfig->getValue('amconnectorsync/productimagesync/autocron', $storeType, $storeId),
            'customerAttribute' => '0',
            'customer' => $this->scopeConfig->getValue('amconnectorsync/customersync/autocron', $storeType, $storeId),
            'order' => $this->scopeConfig->getValue('amconnectorsync/ordersync/autocron', $storeType, $storeId),
            'productPrice' => $this->scopeConfig->getValue('amconnectorsync/productinventorysync/autocron', $storeType, $storeId),
            'failedOrder' => $this->scopeConfig->getValue('amconnectorsync/failedordersync/autocron', $storeType, $storeId),
            'productConfigurator' => $this->scopeConfig->getValue('amconnectorsync/configuratorsync/autocron', $storeType, $storeId),
            'merchandise' => 0,
            'orderShipment' => $this->scopeConfig->getValue('amconnectorsync/shipmentsync/autocron', $storeType, $storeId),
        );

        $cronData = array(
            '1' => 'amconnector_categorysync',
            '3' => 'amconnector_productsync',
            '7' => 'amconnector_customersync',
            '8' => 'amconnector_ordersync',
            '9' => 'amconnector_productinventorysync',
            '10' => 'amconnector_failedordersync',
            '11' => 'amconnector_productconfiguratorsync',
            '13' => 'amconnector_shipmentsync',
        );

        $attributeName = array(
            '1' => 'Category Sync',
            '2' => 'Product Attribute Sync',
            '3' => 'Product Sync',
            '4' => 'Product Inventory Sync',
            '5' => 'Product Image Sync',
            '6' => 'Customer Attribute Sync',
            '7' => 'Customer Sync',
            '8' => 'Order Sync',
            '9' => 'Product Inventory Sync',
            '10' => 'Failed Order Sync',
            '11' => 'Product Configurator Sync',
            '12' => 'Merchandise Sync',
            '13' => 'Shipment Sync'
        );

        /**
         * Removing scheduled Cron jobs if we disabled from admin configuration
         */
        foreach($syncAutoCron as $_key => $_value)
        {
            if($_value == 0)
            {
                if(isset($cronData[$_key])){

                    if($cronData[$_key] != '')
                    {
                        /**
                         * here we need to check the paths are exist in database
                         */
                        $cronExpression = "crontab/default/jobs/".$cronData[$_key]."/schedule/cron_expr";
                        //$cronTime = "crontab/default/jobs/".$cronData[$_key]."/schedule/cron_expr";
                        if($_key == "1"){
                            $startTimePath = "amconnectorsync/categorysync/start_time";
                            $frequency = "amconnectorsync/categorysync/frequency";
                        }elseif($_key == "3"){
                            $startTimePath = "amconnectorsync/productsync/start_time";
                            $frequency = "amconnectorsync/productsync/frequency";
                        }elseif($_key == "7"){
                            $startTimePath = "amconnectorsync/customersync/start_time";
                            $frequency = "amconnectorsync/customersync/frequency";
                        }elseif($_key == "8"){
                            $startTimePath = "amconnectorsync/ordersync/start_time";
                            $frequency = "amconnectorsync/ordersync/frequency";
                        }elseif($_key == "9"){
                            $startTimePath = "amconnectorsync/productinventorysync/start_time";
                            $frequency = "amconnectorsync/productinventorysync/frequency";
                        }elseif($_key == "10"){
                            $startTimePath = "amconnectorsync/failedordersync/start_time";
                            $frequency = "amconnectorsync/failedordersync/frequency";
                        }elseif($_key == "11"){
                            $startTimePath = "amconnectorsync/configuratorsync/start_time";
                            $frequency = "amconnectorsync/configuratorsync/frequency";
                        }elseif($_key == "13"){
                            $startTimePath = "amconnectorsync/shipmentsync/start_time";
                            $frequency = "amconnectorsync/shipmentsync/frequency";
                        }
                        $selectQry = "select * from " . $connection->getTableName("core_config_data") . " where path = '".$cronExpression."'";
                        $cronExpressionResult = $connection->fetchAll($selectQry);
                        if(!empty($cronExpressionResult)) {
                            foreach ($cronExpressionResult as $cronResult) {

                                if($cronResult['config_id'] != '')
                                {
                                    $connection->query("DELETE FROM  " . $connection->getTableName("core_config_data") . " WHERE  config_id = ".$cronResult['config_id']);
                                    $connection->query("DELETE FROM  " . $connection->getTableName("core_config_data") . " WHERE  path = '".$startTimePath."'");
                                    $connection->query("DELETE FROM  " . $connection->getTableName("core_config_data") . " WHERE  path = '".$frequency."'");
                                }

                            }
                        }
                    }
                }

            }
        }
        foreach($syncEnable as $key => $sync)
        {
            $syncId =  $this->syncResourceModel->getSyncId($attributeName[$key],$storeId);
            $selectQry = "select count(*) as counter from " . $connection->getTableName("amconnector_attribute_sync") . " where id = '".$syncId."'
                and store_id = ".$storeId;
            $counter = $connection->fetchAll($selectQry);
            if($counter[0]['counter'] == 0)
            {
                $connection->query("INSERT INTO  " . $connection->getTableName("amconnector_attribute_sync") . " (`attribute_id`,`title`, `store_id`)
                    VALUES ('$key','$attributeName[$key]',$storeId)");
            }
            $syID = $this->syncResourceModel->getSyncId($attributeName[$key],$storeId);
            $this->syncResourceModel->updateSync($sync , $syncAutoCron[$key] , $syID, $storeId);
        }

        /**
         * If minute is selected then start time value should delete from core_config_data
         */

        $frequencyValues = array(
            0 => array($this->scopeConfig->getValue('amconnectorsync/categorysync/frequency',$storeType,$storeId),'amconnectorsync/categorysync/start_time','crontab/default/jobs/amconnector_categorysync/schedule/cron_expr',$this->scopeConfig->getValue('amconnector/categorysync/minutes',$storeType,$storeId)),
            1 => array($this->scopeConfig->getValue('amconnectorsync/productsync/frequency',$storeType,$storeId),'amconnectorsync/productsync/start_time','crontab/default/jobs/amconnector_productsync/schedule/cron_expr',$this->scopeConfig->getValue('amconnector/productsync/minutes',$storeType,$storeId)),
            2 => array($this->scopeConfig->getValue('amconnectorsync/productinventorysync/frequency',$storeType,$storeId),'amconnectorsync/productinventorysync/start_time','crontab/default/jobs/amconnector/schedule/cron_expr',$this->scopeConfig->getValue('amconnector/productinventorysync/minutes',$storeType,$storeId)),
            3 => array($this->scopeConfig->getValue('amconnectorsync/configuratorsync/frequency',$storeType,$storeId),'amconnectorsync/configuratorsync/start_time','crontab/default/jobs/amconnector_productconfiguratorsync/schedule/cron_expr',$this->scopeConfig->getValue('amconnector/configuratorsync/minutes',$storeType,$storeId)),
            4 => array($this->scopeConfig->getValue('amconnectorsync/customersync/frequency',$storeType,$storeId),'amconnectorsync/customersync/start_time','crontab/default/jobs/amconnector_customersync/schedule/cron_expr',$this->scopeConfig->getValue('amconnector/customersync/minutes',$storeType,$storeId)),
            5 => array($this->scopeConfig->getValue('amconnectorsync/ordersync/frequency',$storeType,$storeId),'amconnectorsync/ordersync/start_time','crontab/default/jobs/amconnector_ordersync/schedule/cron_expr',$this->scopeConfig->getValue('amconnector/ordersync/minutes',$storeType,$storeId)),
            6 => array($this->scopeConfig->getValue('amconnectorsync/failedordersync/frequency',$storeType,$storeId),'amconnectorsync/failedordersync/start_time','crontab/default/jobs/amconnector_failedordersync/schedule/cron_expr',$this->scopeConfig->getValue('amconnector/failedordersync/minutes',$storeType,$storeId)),
            7 => array($this->scopeConfig->getValue('amconnectorsync/shipmentsync/frequency',$storeType,$storeId),'amconnectorsync/shipmentsync/start_time','crontab/default/jobs/amconnector_shipmentsync/schedule/cron_expr',$this->scopeConfig->getValue('amconnector/shipmentsync/minutes',$storeType,$storeId)),
        );

        foreach($frequencyValues as $frequencyValue){
            if($frequencyValue[0] == 4){
                $minuteCronExpression = "*/".$frequencyValue[3]." * * * *";
                $connection->query("DELETE FROM  " . $connection->getTableName("core_config_data") . " WHERE  path = '".$frequencyValue[1]."'");
                $connection->query("DELETE FROM  " . $connection->getTableName("core_config_data") . " WHERE  path = '".$frequencyValue[2]."'");
                $connection->query("INSERT INTO  " . $connection->getTableName("core_config_data") . " (path,value) VALUES ('".$frequencyValue[2]."' , '".$minuteCronExpression."') ");
            }
        }

        /**
         * Inventory Title
         */
        $inventoryOptions = $this->scopeConfig->getValue('amconnectorsync/productinventorysync/inventoryoptions', $storeType, $storeId);
        if($inventoryOptions == '' && $storeId == 1){
            $inventoryOptions = $this->scopeConfig->getValue('amconnectorsync/productinventorysync/inventoryoptions');
        }
        if($inventoryOptions == 1){
            $inventoryTitle = 'Product Inventory Sync';
        }elseif($inventoryOptions == 2){
            $inventoryTitle = 'Product Price Sync';
        }else{
            $inventoryTitle = 'Product Inventory and Price Sync';
        }
        $syncTitle = array(
            'category' => 'Category Sync',
            'productAttribute' => 'Product Attribute Sync',
            'product' => 'Product Sync',
            'productInventory' => $inventoryTitle,
            'productimage' => 'Product Image Sync',
            'customerAttribute' => 'Customer Attribute Sync',
            'customer' => 'Customer Sync',
            'order' => 'Order Sync',
            'productPrice' => 'Product Inventory Sync',
            'failedOrder' => 'Failed Order Sync',
            'productConfigurator' => 'Product Configurator Sync',
            'merchandise' => 'Merchandise Product Sync',
            'orderShipment' => 'Shipment Sync',
        );
        $this->syncResourceModel->saveSyncConfig($sync_enable, $sync_autoCron, $syncTitle, $storeId);



        /**
         * Update mapping table directions based on sync configuration direction
         */



        if($storeId == 1){
            $store = 0;
        }else{
            $store = $storeId;
        }

        /* Category sync direction mapping */
        $categoryPath = 'amconnectorsync/categorysync/syncdirection';
        $categorySyncDirectionBefore = $this->getStoreConfigDataBeforeSave($storeId,$categoryPath);
        $categorySyncDirectionAfter = $this->getStoreConfigData($store,$categoryPath);
        if($categorySyncDirectionBefore != $categorySyncDirectionAfter)
        {
            if($categorySyncDirectionAfter == 1){
                $categoryValues = 'Acumatica to Magento';
            }elseif($categorySyncDirectionAfter == 2){
                $categoryValues = 'Magento to Acumatica';
            }else{
                $categoryValues = 'Bi-Directional (Last Update Wins)';
            }
            /* Update direction of mapping */
            $categoryMappingQry = "SELECT * from ".$this->resourceConfig->getTable('amconnector_category_mapping')." WHERE store_id = '".$storeId."'";
            $categoryMappingCounter = $connection->fetchAll($categoryMappingQry);

            if(count($categoryMappingCounter) > 0){
                $categoryMappingUpdateQuery = "UPDATE " . $this->resourceConfig->getTable('amconnector_category_mapping') . " SET sync_direction = '".$categoryValues."' WHERE store_id = '".$storeId."'";
                $connection->query($categoryMappingUpdateQuery);

                $parentCategoryQuery = "UPDATE " . $this->resourceConfig->getTable('amconnector_category_mapping'). " SET sync_direction = 'Acumatica to Magento' WHERE magento_attr_code = 'acumatica_parent_category_id' and store_id = '".$storeId."'";
                $connection->query($parentCategoryQuery);

                $categoryQuery = "UPDATE " . $this->resourceConfig->getTable('amconnector_category_mapping') . " SET sync_direction = 'Acumatica to Magento' WHERE magento_attr_code = 'acumatica_category_id' and store_id = '".$storeId."'";
                $connection->query($categoryQuery);
            }
            if($categorySyncDirectionBefore){
                /* Update direction */
                $categoryDirectionUpdateQuery = "UPDATE " . $this->resourceConfig->getTable('amconnector_sync_direction_data') . " SET value = '".$categorySyncDirectionAfter."' WHERE path = '".$categoryPath."' AND store_id = '".$storeId."' ";
                $connection->query($categoryDirectionUpdateQuery);
            }else{
                /* Insert direction */
                $categoryDirectionInsertQuery = "INSERT INTO " . $this->resourceConfig->getTable('amconnector_sync_direction_data') . " (id,path,value,store_id) VALUES ('','".$categoryPath."','".$categorySyncDirectionAfter."','".$storeId."')";
                $connection->query($categoryDirectionInsertQuery);
            }
        }
    }

    /**
     * @param $storeId
     * @param $path
     * @return mixed
     * To get store config value based on store Id
     */
    public function getStoreConfigData($storeId,$path)
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $directionQry = "SELECT value from ".$this->resourceConfig->getTable('core_config_data ')." WHERE path = '".$path."' AND scope_id='".$storeId."'";
        $directionQryResult = $connection->fetchOne($directionQry);
        return $directionQryResult;
    }

    /**
     * To get previous sync direction value
     */
    public function getStoreConfigDataBeforeSave($storeId,$path)
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $directionQry = "SELECT value from ".$this->resourceConfig->getTable('amconnector_sync_direction_data')." where path = '".$path."' and store_id='".$storeId."'";
        $directionQryResult = $connection->fetchOne($directionQry);

        return $directionQryResult;
    }

    /**
     *
     */
    public function cleanLog()
    {
        $storeId = $this->syncHelper->getCurrentStoreId();
        if ($storeId == 0) {
            $storeId = 1;
        }
        $storeType = 'stores';

        $configData = $this->config->getConfigData();
        $connection = $configData['db']['connection']['default'];
        $username = $connection['username'];
        $password = $connection['password'];
        $hostname = $connection['host'];
        $database = $connection['dbname'];
        $username = escapeshellcmd($username);
        $password = escapeshellcmd($password);
        $hostname = escapeshellcmd($hostname);
        $database = escapeshellcmd($database);

        $logCleaning = $this->scopeConfig->getValue('amconnectorcommon/log_setting/log_cleaning', $storeType, $storeId);
        if($logCleaning == '' && $storeId == 1){
            $logCleaning = $this->scopeConfig->getValue('amconnectorcommon/log_setting/log_cleaning');
        }
        $archive = $this->scopeConfig->getValue('amconnectorcommon/log_setting/archive_data', $storeType, $storeId);
        if($archive == '' && $storeId == 1){
            $archive = $this->scopeConfig->getValue('amconnectorcommon/log_setting/archive_data');
        }
        if ($logCleaning) {

            $logCleaningDays = $this->scopeConfig->getValue('amconnectorcommon/log_setting/log_days', $storeType, $storeId);
            if($logCleaningDays == '' && $storeId == 1){
                $logCleaningDays = $this->scopeConfig->getValue('amconnectorcommon/log_setting/log_days');
            }
            $cleanUpDate = $this->date->date('Y-m-d 00:00:00', strtotime("-" . $logCleaningDays . " days"));

            /* Create archive folder*/

            $varDir = $this->baseDirPath->getRoot()."/";
            $archivePath = $this->scopeConfig->getValue('amconnectorcommon/log_setting/archive_datapath', $storeType, $storeId);
            if($archivePath == '' && $storeId == 1){
                $archivePath = $this->scopeConfig->getValue('amconnectorcommon/log_setting/archive_datapath');
            }
            $archiveFolder = $varDir.$archivePath. $this->date->date('Y-m-d');
            $this->ioFile->checkAndCreateFolder($archiveFolder);

            /* backup sql file*/
            $backupFile = $archiveFolder."/amconnector_logs.sql";

            /* All logs and log_details tables*/

            $taxCategoryLogTable =  "amconnector_taxcategory_log";
            $taxCategoryLogTableDetails =  "amconnector_taxcategory_log_details";

            $categoryLogTable =  "amconnector_category_log";
            $categoryLogTableDetails =  "amconnector_category_log_details";

            $productAttributeLogTable = "amconnector_productattribute_log";
            $productAttributeLogTableDetails = "amconnector_productattribute_log_details";

            $productLogTable = "amconnector_product_log";
            $productLogTableDetails = "amconnector_product_log_details";

            $productInventoryLogTable = "amconnector_productinventory_log";
            $productInventoryLogTableDetails = "amconnector_productinventory_log_details";

            $productPriceLogTable = "amconnector_productprice_log";
            $productPriceLogTableDetails = "amconnector_productprice_log_details";

            $customerAttributeLogTable = "amconnector_customerattribute_log";
            $customerAttributeLogTableDetails = "amconnector_customerattribute_log_details";

            $customerLogTable = "amconnector_customer_log";
            $customerLogTableDetails = "amconnector_customer_log_details";

            $orderLogTable = "amconnector_order_log";
            $orderLogTableDetails = "amconnector_order_log_details";

            $shipmentLogTable = "amconnector_shipment_log";
            $shipmentLogTableDetails = "amconnector_shipment_log_details";

            $productConfiguratorLogTable = "amconnector_productconfigurator_log";
            $productConfiguratorLogTableDetails = "amconnector_productconfigurator_log_details";

            $productImageLogTable = "amconnector_productimage_log";
            $productImageLogTableDetails = "amconnector_productimage_log_details";

            /* Dump record of all logs and log_details table in one file */
            $successLog = 0;
            $errorLog = 0;
            try{
                if($archive == 'archive'){
                    $command = "mysqldump -u$username -p$password -h$hostname $database $taxCategoryLogTable $taxCategoryLogTableDetails $categoryLogTable $categoryLogTableDetails $productAttributeLogTable $productAttributeLogTableDetails $productLogTable $productLogTableDetails $productInventoryLogTable $customerAttributeLogTable $customerAttributeLogTableDetails $customerLogTable $customerLogTableDetails $orderLogTable $orderLogTableDetails $productConfiguratorLogTable $productConfiguratorLogTableDetails $productImageLogTable $productImageLogTableDetails $productPriceLogTable > $backupFile";
                    system($command, $result);
                }
                /* Log array */
                $logArray = array(
                    array($taxCategoryLogTable,$taxCategoryLogTableDetails),
                    array($categoryLogTable,$categoryLogTableDetails),
                    array($productAttributeLogTable,$productAttributeLogTableDetails),
                    array($productLogTable,$productLogTableDetails),
                    array($productInventoryLogTable,$productInventoryLogTableDetails),
                    array($customerAttributeLogTable,$customerAttributeLogTableDetails),
                    array($customerLogTable,$customerLogTableDetails),
                    array($orderLogTable,$orderLogTableDetails),
                    array($productConfiguratorLogTable,$productConfiguratorLogTableDetails),
                    array($productImageLogTable,$productImageLogTableDetails),
                    array($productPriceLogTable,$productPriceLogTableDetails)
                );
                /* Delete record from log and log_details tables */
                $sqlConnection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
                foreach($logArray as $logTable){
                    try{

                        $query = "select * from ".$this->resourceConfig->getTable($logTable[0])  ." where `created_at` <= '" . $cleanUpDate . "'" ;
                        $queryRead = $sqlConnection->query($query);
                        $row = $queryRead->fetchAll();
                        if (count($row)) {
                            foreach ($row as $result) {
                                $sqlConnection->query("delete from `" . $this->resourceConfig->getTable($logTable[0]). "` where `id`=" . $result['id']);
                                $sqlConnection->query("delete from `" . $this->resourceConfig->getTable($logTable[1]). "` where `sync_record_id`=" . $result['id']);
                            }
                        }
                    }catch(Exception $e){
                        $errorLog = 1;
                        $errorResult[] = $e->getMessage();
                    }
                }
                $successLog =1;
            }catch (Exception $e){
                $errorLog = 1;
                $errorResult[] = $e->getMessage();
            }

            /**
             * Email send if failure
             */

            $errorEmailData = $this->clientHelper->logErrorSenderEmail();
            $senderEmailId = $errorEmailData['email'];
            $senderEmailName = $errorEmailData['name'];
            $logEmailRecipient = $this->scopeConfig->getValue('amconnectorcommon/log_setting/log_email_recipient', $storeType, $storeId);
            if($logEmailRecipient == '' && $storeId == 1){
                $logEmailRecipient = $this->scopeConfig->getValue('amconnectorcommon/log_setting/log_email_recipient');
            }
            if($successLog == 1){
                $templateOptions = array('area' =>  \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 'store' => $storeId);
                $templateVars = array(
                    'store' => $storeId,
                    'message'   => $backupFile
                );
                $from = array(
                    'email' => $senderEmailId,
                    'name' => $senderEmailName
                );
                $this->inlineTranslation->suspend();
                $to = array(
                    'email' => $logEmailRecipient,
                    'name' => $logEmailRecipient
                );
                $transport = $this->_transportBuilder->setTemplateIdentifier('amconnector_log_cleaning_template')
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars($templateVars)
                    ->setFrom($from)
                    ->addTo($to)
                    ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            }

            /*Send email for Error in archived File*/
            if($errorLog == 1){
                $errorMessage = implode(",",$errorResult);
                $templateOptions = array('area' =>  \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 'store' => $storeId);
                $templateVars = array(
                    'store' => $storeId,
                    'message' => $errorMessage
                );
                $from = array(
                    'email' => $senderEmailId,
                    'name' => $senderEmailName
                );
                $this->inlineTranslation->suspend();
                $to = array(
                    'email' => $logEmailRecipient,
                    'name' => $logEmailRecipient
                );
                $transport = $this->_transportBuilder->setTemplateIdentifier('amconnector_log_cleaning_error_template')
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars($templateVars)
                    ->setFrom($from)
                    ->addTo($to)
                    ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            }

            /**
             * Delete the archived Data older than $deleteArchivedData
             */
            if($archive == 'archive'){
                $deleteArchivedData = $this->scopeConfig->getValue('amconnectorcommon/log_setting/delete_archive_days', $storeType, $storeId); // Number of days to delete old archived data
                if($deleteArchivedData  == ''  && $storeId == 1){
                    $deleteArchivedData = $this->scopeConfig->getValue('amconnectorcommon/log_setting/delete_archive_days');
                }
                $deleteDate = $this->date->date('Y-m-d', strtotime("-" . $deleteArchivedData . " days")); //date to delete old archived data
                $archiveFolderPath = $archivePath;                                      //Archive folder path
                $dir = new \DirectoryIterator($archiveFolderPath);                      //Sub-dir inside archive dir
                foreach ($dir as $fileinfo) {
                    if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                        $path = $archiveFolderPath."/".$fileinfo->getFilename();        //get sub-dir name
                        $stat = stat($path);
                        $folderCreateDate = $this->date->date('Y-m-d',$stat['ctime']);  //create date of sub-dir
                        /* If create date of sub-dir less than delete date of sub-dir then delete sub-dir */
                        if($folderCreateDate <= $deleteDate && is_dir($path)){
                            array_map('unlink', glob($path."/*"));                      //delete all files inside directory
                            rmdir($path);                                               //delete directory
                        }
                    }
                }
            }
        }
    }
}
