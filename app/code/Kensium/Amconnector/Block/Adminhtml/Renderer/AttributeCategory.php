<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;

class AttributeCategory extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Kensium\Amconnector\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Backend\Helper\Data $backendHelper
    )
    {
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->backendHelper = $backendHelper;
    }


    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        global $colValue;
        $colValue = '';
        $attributeCode = $row->getData('attribute_code');
        $url = $this->backendHelper->getUrl('*/*/changeValue');
        
        $columnAttr = 'sync_direction';
        $collection = $this->collectionFactory->create()->addFieldToFilter('magento_attr_code', $attributeCode);
        $session = $this->session->getData();
        if(empty($session['storeId'])){
            $session['storeId'] = 1;
        }
        if (isset($session['storeId'])) {
            $gridSessionStoreId = $session['storeId'];
            $collection->addFieldToFilter('store_id', $gridSessionStoreId);
        }

        if($gridSessionStoreId == 0){
            $scopeType = 'default';
        }else{
            $scopeType = 'stores';
        }
        $data = $collection->getData();
        foreach ($data as $col) {
            $colValue = $col['sync_direction'];
        }
        $directionValue = $this->scopeConfigInterface->getValue('amconnectorsync/categorysync/syncdirection',$scopeType,$gridSessionStoreId);

        if($directionValue == '1'){
            $values = array('Acumatica to Magento');
        }elseif($directionValue == '2'){
            $values = array('Magento to Acumatica');
        }else {
            $values = array('Acumatica to Magento', 'Magento to Acumatica', 'Bi-Directional (Last Update Wins)', 'Bi-Directional (Magento Wins)', 'Bi-Directional (Acumatica Wins)');
        }

        if ($attributeCode == 'acumatica_category_id' || $attributeCode == 'acumatica_parent_category_id') {
            $html = $colValue;
        } else {
            $html = '<select class="admin__control-select" name="directions"  style="width:200px;" onchange="changeValue(\'' . $url . '\', \'' . $attributeCode . '\', \'' . $columnAttr . '\', this.value);">
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
        }
        return $html;
    }
}
