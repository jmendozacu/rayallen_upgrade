<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Model\System\Config\Source;

class LogDays implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '15', 'label' => '15'),
            array('value' => '30', 'label' => '30'),
            array('value' => '45', 'label' => '45'),
            array('value' => '60', 'label' => '60'),
            array('value' => '90', 'label' => '90')
        );
    }
}
