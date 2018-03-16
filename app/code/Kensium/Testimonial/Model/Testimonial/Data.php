<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\Testimonial\Model\Testimonial;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Kensium\Testimonial\Model\Config;

/**
 * Testimonial section
 */
class Data implements SectionSourceInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Store Testimonial resource instance
     *
     * @var \Magento\Testimonial\Model\ResourceModel\Testimonial
     */
    protected $testimonialResource;

    /**
     * Testimonial instance
     *
     * @var \Magento\Testimonial\Model\Testimonial
     */
    protected $testimonial;

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
    protected $testimonials = [];

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Testimonial\Model\ResourceModel\Testimonial $testimonialResource
     * @param \Magento\Testimonial\Model\Testimonial $testimonial
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Kensium\Testimonial\Model\ResourceModel\Testimonial $testimonialResource,
        \Kensium\Testimonial\Model\Testimonial $testimonial,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->testimonialResource = $testimonialResource;
        $this->testimonial = $testimonial;
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
                Config::BANNER_WIDGET_DISPLAY_SALESRULE => $this->getSalesRuleRelatedTestimonials(),
                Config::BANNER_WIDGET_DISPLAY_CATALOGRULE => $this->getCatalogRuleRelatedTestimonials(),
                Config::BANNER_WIDGET_DISPLAY_FIXED => $this->getFixedTestimonials(),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getSalesRuleRelatedTestimonials()
    {
        $appliedRules = [];
        if ($this->checkoutSession->getQuoteId()) {
            $quote = $this->checkoutSession->getQuote();
            if ($quote && $quote->getAppliedRuleIds()) {
                $appliedRules = explode(',', $quote->getAppliedRuleIds());
            }
        }
        return $this->getTestimonialsData($this->testimonialResource->getSalesRuleRelatedTestimonialIds($appliedRules));
    }

    /**
     * @return array
     */
    protected function getCatalogRuleRelatedTestimonials()
    {
        return $this->getTestimonialsData($this->testimonialResource->getCatalogRuleRelatedTestimonialIds(
            $this->storeManager->getWebsite()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP)
        ));
    }

    /**
     * @return array
     */
    protected function getFixedTestimonials()
    {
        return $this->getTestimonialsData($this->testimonialResource->getActiveTestimonialIds());
    }

    /**
     * @param array $testimonialsIds
     * @return array
     */
    protected function getTestimonialsData($testimonialsIds)
    {
        $testimonials = [];
        foreach ($testimonialsIds as $testimonialId) {
            if (!isset($this->testimonials[$testimonialId])) {
                $content = $this->testimonialResource->getStoreContent($testimonialId, $this->storeId);
                if (!empty($content)) {
                    $this->testimonials[$testimonialId] = [
                        'content' => $this->filterProvider->getPageFilter()->filter($content),
                        'types' => $this->testimonial->load($testimonialId)->getTypes(),
                        'id' => $testimonialId,
                    ];
                } else {
                    $this->testimonials[$testimonialId] = null;
                }
            }
            $testimonials[$testimonialId] = $this->testimonials[$testimonialId];
        }
        return array_filter($testimonials);
    }
}
