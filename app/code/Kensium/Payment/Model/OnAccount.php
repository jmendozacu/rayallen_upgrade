<?php
namespace Kensium\Payment\Model;
use Magento\Framework\DataObject;
class OnAccount extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = 'onaccount';
    protected $_isOffline = true;

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $customerRepository = $objectManager->get('Magento\Customer\Api\CustomerRepositoryInterface');
            $customer = $customerRepository->getById($customerSession->getId());
            if($customer->getCustomAttribute('custom_onacc_attribute')!=null){
                $cattrValue = $customer->getCustomAttribute('custom_onacc_attribute')->getValue();
                // $objectManager->get('Psr\Log\LoggerInterface')->info(print_r($cattrValue, true));
                if (!$cattrValue) {
                    return false;
                }
                if (!$this->isActive($quote ? $quote->getStoreId() : null)) {
                    return false;
                }

                $checkResult = new DataObject();
                $checkResult->setData('is_available', true);

                // for future use in observers
                $this->_eventManager->dispatch(
                    'payment_method_is_active',
                    [
                        'result' => $checkResult,
                        'method_instance' => $this,
                        'quote' => $quote
                    ]
                );

                return $checkResult->getData('is_available');
            } else{
                return false;
            }

        } else {
            return false;
        }


    }

}