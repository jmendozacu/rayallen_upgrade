<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Kensium\Amconnector\Helper\OrderSync;
use Magento\Framework\App\Config\ScopeConfigInterface;

class RealtimeOrder implements ObserverInterface
{

    private $logger;
    /**
     * @var orderSync
     */
    protected $orderSync;

    /**
     * @var orderFactory
     */
    protected $orderFactory;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Order
     */
    protected $resourceModelKemsOrder;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;



    public function __construct(

        LoggerInterface $logger,
        \Kensium\Amconnector\Helper\OrderSync $orderSync,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Kensium\Amconnector\Model\ResourceModel\Order $resourceModelKemsOrder,
        ScopeConfigInterface $scopeConfigInterface
    )
    {
        $this->logger = $logger;
        $this->orderSync = $orderSync;
        $this->orderFactory = $orderFactory;
        $this->resourceModelKemsOrder = $resourceModelKemsOrder;
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (isset($observer)) {
            $orders = array();
            $orders = $observer->getOrderIds();
            if (isset($orders[0])) {
                $curentOrder = $this->orderFactory->create()->load($orders[0]);
                $orderId = $curentOrder->getIncrementId();
                $storeId = $curentOrder->getStoreId();
                $syncId = $this->resourceModelKemsOrder->getSyncIdforOrder($storeId);
                $realtimeSync = $this->scopeConfigInterface->getValue('amconnectorsync/ordersync/realtimesync', 'stores', $storeId);
		        //$realtimeSync = 1;
                if($realtimeSync) {
                    if (isset($syncId)) {
                        $this->logger->addInfo("php ".BP."/bin/magento kensium:sync order " . $syncId . " " . $storeId . " INDIVIDUAL REALTIME ".$orderId."  NULL");
                        //$this->orderSync->getOrderSync('INDIVIDUAL', 'REALTIME', $syncId, $storeId, $orderId, NULL);
                        exec("php " . BP . "/bin/magento kensium:sync order " . $syncId . " " . $storeId . " INDIVIDUAL REALTIME  NULL " . $orderId . "> /dev/null & 1 & echo $!", $out);
                        }
                    }
                }
            }
        }
    }

?>
