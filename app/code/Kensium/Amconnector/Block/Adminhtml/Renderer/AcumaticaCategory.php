<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;

class AcumaticaCategory extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Category\Collection
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
     * @param \Kensium\Amconnector\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Kensium\Amconnector\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
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
        $url = $this->backendHelper->getUrl('*/*/changeValue');
        $coulmnAttr = 'acumatica_attr_code';

        $session = $this->session->getData();
        $collection = $this->collectionFactory->create()->addFieldToFilter('magento_attr_code', $attributeCode);
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
        $acumaticaCatAttributes = $this->syncResourceModel->getAttributes('category',$gridSessionStoreId);

$html = '<select class="admin__control-select" name="directions"  style="width:200px;" onchange="changeValue(\''.$url.'\', \''.$attributeCode.'\', \''.$coulmnAttr.'\', this.value);">
            <option value="0">Please Select</option>';
        foreach($acumaticaCatAttributes as $catValue){
            $sel = '';
            if(trim($colValue) == trim($catValue['code']))
            {
                $sel= 'selected == selected';
            }
            $html .= '<option '.$sel.' value="'.$catValue['code'].'">'.$catValue['label'].'</option>';
        }

        $html .='</select>';

        return $html;
    }
}
