<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;

class AcumaticaCustomer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Customer\Collection
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $syncResourceModel;

    /**
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Kensium\Amconnector\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Magento\Backend\Helper\Data $backendHelper
    )
    {
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;
        $this->syncResourceModel = $syncResourceModel;
        $this->backendHelper = $backendHelper;
    }


    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(DataObject $row)
    {
        global $colValue;
        $colValue = '';
        $attributeCode = $row->getData('attribute_code');
        $entityTypeId = $row->getEntityTypeId();
        if ($entityTypeId == 2) {
            $attributeCodeFilter = 'BILLADD_' . $row->getData('attribute_code');
        } else {
            $attributeCodeFilter = $row->getData('attribute_code');
        }
        $url = $this->backendHelper->getUrl('*/*/saveRow');
        $columnAttr = 'acumatica_attr_code';

        $session = $this->session->getData();
        $collection = $this->collectionFactory->create()->addFieldToFilter('magento_attr_code', $attributeCodeFilter);
        if (isset($session['storeId'])) {
            $gridSessionStoreId = $session['storeId'];
            if ($gridSessionStoreId == 0) {
                $gridSessionStoreId = 1;
            }
            $collection->addFieldToFilter('store_id', $gridSessionStoreId);
        }

        $data = $collection->getData();
        foreach ($data as $col) {
            $colValue = $col['acumatica_attr_code'];
        }
        $acumaticaCustomerAttributes = $this->syncResourceModel->getAttributes('customer',$gridSessionStoreId);


        $html = '<select name="directions" class="admin__control-select" style="width:200px;" onchange="changeValue(\'' . $url . '\', \'' . $attributeCode . '\', \'' . $columnAttr . '\', this.value,\'' . $entityTypeId . '\');">
            <option value="0">Please Select</option>';
        foreach ($acumaticaCustomerAttributes as $custValue) {
            $sel = '';

            if ($colValue == $custValue['code']) {
                $sel = 'selected == selected';
            }
            $acumaticaAttrCode = explode(" ", $custValue['label']);
            if ($acumaticaAttrCode[0] != 'BillingAddress' && $acumaticaAttrCode[0] != 'BillingContact' && $acumaticaAttrCode[0] != 'DeliveryAddress' && $acumaticaAttrCode[0] != 'DeliveryContact') {
                $html .= '<option ' . $sel . ' value="' . $custValue['code'] . '">' . $custValue['label'] . '</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }
}
