<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\ResourceModel\Sync;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Kensium\Amconnector\Model\Sync', 'Kensium\Amconnector\Model\ResourceModel\Sync');
    }
}
