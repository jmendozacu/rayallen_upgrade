<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Csvenvelopes\Block\Adminhtml;

class Csvenvelopes extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize csvenvelopess manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_csvenvelopes';
        $this->_blockGroup = 'Kensium_Csvenvelopes';
        $this->_headerText = __('Csvenvelopes');
        $this->_addButtonLabel = __('Add Envelope');
        parent::_construct();
    }

}
