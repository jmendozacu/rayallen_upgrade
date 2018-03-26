<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Promo
 */


namespace Amasty\Promo\Observer\Salesrule;

use Amasty\Promo\Model\Rule;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class Discount implements ObserverInterface
{
    const PROMO_RULES = [
        Rule::PER_PRODUCT,
        Rule::SAME_PRODUCT,
        Rule::SPENT,
        Rule::WHOLE_CART
    ];
    /**
     * @var \Amasty\Promo\Helper\Item
     */
    private $promoItemHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Amasty\Promo\Model\DiscountCalculator
     */
    private $discountCalculator;

    /**
     * @var \Amasty\Promo\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(
        \Amasty\Promo\Helper\Item $promoItemHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Amasty\Promo\Model\DiscountCalculator $discountCalculator,
        \Amasty\Promo\Model\RuleFactory $ruleFactory,
        \Magento\Framework\App\State $state
    ) {
        $this->promoItemHelper = $promoItemHelper;
        $this->productRepository = $productRepository;
        $this->discountCalculator = $discountCalculator;
        $this->ruleFactory = $ruleFactory;
        $this->state = $state;
    }

    /**
     * @param Observer $observer
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data|void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote\Item $item */
        $item = $observer->getItem();
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $result */
        $result = $observer->getResult();

        if ($this->promoItemHelper->isPromoItem($item)
            && in_array($observer->getRule()->getSimpleAction(), self::PROMO_RULES)
        ) {
            $isValid = $this->checkItemForPromo($observer, $item);
            try {
                $areaCode = $this->state->getAreaCode();
            } catch (LocalizedException $exception) {
                $areaCode = 'frontend';
            }
            if ($isValid) {
                if (!$item->getDiscountCalculator() && !$item->getAmDiscountAmount()) {
                    $baseDiscount = $this->discountCalculator->getBaseDiscountAmount($observer->getRule(), $item);
                    $discount = $this->discountCalculator->getDiscountAmount();

                    $result->setBaseAmount($baseDiscount);
                    $result->setAmount($discount);
                    $item->setAmBaseDiscountAmount($baseDiscount);
                    $item->setAmDiscountAmount($discount);
                } else if ($areaCode === 'webapi_rest') {
                    $result->setAmount($item->getAmDiscountAmount());
                    $result->setBaseAmount($item->getAmBaseDiscountAmount());
                }
            }
        }
    }

    /**
     * @param Observer $observer
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return bool
     */
    private function checkItemForPromo($observer, $item)
    {
        $itemSku = $item->getSku();
        $ampromoRule = $observer->getRule()->getAmpromoRule();
        if (!$ampromoRule) {
            $ampromoRule = $this->ruleFactory->create();
            $ampromoRule = $ampromoRule->loadBySalesrule($observer->getRule());
        }
        $promoDiscount = $ampromoRule->getItemsDiscount();
        $minimalPrice = $ampromoRule->getMinimalItemsPrice();
        if (!$minimalPrice && (!$promoDiscount || $promoDiscount === "100%")) {
            return false;
        }
        $promoSku = explode(",", $observer->getRule()->getAmpromoRule()->getSku());
        $isValid = false;
        foreach ($promoSku as $sku) {
            if ($sku && stristr($itemSku, $sku)) {
                $isValid = true;
                break;
            }
        }

        return $isValid || $observer->getRule()->getSimpleAction() === Rule::SAME_PRODUCT;
    }
}
