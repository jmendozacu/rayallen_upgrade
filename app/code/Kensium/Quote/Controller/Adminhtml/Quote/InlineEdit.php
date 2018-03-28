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
namespace Kensium\Quote\Controller\Adminhtml\Quote;

abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * JSON Factory
     * 
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * Quote Factory
     * 
     * @var \Kensium\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Kensium\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Kensium\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->jsonFactory  = $jsonFactory;
        $this->quoteFactory = $quoteFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($postItems) as $quoteId) {
            /** @var \Kensium\Quote\Model\Quote $quote */
            $quote = $this->quoteFactory->create()->load($quoteId);
            try {
                $quoteData = $postItems[$quoteId];//todo: handle dates
                $quote->addData($quoteData);
                $quote->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithQuoteId($quote, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithQuoteId($quote, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithQuoteId(
                    $quote,
                    __('Something went wrong while saving the Quote.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Quote id to error message
     *
     * @param \Kensium\Quote\Model\Quote $quote
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithQuoteId(\Kensium\Quote\Model\Quote $quote, $errorText)
    {
        return '[Quote ID: ' . $quote->getId() . '] ' . $errorText;
    }
}
