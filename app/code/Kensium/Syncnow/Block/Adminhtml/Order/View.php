<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Syncnow\Block\Adminhtml\Order;

use Magento\Sales\Block\Adminhtml\Order;

/**
 * Adminhtml sales order view
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Sales\Block\Adminhtml\Order\View
{



    /**
     * Constructor
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */


    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'adminhtml_order';
        $this->_mode = 'view';
        parent::_construct();
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
        $this->setId('sales_order_view');
        $order = $this->getOrder();

        $storeId = $order->getStoreId();

        $orderStauses = '';
        $orderStauses =  $this->_scopeConfig->getValue('amconnectorsync/ordersync/orderstatuses', 'stores', $storeId);
        if (!empty($orderStauses)) {
            $status = explode(",", $orderStauses);
        }else{
            $status = array();
        }
        $orderStatus = $order->getStatus();


        if(!$order->getAcumaticaOrderId() && (in_array($orderStatus,$status))) {
            $this->buttonList->add(
                'order_syncnow',
                [
                    'label' => __('Syncnow'),
                    'onclick' => 'syncnow(\'' . $this->getSyncnowUrl() . '\')',
                    'class' => 'syncnow',
                    'id' => 'order-view-sync-button'
                ]
            );

        }elseif ($order->getAcumaticaOrderId() && $order->getStatus() != 'complete') {
            $this->buttonList->add(
                'order_syncnow',
                [
                    'label' => __('Syncnow'),
                    'onclick' => 'syncnow(\'' . $this->getSyncnowUrl() . '\')',
                    'class' => 'syncnow',
                    'id' => 'order-view-sync-button'
                ]
            );
    }


    }

    /**
     * Syncnow URL getter
     *
     * @return string
     */
    public function getSyncnowUrl()
    {
        return $this->getUrl('amconnector/sync/order/inc_id/'.$this->getOrder()->getIncrementId());
    }

}
