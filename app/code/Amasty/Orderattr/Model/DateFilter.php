<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Model;

use Magento\Framework\Stdlib\DateTime;

class DateFilter extends \Magento\Framework\Data\Form\Filter\Date
{
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
            $format = $this->config->getCheckoutDateFormat();
        }
        parent::__construct($format, $localeResolver);
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     *
     * @return string
     */
    public function inputFilter($value)
    {
        if (!$this->validateInputDate($value)) {
            return $value;
        }
        $filterInput = new \Zend_Filter_LocalizedToNormalized(
            ['date_format' => $this->_dateFormat, 'locale' => $this->localeResolver->getLocale()]
        );
        $filterInternal = new \Zend_Filter_NormalizedToLocalized(
            ['date_format' => DateTime::DATE_INTERNAL_FORMAT, 'locale' => $this->localeResolver->getLocale()]
        );

        $value = $filterInput->filter($value);
        $value = $filterInternal->filter($value);

        return $value;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     *
     * @return string
     */
    public function outputFilter($value)
    {
        if (!$this->validateOutputDate($value)) {
            return $value;
        }
        $filterInput = new \Zend_Filter_LocalizedToNormalized(
            ['date_format' => DateTime::DATE_INTERNAL_FORMAT, 'locale' => $this->localeResolver->getLocale()]
        );
        $filterInternal = new \Zend_Filter_NormalizedToLocalized(
            ['date_format' => $this->_dateFormat, 'locale' => $this->localeResolver->getLocale()]
        );

        $value = $filterInput->filter($value);
        $value = $filterInternal->filter($value);

        return $value;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function validateInputDate($value)
    {
        $options = [
            'date_format' => $this->_dateFormat,
            'locale'      => $this->localeResolver->getLocale()
        ];

        return \Zend_Locale_Format::checkDateFormat($value, $options);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function validateOutputDate($value)
    {
        $options = [
            'date_format' => DateTime::DATE_INTERNAL_FORMAT,
            'locale'      => $this->localeResolver->getLocale()
        ];

        return \Zend_Locale_Format::checkDateFormat($value, $options);
    }
}
