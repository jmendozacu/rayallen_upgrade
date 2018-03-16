<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class SaveProductUpdatedAt implements ObserverInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Product
     */
    protected $productResourceModel;

    /**
     * @param LoggerInterface $logger
     * @param \Kensium\Amconnector\Model\ResourceModel\Product $productResourceModel
     */
    public function __construct(

        LoggerInterface $logger,
        \Kensium\Amconnector\Model\ResourceModel\Product $productResourceModel,
        \Magento\Eav\Model\Entity $entityModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->logger = $logger;
        $this->productResourceModel = $productResourceModel;
        $this->entityModel = $entityModel;
        $this->_storeManager = $storeManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $productData = $observer->getEvent()->getProduct();
        //$this->productResourceModel->updateProductDate($productData->getRowId());
    }
}
