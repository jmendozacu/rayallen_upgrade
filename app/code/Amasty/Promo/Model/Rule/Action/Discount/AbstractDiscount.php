<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Promo
 */

namespace Amasty\Promo\Model\Rule\Action\Discount;

abstract class AbstractDiscount extends \Magento\SalesRule\Model\Rule\Action\Discount\AbstractDiscount
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Amasty\Promo\Model\Registry
     */
    protected $promoRegistry;

    /**
     * @var \Amasty\Promo\Helper\Item
     */
    protected $promoItemHelper;

    /**
     * @param \Magento\SalesRule\Model\Validator $validator
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory $discountDataFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Amasty\Promo\Helper\Item $promoItemHelper
     * @param \Amasty\Promo\Model\Registry $promoRegistry
     * 
     */
    public function __construct(
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory $discountDataFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Promo\Helper\Item $promoItemHelper,
        \Amasty\Promo\Model\Registry $promoRegistry
    ) {
        parent::__construct($validator, $discountDataFactory, $priceCurrency);

        $this->_objectManager = $objectManager;
        $this->promoItemHelper = $promoItemHelper;
        $this->promoRegistry = $promoRegistry;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     *
     */
    public function calculate($rule, $item, $qty)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        $this->_addFreeItems($rule, $item, $qty);

        return $discountData;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param $qty
     *
     */
    protected function _addFreeItems(
        \Magento\SalesRule\Model\Rule $rule,
        \Magento\Quote\Model\Quote\Item $item,
        $qty
    ) {
        if (!$this->promoRegistry->getApplyAttempt($rule->getId()))
            return;

        $ampromoRule = $this->_objectManager->get('Amasty\Promo\Model\Rule');

        $ampromoRule = $ampromoRule->loadBySalesrule($rule);

        $promoSku = $ampromoRule->getSku();
        if (!$promoSku)
            return;

        $quote = $item->getQuote();

        $qty = $this->_getFreeItemsQty($rule, $quote);
        if (!$qty)
            return;

        if ($ampromoRule->getType() == \Amasty\Promo\Model\Rule::RULE_TYPE_ONE)
        {
            $this->promoRegistry->addPromoItem(
                preg_split('/\s*,\s*/', $promoSku, -1, PREG_SPLIT_NO_EMPTY),
                $qty,
                $rule->getId()
            );
        }
        else
        {
            $promoSku = explode(',', $promoSku);
            foreach ($promoSku as $sku){
                $sku = trim($sku);
                if (!$sku){
                    continue;
                }

                $this->promoRegistry->addPromoItem($sku, $qty, $rule->getId());
            }
        }
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote $quote
     * @return mixed
     *
     */
    protected function _getFreeItemsQty(
        \Magento\SalesRule\Model\Rule $rule,
        \Magento\Quote\Model\Quote $quote
    ) {
        $qty = max(1, $rule->getDiscountAmount());

        return $qty;
    }
}
