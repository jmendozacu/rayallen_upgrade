<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Testimonial\Controller\Adminhtml\Testimonial;

class NewAction extends \Kensium\Testimonial\Controller\Adminhtml\Testimonial
{
    /**
     * Create new banner
     *
     * @return void
     */
    public function execute()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
}
