<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Ship;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $dataCollectionFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfig;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Ship
     */
    protected $resourceModelShip;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Data\CollectionFactory $dataCollectionFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Data\CollectionFactory $dataCollectionFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Kensium\Amconnector\Model\ResourceModel\Ship $resourceModelShip,
        $data = array()
    )
    {
        $this->session = $context->getBackendSession();
        $this->moduleManager = $moduleManager;
        $this->backendHelper = $backendHelper;
        $this->dataCollectionFactory = $dataCollectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->shippingConfig = $shippingConfig;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->resourceModelShip = $resourceModelShip;
        parent::__construct($context, $backendHelper, $data = array());
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('shipmentMapGrid');
        $this->setDefaultSort('ship_method');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('ship_method');
        $this->session->setData('gridData', '');
        $storeId = $this->getRequest()->getParam('store');
        if ($storeId == 0 || $storeId == NULL) {
            $storeId = 1;
        }
        $collection = $this->dataCollectionFactory->create();
        $methods = $this->shippingConfig->getActiveCarriers();
        $shipping = array();
        foreach ($methods as $_ccode => $_carrier) {
            if ($_methods = $_carrier->getAllowedMethods()) {
                if (!$_title = $this->scopeConfigInterface->getValue("carriers/$_ccode/title" , "stores",$storeId))
                    $_title = $_ccode;
                foreach ($_methods as $_mcode => $_method) {
                        $_code = $_ccode . '_' . $_mcode;
			$shipping[$_code] = array('title' => $_method, 'carrier' => $_title);
                        $shipMethods['ship_method'] = $_title . ' - ' . $_method;
                        $shipMethods['carrier'] = $_ccode;
                        $rowObj = $this->dataObjectFactory->create();
                        $rowObj->setData($shipMethods);
                        $collection->addItem($rowObj);
                }
            }
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
        $collection = $this->dataCollectionFactory->create();
        $methods = $this->shippingConfig->getActiveCarriers();
        $storeId = $this->getRequest()->getParam('store');
        $shipping = array();
        foreach ($methods as $_ccode => $_carrier) {
            if ($_methods = $_carrier->getAllowedMethods()) {
                if (!$_title = $this->scopeConfigInterface->getValue("carriers/$_ccode/title" , "stores",$storeId))
                    $_title = $_ccode;
                $shippingName = array();
                foreach ($_methods as $_mcode => $_method) {
                    if (!in_array($_method, $shippingName)) {
                        $_code = $_ccode . '_' . $_mcode;
                        $shipping[$_code] = array('title' => $_method, 'carrier' => $_title);
                        $shipMethods['ship_method'] = $_title . ' - ' . $_method;
                        $shipMethods['carrier'] = $_ccode;
                        $rowObj = $this->dataObjectFactory->create();
                        $rowObj->setData($shipMethods);
                        $collection->addItem($rowObj);
                    }
                }

            }
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'ship_method',
            [
                'header' => __('Magento Shipping Methods'),
                'type' => 'varchar',
                'index' => 'ship_method',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'acumatica_attr_code',
            [
                'header' => __('Acumatica Shipping Methods'),
                'renderer' => '\Kensium\Amconnector\Block\Adminhtml\Renderer\AcumaticaShip',
                'filter' => false
            ]
        );
        $this->addColumn(
            'carrier',
            [
                'header' => __('Carrier'),
                'index' => 'carrier',
                'filter' => false,
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
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
        $adminUrlAmconnectorProduct = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameProduct . '/index/store/'.$storeId);
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
        $shipMappingCount = $this->resourceModelShip->getAcumaticaAttrCount($storeId);
        $schemaButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label' => 'Update Schema',
                'onclick' => "updateSchema('" . $this->backendHelper->getUrl('*/*/saveSchema') . "','" . $shipMappingCount . "')",
                'class' => 'add'
            ))->toHtml();

        $addSelect = '
                    <form name="productselect">
                        <select name="menu" class="admin__control-select" onChange="top.location.href=this.options[this.selectedIndex].value;" value="GO">
                            <option value=' . "$adminUrlAmconnectorPleaseSelect" . '>Please Select</option>
                            <option value=' . "$adminUrlAmconnectorCategory" . '>Category</option>
                            <option value=' . "$adminUrlAmconnectorProduct" . '>Product</option>
                            <option  value=' . "$adminUrlAmconnectorCustomer" . '>Customer</option>
			    			<option selected="selected" value=' . "$adminUrlAmconnectorShip" . ' >Shipping Methods</option>
	                    	<option  value=' . "$adminUrlAmconnectorPayment" . ' >Payment Methods</option>
                            <option value=' . "$adminUrlAmconnectorOrder" . ' >Order Status</option>
                        </select>
                    </form>';
        if (isset($recommendedSettings))
            return $addSelect . $addButton . $html . $schemaButton . $recommendedSettings;
        else
            return $addSelect . $addButton . $html . $schemaButton;
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
