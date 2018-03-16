<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Csvenvelopes\Controller\Adminhtml\Csvenvelopes;

class Edit extends \Kensium\Csvenvelopes\Controller\Adminhtml\Csvenvelopes
{
    /**
     * Edit action
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $csvenvelopesId = $this->getRequest()->getParam('id');
        $model = $this->_initCsvenvelopes('id');

        if (!$model->getId() && $csvenvelopesId) {
            $this->messageManager->addError(__('This csv envelop no longer exists.'));
            $this->_redirect('adminhtml/*/');
            return;
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Kensium_Csvenvelopes::kensium_csvenvelop');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Csv Envelop'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Csv Envelop')
        );

        $this->_addBreadcrumb(
            $csvenvelopesId ? __('Edit Csvenvelop') : __('New Csv Envelop'),
            $csvenvelopesId ? __('Edit Csvenvelop') : __('New Csv Envelop')
        );
        $this->_view->renderLayout();
    }
}
