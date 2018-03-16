<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\GiftCard\Api;

interface ItemsdataInterface
{
    /**
     * @api
     * @return string
     */
    public function getSkus() ;

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setSkus($value) ;

    /**
     * @api
     * @return string
     */
    public function getQtys() ;

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setQtys($value) ;

    /**
     * @api
     * @return string
     */
    public function getPrices();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setPrices($value) ;

    /**
     * @api
     * @return string
     */
    public function getSenderNames();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setSenderNames($value) ;
    /**
     * @api
     * @return string
     */
    public function getSenderEmails();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setSenderEmails($value) ;
    /**
     * @api
     * @return string
     */
    public function getReceiverNames();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setReceiverNames($value) ;
    /**
     * @api
     * @return string
     */
    public function getReceiverEmails();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setReceiverEmails($value) ;
    /**
     * @api
     * @return string
     */
    public function getMessages();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setMessages($value) ;

}
