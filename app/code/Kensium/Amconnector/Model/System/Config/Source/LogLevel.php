<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;
class LogLevel
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => 'EMERG'),
            array('value' => '1', 'label' => 'ALERT'),
            array('value' => '2', 'label' => 'CRIT'),
            array('value' => '3', 'label' => 'ERROR'),
            array('value' => '4', 'label' => 'WARN'),
            array('value' => '5', 'label' => 'NOTICE'),
            array('value' => '6', 'label' => 'INFO'),
            array('value' => '7', 'label' => 'DEBUG')
        );
    }
}
