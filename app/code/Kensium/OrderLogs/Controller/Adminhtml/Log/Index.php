<?php
/**
 * Copyright © 2016 Kensium . All rights reserved.
*/
namespace Kensium\OrderLogs\Controller\Adminhtml\Log;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
        
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Order Error Logs'), __('Order Error Logs'));
        $resultPage->getConfig()->getTitle()->prepend(__('Order Error Logs'));
        return $resultPage;
    }
}
