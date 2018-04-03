<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Category;

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
     * @var \Kensium\Amconnector\Model\ResourceModel\Category
     */
    protected $resourceModelCategory;

    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $dataCollection;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $entityType;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $attribute;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Eav\Model\Entity\Type $entityType
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @param \Magento\Framework\Data\Collection $dataCollection
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Category $resourceModelCategory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Backend\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Eav\Model\Entity\Attribute $attribute,
        \Magento\Framework\Data\Collection $dataCollection,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Kensium\Amconnector\Model\ResourceModel\Category $resourceModelCategory,
        \Magento\Framework\Module\Manager $moduleManager,
        $data = []
    )
    {
        $this->session = $context->getBackendSession();
        $this->entityType = $entityType;
        $this->attribute = $attribute;
        $this->moduleManager = $moduleManager;
        $this->backendHelper = $backendHelper;
        $this->dataCollection = $dataCollection;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->resourceModelCategory = $resourceModelCategory;
        parent::__construct($context, $backendHelper, []);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('CategoryMapGrid');
        $this->setDefaultSort('frontend_label');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('frontend_label');
        $this->session->setData('gridData', '');
        $storeId = $this->getRequest()->getParam('store');
        $entityType = $this->entityType->loadByCode('catalog_category');
        $methods = $this->attribute->getCollection()->setEntityTypeFilter($entityType)->addFieldToFilter('attribute_code',array('in'=>array('name','acumatica_category_id','acumatica_parent_category_id','description','include_in_menu','url_key','default_sort_by','meta_description','meta_keywords','meta_title')));
        $categoryMethods = array();
        foreach ($methods as $categoryCode => $categoryModel) {
            $rowObj = $this->dataObjectFactory->create();
            $rowObj->setData($categoryMethods);
            $this->dataCollection->addItem($rowObj);
        }
        $gridArray = array();
        foreach ($this->dataCollection as $col) {
            $attrCode = $col->getAttributeCode();
            $gridArray[$attrCode] = array('acumatica_attr_code' => 'Please Select', 'sync_direction' => 'Please Select', 'store_id' => $storeId);
        }
        $this->session->setData('gridData', $gridArray);
        $this->session->setData('storeId', $storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        
        $entityType = $this->entityType->loadByCode('catalog_category');
        $collection = $this->attribute->getCollection()->setEntityTypeFilter($entityType)
                            ->addFieldToFilter('attribute_code',array('in'=>array('name','acumatica_category_id','acumatica_parent_category_id','description','include_in_menu','url_key','default_sort_by','meta_description','meta_keywords','meta_title')));
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
            'frontend_label',
            [
                'header' => __('Magento Attribute'),
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
                'renderer' => '\Kensium\Amconnector\Block\Adminhtml\Renderer\AcumaticaCategory',
                'filter' => false
            ]
        );

        $this->addColumn(
            'sync_direction',
            [
                'header' => __('Direction'),
                'renderer' => '\Kensium\Amconnector\Block\Adminhtml\Renderer\AttributeCategory',
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

        $html = parent::getMainButtonsHtml();

        $categoryMappingCount = $this->resourceModelCategory->getAcumaticaAttrCount();

        $addButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label' => 'Save',
                'onclick' => "setLocation('" . $this->backendHelper->getUrl('*/*/save/'.$storeId) . "')",
                'class' => 'add'
            ))->toHtml();

          $schemaButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label' => 'Update Schema',
                'onclick' => "updateSchema('" . $this->backendHelper->getUrl('*/*/saveSchema') . "','" . $categoryMappingCount . "')",
                'class' => 'add'
            ))->toHtml();


        if ($categoryMappingCount > 0) {
            $categoryMappingCheck = $this->resourceModelCategory->checkCategoryMapping($storeId);
            $recommendedSettings = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => 'Recommended Mappings',
                    'onclick' => "checkMapping('" . $this->backendHelper->getUrl('*/*/saveRecommended') . "','" . $categoryMappingCheck . "')",
                    'class' => 'add'
                ))->toHtml();
        }

        $addSelect = '
                    <form name="orderselect">
                        <select class="admin__control-select" name="menu" onChange="top.location.href=this.options[this.selectedIndex].value;" value="GO">
                            <option value=' . "$adminUrlAmconnectorPleaseSelect" . '>Please Select</option>
			                <option selected="selected" value=' . "$adminUrlAmconnectorCategory" . '>Category</option>
                            <option value=' . "$adminUrlAmconnectorProduct" . '>Product</option>
                            <option value=' . "$adminUrlAmconnectorCustomer" . '>Customer</option>
			                <option value=' . "$adminUrlAmconnectorShip" . ' >Shipping Methods</option>
	                        <option value=' . "$adminUrlAmconnectorPayment" . ' >Payment Methods</option>
                            <option value=' . "$adminUrlAmconnectorOrder" . '>Order Status</option>
                        </select>
                    </form>';
        if (isset($recommendedSettings))
            return $addSelect . $addButton . $html . $schemaButton . $recommendedSettings;
        else
            return $addSelect . $addButton . $html . $schemaButton;
    }
    public function getRowUrl($row)
   {
       return '';
   }
}
