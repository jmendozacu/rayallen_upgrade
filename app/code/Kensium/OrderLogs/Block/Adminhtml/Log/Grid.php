<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\OrderLogs\Block\Adminhtml\Log;

/**
 * Class Cron
 * @package Kensium\Scheduler\Block\Adminhtml
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
     * @var orderLogsFactory
     */
     protected $orderLogsFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Kensium\OrderLogs\Model\ResourceModel\OrderLogs\CollectionFactory $orderLogsFactory,
        $data = array()
    )
    {
        $this->session = $context->getBackendSession();
        $this->orderLogsFactory = $orderLogsFactory;
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
        try {
            $collection = $this->orderLogsFactory->create();
            $collection->getSelect()->join('amconnector_order_log_details','main_table.id = amconnector_order_log_details.sync_record_id')->where("main_table.message_type='Error'");
	    $collection->getSelect()->join('sales_order','main_table.order_id=sales_order.increment_id')->where('sales_order.acumatica_order_id="" AND sales_order.sync_order_failed=1 AND sales_order.status="processing"');
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    protected function _prepareColumns()
    {

      $this->addColumn("order_id", array(
            "header" => __("Order ID"),
            "align" => "left",
            'width' => '25',
            "index" => "order_id",
	     'renderer' => 'Kensium\OrderLogs\Block\Adminhtml\Log\Renderer\OrderLink',
      ));
      $this->addColumn("description", array(
            "header" => __("Description"),
            "align" => "left",
            'width' => '25',
            "index" => "description",
        ));

      
     $this->addColumn("long_message", array(
            "header" => __("Long Message"),
            "align" => "center",
            "index" => "long_message",
            'width' => '150'

        ));

/*        $this->addColumn("action", array(
            "header" =>__("Action"),
            "align"  => "left",
            "index"  => "action",
            'width'  => '150'

        ));*/
        
        
        
        
        
      /*  $this->addColumn("message_type", array(
            "header" =>__("Message Type"),
            "align"  => "left",
            "index"  => "message_type",
            'type'  => 'options',
            'renderer' => 'Kensium\Synclogs\Block\Adminhtml\ProductSynclogs\Renderer\Messagetype',
            'options' => array('Success' => 'Success', 'Failure' => 'Failure'),
            'width'  => '150'

        ));*/
       
        return parent::_prepareColumns();
    }
    public function getRowUrl($item) {
        parent::getRowUrl($item);
    }
    
}

?>
