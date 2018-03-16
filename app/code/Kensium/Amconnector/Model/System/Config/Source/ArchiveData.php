<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;
class ArchiveData
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'delete', 'label' => 'Delete'),
            array('value' => 'archive', 'label' => 'Archive')
        );
    }
}
