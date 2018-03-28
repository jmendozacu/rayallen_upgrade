<?php
/**
 *
 * Copyright © 2015 Kensiumcommerce. All rights reserved.
 */
namespace Kensium\QuickOrder\Controller\Quick;

use Magento\AdvancedCheckout\Controller\Sku;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\AdvancedCheckout\Controller\Cart;

class TestForm extends \Magento\AdvancedCheckout\Controller\Cart
{
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
        $items = $this->getRequest()->getPost('items');
        if (!is_array($items)) {
            $items = [];
        }
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        // check empty data
        /** @var $helper \Magento\AdvancedCheckout\Helper\Data */
        $helper = $this->_objectManager->get('Magento\AdvancedCheckout\Helper\Data');

        foreach ($items as $k => $item) {
            if (!isset($item['sku']) || (empty($item['sku']) && $item['sku'] !== '0')) {
                unset($items[$k]);
            }
        }
        if (empty($items) && !$helper->isSkuFileUploaded($this->getRequest())) {
            $this->messageManager->addError($helper->getSkuEmptyDataMessageText());
            return $resultRedirect->setPath('checkout/cart');
        }

        try {
            $updateCart =  $this->_objectManager->get('Magento\Checkout\Model\Cart');
            // perform data
            $cart = $this->_getFailedItemsCart()->prepareAddProductsBySku($items)->saveAffectedProducts();

            $this->messageManager->addMessages($cart->getMessages());

            if ($cart->hasErrorMessage()) {
                throw new LocalizedException(__($cart->getErrorMessage()));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addException($e, $e->getMessage());
        }
        $this->_eventManager->dispatch('collect_totals_failed_items');


        try {
            $cartData = array('203' => array('qty' => 2));
            if (is_array($cartData)) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                if (!$updateCart->getCustomerSession()->getCustomerId() && $updateCart->getQuote()->getCustomerId()) {
                    $updateCart->getQuote()->setCustomerId(null);
                }

                $cartData = $updateCart->suggestItemsQty($cartData);
                $updateCart->updateItems($cartData)->save();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError(
                $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t update the shopping cart.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }


        return $resultRedirect->setPath('checkout/cart');
    }


}
