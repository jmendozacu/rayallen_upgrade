<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\CalculatorWebService\Model;

use Kensium\CalculatorWebService\Api\JsondataInterface;

class Jsondata implements JsondataInterface
{
    protected $currency_id;
    protected $email;
    protected $customer_type;
    protected $coupon_code;
    protected $giftcard; //09OKWB1QBN35,01WPQVLOA8YQ
    protected $shipping_method;
    protected $shipping_amount;
    protected $payment_method;
    protected $items;
    protected $branch_name;
    protected $customer_id;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->currency_id = '';
        $this->email = '';
        $this->customer_type = '';
        $this->coupon_code = '';
        $this->giftcard = '';
        $this->shipping_method = '';
	$this->shipping_amount = '';
        $this->payment_method = '';
        $this->items = [];
        $this->branch_name ='';
        $this->customer_id ='';
    }

    /**
     * @api
     * @return string
     */
    public function getCurrencyId() {
        return $this->currency_id;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCurrencyId($value) {
        $this->currency_id = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setEmail($value) {
        $this->email = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getCustomerType() {
        return $this->customer_type;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCustomerType($value) {
        $this->customer_type = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getCouponCode() {
        return $this->coupon_code;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCouponCode($value) {
        $this->coupon_code = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getGiftcard() {
        return $this->giftcard;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setGiftcard($value) {
        $this->giftcard = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getPaymentMethod() {
        return $this->payment_method;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setPaymentMethod($value) {
        $this->payment_method = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getShippingMethod() {
        return $this->shipping_method;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setShippingMethod($value) {
        $this->shipping_method = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getShippingAmount() {
        return $this->shipping_amount;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setShippingAmount($value) {
        $this->shipping_amount = $value;
    }

    /**
     * @api
     * @return string[]
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * @api
     * @param $value string[]
     * @return null
     */
    public function setItems($value) {
        $this->items = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getBranchName() {
        return $this->branch_name;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setBranchName($value) {
        $this->branch_name = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getCustomerId() {
        return $this->customer_id;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCustomerId($value) {
        $this->customer_id = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getAcmRefNo() {
        return $this->acm_ref_no;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setAcmRefNo($value) {
        $this->acm_ref_no = $value;
    }
}

