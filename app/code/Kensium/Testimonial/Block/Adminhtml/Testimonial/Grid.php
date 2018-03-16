<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Testimonial\Block\Adminhtml\Testimonial;


class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * Testimonial resource collection factory
     *
     * @var \Kensium\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory
     */
    protected $_testimonialColFactory = null;

    /**
     * Testimonial config
     *
     * @var \Kensium\Testimonial\Model\Config
     */
    protected $_testimonialConfig = null;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Kensium\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory $testimonialColFactory
     * @param \Kensium\Testimonial\Model\Config $testimonialConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Kensium\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory $testimonialColFactory,
        \Kensium\Testimonial\Model\Config $testimonialConfig,
        \Kensium\Testimonial\Model\Status $status,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->_testimonialColFactory = $testimonialColFactory;
        $this->_testimonialConfig = $testimonialConfig;
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
        $this->setId('testimonialGrid');
        $this->setDefaultSort('testimonial_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('testimonial_filter');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_testimonialColFactory->create()->addStoresVisibility();
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
        $this->addColumn(
            'testimonial_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'testimonial_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'creation_time',
            [
                'header' => __('Created Date'),
                'index' => 'creation_time',
                'type' => 'datetime',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'align' => 'center',
                'width' => 1,
                'index' => 'status',
                'type' => 'options',
                'options' => [
                    0 => __('Pending'),
                    1 => __('Approved'),
                    2 => __('Rejected'),
                ]
            ]
        );
        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                [
                    'header' => __('Websites'),
                    'type' => 'store',
                    'index' => 'store_id',
                    'sortable' => false,
                    'store_view' => true
                ]
            );
        }
        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action options for this grid
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('testimonial_id');
        $this->getMassactionBlock()->setFormFieldName('testimonial');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('kensium_testimonial/testimonial/massDelete'),
                'confirm' => __('Are you sure you want to delete these testimonials?')
            ]
        );

        $statuses = $this->_status->toOptionArray();
        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change Status'),
                'url' => $this->getUrl('kensium_testimonial/testimonial/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );

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
        return $this->getUrl('kensium_testimonial/testimonial/edit', ['id' => $row->getTestimonialId()]);
    }

    /**
     * Define row click callback
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('kensium_testimonial/testimonial/grid', ['_current' => true]);
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
