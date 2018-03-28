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

class Save extends \Kensium\Quote\Controller\Adminhtml\Quote
{
    /**
     * Backend session
     * 
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * constructor
     * 
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Kensium\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Kensium\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->backendSession = $backendSession;
        parent::__construct($quoteFactory, $registry, $resultRedirectFactory, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('quote');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $quote = $this->initQuote();
            $quote->setData($data);
            $this->_eventManager->dispatch(
                'kensium_quote_quote_prepare_save',
                [
                    'quote' => $quote,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $quote->save();
                $this->messageManager->addSuccess(__('The Quote has been saved.'));
                $this->backendSession->setKensiumQuoteQuoteData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'kensium_quote/*/edit',
                        [
                            'quote_id' => $quote->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('kensium_quote/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Quote.'));
            }
            $this->_getSession()->setKensiumQuoteQuoteData($data);
            $resultRedirect->setPath(
                'kensium_quote/*/edit',
                [
                    'quote_id' => $quote->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('kensium_quote/*/');
        return $resultRedirect;
    }
}
