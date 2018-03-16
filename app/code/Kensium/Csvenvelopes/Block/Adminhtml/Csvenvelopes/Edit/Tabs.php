<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Csvenvelopes\Block\Adminhtml\Csvenvelopes\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize csvenvelopes edit page tabs
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('csvenvelopes_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Csv Envelopes Information'));
    }
}
