<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;

class AttributeCustomer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    protected $scopeConfigInterface;
    /**
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Kensium\Amconnector\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    )
    {
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;
        $this->backendHelper = $backendHelper;
        $this->scopeConfigInterface = $scopeConfigInterface;
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
        $url = $this->backendHelper->getUrl('*/*/saveRow');
        $entityTypeId = $row->getEntityTypeId();
        if ($entityTypeId == 2) {
            $attributeCodeFilter = 'BILLADD_' . $row->getData('attribute_code');
        } else {
            $attributeCodeFilter = $row->getData('attribute_code');
        }
//        echo $attributeCodeFilter;
        $columnAttr = 'sync_direction';
        $collection = $this->collectionFactory->create()->addFieldToFilter('magento_attr_code', $attributeCodeFilter);
        $session = $this->session->getData();
        if (isset($session['storeId'])) {
            $gridSessionStoreId = $session['storeId'];
            if ($gridSessionStoreId == 0) {
                $gridSessionStoreId = 1;
            }
            $collection->addFieldToFilter('store_id', $gridSessionStoreId);
        }

        $syncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/syncdirection', \Kensium\Amconnector\Helper\Url::SCOPE_TYPE,$gridSessionStoreId);
        if ($syncDirection == 1){
            $values = array('Acumatica to Magento');
        }else if ($syncDirection == 2){
            $values = array( 'Magento to Acumatica');
        }else {
            $values = array('Acumatica to Magento', 'Magento to Acumatica', 'Bi-Directional (Last Update Wins)', 'Bi-Directional (Magento Wins)', 'Bi-Directional (Acumatica Wins)');
        }

        $data = $collection->getData();
        foreach ($data as $col) {
            $colValue = $col['sync_direction'];
        }

        if ($attributeCodeFilter == 'acumatica_customer_id')
        {
            $html = "<input type = 'hidden'  value = 'Acumatica to Magento' /> <label> Acumatica To Magento </label>";
        }
        else
        {
            $html = '<select name="directions" class="admin__control-select" style="width:200px;" onchange="changeValue(\'' . $url . '\', \'' . $attributeCode . '\', \'' . $columnAttr . '\', this.value,\'' . $entityTypeId . '\');">
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
