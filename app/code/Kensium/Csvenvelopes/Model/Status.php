<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Csvenvelopes\Model;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    const PENDING  = 0;
    const APPROVED = 1;
    const REJECTED = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::PENDING => __('Pending'),
            self::APPROVED => __('Approved'),
            self::REJECTED => __('Rejected')
        ];

        return $options;
    }
}
?>