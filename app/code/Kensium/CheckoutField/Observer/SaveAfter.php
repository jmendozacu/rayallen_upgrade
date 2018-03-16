<?php
/*
 * Observer for updating file flag for an order.
 * 
 * 
 */
namespace Kensium\CheckoutField\Observer;

/**
 * Class SaveAfter
 * @package Kensium\Process\Observer
 */
class SaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $_resource;
    protected $_customerSession;
    
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Session $customerSession

    )
    {
    $this->_resource = $resource;
    $this->_customerSession = $customerSession;	
    }
    
    /**
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrderIds();
        $prNumber = $this->_customerSession->getPrNumber(); 
        
        $connection = $this->_resource->getConnection();
        $tbl = $this->_resource->getTableName('sales_order');
        $query = "Update " . $tbl . " set pr_number = '{$prNumber}' Where entity_id = '{$orderId[0]}'";
        $connection->query($query);        
        
    }

}
