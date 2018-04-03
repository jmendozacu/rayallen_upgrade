<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;
class DeleteArchiveDay
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '90', 'label' => '90'),
            array('value' => '120', 'label' => '120'),
            array('value' => '150', 'label' => '150')
        );
    }
}
