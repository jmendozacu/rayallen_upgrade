<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Customermapping;

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
     * @param \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Eav\Model\Entity\Attribute $attribute,
        \Magento\Framework\Data\Collection $dataCollection,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Kensium\Amconnector\Model\ResourceModel\Customer $resourceModelCustomer,
        \Magento\Framework\Module\Manager $moduleManager,
         $data = array()
    )
    {
        $this->session = $context->getBackendSession();
        $this->entityType = $entityType;
        $this->attribute = $attribute;
        $this->moduleManager = $moduleManager;
        $this->backendHelper = $backendHelper;
        $this->dataCollection = $dataCollection;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->resourceModelCustomer = $resourceModelCustomer;
        parent::__construct($context, $backendHelper, $data = array());
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('CustomerMapGrid');
        $this->setDefaultSort('frontend_label');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('frontend_label');
        $this->session->setData('gridData', '');
        $storeId = $this->getRequest()->getParam('store');
        $entityType = $this->entityType->loadByCode('customer_address');
        $methods = $this->attribute->getCollection()->setEntityTypeFilter($entityType);
        $customerMethods = array();
        foreach ($methods as $customerCode => $customerModel) {
            $rowObj = $this->dataObjectFactory->create();
            $rowObj->setData($customerMethods);
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
        $entitiesToMerge = array('customer_address', 'customer');
        $entitiesData = array();
        // get the entities objects and data
        if (!empty($entitiesToMerge)) {
            foreach ($entitiesToMerge as $entityCode) {
                $entity = $this->entityType->loadByCode($entityCode);
                $entitiesData['id'][] = $entity->getId();
                $entitiesData['additional'][] = $entity->getAdditionalAttributeTable();
            }
        }
        // sanitize
        $entitiesData['id'] = array_unique($entitiesData['id']);
        $entitiesData['additional'] = array_unique($entitiesData['additional']);
        // create custom collection
        $customCollection = $this->attribute->getCollection();
        //filter the collection with the entity_types
        $customCollection->addFieldToFilter('main_table.entity_type_id', array('in' => $entitiesData['id']));
        $customCollection->addFieldToFilter('frontend_label', array('notnull' => true));
        // join additionnal attributes data table
        if (!empty($entitiesData['additional'])) {
            foreach ($entitiesData['additional'] as $idx => $additionalTable) {
                $customCollection->join(
                    array('additional_table_' . $idx => $additionalTable),
                    'additional_table_' . $idx . '.attribute_id = main_table.attribute_id'
                );
            }
        }
        $this->setCollection($customCollection);
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
        $this->addColumn('entity_type_id', array(
            'header' => __('Magento Entity Type Id'),
            'align' => 'left',
            'index' => 'entity_type_id',
            'column_css_class' => 'no-display',//this sets a css class to the column row item
            'header_css_class' => 'no-display',//this sets a css class to the column header
        ));
        $this->addColumn(
            'acumatica_attr_code',
            [
                'header' => __('Acumatica Attribute'),
                'renderer' => '\Kensium\Amconnector\Block\Adminhtml\Renderer\AcumaticaCustomer',
                'filter' => false
            ]
        );

        $this->addColumn(
            'sync_direction',
            [
                'header' => __('Direction'),
                'renderer' => '\Kensium\Amconnector\Block\Adminhtml\Renderer\AttributeCustomer',
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
        $adminUrlAmconnectorProduct = $this->backendHelper->getUrl($moduleName. '/' . $controllerNameProduct . '/index/store/'.$storeId );
        $adminUrlAmconnectorCustomer = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameCustomer . '/index/store/'.$storeId);
        $adminUrlAmconnectorOrder = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameOrder . '/index/store/'.$storeId);
        $adminUrlAmconnectorPayment = $this->backendHelper->getUrl($moduleName . '/' . $controllerNamePayment . '/index/store/'.$storeId);
        $adminUrlAmconnectorPleaseSelect = $this->backendHelper->getUrl($moduleName . '/' . $controllerNamePleaseSelect . '/index/store/'.$storeId);
        $adminUrlAmconnectorShip = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameShip . '/index/store/'.$storeId);
        $html = parent::getMainButtonsHtml();
        $addButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label' => 'Save',
                'onclick' => "setLocation('" . $this->backendHelper->getUrl('*/*/save/store/'.$storeId) . "')",
                'class' => 'add'
            ))->toHtml();                
        $customerMappingCount = $this->resourceModelCustomer->getAcumaticaAttrCount();

        $schemaButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label' => 'Update Schema',
                'onclick' => "updateSchema('" . $this->backendHelper->getUrl('*/*/saveSchema') . "','" . $customerMappingCount . "')",
                'class' => 'add'
            ))->toHtml();

        if ($customerMappingCount > 0) {
            
            $customerMappingCheck = $this->resourceModelCustomer->checkCustomerMapping($storeId);
            $recommendedSettings = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => 'Recommended Mappings',
                    'onclick' => "checkMapping('" . $this->backendHelper->getUrl('*/*/saveRecommended/store/'.$storeId) . "','" . $customerMappingCheck . "')",
                    'class' => 'add'
                ))->toHtml();
        }

        $addSelect = '
                   <form name="productselect">
                       <select class="admin__control-select" name="menu" onChange="top.location.href=this.options[this.selectedIndex].value;" value="GO">
                           <option value=' . "$adminUrlAmconnectorPleaseSelect" . '>Please Select</option>
						   <option value=' . "$adminUrlAmconnectorCategory" . '>Category</option>
						   <option value=' . "$adminUrlAmconnectorProduct" . '>Product</option>
						   <option selected="selected" value=' . "$adminUrlAmconnectorCustomer" . '>Customer</option>
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
