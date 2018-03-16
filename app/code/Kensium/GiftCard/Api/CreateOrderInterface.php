<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\GiftCard\Api;

/**
 * @api
 */
interface CreateOrderInterface
{
    /**
     * @param string $giftCardCode
     * @return boolean;
     * @throws \Exception
     */
    public function checkGiftCard($giftCardCode);

    /**
     * @param Kensium\GiftCard\Api\JsondataInterface $orderData
     * @param Kensium\GiftCard\Api\AddressdataInterface $customerAddress
     * @param Kensium\GiftCard\Api\BillingdataInterface $billingAddress
     * @param Kensium\GiftCard\Api\ShippingdataInterface $shippingAddress
     * @param Kensium\GiftCard\Api\ItemsdataInterface $items
     * @return string;
     * @throws \Exception
     */
    public function createOrder($orderData, $customerAddress,$billingAddress,$shippingAddress,$items);
}
