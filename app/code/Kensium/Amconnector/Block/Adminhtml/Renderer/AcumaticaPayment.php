<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;

class AcumaticaPayment extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Payment\CollectionFactory
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
     * @param \Kensium\Amconnector\Model\ResourceModel\Payment\CollectionFactory $collectionFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Kensium\Amconnector\Model\ResourceModel\Payment\CollectionFactory $collectionFactory,
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
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $attributeCode = $row->getPayments();
        $url = $this->backendHelper->getUrl('*/*/saveRow');
        $coulmnAttr = 'acumatica_attr_code';
        $session = $this->session->getData();
        $gridSessionStoreId = $session['storeId'];
        $collection = $this->collectionFactory->create()->addFieldToFilter('magento_attr_code', $attributeCode)->addFieldToFilter('store_id', $gridSessionStoreId);
        $data = $collection->getData();
        $acumaticaPaymentAttributes = $this->syncResourceModel->getAttributes('payment',$gridSessionStoreId);
        $html = '<select name="directions" class="admin__control-select" style="width:200px;" onchange="changeValue(\'' . $url . '\', \'' . $attributeCode . '\', \'' . $coulmnAttr . '\', this.value);">
            <option value="0">Please Select</option>';

        foreach ($acumaticaPaymentAttributes as $payValue) {
            $sel = '';
            if (isset($data[0]['acumatica_attr_code']) && $data[0]['acumatica_attr_code'] == $payValue['code']) {
                $sel = 'selected == selected';
            }
            $html .= '<option ' . $sel . ' value="' . $payValue['code'] . '">' . $payValue['label'] . '</option>';

        }
        $html .= '</select>';
        return $html;
    }
}
