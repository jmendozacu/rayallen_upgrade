<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Scheduler\Block\Adminhtml;

/**
 * Class Cron
 * @package Kensium\Scheduler\Block\Adminhtml
 */
class Cron extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * @var \Magento\Cron\Model\ConfigInterface
     */
     protected $cronConfig;

    /**
     * @var \Magento\Framework\Dataobject
     */
     protected $dataObject;

    /**
     * @var CronData
     */
     protected $cronData;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Data\Collection $dataCollection
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param CronData $cronData
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
        
        \Kensium\Scheduler\Block\Adminhtml\CronData $cronData,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Cron\Model\ConfigInterface $cronConfig,
        \Magento\Framework\Dataobject $dataObject,
        $data = array()
    )
    {
        $this->session = $context->getBackendSession();
        $this->moduleManager = $moduleManager;
        $this->backendHelper = $backendHelper;
        $this->dataCollection = $dataCollection;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->cronConfig = $cronConfig;
        $this->dataObject = $dataObject;
        $this->cronData  = $cronData;
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
        $this->setUseAjax(true);
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
            $count = 1;
            $rows = $this->cronData->getCronData();
            $collection = $this->dataCollection;
            foreach ($rows as $row) {
                foreach ($row as $key => $datahere) {
                    $datahere['rowid'] = $count;
                    if(isset($datahere['instance'])){
                        if (stristr($datahere['instance'], 'Amconnector')) {
                            if(isset($datahere['config_path'])){
                                $datahere['schedule'] = $this->_scopeConfig->getValue($datahere['config_path']);
                            }
                            /*condition to filter only amconnector starts here, to get all the crons in this mageto remove the below if condition*/
                            $amconnectorexp = explode("_", $datahere['name']);
                            /* filter code ends here */

                            $rowObj = $this->dataObjectFactory->create();// use this create method to prevent getting only the last data in the grid.
                            $rowObj->setData($datahere)->toJson();
                            $collection->addItem($rowObj);
                            $collection->loadData();
                            $count++;
                        }
                    }

                }
            }
            $storeId = $this->getRequest()->getParam('store');
            if ($storeId == 0) {
                $storeId = 1;
            }
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    protected function _prepareColumns()
    {
       // die('Prepare Columns');
      $this->addColumn("name", array(
            "header" => __("Code"),
            "align" => "left",
            'width' => '25',
            "index" => "name",
        ));

      
     $this->addColumn("method", array(
            "header" => __("Method"),
            "align" => "center",
            "index" => "method",
            'width' => '150'

        ));

        $this->addColumn("schedule", array(
            "header" =>__("Schedule"),
            "align"  => "left",
            "index"  => "schedule",
            'width'  => '150'

        ));
        
        
       $this->addColumn("rowid", array(
            "header" =>__("Action"),
            "align"  => "left",
            "index"  => "rowid",
            //'type'  => 'options',
            'renderer' => 'Kensium\Scheduler\Block\Adminhtml\Cron\Renderer\Messagetype',
           //'options' => array('Success' => 'Success', 'Failure' => 'Failure'),
            'width'  => '150'

        ));
        
        
        
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
    
}

?>
