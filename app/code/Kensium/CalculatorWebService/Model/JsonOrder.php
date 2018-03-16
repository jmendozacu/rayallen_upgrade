<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\CalculatorWebService\Model;

use Kensium\CalculatorWebService\Api\JsonOrderInterface;

class JsonOrder implements JsonOrderInterface
{
    protected $order_increment_id;
    protected $acumatica_order_reference;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->order_increment_id = '';
        $this->acumatica_order_reference = '';
    }

    /**
     * @api
     * @return string
     */
    public function getOrderIncrementId() {
        return $this->order_increment_id;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setOrderIncrementId($value) {
        $this->order_increment_id = $value;
    }

    /**
     * @api
     * @return string
     */
    public function getAcumaticaOrderReference() {
        return $this->acumatica_order_reference;
    }

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setAcumaticaOrderReference($value) {
        $this->acumatica_order_reference = $value;
    }
}

