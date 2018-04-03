<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\Category\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Catalog\Block\Adminhtml\Category\AbstractCategory;

/**
 * Class SyncButton
 */
class SyncButton extends AbstractCategory implements ButtonProviderInterface
{
    /**
     * Save button
     *
     * @return array
     */
    public function getButtonData()
    {
        $category = $this->getCategory();
        $storeId = $this->getStore()->getStoreId();
        if($storeId == 0){
            $scopeType = 'default';
            $individualSyncEnable = $this->_scopeConfig->getValue('amconnectorsync/categorysync/individualsync', $scopeType,$storeId);
            $storeId = 1;
        }else{
            $scopeType = 'stores';
            $individualSyncEnable = $this->_scopeConfig->getValue('amconnectorsync/categorysync/individualsync', $scopeType,$storeId);
        }

        /**
         * If Individual category sync is Enabled
         */
        if (!$category->isReadonly() && $this->hasStoreRootCategory() && ($category->getId() != 2 ) && $individualSyncEnable == 1) {
            return [
                'label' => __('Sync Now'),
                'class' => 'save primary',
                'on_click' => "syncNow('".$this->syncNowUrl()."category_id/".$category->getId()."/store_id/".$storeId."')",
                'sort_order' => 40,
            ];
        }
        return [];
    }
    /**
     * @param array $args
     * @return string
     */
    public function syncNowUrl(array $args = [])
    {
        $params = array_merge($this->getDefaultUrlParams(), $args);
        return $this->getUrl('amconnector/sync/individualCategory', $params);
    }

    /**
     * @return array
     */
    protected function getDefaultUrlParams()
    {
        return ['_current' => true, '_query' => ['isAjax' => null]];
    }
}
