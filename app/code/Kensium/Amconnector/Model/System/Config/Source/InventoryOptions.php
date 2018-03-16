<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;
class InventoryOptions
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' =>'Inventory only'),
            array('value' => '2', 'label' =>'Price only'),
            array('value' => '3', 'label' =>'Inventory and Price'),
        );
    }
}
