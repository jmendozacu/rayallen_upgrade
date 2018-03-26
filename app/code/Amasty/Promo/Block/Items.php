<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Promo
 */


namespace Amasty\Promo\Block;

use Magento\Framework\Exception\NoSuchEntityException;

class Items extends \Magento\Framework\View\Element\Template
{
    const REGULAR_PRICE = 0;
    const FINAL_PRICE = 1;

    /**
     * @var \Amasty\Promo\Helper\Data
     */
    protected $promoHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $helperImage;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Amasty\Promo\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Magento\Catalog\Block\Product\View
     */
    private $productView;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    private $catalogHelper;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    private $localeFormat;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    private $context;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Promo\Helper\Data $promoHelper,
        \Magento\Catalog\Helper\Image $helperImage,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Amasty\Promo\Helper\Config $config,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Block\Product\View $productView,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\Store $store,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->promoHelper = $promoHelper;
        $this->helperImage = $helperImage;
        $this->urlHelper = $urlHelper;
        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->store = $store;
        $this->productView = $productView;
        $this->registry = $registry;
        $this->catalogHelper = $catalogHelper;
        $this->localeFormat = $localeFormat;
        $this->jsonEncoder = $jsonEncoder;
        $this->priceCurrency = $priceCurrency;
        $this->context = $context;
    }

    /**
     * @return $this|bool|\Magento\Framework\Data\Collection\AbstractDb|null
     */
    public function getItems()
    {
        return $this->promoHelper->getNewItems();
    }

    /**
     * @return \Magento\Catalog\Helper\Image
     */
    public function getImageHelper()
    {
        return $this->helperImage;
    }

    /**
     * @return mixed|string
     */
    public function getCurrentBase64Url()
    {
        if ($this->hasData('current_url')) {
            return $this->getData('current_url');
        }
        
        return $this->urlHelper->getCurrentBase64Url();
    }

    /**
     * @return mixed
     */
    public function getPopupMode()
    {
        return $this->config->getScopeValue("messages/gift_selection_method");
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('amasty_promo/cart/add');
    }

    public function getShowPriceInPopup()
    {
        return $this->config->getScopeValue("messages/show_price_in_popup");
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $product = $this->productRepository->getById($product->getId());
        $price = $product->getPrice() * $this->store->getCurrentCurrencyRate();

        $price = $this->catalogHelper->getTaxPrice($product, $price);

        return $price;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function getOptionsHtml(\Magento\Catalog\Model\Product $product)
    {
        $this->registry->register('current_product', $product);
        $optionsHtml = $this->getChildBlock('options_prototype')->setProduct($product)->toHtml();
        $this->registry->unregister('current_product');
        $optionsHtml = str_replace(
            "#product_addtocart_form",
            "#ampromo_items_form-" . $product->getId(),
            $optionsHtml
        );
        $optionsHtml = str_replace(
            "[data-role=priceBox]",
            ".price-box-" . $product->getId(),
            $optionsHtml
        );

        return $optionsHtml;
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getJsonConfig(\Magento\Catalog\Model\Product $product)
    {
        $product = $this->productRepository->getById($product->getId());
        $this->registry->register('product', $product);
        $jsonConfig = $this->productView->getJsonConfig();
        $this->registry->unregister('product');

        return $jsonConfig;
    }

    /**
     * Return true if product has options
     *
     * @param $product
     * @return bool
     */
    public function hasOptions($product)
    {
        if ($product->getTypeInstance()->hasOptions($product)) {
            return true;
        }
        return false;
    }

    /**
     * @param $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProductById($productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $this->context->getLogger()->critical($e->getLogMessage());
        }
    }
}
