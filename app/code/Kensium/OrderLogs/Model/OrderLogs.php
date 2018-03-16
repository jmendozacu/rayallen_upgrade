<?php
/**
 * Copyright © 2016 Kensium . All rights reserved.
*/
namespace Kensium\OrderLogs\Model;

class OrderLogs extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Kensium\OrderLogs\Model\ResourceModel\OrderLogs');
    }
}
