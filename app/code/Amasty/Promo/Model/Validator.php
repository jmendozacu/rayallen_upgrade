<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Amasty\Promo\Model;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * SalesRule Validator Model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @method mixed getCouponCode()
 * @method Validator setCouponCode($code)
 * @method mixed getWebsiteId()
 * @method Validator setWebsiteId($id)
 * @method mixed getCustomerGroupId()
 * @method Validator setCustomerGroupId($id)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Validator extends \Magento\SalesRule\Model\Validator
{
    /**
     * Calculate quote totals for each rule and save results
     *
     * @param mixed $items
     * @param Address $address
     * @return $this
     */
    public function initTotals($items, Address $address)
    {
        $address->setCartFixedRules([]);

        if (!$items) {
            return $this;
        }

        /** @var \Magento\SalesRule\Model\Rule $rule */
        foreach ($this->_getRules() as $rule) {
            if ((\Magento\SalesRule\Model\Rule::CART_FIXED_ACTION == $rule->getSimpleAction() || \Amasty\Promo\Model\Rule::CART_PERCENTAGE_ACTION == $rule->getSimpleAction())
                && $this->validatorUtility->canProcessRule($rule, $address)
            ) {
                $ruleTotalItemsPrice = 0;
                $ruleTotalBaseItemsPrice = 0;
                $validItemsCount = 0;

                foreach ($items as $item) {
                    //Skipping child items to avoid double calculations
                    if ($item->getParentItemId()) {
                        continue;
                    }
                    if (!$rule->getActions()->validate($item)) {
                        continue;
                    }
                    if (!$this->canApplyDiscount($item)) {
                        continue;
                    }
                    $qty = $this->validatorUtility->getItemQty($item, $rule);
                    $ruleTotalItemsPrice += $this->getItemPrice($item) * $qty;
                    $ruleTotalBaseItemsPrice += $this->getItemBasePrice($item) * $qty;
                    $validItemsCount++;
                }

                $this->_rulesItemTotals[$rule->getId()] = [
                    'items_price' => $ruleTotalItemsPrice,
                    'base_items_price' => $ruleTotalBaseItemsPrice,
                    'items_count' => $validItemsCount,
                ];
            }
        }

        return $this;
    }


}
