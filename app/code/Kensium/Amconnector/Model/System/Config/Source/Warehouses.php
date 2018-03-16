<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Model\System\Config\Source;

use Symfony\Component\Config\Definition\Exception\Exception;

class Warehouses implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var
     */
    protected $syncHelper;
    /**
     * @var
     */
    protected $resource;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var
     */
    protected $connection;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $config;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Magento\Config\Model\ResourceModel\Config $config,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->_storeManager = $storeManager;
        $this->syncHelper = $syncHelper;
        $this->config = $config;
        $this->_resource = $resource;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $storeId = $this->_storeManager->getStore()->getStoreId();
        if($storeId == 0){
            $storeId = 1;
        }
        $result = array();
        $sql = "SELECT * FROM " . $this->config->getTable('amconnector_warehouse_details'). " WHERE store_id = ".$storeId ;
        try{
            $result = $connection->fetchAll($sql);
        }catch (Exception $e){
            echo $e->getMessage();
        }
        $warehouseData = array();
        foreach($result as $warehouse){
            $warehouseData[] = array('value' => $warehouse['warehouse_name'],'label' => $warehouse['warehouse_name']);
        }
        return $warehouseData;
    }
}
