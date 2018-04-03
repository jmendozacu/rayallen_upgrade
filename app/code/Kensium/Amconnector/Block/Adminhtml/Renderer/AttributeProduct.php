<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;

class AttributeProduct extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Product\Collection
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;
    
    /**
     *@var  \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\ResourceModel\Product\Collection $collection
     * @param \Magento\Backend\Helper\Data $backendHelper
     */

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Kensium\Amconnector\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
         \Magento\Framework\App\Request\Http $request,
         \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface ,
         \Magento\Backend\Helper\Data $backendHelper
    )
    {
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;
        $this->backendHelper = $backendHelper;
        $this->request = $request;
        $this->scopeConfigInterface = $scopeConfigInterface;
    }


    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $storeId = $this->request->getParam('store');
        global $colValue;
        $colValue = '';
        $attributeCode = $row->getData('attribute_code');
        $url = $this->backendHelper->getUrl('*/*/saveRow');
        $entityTypeId = $row->getEntityTypeId();
        if ($entityTypeId == 2) {
            $attributeCodeFilter = 'BILLADD_' . $row->getData('attribute_code');
        } else {
            $attributeCodeFilter = $row->getData('attribute_code');
        }
        $coulmnAttr = 'sync_direction';
        
        $collection = $this->collectionFactory->create()->addFieldToFilter('magento_attr_code', $attributeCodeFilter);
        if (isset($storeId))
            $collection->addFieldToFilter('store_id', $storeId);
        $data = $collection->getData();
        foreach ($data as $col) {
            $colValue = $col['sync_direction'];
        }
        $productSyncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/syncdirection',\Kensium\Amconnector\Helper\Url::SCOPE_TYPE,$storeId);
        if($productSyncDirection==1){
            $values = array('Acumatica to Magento');
        }
        elseif($productSyncDirection==2){
            $values = array('Magento to Acumatica');
        }
        else {
            $values = array('Acumatica to Magento', 'Magento to Acumatica', 'Bi-Directional (Last Update Wins)', 'Bi-Directional (Magento Wins)', 'Bi-Directional (Acumatica Wins)');
        }
            $html = '<select name="directions" class="admin__control-select" style="width:200px;" onchange="changeValue(\'' . $url . '\', \'' . $attributeCode . '\', \'' . $coulmnAttr . '\', this.value);">
            <option value="0">Please Select</option>';
        foreach ($values as $val) {
            if ($colValue == $val) {
                $sel = 'selected == selected';
            } else {
                $sel = '';
            }
            $html .= '<option ' . $sel . ' value="' . $val . '">' . $val . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}
