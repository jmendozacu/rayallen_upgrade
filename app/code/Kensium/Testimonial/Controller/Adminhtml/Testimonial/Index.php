<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Testimonial\Controller\Adminhtml\Testimonial;

class Index extends \Kensium\Testimonial\Controller\Adminhtml\Testimonial
{
    /**
     * Testimonials list
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Kensium_Testimonial::kensium_testimonial');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Testimonials'));
        $this->_view->renderLayout();
    }
}
