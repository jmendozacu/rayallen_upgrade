<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model;

use Magento\Framework\Model\AbstractModel;

class Order extends AbstractModel
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('Kensium\Amconnector\Model\ResourceModel\Order');
    }
}
