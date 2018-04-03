<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\Product\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Kensium\Amconnector\Block\Adminhtml\Product\Edit\Button\Generic;
/**
 * Class SyncButton
 */
class SyncButton extends Generic
{
    /**
     * Save button
     *
     * @return array
     */
    public function getButtonData()
    {
        $product = $this->getProduct();
        $storeId = $this->getRequest()->getParam('store');
        if(!isset($storeId))
            $storeId = 0;
        if($storeId == 0){
            $scopeType = 'default';
            $individualSyncEnable = $this->getCoreConfigData('amconnectorsync/productsync/individualsync',$scopeType,$storeId);
            $storeId = 1;
        }else{
            $scopeType = 'stores';
            $individualSyncEnable = $this->getCoreConfigData('amconnectorsync/productsync/individualsync',$scopeType,$storeId);
            if($individualSyncEnable == '' && $storeId == 1)
                $individualSyncEnable = $this->getCoreConfigData('amconnectorsync/productsync/individualsync',NULL,NULL);
        }
        /**
         * If Individual category sync is Enabled
         */
        if (!$product->isReadonly() && $individualSyncEnable == 1 && $product->getId() != '' && $product->getTypeId() == "simple")
        {
            return [
                'label' => __('Sync Now'),
                'class' => 'save primary',
                'on_click' => "syncNow('".$this->syncNowUrl()."product_id/".$product->getId()."/store_id/".$storeId."')",
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
        return $this->getUrl('amconnector/sync/individualProduct', $params);
    }

    /**
     * @return array
     */
    protected function getDefaultUrlParams()
    {
        return ['_current' => true, '_query' => ['isAjax' => null]];
    }
}
