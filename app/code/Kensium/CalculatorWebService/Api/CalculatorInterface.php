<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\CalculatorWebService\Api;

/**
 * @api
 */
interface CalculatorInterface
{
    /**
     * @param string $giftCardCode Left hand operand.
     * @param string $branchName Right hand operand.
     * @return boolean;
     * @throws \Exception
     */
    public function checkGiftCard($giftCardCode,$branchName);

    /**
     * @param Kensium\CalculatorWebService\Api\JsondataInterface $orderData
     * @param Kensium\CalculatorWebService\Api\AddressdataInterface $customerAddress
     * @param Kensium\CalculatorWebService\Api\BillingdataInterface $billingAddress
     * @param Kensium\CalculatorWebService\Api\ShippingdataInterface $shippingAddress
     * @param Kensium\CalculatorWebService\Api\ItemsdataInterface $items
     * @return string;
     * @throws \Exception
     */
    public function createorder($orderData, $customerAddress,$billingAddress,$shippingAddress,$items);

    /**
     * @param Kensium\CalculatorWebService\Api\JsonOrderInterface $orderData
     * @return string;
     * @throws \Exception
     */
    public function updateOrder($orderData);
}
