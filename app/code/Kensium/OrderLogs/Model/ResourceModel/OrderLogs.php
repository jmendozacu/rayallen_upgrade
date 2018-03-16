<?php
/**
 * Copyright © 2016 Kensium . All rights reserved.
*/
namespace Kensium\OrderLogs\Model\ResourceModel;

class OrderLogs extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amconnector_order_log', 'id');
    }
}
