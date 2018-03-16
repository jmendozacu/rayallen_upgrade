<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Help\Block\Adminhtml\Support;

/**
 * Class Edit
 * @package Kensium\Help\Block\Adminhtml\Support
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Kensium_Help';
        $this->_controller = 'adminhtml_support';
        $this->buttonList->update('save', 'label', __('Submit'));
        $this->buttonList->remove('delete');
        $this->buttonList->remove('back');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Support Information');
    }
}
