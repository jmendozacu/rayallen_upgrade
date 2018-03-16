<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Model\System\Config\Source;

use Symfony\Component\Config\Definition\Exception\Exception;

class CustomerClass implements \Magento\Framework\Option\ArrayInterface
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
     * @var
     */
    protected $connection;

    /**
     * @param \Kensium\Amconnector\Helper\Sync $syncHelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Config\Model\ResourceModel\Config $config
    )
    {
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
        $storeId = $this->syncHelper->getCurrentStoreId();
        if($storeId == 0){
            $storeId = 1;
        }
        $result = array();
        $sql = "SELECT * FROM " . $this->config->getTable('amconnector_customerclass_details'). " WHERE store_id = ".$storeId ;
        try{
            $result = $connection->fetchAll($sql);
        }catch (Exception $e){
            echo $e->getMessage();
        }
        $customerClassData = array();
        foreach($result as $customerClass){
            $customerClassData[]= array('value' => $customerClass['customer_class'],'label' => $customerClass['customer_class']);
        }
        return $customerClassData;
    }
}
