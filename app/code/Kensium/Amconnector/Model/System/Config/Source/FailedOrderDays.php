<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;
class FailedOrderDays
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' => '1'),
            array('value' => '2', 'label' => '2'),
            array('value' => '3', 'label' => '3'),
            array('value' => '4', 'label' => '4'),
            array('value' => '5', 'label' => '5'),
            array('value' => '6', 'label' => '6'),
            array('value' => '7', 'label' => '7')
        );
    }
}
