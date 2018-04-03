<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;
use Kensium\Orderstatus\Model\ResourceModel\Orderstatus\Collection;

class AttributeOrder extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Order\Collection
     */
    protected $collectionFactory;

    protected $orderStatusCollection;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\ResourceModel\Order\Collection $collection
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Kensium\Amconnector\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Backend\Helper\Data $backendHelper,
        Collection $orderStatusCollection
    )
    {
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;
        $this->backendHelper = $backendHelper;
        $this->orderStatusCollection = $orderStatusCollection;
    }


    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        global $colValue;
        $colValue = '';
        $attributeCode = $row->getData('label');
        $url = $this->backendHelper->getUrl('*/*/saveRow');

        $columnAttr = 'acumatica_attr_code';
        $session = $this->session->getData();
        if (isset($session['storeId'])) {
            $gridSessionStoreId = $session['storeId'];
            if ($gridSessionStoreId == 0) {
                $gridSessionStoreId = 1;
            }
        }
	      $collection = $this->collectionFactory->create()->addFieldToFilter('magento_attr_code', $attributeCode);
        if (isset($gridSessionStoreId))
            $collection->addFieldToFilter('store_id', $gridSessionStoreId);
        $data = $collection->getData();
        foreach ($data as $col) {
            $colValue = $col['acumatica_attr_code'];
        }
        $collection = $this->orderStatusCollection->addFilter('status',0)->getData();
        $html = '<select name="directions" class="admin__control-select" style="width:200px;" onchange="changeValue(\'' . $url . '\', \'' . $attributeCode . '\', \'' . $columnAttr . '\', this.value);">
            <option value="0">Please Select</option>';
        foreach ($collection as $val) {
            $checkValue = $val['orderstatus_id']."||".$val['status_label'];
            if ($colValue == $checkValue) {
                $sel = 'selected == selected';
            } else {
                $sel = '';
            }
            $html .= '<option ' . $sel . ' value="' . $checkValue . '">' . $val['status_label'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}
