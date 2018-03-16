<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Orderstatus\Controller\Adminhtml\Orderstatus;

class Edit extends \Kensium\Orderstatus\Controller\Adminhtml\Orderstatus
{

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

        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Kensium\Orderstatus\Model\Orderstatus');

        if ($id) {
            $model->load($id);
            if (!$model->getOrderstatusId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('kensium_orderstatus/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_kensium_orderstatus_orderstatus', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('orderstatus_orderstatus_edit');
        $this->_view->renderLayout();
    }
}
