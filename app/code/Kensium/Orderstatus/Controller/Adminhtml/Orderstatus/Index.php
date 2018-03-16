<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Orderstatus\Controller\Adminhtml\Orderstatus;

class Index extends \Kensium\Orderstatus\Controller\Adminhtml\Orderstatus
{
    /**
     * Items list.
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
        $resultPage->setActiveMenu('Kensium_Orderstatus::orderstatus');
        $resultPage->getConfig()->getTitle()->prepend(__('Acumatica Order Status'));
        $resultPage->addBreadcrumb(__('Kensium'), __('Kensium'));
        $resultPage->addBreadcrumb(__('Orderstatus'), __('Order Status'));
        return $resultPage;
    }
}
