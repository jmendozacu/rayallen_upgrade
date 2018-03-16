<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Scheduler\Helper;

use Magento\Framework\Stdlib\DateTime\Timezone as TimeZone;

/**
 * Class Data
 * @package Kensium\Scheduler\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $timezone;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param TimeZone $timezone
     */
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        TimeZone $timezone
	)
    {
        $this->timezone = $timezone;
        parent::__construct($context);
    }
/**
 * @param $value
 * @return mixed
 */
    public function decorateTimeFrameCallBack($value) {
        if($value) {
            return $this->decorateTime($value, false, NULL);
        }
    }    /**
 * @param $value
 * @return string
 */
    public function decorateTime($value) {
        $formattedDateTime =  $this->timezone->date($value)->format('M d, Y h:i:s A');
        return $formattedDateTime;
    }
}