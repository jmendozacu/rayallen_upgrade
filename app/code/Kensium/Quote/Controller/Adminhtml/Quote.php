<?php
/**
 * Kensium_Quote extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Quote
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Quote\Controller\Adminhtml;

abstract class Quote extends \Magento\Backend\App\Action
{
    /**
     * Quote Factory
     * 
     * @var \Kensium\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     * 
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * constructor
     * 
     * @param \Kensium\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Kensium\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->quoteFactory          = $quoteFactory;
        $this->coreRegistry          = $coreRegistry;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Init Quote
     *
     * @return \Kensium\Quote\Model\Quote
     */
    protected function initQuote()
    {
        $quoteId  = (int) $this->getRequest()->getParam('quote_id');
        /** @var \Kensium\Quote\Model\Quote $quote */
        $quote    = $this->quoteFactory->create();
        if ($quoteId) {
            $quote->load($quoteId);
        }
        $this->coreRegistry->register('kensium_quote_quote', $quote);
        return $quote;
    }
}
