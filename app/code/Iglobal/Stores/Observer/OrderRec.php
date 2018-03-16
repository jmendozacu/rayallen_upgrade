<?php

namespace Iglobal\Stores\Observer;

class OrderRec
{
    /**
     * @var \Iglobal\Stores\Model\RestFactory
     */
    protected $_storesFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Iglobal\Stores\Model\OrderFactory
    */
    protected $_salesOrderFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
    */
    protected $_orderFactory;


    public function __construct(
        \Iglobal\Stores\Model\RestFactory $storesFactory,
        \Psr\Log\LoggerInterface $logger,
        \Iglobal\Stores\Model\OrderFactory $salesOrderFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->_storesFactory = $storesFactory;
        $this->_logger = $logger;
        $this->_salesOrderFactory = $salesOrderFactory;
        $this->_orderFactory = $orderFactory;
    }

    public function execute(\Magento\Cron\Model\Schedule $schedule)
    {
        //build array of orders currently in magento
        $magentoOrders = array();
        $orders = $this->_orderFactory->create()->getCollection()->addFilter('international_order', 1)
            ->addFieldToFilter('ig_order_number', array('notnull'=> true))
            ->getItems();

        foreach ($orders as $order) {
            $magentoOrders[$order->getIgOrderNumber()] = $order;
        }

        //get array with all orders in past
        $date = date("Ymd", strtotime("-1 week"));
        $data = $this->_storesFactory->create()->getAllOrdersSinceDate($date);
        foreach ($data->orders as $igOrder)
        {
            if ($igOrder->testOrder)
            {
                continue;
            }
            if(array_key_exists($igOrder->id, $magentoOrders))
            {
                // check status
                $this->_salesOrderFactory->create()->checkStatus($magentoOrders[$igOrder->id]);
            }
            // TODO possibly remove this section below?
            else
            {
                try
                {
                  // re-import the order
                  // $this->_salesOrderFactory->create()->processOrder($igOrder->id);
                }
                catch(\Exception $e)
                {
                    mail('monitoring@iglobalstores.com, magentomissedorders@iglobalstores.com',
                        'Magento Integration Error - International order failed to import',
                        'International order# '. $igOrder->id .'.'. ' Exception Message: '.$e->getMessage());
                    $this->_logger->log(\Monolog\Logger::ERROR, "International order #{$igOrder->id} failed to import!".$e);
                }
            }
        }
    }
}
