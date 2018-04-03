<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Attributemanager\Block\Adminhtml;

/**
 * Class Address
 * @package Kensium\Attributemanager\Block\Adminhtml
 */
class Address extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_template = 'customer/index.phtml';

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        $data = array()
    )
    {
        parent::__construct($context, $data = array());
    }

    public function _construct()
    {
        $this->_controller = 'adminhtml_address';
        $this->_blockGroup = 'attributemanager';
        $this->_headerText = 'Manage Customer Address Attribute';
        $this->_addButtonLabel = 'Add New Attribute';
        parent::_construct();

        $this->addButton('add', array(
            'label'     => 'Add New Attribute',
            'onclick'   => 'setLocation(\''.$this->getUrl('*/*/newAttribute', array('type' => 'customer_address','attribute_id'=>0)).'\')',
            'class'     => 'action-default scalable add primary',
            'id'        => 'add',
        ));
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Kensium\Attributemanager\Block\Adminhtml\Address\Grid', 'customer_address_attribute')
        );
        $this->_template='customer/grid.phtml';
        return parent::_prepareLayout();
    }


    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
