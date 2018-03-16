<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\GiftCard\Api;

interface JsondataInterface
{
    /**
     *
     * @api
     * @return string.
     */
    public function getCurrencyId();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCurrencyId($value);

    /**
     *
     * @api
     * @return string.
     */
    public function getEmail();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setEmail($value);

    /**
     *
     * @api
     * @return string.
     */
    public function getCustomerType();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCustomerType($value);

    /**
     * @api
     * @return string
     */
    public function getCouponCode() ;

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCouponCode($value) ;

    /**
     * @api
     * @return string
     */
    public function getPaymentMethod() ;

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setPaymentMethod($value);

    /**
     * @api
     * @return string
     */
    public function getShippingMethod() ;

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setShippingMethod($value);

    /**
     * @api
     * @return string[]
     */
    public function getItems() ;

    /**
     * @api
     * @param $value string[]
     * @return null
     */
    public function setItems($value);

    /**
     * @api
     * @return string
     */
    public function getAcmRefNo() ;

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setAcmRefNo($value);

    /**
     * @api
     * @return string
     */
    public function getBranchName() ;

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setBranchName($value);

    /**
     * @api
     * @return string
     */
    public function getCustomerId() ;

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCustomerId($value);
}
