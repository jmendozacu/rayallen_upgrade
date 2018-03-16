<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\GiftCard\Model;

use Kensium\GiftCard\Api\ShippingdataInterface;

class Shippingdata implements ShippingdataInterface
{
    protected $first_name;
    protected $last_name;
    protected $city;
    protected $street;
    protected $region;
    protected $country_id;
    protected $postcode;
    protected $telephone;
    protected $fax;
    protected $save_in_address_book;
    protected $is_default_shipping;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->first_name = '';
        $this->last_name = '';
        $this->city = '';
        $this->street = '';
        $this->region = '';
        $this->country_id = '';
        $this->postcode = '';
        $this->telephone = '';
        $this->fax = '';
        $this->save_in_address_book = '';
    }

    /**
     * @api
     * @return array
     */
    public function getFirstName() {
        return $this->first_name;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setFirstName($value) {
        $this->first_name = $value;
    }

    /**
     * @api
     * @return array
     */
    public function getLastName() {
        return $this->last_name;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setLastName($value) {
        $this->last_name = $value;
    }

    /**
     * @api
     * @return array
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCity($value) {
        $this->city = $value;
    }

    /**
     * @api
     * @return array
     */
    public function getStreet() {
        return $this->street;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setStreet($value) {
        $this->street = $value;
    }

    /**
     * @api
     * @return array
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setRegion($value) {
        $this->region = $value;
    }

    /**
     * @api
     * @return array
     */
    public function getCountryId() {
        return $this->country_id;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setCountryId($value) {
        $this->country_id = $value;
    }

    /**
     * @api
     * @return array
     */
    public function getTelephone() {
        return $this->telephone;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setTelephone($value) {
        $this->telephone = $value;
    }

    /**
     * @api
     * @return array
     */
    public function getPostcode() {
        return $this->postcode;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setPostcode($value) {
        $this->postcode = $value;
    }

    /**
     * @api
     * @return array
     */
    public function getFax() {
        return $this->fax;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setFax($value) {
        $this->fax = $value;
    }

    /**
     * @api
     * @return boolean
     */
    public function getSaveInAddressBook() {
        return $this->save_in_address_book;
    }

    /**
     * @api
     * @param $value boolean
     * @return null
     */
    public function setSaveInAddressBook($value) {
        $this->save_in_address_book = $value;
    }
}

