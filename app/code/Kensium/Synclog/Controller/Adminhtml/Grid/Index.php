<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        // echo $syncId = $this->getRequest()->getParam('job_code', NULL);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $jobCode = $syncId = $this->getRequest()->getParam('job_code');

        if ($jobCode == 'customer') {
            $resultPage->addBreadcrumb(__('Sync Log'), __('Customer Sync Log'));
            $resultPage->getConfig()->getTitle()->prepend(__('Customer Sync Log'));
        }
        if ($jobCode == 'order') {
            $resultPage->addBreadcrumb(__('Sync Log'), __('Order Sync Log'));
            $resultPage->getConfig()->getTitle()->prepend(__('Order Sync Log'));
        }
        if ($jobCode == 'inventory') {
            $resultPage->addBreadcrumb(__('Sync Log'), __('Inventory Sync Log'));
            $resultPage->getConfig()->getTitle()->prepend(__('Inventory Sync Log'));
        }
        if ($jobCode == 'product') {
            $resultPage->addBreadcrumb(__('Sync Log'), __('Product Sync Log'));
            $resultPage->getConfig()->getTitle()->prepend(__('Product Sync Log'));
        }
        return $resultPage;
    }
}

