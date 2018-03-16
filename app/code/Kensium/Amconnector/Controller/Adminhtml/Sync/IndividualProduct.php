<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Sync;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
class IndividualProduct extends \Magento\Backend\App\Action
{
    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $amconnectorHelper;
    /**
     * @var
     */
    protected $productHelper;

    /**
     * @var
     */
    protected $resourceModelSync;


    /**
     * @param Context $context
     * @param \Kensium\Amconnector\Helper\Product $productHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync
     */
    public function __construct(
        Context $context,
        \Kensium\Amconnector\Helper\Product $productHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync
    )
    {
        parent::__construct($context);
        $this->productHelper = $productHelper;
        $this->resourceModelSync = $resourceModelSync;
    }

    /**
     * Index action
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id', NULL);
        $entity = 'product';
        $storeId = $this->getRequest()->getParam('store_id', NULL);
        $syncId = $this->resourceModelSync->getSyncId($entity, $storeId);
        try{
            $this->productHelper->getProductSync('INDIVIDUAL', 'MANUAL', NULL, NULL, $storeId, $productId);
        }catch (Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
