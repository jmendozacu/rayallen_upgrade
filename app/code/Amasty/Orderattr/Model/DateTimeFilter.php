<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Model;

use Magento\Framework\Stdlib\DateTime;

class DateTimeFilter extends \Magento\Framework\Data\Form\Filter\Date
{
    /**
     * Sometimes Magento is not returning seconds - remove seconds from pattern before validate
     */
    const DATETIME_INTERNAL_VALIDATION_FORMAT = 'yyyy-MM-dd HH:mm';

    /**
     * @var \Amasty\Orderattr\Helper\Config
     */
    private $config;

    public function __construct(
        \Amasty\Orderattr\Helper\Config $config,
        $format = null,
        \Magento\Framework\Locale\ResolverInterface $localeResolver = null
    ) {
        $this->config = $config;
        if ($format === null) {
            $format = $this->config->getCheckoutDateTimeFormat();
        }
        parent::__construct($format, $localeResolver);
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value date in format $this->_dateFormat
     *
     * @return string          date in format DateTime::DATETIME_INTERNAL_FORMAT
     */
    public function inputFilter($value)
    {
        if (!$this->validateInputDate($value)) {
            return $value;
        }
        $options = [
            'date_format' => $this->_dateFormat,
            'locale'      => $this->localeResolver->getLocale()
        ];
        $filterInternal = new \Zend_Filter_NormalizedToLocalized(
            ['date_format' => DateTime::DATETIME_INTERNAL_FORMAT, 'locale' => $this->localeResolver->getLocale()]
        );

        //parse date
        $value = \Zend_Locale_Format::getDate($value, $options);
        $value = $filterInternal->filter($value);

        return $value;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value date in format DateTime::DATETIME_INTERNAL_FORMAT
     *
     * @return string         date in format $this->_dateFormat
     */
    public function outputFilter($value)
    {
        if (!$this->validateOutputDate($value)) {
            return $value;
        }
        $options = [
            'date_format' => DateTime::DATETIME_INTERNAL_FORMAT,
            'locale'      => $this->localeResolver->getLocale()
        ];
        $filterInternal = new \Zend_Filter_NormalizedToLocalized(
            ['date_format' => $this->_dateFormat, 'locale' => $this->localeResolver->getLocale()]
        );

        //parse date
        $value = \Zend_Locale_Format::getDate($value, $options);
        $value = $filterInternal->filter($value);

        return $value;
    }

    /**
     * Sometimes Magento is not returning seconds - remove seconds from pattern before validate
     * if in date pattern will be seconds, it will not passed
     *
     * @param string $value
     *
     * @return bool
     */
    public function validateInputDate($value)
    {
        $options = [
            'date_format' => str_replace('s', '', $this->_dateFormat),
            'locale'      => $this->localeResolver->getLocale()
        ];

        return \Zend_Locale_Format::checkDateFormat($value, $options);
    }

    /**
     * Sometimes Magento is not returning seconds - remove seconds from pattern before validate
     * if in date pattern will be seconds, it will not passed
     *
     * @param string $value
     *
     * @return bool
     */
    public function validateOutputDate($value)
    {
        $options = [
            'date_format' => self::DATETIME_INTERNAL_VALIDATION_FORMAT,
            'locale'      => $this->localeResolver->getLocale()
        ];

        return \Zend_Locale_Format::checkDateFormat($value, $options);
    }
}
