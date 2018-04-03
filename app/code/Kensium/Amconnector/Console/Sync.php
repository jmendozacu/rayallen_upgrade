<?php
/**
 *
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Magento\Framework\App\State;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Psr\Log\LoggerInterface as Logger;

/**
 * kensium  sync
 */
class Sync extends Command
{
    const ENTITY = "entity";
    const SYNC_ID = "syncId";
    const STORE_ID = "storeId";
    const AUTO_SYNC = "autoSync";
    const SYNC_TYPE = "syncType";
    const FLAG = "flag";
    const INDIVIDUAL = "individual";

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var State
     */
    protected $state;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var ConfigLoader
     */
    protected $configLoader;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Kensium\Amconnector\Helper\Customer
     */
    protected $helperCustomer;
    /**
     * @var \Kensium\Amconnector\Helper\Category
     */
    protected $helperCategory;
    /**
     * @var \Kensium\Amconnector\Helper\OrderSync
     */
    protected $orderSync;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\CustomerAttribute
     */
    protected $customerAttribute;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\TaxCategory
     */
    protected $taxCategory;
    /**
     * @var \Kensium\Amconnector\Helper\Inventory
     */
    protected $inventoryHelper;
    /**
     * @var \Kensium\Amconnector\Helper\Product
     */
    protected $productHelper;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\ProductAttribute
     */
    protected $prodAttribute;
    /**
     * @var
     */
    protected $inventoryResourceModel;
    /**
     * @var \Kensium\Amconnector\Helper\TestConnection
     */
    protected $testConnectionHelper;

    /**
     * @var \Kensium\Amconnector\Helper\ProductConfigurator
     */
    protected $productConfiguratorHelper;

    /**
     * @var \Kensium\Amconnector\Helper\Merchandise
     */
    protected  $merchandiseHelper;

