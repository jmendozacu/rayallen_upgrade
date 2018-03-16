<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector;

/**
 * Class CronSync
 * @package Kensium\Amconnector
 */
class CronSync
{

    /**
     * @var Helper\Customer
     */
    protected $customerHelper;

    /**
     * @var Model\ResourceModel\Sync
     */
    protected $syncResourceModel;

    /**
     * @var Helper\Category
     */
    protected $categoryHelper;

    /**
     * @var Model\ResourceModel\Inventory
     */
    protected $inventoryResourceModel;

    /**
     * @var Helper\ProductImage
     */
    protected $productImageHelper;

    /**
     * @var Helper\Product
     */
    protected $productHelper;

    /**
     * @var Helper\OrderSync
     */
    protected $amconnectorOrderHelper;

    /**
     * @param Helper\Customer $customerHelper
     * @param Model\ResourceModel\Sync $syncResourceModel
     * @param Helper\Category $categoryHelper
     * @param Model\ResourceModel\Inventory $inventoryResourceModel
     * @param Helper\ProductImage $productImageHelper
     * @param Helper\Product $productHelper
     * @param Helper\OrderSync $amconnectorOrderHelper
     */
    public function __construct(
        \Kensium\Amconnector\Helper\Customer $customerHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Amconnector\Helper\Category $categoryHelper,
        \Kensium\Amconnector\Model\ResourceModel\Inventory $inventoryResourceModel,
        \Kensium\Amconnector\Helper\ProductImage $productImageHelper,
        \Kensium\Amconnector\Helper\Product $productHelper,
        \Kensium\Amconnector\Helper\OrderSync $amconnectorOrderHelper
    )
    {
        $this->customerHelper = $customerHelper;
        $this->syncResourceModel = $syncResourceModel;
        $this->categoryHelper = $categoryHelper;
        $this->inventoryResourceModel = $inventoryResourceModel;
        $this->productImageHelper = $productImageHelper;
        $this->productHelper = $productHelper;
        $this->amconnectorOrderHelper = $amconnectorOrderHelper;
    }


    /**
     * @param $schedule
     * Customer sync cron
     */
    public function customerSync($schedule)
    {
        $scheduleId =  $schedule->getScheduleId();
        $storeIds = $this->syncResourceModel->getAllStoreIdsByLicence();
        foreach($storeIds as $storeId)
        {
            if($storeId != 0) {
                $getSyncId = $this->syncResourceModel->getAutoSyncId('customer', $storeId);
                if ($getSyncId != '') {
                    $this->customerHelper->getcustomerSync('COMPLETE','AUTO',$getSyncId, $scheduleId, $storeId,NULL,NULL,NULL);
                }
            }
        }
    }

    /**
     * @param $schedule
     * Category sync cron
     */
    public function categorySync($schedule)
    {
        $scheduleId =  $schedule->getScheduleId();
        $storeIds = $this->syncResourceModel->getAllStoreIdsByLicence();
        foreach($storeIds as $storeId)
        {
            if($storeId != 0) {
                $getSyncId = $this->syncResourceModel->getAutoSyncId('category', $storeId);
                if ($getSyncId != '') {
                    $this->categoryHelper->getCategorySync('COMPLETE','AUTO',$getSyncId, $scheduleId, $storeId,NULL,NULL,NULL);
                }
            }
        }
    }

    /**
     * @param $schedule
     * Failed order sync cron
     */
    public function failedOrderSync($schedule)
    {
        $scheduleId =  $schedule->getScheduleId();
        $storeIds = $this->syncResourceModel->getAllStoreIdsByLicence();
        foreach($storeIds as $storeId)
        {
            if($storeId != 0) {
                $getSyncId = $this->syncResourceModel->getAutoSyncId('category', $storeId);
                if ($getSyncId != '') {
                    $this->amconnectorOrderHelper->getOrderSync('COMPLETE','AUTO',$getSyncId, $scheduleId, $storeId,NULL,$failedOrderFlag = 1);
                }
            }
        }
    }

    /**
     * @param $schedule
     * Order sync cron
     */
    public function orderSync($schedule)
    {
        $scheduleId =  $schedule->getScheduleId();
        $storeIds = $this->syncResourceModel->getAllStoreIdsByLicence();
        foreach($storeIds as $storeId)
        {
            if($storeId != 0) {
                $getSyncId = $this->syncResourceModel->getAutoSyncId('category', $storeId);
                if ($getSyncId != '') {
                    $this->amconnectorOrderHelper->getOrderSync('COMPLETE','AUTO',$getSyncId, $scheduleId, $storeId,NULL,$failedOrderFlag = 1);
                }
            }
        }
    }

    /**
     * @param $schedule
     * Product sync cron
     */
    public function productSync($schedule)
    {
        $scheduleId =  $schedule->getScheduleId();
        $storeIds = $this->syncResourceModel->getAllStoreIdsByLicence();
        foreach($storeIds as $storeId)
        {
            if($storeId != 0) {
                $getSyncId = $this->syncResourceModel->getAutoSyncId('product', $storeId);
                if ($getSyncId != '') {
                    $this->productHelper->getProductSync('COMPLETE','AUTO',$getSyncId, $scheduleId, $storeId);
                }
            }
        }
    }

    /**
     * @param $schedule
     * Product configorator sync cron
     */
    public function productConfiguratorSync($schedule)
    {
        $scheduleId =  $schedule->getScheduleId();
        $storeIds = $this->syncResourceModel->getAllStoreIdsByLicence();
        foreach($storeIds as $storeId)
        {
            if($storeId != 0) {
                $getSyncId = $this->syncResourceModel->getAutoSyncId('productconfigurator', $storeId);
                if ($getSyncId != '') {
                    //$this->productHelper->getProductConfiguratorSync('COMPLETE','AUTO',$getSyncId, $scheduleId, $storeId);
                }
            }
        }
    }

    /**
     * @param $schedule
     * Product image sync cron
     */
    public function productImageSync($schedule)
    {
        $scheduleId =  $schedule->getScheduleId();
        $storeIds = $this->syncResourceModel->getAllStoreIdsByLicence();
        foreach($storeIds as $storeId)
        {
            if($storeId != 0) {
                $getSyncId = $this->syncResourceModel->getAutoSyncId('productimage', $storeId);
                if ($getSyncId != '') {
                    //$this->productImageHelper->syncProductImage('COMPLETE','AUTO',$getSyncId, $scheduleId, $storeId);
                }
            }
        }
    }

    /**
     * @param $schedule
     * Product inventory sync cron
     */
    public function productInventorySync($schedule)
    {
        $scheduleId =  $schedule->getScheduleId();
        $storeIds = $this->syncResourceModel->getAllStoreIdsByLicence();
        foreach($storeIds as $storeId)
        {
            if($storeId != 0) {
                $getSyncId = $this->syncResourceModel->getAutoSyncId('productinventory', $storeId);
                if ($getSyncId != '') {
                    $this->inventoryResourceModel->syncProductInventoryAndPrice('COMPLETE','AUTO',$getSyncId, $scheduleId, $storeId);
                }
            }
        }
    }
}
