<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Testimonial\Controller\Adminhtml\Testimonial;

class Grid extends \Kensium\Testimonial\Controller\Adminhtml\Testimonial
{
    /**
     * Render Testimonial grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
