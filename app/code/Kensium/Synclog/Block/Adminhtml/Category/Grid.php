<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\Category;
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
     * @var \Magento\Framework\Dataobject
     */
    protected $dataObject;

    /**
     * @var \Kensium\Synclog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Data\Collection $dataCollection
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Kensium\Synclog\Model\CategoryFactory $categoryFactory
     * @param \Kensium\Scheduler\Block\Adminhtml\CronData $cronData
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Cron\Model\ConfigInterface $cronConfig
     * @param \Magento\Framework\Dataobject $dataObject
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Data\Collection $dataCollection,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Kensium\Synclog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Dataobject $dataObject,
        $data = array()
    )
    {
        $this->session = $context->getBackendSession();
        $this->moduleManager = $moduleManager;
        $this->backendHelper = $backendHelper;
        $this->dataCollection = $dataCollection;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->dataObject = $dataObject;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context, $backendHelper, $data = array());
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('categoryGrid');
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
        $scheduleId = $this->_request->getParam('schedule_id');
        $collection = $this->categoryFactory->create()->getCollection()->addFieldToFilter('sync_exec_id',$scheduleId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn("created_at", array(
            "header" => __("Creation Date"),
            "align" => "left",
            'width' => '25',
            "index" => "created_at",
            'type' => 'datetime'
        ));

        $this->addColumn("action", array(
            "header" => __("Action"),
            "align" => "left",
            'width' => '25',
            "index" => "action"
        ));

        $this->addColumn("cat_id", array(
            "header" => __("Magento Category ID"),
            "align" => "left",
            'width' => '25',
            'frame_callback' => array($this, 'callbackColumnCategoryId')
        ));

        $this->addColumn("acumatica_category_id", array(
            "header" => __("Acumatica Category ID"),
            "align" => "left",
            'width' => '25',
            "index" => "acumatica_category_id"
        ));

        $this->addColumn("acumatica_category_name", array(
            "header" => __("Acumatica Category Name"),
            "align" => "left",
            'width' => '25',
            "index" => "acumatica_category_name"
        ));

        $this->addColumn("catgeory_status", array(
            "header" => __("Category Status"),
            "align" => "left",
            'width' => '25',
            'renderer' => 'Kensium\Synclog\Block\Adminhtml\Category\Renderer\Status',
        ));

        $this->addColumn("description", array(
            "header" => __("Description"),
            "align" => "left",
            'width' => '25',
            "index" => "description",
        ));

        $this->addColumn("message_type", array(
            "header" => __("Type"),
            "align" => "left",
            "index" => "message_type",
            'width' => '150',
            'renderer' => 'Kensium\Synclog\Block\Adminhtml\Category\Renderer\Messagetype',
        ));
        return parent::_prepareColumns();
    }

    /**
     * @param $value
     * @param $row
     * @return string
     */
    public function callbackColumnCategoryId($value,$row)
    {
        $categoryId = (int)$row->getData('cat_id');;
        $categoryName = $row->getData('acumatica_category_name');
        $url = $this->getUrl('adminhtml/catalog_category/index', array('id'=>$categoryId, 'clear'=>1));
        return <<<HTML
       <a href="{$url}" target="_blank" title="{$categoryName}" >
           {$categoryId}
       </a>
HTML;
    }

    public function getRowUrl($item) {
        parent::getRowUrl($item);
    }
}
?>
