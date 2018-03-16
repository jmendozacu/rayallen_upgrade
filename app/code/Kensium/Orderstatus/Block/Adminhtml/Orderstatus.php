<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Orderstatus\Block\Adminhtml;

class Orderstatus extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'orderstatus';
        $this->_blockGroup = 'Kensium_Orderstatus';
        $this->_headerText = __('Order Status');
        $this->_addButtonLabel = __('Add New Status');
        parent::_construct();
    }
}
