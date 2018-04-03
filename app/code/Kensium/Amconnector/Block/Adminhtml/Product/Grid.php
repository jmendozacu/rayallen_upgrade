<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Product;

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
     * @var \Kensium\Amconnector\Model\ResourceModel\Product
     */
    protected $resourceModelProduct;

    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $dataCollection;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $_collection
     * @param \Magento\Framework\Data\Collection $dataCollection
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Backend\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $_collection,
        \Magento\Framework\Data\Collection $dataCollection,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct,
        \Magento\Framework\Module\Manager $moduleManager,
        $data = array()
    )
    {
        $this->session = $context->getBackendSession();
        $this->_collection = $_collection;
        $this->moduleManager = $moduleManager;
        $this->backendHelper = $backendHelper;
        $this->dataCollection = $dataCollection;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->resourceModelProduct = $resourceModelProduct;
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
        $this->setUseAjax(false);
        $this->setVarNameFilter('grid_record');
        $this->session->setData('gridData', '');
        $storeId = $this->getRequest()->getParam('store');
        $methods = $this->_collection->addVisibleFilter();
        $productMethods = array();
        foreach ($methods as $productCode => $productModel) {
            $productMethods['product'] = $productCode;
            $rowObj = $this->dataObjectFactory->create();
            $rowObj->setData($productMethods);
            $this->dataCollection->addItem($rowObj);
        }

        $gridArray = array();
        foreach ($this->dataCollection as $col) {
            $attrCode = $col->getAttributeCode();
            $gridArray[$attrCode] = array('acumatica_attr_code' => 'please select', 'sync_direction' => 'please select', 'store_id' => $storeId);
        }
        $this->session->setData('gridData', $gridArray);
        $this->session->setData('storeId', $storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_collection->addVisibleFilter());
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'frontend_label',
            [
                'header' => __('ID'),
                'type' => 'varchar',
                'index' => 'frontend_label',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'acumatica_attr_code',
            [
                'header' => __('Acumatica Attribute'),
                'renderer' => '\Kensium\Amconnector\Block\Adminhtml\Renderer\AcumaticaProduct',
                'class' => 'xxx',
                'filter' => false
            ]
        );

        $this->addColumn(
            'sync_direction',
            [
                'header' => __('Direction'),
                'renderer' => '\Kensium\Amconnector\Block\Adminhtml\Renderer\AttributeProduct',
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
        $adminUrlAmconnectorProduct = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameProduct . '/index/store/'.$storeId);
        $adminUrlAmconnectorCustomer = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameCustomer . '/index/store/'.$storeId);
        $adminUrlAmconnectorOrder = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameOrder . '/index/store/'.$storeId);
        $adminUrlAmconnectorPayment = $this->backendHelper->getUrl($moduleName . '/' . $controllerNamePayment . '/index/store/'.$storeId);
        $adminUrlAmconnectorPleaseSelect = $this->backendHelper->getUrl($moduleName . '/' . $controllerNamePleaseSelect . '/index/store/'.$storeId);
        $adminUrlAmconnectorShip = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameShip . '/index/store/'.$storeId);
        $productMappingCount = $this->resourceModelProduct->getAcumaticaAttrCount();
        $html = parent::getMainButtonsHtml();
        $addButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label' => 'Save',
                'onclick' => "setLocation('" . $this->backendHelper->getUrl('*/*/save/store/'.$storeId) . "')",
                'class' => 'add'
            ))->toHtml();

        $schemaButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label' => 'Update Schema',
                'onclick' => "updateSchema('" . $this->backendHelper->getUrl('*/*/saveSchema') ."','" . $productMappingCount . "')",
                'class' => 'add'
            ))->toHtml();

        $productMappingCount = $this->resourceModelProduct->getAcumaticaAttrCount();
        if ($productMappingCount > 0) {            
            $productMappingCheck = $this->resourceModelProduct->checkProductMapping($storeId);
            $recommendedSettings = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => 'Recommended Mappings',
                    'onclick' => "checkMapping('" . $this->backendHelper->getUrl('*/*/saveRecommended/store/'.$storeId) . "','" . $productMappingCheck . "')",
                    'class' => 'add'
                ))->toHtml();
        }

        $addSelect = '
                    <form name="productselect">
                        <select name="menu" class="admin__control-select" onChange="top.location.href=this.options[this.selectedIndex].value;" value="GO">
                            <option value=' . "$adminUrlAmconnectorPleaseSelect" . '>Please Select</option>
			                <option value=' . "$adminUrlAmconnectorCategory" . '>Category</option>
                            <option selected="selected" value=' . "$adminUrlAmconnectorProduct" . '>Product</option>
                            <option  value=' . "$adminUrlAmconnectorCustomer" . '>Customer</option>
			                <option value=' . "$adminUrlAmconnectorShip" . ' >Shipping Methods</option>
	                        <option value=' . "$adminUrlAmconnectorPayment" . ' >Payment Methods</option>
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
