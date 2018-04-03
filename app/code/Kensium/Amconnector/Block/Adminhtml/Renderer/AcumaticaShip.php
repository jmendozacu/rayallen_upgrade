<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;

class AcumaticaShip extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Ship\CollectionFactory
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
     * @param \Kensium\Amconnector\Model\ResourceModel\Ship\CollectionFactory $collectionFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Kensium\Amconnector\Model\ResourceModel\Ship\CollectionFactory $collectionFactory,
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
        global $colValue;
        $colValue = '';
        $attributeCode = $row->getShipMethod() . "|" . $row->getCarrier();
        $key = $row->getShipMethod();
        $url = $this->backendHelper->getUrl('*/*/saveRow');
        $coulmnAttr = 'acumatica_attr_code';
        $session = $this->session->getData();
        $gridSessionStoreId = $session['storeId'];
        $collection = $this->collectionFactory->create()->addFieldToFilter('magento_attr_code', $key)->addFieldToFilter('store_id', $gridSessionStoreId);
        $data = $collection->getData();

        $colValue = '';
        foreach ($data as $col) {
            $colValue = $col['acumatica_attr_code'];
        }

        $acumaticaShipAttributes = $this->syncResourceModel->getAttributes('ship',$gridSessionStoreId);

        $html = '<select name="directions" class="admin__control-select" style="width:200px;" onchange="changeValue(\'' . $url . '\', \'' . $attributeCode . '\', \'' . $coulmnAttr . '\', this.value);">
            <option value="0">Please Select</option>';
        foreach ($acumaticaShipAttributes as $shipValue) {
            $sel = '';
            if ($colValue == $shipValue['code']) {
                $sel = 'selected == selected';
            }

            $html .= '<option ' . $sel . ' value="' . $shipValue['code'] . '">' . $shipValue['label'] . '</option>';

        }
        $html .= '</select>';
        return $html;
    }
}
