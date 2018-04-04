<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class Config
 *
 * @package Amasty\Orderattr\Helper
 *
 * @method boolean getCheckoutProgress
 * @method boolean getCheckoutHideEmpty
 * @method boolean getPdfShipment
 * @method boolean getPdfInvoice
 * @method boolean getShowInvoiceGrid
 * @method boolean getShowInvoiceView
 * @method boolean getShowShipmentGrid
 * @method boolean getShowShipmentView
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CHECKOUT_PROGRESS = 'amorderattr/checkout/progress';
    const CHECKOUT_HIDE_EMPTY = 'amorderattr/checkout/hide_empty';
    const CHECKOUT_DATE_FORMAT = 'amorderattr/checkout/format';
    const PDF_SHIPMENT = 'amorderattr/pdf/shipment';
    const PDF_INVOICE = 'amorderattr/pdf/invoice';
    const SHOW_INVOICE_GRID = 'amorderattr/invoices_shipments/invoice_grid';
    const SHOW_INVOICE_VIEW = 'amorderattr/invoices_shipments/invoice_view';
    const SHOW_SHIPMENT_GRID = 'amorderattr/invoices_shipments/shipment_grid';
    const SHOW_SHIPMENT_VIEW = 'amorderattr/invoices_shipments/shipment_view';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface\Proxy $localeDate
    ) {
        $this->localeDate = $localeDate;
        parent::__construct($context);
    }

    public function getCarrierConfigValue($carrierCode)
    {
        $configPath = sprintf('carriers/%s/title', $carrierCode);
        return $this->scopeConfig->getValue($configPath);
    }

    public function getRequiredOnFrontOnlyId()
    {
        return 2;
    }

    public function getCheckoutDateFormat()
    {
        $value = $this->getValue(self::CHECKOUT_DATE_FORMAT);
        if (!$value) {
            $value = $this->localeDate->getDateFormatWithLongYear();
        }

        return $value;
    }

    /**
     * Return Date format ready for calendar use
     *
     * @return string
     */
    public function getDateFormatJs()
    {
        $format = $this->getCheckoutDateFormat();
        return $this->convertDateFormat($format);
    }

    /**
     * Return Time format ready for calendar use
     *
     * @return string
     */
    public function getTimeFormatJs()
    {
        $format = $this->getTimeFormat();
        return $this->convertDateFormat($format);
    }

    /**
     * Prepare date format template for calendar
     *
     * @param string $format
     *
     * @return string
     */
    public function convertDateFormat($format)
    {
        return preg_replace(['/y{2,}/s', '/z{2,}/s'], ['Y', 'z'], $format);
    }

    /**
     * @return string
     */
    public function getTimeFormat()
    {
        return $this->localeDate->getTimeFormat();
    }

    /**
     * Date with Time format
     *
     * @return string
     */
    public function getCheckoutDateTimeFormat()
    {
        return $this->getCheckoutDateFormat() . ' ' . $this->getTimeFormat();
    }

    protected function underscore($name)
    {
        return strtolower(
            trim(preg_replace('/([A-Z]|[0-9]+)/', "_$1", $name), '_')
        );
    }

    protected function getValue($key)
    {
        return $this->scopeConfig->getValue($key);
    }

    public function __call($getterName, $arguments)
    {
        switch (substr($getterName, 0, 3)) {
            case 'get':
                $key = $this->underscore(substr($getterName, 3));
                $key = function_exists('mb_strtoupper')
                    ? mb_strtoupper($key) : strtoupper($key);
                $configPath = constant("static::$key");
                return $this->getValue($configPath);
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('Invalid method %1::%2(%3)', [get_class($this), $getterName])
        );
    }

}
