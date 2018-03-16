<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Csvenvelopes\Model\Csvenvelopes;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Kensium\Csvenvelopes\Model\Config;

/**
 * Csvenvelopes section
 */
class Data implements SectionSourceInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Store Csvenvelopes resource instance
     *
     * @var \Magento\Csvenvelopes\Model\ResourceModel\Csvenvelopes
     */
    protected $csvenvelopesResource;

    /**
     * Csvenvelopes instance
     *
     * @var \Magento\Csvenvelopes\Model\Csvenvelopes
     */
    protected $csvenvelopes;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var array
     */
    protected $csvenvelopess = [];

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Csvenvelopes\Model\ResourceModel\Csvenvelopes $csvenvelopesResource
     * @param \Magento\Csvenvelopes\Model\Csvenvelopes $csvenvelopes
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Kensium\Csvenvelopes\Model\ResourceModel\Csvenvelopes $csvenvelopesResource,
        \Kensium\Csvenvelopes\Model\Csvenvelopes $csvenvelopes,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->csvenvelopesResource = $csvenvelopesResource;
        $this->csvenvelopes = $csvenvelopes;
        $this->storeManager = $storeManager;
        $this->httpContext = $httpContext;
        $this->filterProvider = $filterProvider;
        $this->storeId = $this->storeManager->getStore()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        return [
            'items' => [
                Config::BANNER_WIDGET_DISPLAY_SALESRULE => $this->getSalesRuleRelatedCsvenvelopess(),
                Config::BANNER_WIDGET_DISPLAY_CATALOGRULE => $this->getCatalogRuleRelatedCsvenvelopess(),
                Config::BANNER_WIDGET_DISPLAY_FIXED => $this->getFixedCsvenvelopess(),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getSalesRuleRelatedCsvenvelopess()
    {
        $appliedRules = [];
        if ($this->checkoutSession->getQuoteId()) {
            $quote = $this->checkoutSession->getQuote();
            if ($quote && $quote->getAppliedRuleIds()) {
                $appliedRules = explode(',', $quote->getAppliedRuleIds());
            }
        }
        return $this->getCsvenvelopessData($this->csvenvelopesResource->getSalesRuleRelatedCsvenvelopesIds($appliedRules));
    }

    /**
     * @return array
     */
    protected function getCatalogRuleRelatedCsvenvelopess()
    {
        return $this->getCsvenvelopessData($this->csvenvelopesResource->getCatalogRuleRelatedCsvenvelopesIds(
            $this->storeManager->getWebsite()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP)
        ));
    }

    /**
     * @return array
     */
    protected function getFixedCsvenvelopess()
    {
        return $this->getCsvenvelopessData($this->csvenvelopesResource->getActiveCsvenvelopesIds());
    }

    /**
     * @param array $csvenvelopessIds
     * @return array
     */
    protected function getCsvenvelopessData($csvenvelopessIds)
    {
        $csvenvelopess = [];
        foreach ($csvenvelopessIds as $csvenvelopesId) {
            if (!isset($this->csvenvelopess[$csvenvelopesId])) {
                $content = $this->csvenvelopesResource->getStoreContent($csvenvelopesId, $this->storeId);
                if (!empty($content)) {
                    $this->csvenvelopess[$csvenvelopesId] = [
                        'content' => $this->filterProvider->getPageFilter()->filter($content),
                        'types' => $this->csvenvelopes->load($csvenvelopesId)->getTypes(),
                        'id' => $csvenvelopesId,
                    ];
                } else {
                    $this->csvenvelopess[$csvenvelopesId] = null;
                }
            }
            $csvenvelopess[$csvenvelopesId] = $this->csvenvelopess[$csvenvelopesId];
        }
        return array_filter($csvenvelopess);
    }
}
