<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Sync;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Customer
     */
    protected $resourceModelCustomer;

    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $dataCollection;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Kensium\Amconnector\Model\SyncFactory
     */
    protected $syncFactory;

    protected $schedulerHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Kensium\Scheduler\Helper\Data $schedulerHelper
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Data\Collection $dataCollection
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Kensium\Amconnector\Model\SyncFactory $syncFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Kensium\Scheduler\Helper\Data $schedulerHelper,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Data\Collection $dataCollection,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Kensium\Amconnector\Model\SyncFactory $syncFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        $data = array()
    )
    {
        $this->session = $context->getBackendSession();
        $this->moduleManager = $moduleManager;
        $this->schedulerHelper = $schedulerHelper;
        $this->backendHelper = $backendHelper;
        $this->dataCollection = $dataCollection;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->syncFactory = $syncFactory;
        parent::__construct($context, $backendHelper, $data = array());
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('SyncGrid');
        $this->setDefaultSort('frontend_label');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('frontend_label');
        $storeId = $this->getRequest()->getParam('store');
        $this->session->setData('storeId', $storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $storeId = $this->getRequest()->getParam('store');
        if ($storeId == 0) {
            $storeId = 1;
        }
        $collection = $this->syncFactory->create()->getCollection()
            ->addFieldToFilter('sync_enable', 1)
            ->addFieldToFilter('store_id', $storeId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $viewHelper = $this->schedulerHelper;
        $this->addColumn("id", array(
            "header" => __("Sync ID"),
            "align" => "left",
            'width' => '25',
            "index" => "id",
        ));

        $this->addColumn("title", array(
            "header" => __("Sync Title"),
            "align" => "left",
            "index" => "title",
        ));

        $this->addColumn("sync_auto_cron", array(
            "header" => __("Auto Cron Sync"),
            "align" => "center",
            "index" => "sync_auto_cron",
            'width' => '150'

        ));

        $this->addColumn("last_sync_date", array(
            "header" => __("Last Sync Date"),
            "align" => "left",
            "index" => "last_sync_date",
            'width' => '150',
            'frame_callback' => array($viewHelper, 'decorateTimeFrameCallBack')

        ));

        $this->addColumn('status',
            array(
                'header' => __('Status'),
                'index' => 'status',
                'width' => '120',
                'getter' => 'getId',
                'renderer' => 'Kensium\Amconnector\Block\Adminhtml\Renderer\Status'
            ));

        $this->addColumn('action',
            array(
                'header' => __('Action'),
                'width' => '100',
                'align' => 'center',
                'type' => 'select',
                'getter' => 'getId',
                'renderer' => 'Kensium\Amconnector\Block\Adminhtml\Renderer\Action',
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));
        return parent::_prepareColumns();
    }
    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {

        return '';
    }
}
