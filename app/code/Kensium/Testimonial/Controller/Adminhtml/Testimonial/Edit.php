<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Testimonial\Controller\Adminhtml\Testimonial;

class Edit extends \Kensium\Testimonial\Controller\Adminhtml\Testimonial
{
    /**
     * Edit action
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $testimonialId = $this->getRequest()->getParam('id');
        $model = $this->_initTestimonial('id');

        if (!$model->getId() && $testimonialId) {
            $this->messageManager->addError(__('This testimonial no longer exists.'));
            $this->_redirect('adminhtml/*/');
            return;
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Kensium_Testimonial::kensium_testimonial');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Testimonials'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Testimonial')
        );

        $this->_addBreadcrumb(
            $testimonialId ? __('Edit Testimonial') : __('New Testimonial'),
            $testimonialId ? __('Edit Testimonial') : __('New Testimonial')
        );
        $this->_view->renderLayout();
    }
}
