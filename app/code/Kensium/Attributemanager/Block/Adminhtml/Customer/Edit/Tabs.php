<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Attributemanager\Block\Adminhtml\Customer\Edit;

/**
 * Class Tabs
 * @package Kensium\Attributemanager\Block\Adminhtml\Customer\Edit
 */
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
        $this->setId('customerAttribute_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main',
            [
                'label' => __('Properties'),
                'title' => __('Properties'),
                'content' => $this->getLayout()->createBlock('Kensium\Attributemanager\Block\Adminhtml\Customer\Edit\Tab\Main')->toHtml(),//$this->getChildHtml('main'),
                'active' => true
            ]
        );
        $this->addTab(
            'labels',
            [
                'label' => __('Manage Labels / Options'),
                'title' => __('Manage Labels / Options'),
                'content' => $this->getLayout()->createBlock('Kensium\Attributemanager\Block\Adminhtml\Customer\Edit\Tab\Options')->toHtml()
            ]
        );

        return parent::_beforeToHtml();
    }

}