    /**
     * @var \Kensium\Amconnector\Helper\Merchandise
     */
    protected $merchandise;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param State $state
     * @param ConfigLoader $loader
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Kensium\Amconnector\Model\ResourceModel\CustomerAttribute $customerAttribute
     * @param \Kensium\Amconnector\Helper\Product $productHelper
     * @param \Kensium\Amconnector\Helper\ProductConfigurator $productConfiguratorHelper
     * @param \Kensium\Amconnector\Helper\Merchandise $merchandiseHelper
     * @param \Kensium\Amconnector\Helper\Customer $helperCustomer
     * @param \Kensium\Amconnector\Helper\OrderSync $orderSync
     * @param \Kensium\Amconnector\Helper\TestConnection $testConnectionHelper
     * @param \Kensium\Amconnector\Helper\Category $helperCategory
     * @param \Kensium\Amconnector\Model\ResourceModel\TaxCategory $taxCategory
     * @param \Kensium\Amconnector\Model\ResourceModel\Inventory $inventoryResourceModel
     * @param \Kensium\Amconnector\Model\ResourceModel\ProductAttribute $prodAttribute
     * @param \Kensium\Amconnector\Helper\Merchandise $merchandise
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        State $state,
        ConfigLoader $loader,
        \Kensium\Amconnector\Model\ResourceModel\CustomerAttribute $customerAttribute,
        \Kensium\Amconnector\Helper\Product $productHelper,
        \Kensium\Amconnector\Helper\ProductConfigurator $productConfiguratorHelper,
        \Kensium\Amconnector\Helper\Customer $helperCustomer,
        \Kensium\Amconnector\Helper\ProductImage $productImage,
        \Kensium\Amconnector\Helper\OrderSync $orderSync,
        \Kensium\Amconnector\Helper\TestConnection $testConnectionHelper,
        \Kensium\Amconnector\Helper\Category $helperCategory,
        \Kensium\Amconnector\Model\ResourceModel\TaxCategory $taxCategory,
        \Kensium\Amconnector\Model\ResourceModel\Inventory $inventoryResourceModel,
        \Kensium\Amconnector\Model\ResourceModel\ProductAttribute $prodAttribute,
        \Kensium\Amconnector\Helper\ShipmentSync $shipmentSync,
        Logger $logger
    )
    {
        $this->registry = $registry;
        $this->helperCustomer = $helperCustomer;
        $this->helperCategory = $helperCategory;
        $this->customerAttribute = $customerAttribute;
        $this->state = $state;
        $this->configLoader = $loader;
        $this->productHelper = $productHelper;
        $this->productConfiguratorHelper = $productConfiguratorHelper;
        $this->orderSync = $orderSync;
        $this->shipmentSync = $shipmentSync;
        $this->testConnectionHelper = $testConnectionHelper;
        $this->taxCategory = $taxCategory;
        $this->inventoryResourceModel = $inventoryResourceModel;
        $this->prodAttribute = $prodAttribute;
        $this->logger = $logger;
        $this->productImage = $productImage;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('kensium:sync')
            ->setDescription('Sync customers')->setDefinition([
                new InputArgument(self::ENTITY, InputArgument::REQUIRED, 'entity'),
                new InputArgument(self::SYNC_ID, InputArgument::REQUIRED, 'syncId'),
                new InputArgument(self::STORE_ID, InputArgument::REQUIRED, 'storeId'),
                new InputArgument(self::AUTO_SYNC, InputArgument::REQUIRED, 'autoSync'),
                new InputArgument(self::SYNC_TYPE, InputArgument::REQUIRED, 'syncType'),
                new InputArgument(self::FLAG, InputArgument::REQUIRED, 'flag'),
                new InputArgument(self::INDIVIDUAL, InputArgument::REQUIRED, 'individual'),
            ]);
        //parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->registry->register('isSecureArea', true);
        $this->state->setAreaCode('adminhtml');
        //$this->objectManager->configure($this->configLoader->load('adminhtml'));
        $entity = $input->getArgument(self::ENTITY);
        $storeId = $input->getArgument(self::STORE_ID);
        $autoSync = $input->getArgument(self::AUTO_SYNC);
        $syncType = $input->getArgument(self::SYNC_TYPE);
        $syncId = $input->getArgument(self::SYNC_ID);
        $flag = $input->getArgument(self::FLAG);
        $scheduleId = '';
        $individual = $input->getArgument(self::INDIVIDUAL);
        $individualCustomerId = '';
        $individualProductId = '';
        if ($entity == 'customer')
            $this->helperCustomer->getcustomerSync($autoSync, $syncType, $syncId, $scheduleId, $storeId, $flag = 'CUSTOMER', $orderData = NULL, $individualCustomerId);
        if ($entity == 'category')
            $this->helperCategory->getCategorySync($autoSync,$syncType, $syncId, $scheduleId,$storeId,$individual);
            if ($entity == 'customerattribute') {
                $this->customerAttribute->syncCustomerAttributes($autoSync, $syncType, $syncId, $scheduleId, $storeId);
            }
        if ($entity == 'order') {

            if($flag == 'NULL'){
                $flag = '';
            }
            // $flag is to set failed order flag
            if ($flag == 1)
                $entity = 'failedorder';
            $orderId = $individual;
            $this->orderSync->getOrderSync($autoSync, $syncType, $syncId, $storeId, $orderId = NULL, $flag);
        }

        if ($entity == 'orderShipment') {
            $this->shipmentSync->getShipmentSync($autoSync, $syncType, $syncId, $storeId, NULL, $flag);
        }

        if ($entity == "failedorder") {
            $this->orderSync->getOrderSync($autoSync, $syncType, $syncId, $storeId, $orderId = NULL, $flag=1);
        }
        if ($entity == "taxCategory") {
            $this->taxCategory->getTaxCategorySync($autoSync, $syncType, $syncId, $scheduleId, $storeId);
        }
        if ($entity == "productinventory") {
            $this->inventoryResourceModel->syncProductInventoryAndPrice($autoSync, $syncType, $syncId, $scheduleId, $storeId);
        }
        if ($entity == "product") {
            $this->productHelper->getProductSync($autoSync,$syncType, $syncId, $scheduleId ,$storeId,$individual);
        }
        if ($entity == "productConfigurator") {
            $this->productConfiguratorHelper->getProductConfiguratorSync($autoSync,$syncType, $syncId, $scheduleId ,$storeId,$individual);
        }
        if ($entity == "merchandise") {
            $this->merchandiseHelper->getMerchandiseSync($autoSync,$syncType, $syncId, $scheduleId ,$storeId,$individual);
        }
        if ($entity == "productimage") {
            $this->productImage->syncProductImage($autoSync, $syncType, $syncId, $scheduleId, $storeId);
        }
        if ($entity == "productattribute") {
            $this->prodAttribute->syncProductAttributes($autoSync, $syncType, $syncId, $scheduleId, $storeId);
        }
        if ($entity == "testConnection") {
            $serverUrl = $syncId;
            $userName = $storeId;
            $password = $autoSync;
            $confirmPassword = $syncType;
            $company = $flag;
            if($serverUrl == "NULL")
                $serverUrl = '';
            if($userName == "NULL")
                $userName = '';
            if($password == "NULL")
                $password = '';
            if($confirmPassword == "NULL")
                $confirmPassword = '';
            if($company == "NULL")
                $company = '';
            $storeId = $individual;
            $this->testConnectionHelper->testConnection($serverUrl,$userName,$password,$confirmPassword,$company,$storeId);
        }
        if($entity == "merchandiseapproval")
        {
            $_storeId = $syncId;
            $configurableId = $storeId;
            $syncLogID = $autoSync;
            $this->merchandise->updateProductInAcumatica($_storeId,$configurableId,$syncLogID);
        }
        if($entity == "markdownpriceapproval")
        {
            $_storeId = $syncId;
            $configurableId = $storeId;
            $syncLogID = $autoSync;
            $this->merchandise->updateProductPriceInAcumatica($_storeId,$configurableId,$syncLogID);
        }
        if($entity == "acumaticaBaseData"){
            $storeId = $syncId;
            $this->testConnectionHelper->acumaticaBaseData($storeId);
        }
    }
}
