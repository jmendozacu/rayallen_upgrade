<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Scheduler\Block\Adminhtml\Scheduler;
/**
 * Class Grid
 * @package Kensium\Scheduler\Block\Adminhtml\Scheduler
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
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
     * @var \Magento\Framework\Dataobject
     */
    protected $dataObject;
    /**
     * @var \Kensium\Scheduler\Model\SchedulerFactory
     */
    protected $schedulerFactory;
    /**
     * @var \Kensium\Scheduler\Helper\Data
     */
    protected $schedulerHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Kensium\Scheduler\Helper\Data $schedulerHelper
     * @param \Magento\Framework\Data\Collection $dataCollection
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Kensium\Scheduler\Model\SchedulerFactory $schedulerFactory
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Cron\Model\ConfigInterface $cronConfig
     * @param \Magento\Framework\Dataobject $dataObject
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Kensium\Scheduler\Helper\Data $schedulerHelper,
        \Magento\Framework\Data\Collection $dataCollection,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Kensium\Scheduler\Model\SchedulerFactory $schedulerFactory,
        \Magento\Framework\Dataobject $dataObject,
        $data = array()
    )
    {

        $this->session = $context->getBackendSession();
        $this->schedulerHelper = $schedulerHelper;
        $this->backendHelper = $backendHelper;
        $this->dataCollection = $dataCollection;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->dataObject = $dataObject;
        $this->schedulerFactory = $schedulerFactory;
        parent::__construct($context, $backendHelper, $data = array());
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('SynclogsGrid');
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
        $filterCode = $this->getRequest()->getParam('filtercode');
        if ($storeId == 0) {
            $storeId = 1;
        }

        $collection = $this->schedulerFactory->create()->getCollection()->setOrder('created_at', 'desc')->addFieldToFilter('store_id', $storeId);
        if ($filterCode != '')
            $collection->addFieldToFilter('job_code', $filterCode);

        $this->setCollection($collection);
        return parent::_prepareCollection();

    }


    protected function _prepareColumns()
    {
        $viewHelper = $this->schedulerHelper;
        $this->addColumn("job_code", array(
            "header" => __("Code"),
            "align" => "left",
            'width' => '25',
            "index" => "job_code",
            'type' => 'options',
            'options' => array('order' => 'order','failedOrder' => 'Failed Order','orderShipment' => 'Shipment', 'customer' => 'Customer', 'taxCategory' => 'Tax Category', 'productinventory' => 'Inventory', 'productattribute' => 'Product Attribute','customerattribute' => 'Customer Attribute','product' => 'product','productConfigurator'=>'Product Configurator','productimage' => 'Product Image','category' => 'Category','merchandise' => 'Merchandise',)
        ));

        $this->addColumn("created_at", array(
            "header" => __("Created"),
            "align" => "left",
            "index" => "created_at",
            'width' => '150',
            'type' => 'timestamp',
            'frame_callback' => array($viewHelper, 'decorateTimeFrameCallBack')
        ));

        $this->addColumn("scheduled_at", array(
            "header" => __("Scheduled"),
            "align" => "left",
            "index" => "scheduled_at",
            'width' => '150',
            'type' => 'timestamp',
            'frame_callback' => array($viewHelper, 'decorateTimeFrameCallBack')
        ));

        $this->addColumn("executed_at", array(
            "header" => __("Executed"),
            "align" => "left",
            "index" => "executed_at",
            'width' => '150',
            'type' => 'timestamp',
            'frame_callback' => array($viewHelper, 'decorateTimeFrameCallBack')
        ));

        $this->addColumn("finished_at", array(
            "header" => __("Finished"),
            "align" => "left",
            "index" => "finished_at",
            'width' => '150',
            'type' => 'timestamp',
            'frame_callback' => array($viewHelper, 'decorateTimeFrameCallBack')
        ));

        $this->addColumn("run_mode", array(
            "header" => __("Run Mode"),
            "align" => "left",
            "index" => "run_mode",
            'width' => '150',
            'type' => 'options',
            'options' => array('Automatic' => 'Automatic', 'Manual' => 'Manual')
        ));

        $this->addColumn("auto_sync", array(
            "header" => __("Sync Type"),
            "align" => "left",
            "index" => "auto_sync",
            'width' => '150',
            'type' => 'options',
            'options' => array('Complete' => 'Complete', 'Individual' => 'Individual')
        ));

        $this->addColumn("messages", array(
            "header" => __("Messages"),
            "align" => "left",
            "index" => "messages",
            'width' => '150'
        ));

        $this->addColumn("status", array(
            "header" => __("Status"),
            "align" => "center",
            "index" => "status",
            'width' => '150',
            'type' => 'options',
            'renderer' => 'Kensium\Scheduler\Block\Adminhtml\Scheduler\Renderer\StatusColor',
            'options' => array('success' => 'success', 'error' => 'error', 'missed' => 'missed', 'running' => 'running', 'notice' => 'notice')

        ));


        $this->addColumn("id", array(
            "header" => __("Details"),
            "align" => "left",
            "index" => "id",
            'renderer' => 'Kensium\Scheduler\Block\Adminhtml\Scheduler\Renderer\ViewButton',
            'width' => '150'

        ));

        return parent::_prepareColumns();
    }

    //the below method return an empty url as the method is empty here so the grid becomes unclickable here
    public function getRowUrl($item)
    {
        parent::getRowUrl($item);
    }
}
