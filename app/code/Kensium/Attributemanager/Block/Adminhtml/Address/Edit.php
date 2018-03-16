<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Attributemanager\Block\Adminhtml\Address;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        $data = []
    ) {
        $this->_registry = $registry;
        parent::__construct($context, []);
    }

    /**
     * Initialize csvenvelopes edit page. Set management buttons
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_address';
        $this->_blockGroup = 'Kensium_Attributemanager';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Attribute'));

        $this->buttonList->add(
            'save_and_edit_button',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            100
        );


        if (! $this->_registry->registry('attributemanager_data')->getIsUserDefined()) {
            $this->buttonList->remove('delete');
        } else {
            $this->buttonList->update('delete', 'label', __('Delete Attribute'));
        }

       // $this->removeButton('delete');
        //$this->removeButton('reset');
        //$this->removeButton("save_and_edit_button");
    }

    /**
     * Get current loaded attribute ID
     *
     * @return mixed
     */
    public function getAttributeId()
    {
        return $this->_registry->registry('attributemanager_data')->getId();
    }

    /**
     * Get header text for attribute edit page
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getHeaderText()
    {
        if ($this->_registry->registry('attributemanager_data')->getId()) {
            return $this->escapeHtml($this->_registry->registry('attributemanager_data')->getName());
        } else {
            return __('New Attribute');
        }
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('attributemanager/address/save');
    }
}
