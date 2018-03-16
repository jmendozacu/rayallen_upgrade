<?php
/**
 * Copyright © 2016 Kensium . All rights reserved.
*/
namespace Kensium\OrderLogs\Model\ResourceModel\OrderLogs;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kensium\OrderLogs\Model\OrderLogs', 'Kensium\OrderLogs\Model\ResourceModel\OrderLogs');
    }
}
