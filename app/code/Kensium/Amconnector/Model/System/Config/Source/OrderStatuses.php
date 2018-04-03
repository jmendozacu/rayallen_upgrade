<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;
class OrderStatuses
{
    /**
     * @var
     */
    protected $resource;
    /**
     * @var
     */
    protected $connection;
    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
      \Magento\Framework\App\ResourceConnection $resource,
      \Magento\Config\Model\ResourceModel\Config $config
    )
    {
        $this->_resource = $resource;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $sql = "SELECT * FROM " . $this->config->getTable('sales_order_status');
        try{
            $orderStatusesCollection = $connection->fetchAll($sql);
        }catch (Exception $e){
            echo $e->getMessage();
        }
        $orderStatusesArray = array();
        foreach($orderStatusesCollection as $order){
            $orderStatusesArray[] = array('value' => $order['status'],'label' => $order['label']);
        }
        return $orderStatusesArray;
    }
}