<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;
class Frequency
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '4', 'label' => 'Minutes'),
            array('value' => '0', 'label' => 'Hourly'),
            array('value' => '1', 'label' => 'Daily'),
            array('value' => '2', 'label' => 'Weekly'),
            array('value' => '3', 'label' => 'Monthly')
        );
    }
}
