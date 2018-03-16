<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Csvenvelopes\Block\Adminhtml\Csvenvelopes;


class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * Csvenvelopes resource collection factory
     *
     * @var \Kensium\Csvenvelopes\Model\ResourceModel\Csvenvelopes\CollectionFactory
     */
    protected $_csvenvelopesColFactory = null;

    /**
     * Csvenvelopes config
     *
     * @var \Kensium\Csvenvelopes\Model\Config
     */
    protected $_csvenvelopesConfig = null;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Kensium\Csvenvelopes\Model\ResourceModel\Csvenvelopes\CollectionFactory $csvenvelopesColFactory
     * @param \Kensium\Csvenvelopes\Model\Config $csvenvelopesConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Kensium\Csvenvelopes\Model\ResourceModel\Csvenvelopes\CollectionFactory $csvenvelopesColFactory,
        \Kensium\Csvenvelopes\Model\Config $csvenvelopesConfig,
        \Kensium\Csvenvelopes\Model\Status $status,
        $data = []
    ) {
        parent::__construct($context, $backendHelper, []);
        $this->_csvenvelopesColFactory = $csvenvelopesColFactory;
        $this->_csvenvelopesConfig = $csvenvelopesConfig;
        $this->_status = $status;
    }

    /**
     * Set defaults
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('csvenvelopesGrid');
        $this->setDefaultSort('csvenvelopes_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('csvenvelopes_filter');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_csvenvelopesColFactory->create()->addStoresVisibility();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('csvenvelopes_id', array(
            'header'    => __('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'csvenvelopes_id',
        ));

        $this->addColumn('envcode', array(
            'header'    => __('Envelope Code'),
            'align'     => 'left',
            'index'     => 'envcode',
        ));

        $this->addColumn('enventity', array(
            'header'    => __('Entity Name'),
            'align'     => 'left',
            'index'     => 'enventity',
        ));

        $this->addColumn('envtype', array(
            'header'    => __('Envelope Type'),
            'align'     => 'left',
            'index'     => 'envtype',
        ));

        $this->addColumn('envversion', array(
            'header'    => __('Envelope Version'),
            'align'     => 'left',
            'index'     => 'envversion',
        ));

        $this->addColumn('methodname', array(
            'header'    => __('Envelope Method'),
            'align'     => 'left',
            'index'     => 'methodname',
        ));

        $this->addColumn('envelope', array(
            'header'    => __('Envelope'),
            'align'     => 'left',
            'index'     => 'envelope',
            'column_css_class'=>'no-display',
            'header_css_class'=>'no-display',
        ));

        $this->addColumn('action',array(
            'header' => __('Action'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => __('Edit'),
                    'url' => array('base'=> '*/*/edit'),
                    'field' => 'id'
                )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();//get the parent class buttons
        $addButton = $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Button') //create the add button
        ->setData(array(
                'label'     => __('Re-Generate CSV'),
                'onclick'   => "setLocation('".$this->getUrl('*/*/generate')."')",
                'class'   => 'task'
            ))->toHtml();
        return $addButton.$html;
    }

    /**
     * Prepare mass action options for this grid
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('csvenvelopes_id');
        $this->getMassactionBlock()->setFormFieldName('csvenvelopes');





        return $this;
    }

    /**
     * Grid row URL getter
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('kensium_csvenvelopes/csvenvelopes/edit', ['id' => $row->getCsvenvelopesId()]);
    }

    /**
     * Define row click callback
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('kensium_csvenvelopes/csvenvelopes/grid', ['_current' => true]);
    }

    /**
     * Add store filter
     *
     * @param \Magento\Backend\Block\Widget\Grid\Column  $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getIndex() == 'stores') {
            $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }





}
