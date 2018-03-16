<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\CalculatorWebService\Model;

use Kensium\CalculatorWebService\Api\ItemsdataInterface;

class Itemsdata implements ItemsdataInterface
{
    protected $sku;
    protected $qty;
    protected $price;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->sku = '';
        $this->qty = '';
        $this->price = '';
    }

    /**
     * @api
     * @return string
     */
    public function getSku() {
        return $this->sku;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setSku($value) {
        $this->sku = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getSkus() {
        return $this->skus;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setSkus($value) {
        $this->skus = $value;
    }


    /**
     * @api
     * @return string
     */
    public function getQty() {
        return $this->qty;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setQty($value) {
        $this->qty = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getQtys() {
        return $this->qtys;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setQtys($value) {
        $this->qtys = $value;
    }


    /**
     * @api
     * @return string
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setPrice($value) {
        $this->price = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getPrices() {
        return $this->prices;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setPrices($value) {
        $this->prices = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getSenderNames() {
        return $this->sender_names;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setSenderNames($value) {
        $this->sender_names = $value;
    }
    /**
     * @api
     * @return string
     */
    public function getSenderEmails() {
        return $this->sender_emails;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setSenderEmails($value) {
        $this->sender_emails = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getReceiverNames() {
        return $this->receiver_names;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setReceiverNames($value) {
        $this->receiverr_names = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getReceiverEmails() {
        return $this->receiver_emails;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setReceiverEmails($value) {
        $this->receiver_emails = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setMessages($value) {
        $this->messages = $value;
    }


}

