<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\Product;

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
     * @var \Kensium\Synclog\Model\CustomersynclogFactory
     */
    protected $productsynclogFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Data\Collection $dataCollection
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Kensium\Synclog\Model\ProductFactory $productsynclogFactory
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
        \Kensium\Synclog\Model\ProductFactory $productsynclogFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Dataobject $dataObject,
        $data = array()
    )
    {
        $this->session = $context->getSession();
        $this->moduleManager = $moduleManager;
        $this->backendHelper = $backendHelper;
        $this->dataCollection = $dataCollection;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->dataObject = $dataObject;
        $this->productsynclogFactory = $productsynclogFactory;
        parent::__construct($context, $backendHelper, $data = array());
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('categorySynclogGrid');
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
        $collection = $this->productsynclogFactory->create()->getCollection()->addFieldToFilter('sync_exec_id',$scheduleId);
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
            "index" => "action",
            'width' => '150'
        ));
         
          $this->addColumn("acumatica_stock_item", array(
            "header" => __("Acumatica Stock Item"),
            "align" => "left",
            "index" => "acumatica_stock_item",
            'width' => '150'
        ));
      

        $this->addColumn("product_name", array(
            "header" => __("Product Name"),
            "align" => "left",
            'renderer' => 'Kensium\Synclog\Block\Adminhtml\Product\Renderer\ProductTitle',
            'width' => '150'


        ));
        
        $this->addColumn("status", array(
            "header" => __("Magento Status"),
            "align" => "left",
            "index" => "id",
            'renderer' => 'Kensium\Synclog\Block\Adminhtml\Product\Renderer\ProductStatus',
            'width' => '150'


        ));
        
        $this->addColumn("description", array(
            "header" => __("Description"),
            "align" => "left",
            "index" => "description",
            'width' => '150'


        ));
       

        $this->addColumn("message_type", array(
                  "header" => __("Type"),
                  "align" => "center",
                  "index" => "message_type",
                  'width' => '150',
                'type' => 'options',
                'renderer' => 'Kensium\Synclog\Block\Adminhtml\Product\Renderer\ViewButton',
                'options' => array('Success' => 'Success', 'Failure' => 'Failure')

              ));
        

        return parent::_prepareColumns();
    }

    public function getRowUrl($item) {
        parent::getRowUrl($item);
    }
}

?>
