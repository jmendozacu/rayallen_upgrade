<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace Kensium\Amconnector\Model\System\Config\Source;

class LicenseType implements \Magento\Framework\Option\ArrayInterface
{

    const FLAG_ANNUAL = 'Annual';
    const FLAG_PERPETUAL = 'Perpetual';
    const FLAG_TRIAL = 'Trial';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => self::FLAG_TRIAL, 'label' => self::FLAG_TRIAL], ['value' => self::FLAG_ANNUAL, 'label' => self::FLAG_ANNUAL], ['value' => self::FLAG_PERPETUAL, 'label' => self::FLAG_PERPETUAL]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('No'), 1 => __('Yes')];
    }
}
