<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\CalculatorWebService\Api;

interface AddressdataInterface
{
    /**
     * @api
     * @return string.
     */
    public function getCity();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCity($value);

    /**
     * @api
     * @return string.
     */
    public function getStreet();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCountryId($value);

    /**
     * @api
     * @return string.
     */
    public function getCountryId();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setStreet($value);

    /**
     * @api
     * @return string.
     */
    public function getRegion();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setRegion($value);

    /**
     * @api
     * @return string.
     */
    public function getPostcode();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setPostcode($value);

    /**
     * @api
     * @return string.
     */
    public function getTelephone();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setTelephone($value);

    /**
     * @api
     * @return string.
     */
    public function getFax();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setFax($value);

    /**
     * @api
     * @return boolean.
     */
    public function getIsDefaultShipping();

    /**
     * @api
     * @param $value boolean
     * @return null
     */
    public function setIsDefaultShipping($value);

    /**
     * @api
     * @return boolean.
     */
    public function getIsDefaultBilling();

    /**
     * @api
     * @param $value boolean
     * @return null
     */
    public function setIsDefaultBilling($value);

}
