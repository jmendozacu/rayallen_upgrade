<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Orderstatus\Model;
 
use Magento\Framework\Option\ArrayInterface;
 
class Status implements ArrayInterface
{
    const ENABLED  = 0;
    const DISABLED = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled'),
        ];
 
        return $options;
    }
}
