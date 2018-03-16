<?php

namespace Iglobal\Stores\Observer;

class OrderCreated implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Iglobal\Stores\Model\Order
    */
    protected $_salesOrder;

    public function __construct(
        \Iglobal\Stores\Model\Order $salesOrder
    ) {
        $this->_salesOrder = $salesOrder;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //$observer contains the object returns in the event.
        $event = $observer->getEvent();
        $order = $event->getOrder();
        if($order->getRelationParentId()){
            $parentOrder = $this->_salesOrder->load($order->getRelationParentId());
            if($parentOrder->getIgOrderNumber()){
                $order->setIglobalTestOrder($parentOrder->getIglobalTestOrder());
                $order->setIgOrderNumber($parentOrder->getIgOrderNumber());
                $order->setInternationalOrder(1);
                $order->save();
            }
        }
        return $this;
    }
}
