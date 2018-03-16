<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\CalculatorWebService\Api;

interface JsonOrderInterface
{
    /**
     *
     * @api
     * @return string.
     */
    public function getOrderIncrementId();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setOrderIncrementId($value);

    /**
     *
     * @api
     * @return string.
     */
    public function getAcumaticaOrderReference();

    /**
     * @api
     * @param $value string
     * @return null
     */
    public function setAcumaticaOrderReference($value);
}
