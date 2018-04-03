<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Time
 * @package Kensium\Amconnector\Model\ResourceModel
 */
class Time extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
    }

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amconnector_server_timing', 'id');
    }

    /**
     * @param $values
     * @param $type
     */
    public function updateTime($values,$type){

        $connection = $this->getConnection();

        $tableName = $this->getTable('amconnector_server_timing');
        if($type == 'insert'){
            $values['scope_id'] = 0;
            $connection->insert($tableName, $values);
            $values['scope_id'] = 1;
            $connection->insert($tableName, $values);
        }elseif($type == 'update'){
            $values['scope_id'] = 0;
            $connection->update($tableName, $values, ['scope_id=?' => 0]);
            $values['scope_id'] = 1;
            $connection->update($tableName, $values, ['scope_id=?' => 1]);
        }
    }

    /**
     * @param $values
     * @param $scopeId
     */
    public function update($values,$scopeId){

        $connection = $this->getConnection();

        $tableName = $this->getTable('amconnector_server_timing');

        $connection->update($tableName, $values, ['scope_id=?' => $scopeId]);
    }

    /**
     * @param $values
     */
    public function insert($values){

        $connection = $this->getConnection();

        $tableName = $this->getTable('amconnector_server_timing');

        $connection->insert($tableName, $values);
    }

    /**
     * @return mixed
     */
    public function isExists(){
        $tableName = $this->getTable('amconnector_server_timing');
        return $this->getConnection()->isTableExists($tableName);
    }

    /**
     * @param $scopeId
     * @return array
     */
    public function getData($scopeId){

        $tableName = $this->getTable('amconnector_server_timing');
        $connection = $this->getConnection();
        $selectQry = $connection->select()->from($tableName, 'id')->where('scope_id=?', $scopeId);
        $resultArray = $connection->fetchAll($selectQry);
        return $resultArray;
    }
}
