<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;

class AcumaticaCashAccount extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
     * @var \Kensium\Amconnector\Model\ResourceModel\Payment
     */
    protected $paymentResourceModel;

    /**
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\ResourceModel\Payment\CollectionFactory $collectionFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Kensium\Amconnector\Model\ResourceModel\Payment\CollectionFactory $collectionFactory,
        \Kensium\Amconnector\Model\ResourceModel\Payment $paymentResourceModel,
        \Magento\Backend\Helper\Data $backendHelper
    )
    {
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;
        $this->paymentResourceModel = $paymentResourceModel;
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
        $coulmnAttr = 'cash_account';
        $session = $this->session->getData();
        $gridSessionStoreId= $session['storeId'];

        $collection = $this->collectionFactory->create()->addFieldToFilter('magento_attr_code',$attributeCode)->addFieldToFilter('store_id',$gridSessionStoreId);
        $data = $collection->getData();
        $acumaticaCashAccountAttributes =$this->paymentResourceModel->getCashAccountAttributes($gridSessionStoreId);

        $html = '<select name="directions" class="admin__control-select" style="width:200px;" onchange="changeValue(\''.$url.'\', \''.$attributeCode.'\', \''.$coulmnAttr.'\', this.value);">
            <option value="0">Please Select</option>';

        foreach($acumaticaCashAccountAttributes as $payValue)
        {
            $sel = '';
            if(isset($data[0]['cash_account']) && $data[0]['cash_account'] == $payValue['cash_account'])
            {
                $sel= 'selected == selected';
            }
            $html .= '<option '.$sel.' value="'.$payValue['cash_account'].'">'.$payValue['cash_account'].'</option>';

        }
        $html .='</select>';
        return $html;


    }
}
