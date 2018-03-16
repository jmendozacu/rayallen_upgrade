<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Attributemanager\Block\Adminhtml\Address;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $session;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var \Magento\Eav\Model\EntityFactory
     */
    protected $entityFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Eav\Model\EntityFactory $entityFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Eav\Model\EntityFactory $entityFactory,
        $data = array()
    )
    {
        $this->session = $context->getBackendSession();
        $this->backendHelper = $backendHelper;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->entityFactory = $entityFactory;
        parent::__construct($context, $backendHelper, $data = array());
    }

    /**
     * @return void
     */
    protected function _construct()
    {

        parent::_construct();
        $this->setId('addressattributemanagergrid');
        $this->setDefaultSort('attribute_code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }


    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $type='customer_address';
        $block='address';
        $this->type =$type;
        $this->block =$block;

        $collection = $this->attributeCollectionFactory->create()->setEntityTypeFilter( $this->entityFactory->create()->setType($type)->getTypeId() )->addFieldToFilter("is_user_defined", 1);
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }


    protected function _prepareColumns()
    {

        $this->addColumn('attribute_code', array(
            'header'=>'Attribute Code',
            'sortable'=>true,
            'index'=>'attribute_code'
        ));

        $this->addColumn('frontend_label', array(
            'header'=>'Attribute Label',
            'sortable'=>true,
            'index'=>'frontend_label'
        ));

        $this->addColumn('is_visible', array(
            'header'=>'Visible',
            'sortable'=>true,
            'index'=>'is_visible_on_front',
            'type' => 'options',
            'options' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'align' => 'center',
        ));

        $this->addColumn('is_global', array(
            'header'=>'Scope',
            'sortable'=>true,
            'index'=>'is_global',
            'type' => 'options',
            'options' => array(
                \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE =>'Store View',
                \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE =>'Website',
                \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL =>'Global',
            ),
            'align' => 'center',
        ));

        $this->addColumn('is_required', array(
            'header'=>'Required',
            'sortable'=>true,
            'index'=>'is_required',
            'type' => 'options',
            'options' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'align' => 'center',
        ));

        $this->addColumn('is_user_defined', array(
            'header'=>'System',
            'sortable'=>true,
            'index'=>'is_user_defined',
            'type' => 'options',
            'align' => 'center',
            'options' => array(
                '0' => 'Yes',   // intended reverted use
                '1' => 'No',    // intended reverted use
            ),
        ));

        $this->addExportType('*/*/exportCsv', 'CSV');
        $this->addExportType('*/*/exportXml', 'XML');
        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('type' => $this->type,'attribute_id' => $row->getAttributeId()));
    }
}
