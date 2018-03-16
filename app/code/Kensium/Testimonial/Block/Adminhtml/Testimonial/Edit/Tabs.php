<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Testimonial\Block\Adminhtml\Testimonial\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize testimonial edit page tabs
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('testimonial_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Testimonial Information'));
    }
}
