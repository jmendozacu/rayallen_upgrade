<?php
namespace Kensium\Configurable\Model\ConfigurableProduct\Block\Product\View\Type\Configurable;

use Magento\Catalog\Model\Product;

class Plugin extends \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
{
    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function afterGetJsonConfig()
    {
        $store = $this->getCurrentStore();
        $currentProduct = $this->getProduct();

        $regularPrice = $currentProduct->getPriceInfo()->getPrice('regular_price');
        $finalPrice = $currentProduct->getPriceInfo()->getPrice('final_price');

        $options = $this->helper->getOptions($currentProduct, $this->getAllowProducts());
        $attributesData = $this->configurableAttributeData->getAttributesData($currentProduct, $options);
        $childOptions = $this->getOptionSkus($this->getAllowProducts());
        $config = [
            'attributes' => $attributesData['attributes'],
            'template' => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
            'optionPrices' => $this->getOptionPrices(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->_registerJsPrice($regularPrice->getAmount()->getValue()),
                ],
                'basePrice' => [
                    'amount' => $this->_registerJsPrice(
                        $finalPrice->getAmount()->getBaseAmount()
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->_registerJsPrice($finalPrice->getAmount()->getValue()),
                ],
            ],
            'productId' => $currentProduct->getId(),
            'productSku' => $currentProduct->getSku(),
            'chooseText' => __('Choose an Option...'),
            'images' => isset($options['images']) ? $options['images'] : [],
            'index' => isset($options['index']) ? $options['index'] : [],
            'childSkus' => isset($childOptions['childsku']) ? $childOptions['childsku'] : [],
        ];

        if ($currentProduct->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
            $config['defaultValues'] = $attributesData['defaultValues'];
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return $this->jsonEncoder->encode($config);
    }

    /**
     * Gte Options skUs
     */
    public function getOptionSkus($allowedProducts){
        $options = [];
        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            $options['childsku'][$productId] = $product->getSku();
        }
        return $options;
    }
}
