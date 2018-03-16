<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Help\Controller\Adminhtml\Support;
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
     * @var
     */
    protected $adminSession;

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
        $this->adminSession = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $data = $this->adminSession->getFormData(true);
        $this->_view->loadLayout();
        $page = $this->resultPageFactory->create();
        $page->getLayout()->getBlock("head");

        $page->addBreadcrumb(__('AMCONNECTOR'), __('Acumatica - Magento Sync'));
        $page->getConfig()->getTitle()->prepend(__('Acumatica - Magento Sync'));
        $this->_view->renderLayout();
    }
}