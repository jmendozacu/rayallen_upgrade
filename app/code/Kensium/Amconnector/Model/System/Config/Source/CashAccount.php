<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Model\System\Config\Source;

use Symfony\Component\Config\Definition\Exception\Exception;

class CashAccount implements \Magento\Framework\Option\ArrayInterface
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
        \Magento\Config\Model\ResourceModel\Config $config,
        \Magento\Framework\App\ResourceConnection $resource
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
        $sql = "SELECT * FROM " . $this->config->getTable('amconnector_acumatica_cashaccount_attribute');
        try{
            $result = $connection->fetchAll($sql);
        }catch (Exception $e){
            echo $e->getMessage();
        }
        $cashAccountData = array();
        foreach($result as $cashAccount){
            $cashAccountData[]= array('value' => $cashAccount['cash_account'],'label' => $cashAccount['cash_account']);
        }
        return $cashAccountData;
    }
}
