<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Scheduler\Controller\Adminhtml\Cron;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class Grid extends \Magento\Backend\App\Action
{

	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param \Kensium\Amconnector\Model\ResourceModel\Licensecheck $resourceModelLicense
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $resourceModelLicense,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceModelLicense = $resourceModelLicense;
    }
    /**
     * Check the permission to run it
     *
     * @return bool
     */
   /*  protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    } */

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $store = $this->getRequest()->getParam('store');
        if($store == '')
            $storeId = 1;
        else
            $storeId = $store;

        $licenseCheck = $this->resourceModelLicense->validateLicense($storeId);
        if($licenseCheck)
            return $licenseCheck;

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Available tasks'), __('Available tasks
	'));
        $resultPage->getConfig()->getTitle()->prepend(__('Available tasks
	'));
        return $resultPage;
    }
}