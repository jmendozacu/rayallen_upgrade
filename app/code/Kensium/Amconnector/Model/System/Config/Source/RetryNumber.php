<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;
class RetryNumber
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' => '1'),
            array('value' => '2', 'label' => '2'),
            array('value' => '3', 'label' => '3')
        );
    }
}
