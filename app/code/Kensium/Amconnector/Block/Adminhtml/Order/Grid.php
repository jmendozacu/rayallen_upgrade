<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Order;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Order\Attribute\Collection
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Order
     */
    protected $resourceModelOrder;

    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $dataCollection;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    protected $orderStatus;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Order\Attribute\Collection $_collection
     * @param \Magento\Framework\Data\Collection $dataCollection
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Order $resourceModelOrder
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Backend\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_collectionFactory,
        \Magento\Sales\Model\Order\Status $orderStatus,
        \Magento\Framework\Data\Collection $dataCollection,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Kensium\Amconnector\Model\ResourceModel\Order $resourceModelOrder,
        \Magento\Framework\Module\Manager $moduleManager,
        $data = array()
    )
    {
        $this->session = $context->getBackendSession();
        $this->_collectionFactory = $_collectionFactory;
        $this->moduleManager = $moduleManager;
        $this->backendHelper = $backendHelper;
        $this->dataCollection = $dataCollection;
        $this->orderStatus = $orderStatus;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->resourceModelOrder = $resourceModelOrder;
        parent::__construct($context, $backendHelper, $data = array());
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridGrid');
        $this->setDefaultSort('grid_record_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('grid_record');
        $this->session->setData('gridData', '');
        $storeId = $this->getRequest()->getParam('store');
        $this->session->setData('gridData', '');
        $collection = $this->dataCollection;
        $methods = $this->orderStatus->getResourceCollection();
        $orderMethods = array();
        foreach ($methods as $orderCode => $orderModel) {
            $shipMethods['order'] = $orderCode;
            $rowObj = $this->dataObjectFactory->create();
            $rowObj->setData($orderMethods);
            $collection->addItem($rowObj);
        }
        $gridArray = array();
        foreach ($collection as $col) {
            $attrCode = $col->getStatus();
            $gridArray[$attrCode] = array('acumatica_attr_code' => 'please select', 'store_id' => $storeId);
        }
        $this->session->setData('gridData', $gridArray);
        $this->session->setData('storeId', $storeId);

    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->orderStatus->getResourceCollection());
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'label',
            [
                'header' => __('ID'),
                'type' => 'varchar',
                'index' => 'label',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'acumatica_attr_code',
            [
                'header' => __('Acumatica Attribute'),
                'renderer' => '\Kensium\Amconnector\Block\Adminhtml\Renderer\AttributeOrder',
                'class' => 'order-acumatica-attr',
                'filter' => false
            ]
        );


        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $storeId = $this->getRequest()->getParam('store');
        if ($storeId == 0 || $storeId == NULL) {
                $storeId = 1;
        }
        $moduleName = 'amconnector';
        $controllerNamePleaseSelect = 'amconnector';
        $controllerNameCategory = 'category';
        $controllerNameProduct = 'product';
        $controllerNameCustomer = 'customermapping';
        $controllerNameOrder = 'order';
        $controllerNamePayment = 'payment';
        $controllerNameShip = 'ship';
		$adminUrlAmconnectorCategory = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameCategory . '/index/store/'.$storeId);
        $adminUrlAmconnectorProduct = $this->backendHelper->getUrl($moduleName. '/' . $controllerNameProduct . '/index/store/'.$storeId);
        $adminUrlAmconnectorCustomer = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameCustomer . '/index/store/'.$storeId);
        $adminUrlAmconnectorOrder = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameOrder . '/index/store/'.$storeId);
        $adminUrlAmconnectorPayment = $this->backendHelper->getUrl($moduleName . '/' . $controllerNamePayment . '/index/store/'.$storeId);
        $adminUrlAmconnectorPleaseSelect = $this->backendHelper->getUrl($moduleName . '/' . $controllerNamePleaseSelect . '/index/store/'.$storeId);
        $adminUrlAmconnectorShip = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameShip . '/index/store/'.$storeId);

        $html = parent::getMainButtonsHtml();
        $addButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label' => 'Save',
                'onclick' => "setLocation('" . $this->backendHelper->getUrl('*/*/save') . "')",
                'class' => 'add'
            ))->toHtml();

        $orderMappingCount = $this->resourceModelOrder->getAcumaticaAttrCount();
        if ($orderMappingCount > 0) {
            
            $orderMappingCheck = $this->resourceModelOrder->checkOrderMapping($storeId);
            $recommendedSettings = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => 'Recommended Mappings',
                    'onclick' => "checkMapping('" . $this->backendHelper->getUrl('*/*/saveRecommended') . "','" . $orderMappingCheck . "')",
                    'class' => 'add'
                ))->toHtml();
        }

        $addSelect = '
                    <form name="orderselect">
                        <select name="menu" class="admin__control-select" onChange="top.location.href=this.options[this.selectedIndex].value;" value="GO">
                            <option value=' . "$adminUrlAmconnectorPleaseSelect" . '>Please Select</option>
							<option value=' . "$adminUrlAmconnectorCategory" . '>Category</option>
                            <option value=' . "$adminUrlAmconnectorProduct" . '>Product</option>
                            <option value=' . "$adminUrlAmconnectorCustomer" . '>Customer</option>
			    			<option value=' . "$adminUrlAmconnectorShip" . ' >Shipping Methods</option>
	                    	<option value=' . "$adminUrlAmconnectorPayment" . ' >Payment Methods</option>
                            <option selected="selected" value=' . "$adminUrlAmconnectorOrder" . ' >Order Status</option>
                        </select>
                    </form>';
        if (isset($recommendedSettings))
            return $addSelect . $addButton . $html . $recommendedSettings;
        else
            return $addSelect . $addButton . $html;
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
