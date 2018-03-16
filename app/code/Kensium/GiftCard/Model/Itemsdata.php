<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\GiftCard\Model;

use Kensium\GiftCard\Api\ItemsdataInterface;

class Itemsdata implements ItemsdataInterface
{
    protected $skus;
    protected $qtys;
    protected $prices;
    protected $sender_names;
    protected $sender_emails;
    protected $receiver_names;
    protected $receiver_emails;
    protected $messages;


    /**
     * Constructor.
     */
    public function __construct() {
        $this->skus = '';
        $this->qtys = '';
        $this->prices = '';
        $this->sender_names = '';
        $this->sender_emails = '';
        $this->receiver_names = '';
        $this->receiver_emails = '';
        $this->messages = '';
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
     * @return float
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
    public function getReceiverNames() {
        return $this->receiver_names;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setReceiverNames($value) {
        $this->receiver_names = $value;
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
    public function getReceiverEmails() {
        return $this->receiver_emails;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setMessages($value) {
        $this->messages = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getMessages() {
        return $this->messages;
    }
}

