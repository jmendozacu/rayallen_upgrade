<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Amasty\Promo\Model;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\FreeShippingInterface;

class Shipping extends \Magento\Quote\Model\Quote\Address\Total\Shipping
{
    /**
     * Collect totals information about shipping
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
        $address = $shippingAssignment->getShipping()->getAddress();
        $method = $shippingAssignment->getShipping()->getMethod();

        $cart = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magento\Checkout\Model\Cart');

        $address->setWeight(0);
        $address->setFreeMethodWeight(0);

        $addressWeight = $address->getWeight();
        $freeMethodWeight = $address->getFreeMethodWeight();

        $address->setFreeShipping(
            $this->freeShipping->isFreeShipping($quote, $shippingAssignment->getItems())
        );
        $total->setTotalAmount($this->getCode(), 0);
        $total->setBaseTotalAmount($this->getCode(), 0);

        if (!count($shippingAssignment->getItems())) {
            return $this;
        }

        $addressQty = 0;
        $OverSizeItem = false;
        foreach ($shippingAssignment->getItems() as $item) {
            /**
             * Skip if this item is virtual
             */
            if ($item->getProduct()->isVirtual()) {
                continue;
            }

            /**
             * Children weight we calculate for parent
             */
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isShipSeparately()) {
                foreach ($item->getChildren() as $child) {
                    if ($child->getProduct()->isVirtual()) {
                        continue;
                    }
                    $addressQty += $child->getTotalQty();

                    if (!$item->getProduct()->getWeightType()) {
                        $itemWeight = $child->getWeight();
                        $itemQty = $child->getTotalQty();
                        $rowWeight = $itemWeight * $itemQty;
                        $addressWeight += $rowWeight;
                        if ($address->getFreeShipping() || $child->getFreeShipping() === true) {
                            $rowWeight = 0;
                        } elseif (is_numeric($child->getFreeShipping())) {
                            $freeQty = $child->getFreeShipping();
                            if ($itemQty > $freeQty) {
                                $rowWeight = $itemWeight * ($itemQty - $freeQty);
                            } else {
                                $rowWeight = 0;
                            }
                        }
                        $freeMethodWeight += $rowWeight;
                        $item->setRowWeight($rowWeight);
                    }
                }
                if ($item->getProduct()->getWeightType()) {
                    $itemWeight = $item->getWeight();
                    $rowWeight = $itemWeight * $item->getQty();
                    $addressWeight += $rowWeight;
                    if ($address->getFreeShipping() || $item->getFreeShipping() === true) {
                        $rowWeight = 0;
                    } elseif (is_numeric($item->getFreeShipping())) {
                        $freeQty = $item->getFreeShipping();
                        if ($item->getQty() > $freeQty) {
                            $rowWeight = $itemWeight * ($item->getQty() - $freeQty);
                        } else {
                            $rowWeight = 0;
                        }
                    }
                    $freeMethodWeight += $rowWeight;
                    $item->setRowWeight($rowWeight);
                }
            } else {
                if (!$item->getProduct()->isVirtual()) {
                    $addressQty += $item->getQty();
                }
                $itemWeight = $item->getWeight();
                $rowWeight = $itemWeight * $item->getQty();
                $addressWeight += $rowWeight;
                if ($address->getFreeShipping() || $item->getFreeShipping() === true) {
                    $rowWeight = 0;
                } elseif (is_numeric($item->getFreeShipping())) {
                    $freeQty = $item->getFreeShipping();
                    if ($item->getQty() > $freeQty) {
                        $rowWeight = $itemWeight * ($item->getQty() - $freeQty);
                    } else {
                        $rowWeight = 0;
                    }
                }
                $freeMethodWeight += $rowWeight;
                $item->setRowWeight($rowWeight);
            }
            $oversizeCollection = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Kensium\OverSize\Model\Oversizeship')->getCollection()->addFieldToFilter('sku', $item->getProduct()->getSku());
            if (count($oversizeCollection)) {
                $OverSizeItem = true;
            }
        }

        if (isset($addressQty)) {
            $address->setItemQty($addressQty);
        }

        $address->setWeight($addressWeight);
        $address->setFreeMethodWeight($freeMethodWeight);
        $ruleId = $cart->getQuote()->getAppliedRuleIds() ? $cart->getQuote()->getAppliedRuleIds() : $cart->getCheckoutSession()->getRuleIds();
        if ($ruleId /* && $cart->getQuote()->getCouponCode() */) {
            $ampromoRule = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Magento\SalesRule\Model\Rule');
            $ampromoRule->load($ruleId);
            if (($ampromoRule->getSimpleFreeShipping() == 1 || $ampromoRule->getSimpleFreeShipping() == 2) && !$OverSizeItem) {
                $address->setFreeShipping(true);
            }
            $cart->getCheckoutSession()->setRuleIds($ruleId);
        } else {
            $ruleId = '';
            $cart->getCheckoutSession()->setRuleIds('');
        }
        $address->collectShippingRates();

        if ($method) {
            foreach ($address->getAllShippingRates() as $rate) {
                if ($rate->getCode() == $method) {
                    $store = $quote->getStore();
                    $amountPrice = $this->priceCurrency->convert(
                        $rate->getPrice(),
                        $store
                    );
                    $total->setTotalAmount($this->getCode(), $amountPrice);
                    $total->setBaseTotalAmount($this->getCode(), $rate->getPrice());
                    $shippingDescription = $rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle();
                    $address->setShippingDescription(trim($shippingDescription, ' -'));
                    $total->setBaseShippingAmount($rate->getPrice());


                    if ($ruleId) {
                        if (($ampromoRule->getSimpleFreeShipping() == 1 || $ampromoRule->getSimpleFreeShipping() == 2) && !$OverSizeItem) {
                            $total->setShippingAmount(0);
                            $total->setShippingDescription('Free Shipping');
                        } else {
                            $total->setShippingAmount($amountPrice);
                            $total->setShippingDescription($address->getShippingDescription());
                        }
                    } else {
                        $total->setShippingAmount($amountPrice);
                        $total->setShippingDescription($address->getShippingDescription());
                    }

                    break;
                }
            }
        }
        return $this;
    }

}
