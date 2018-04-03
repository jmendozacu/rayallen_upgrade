<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Amconnector\Block\Adminhtml\Product\Edit\Button;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Kensium\Amconnector\Model\ResourceModel\Sync;

/**
 * Class Generic
 */
class Generic implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var Context
     */
    protected $context;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Generic constructor
     *
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Request\Http $request,
        Registry $registry,
        Sync $coreConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->coreConfig = $coreConfig;
        $this->request = $request;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrl($route, $params);
    }

    /**
     * Get product
     *
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getscopeConfig()
    {
        return $this->scopeConfig;
    }

    public function getCoreConfigData($path,$scope,$scopeId)
    {
        return $this->coreConfig->getDataFromCoreConfig($path,$scope,$scopeId);
    }

}
