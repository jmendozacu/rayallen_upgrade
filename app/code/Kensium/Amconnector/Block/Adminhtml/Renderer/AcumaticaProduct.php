<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;

class AcumaticaProduct extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $syncResourceModel;

    /**
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\ResourceModel\Product\Collection $collection
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Kensium\Amconnector\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
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
        $attributeCode = $row->getData('attribute_code');
        $entityTypeId = $row->getEntityTypeId();
        $url = $this->backendHelper->getUrl('*/*/saveRow');
        $coulmnAttr = 'acumatica_attr_code';
        $collection = $this->collectionFactory->create()->addFieldToFilter('magento_attr_code', $row->getAttributeCode());
        $session = $this->session->getData();
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

        $acumaticaProductAttributes = $this->syncResourceModel->getAttributes('product',$gridSessionStoreId);
        $acumaticaCustomProductAttributes = $this->syncResourceModel->getAttributes('customproductattributes',$gridSessionStoreId);

        $html = '<select name="directions" class="admin__control-select"  style="width:200px;" onchange="changeValue(\'' . $url . '\', \'' . $attributeCode . '\', \'' . $coulmnAttr . '\', this.value);">
           <option value="0">Please Select</option>';
        foreach ($acumaticaProductAttributes as $prodValue) {
            $sel = '';
            if ($colValue == $prodValue['code']) {
                $sel = 'selected == selected';
            }
            $html .= '<option ' . $sel . ' value="' . $prodValue['code'] . '">' . $prodValue['label'] . '</option>';
        }
        if((count($acumaticaProductAttributes) > 0) && (count($acumaticaCustomProductAttributes) > 0)){
            $html .="<option disabled>----Custom Attributes----</option>'";
            foreach($acumaticaCustomProductAttributes as $customAttribute)
            {
                $sel = '';
                $customId = $customAttribute['id']."_CUSTOM";
                if($colValue == $customId)
                {
                    $sel= 'selected == selected';
                }
                $html .= '<option '.$sel.' value="'.$customId.'">'.$customAttribute['description'].'</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }
}
