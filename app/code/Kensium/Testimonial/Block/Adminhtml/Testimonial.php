<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Testimonial\Block\Adminhtml;

class Testimonial extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize testimonials manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_testimonial';
        $this->_blockGroup = 'Kensium_Testimonial';
        $this->_headerText = __('Testimonials');
        $this->_addButtonLabel = __('Add Testimonial');
        parent::_construct();
    }
}
