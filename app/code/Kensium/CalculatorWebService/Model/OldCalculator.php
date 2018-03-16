<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\CalculatorWebService\Model;

use Kensium\CalculatorWebService\Api\CalculatorInterface;
use Magento\GiftCardAccount\Model\Giftcardaccount as GiftCardAccount;
use Magento\Framework\Exception\CouldNotSaveException;
use Kensium\CalculatorWebService\Api\JsondataInterface;
use Kensium\CalculatorWebService\Api\JsondataInterfaceFactory;
use Kensium\CalculatorWebService\Api\AddressdataInterface;
use Kensium\CalculatorWebService\Api\AddressdataInterfaceFactory;
use Kensium\CalculatorWebService\Api\BillingdataInterface;
use Kensium\CalculatorWebService\Api\BillingdataInterfaceFactory;
use Kensium\CalculatorWebService\Api\ShippingdataInterface;
use Kensium\CalculatorWebService\Api\ShippingdataInterfaceInterfaceFactory;
use Kensium\CalculatorWebService\Api\ItemsdataInterface;
use Magento\TestFramework\Inspection\Exception;


class Calculator implements CalculatorInterface
{

    /**
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory
     */
    protected $giftCardAccountFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Sales\Model\Service\OrderService
     */
    protected $orderService;

    /**
     * @var JsondataInterfaceFactory
     */
    protected $jsondataFactory;

    /**
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCardAccountFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Data\Form\FormKey $formkey
     * @param \Magento\Quote\Model\QuoteFactory $quote
     * @param \Magento\Quote\Model\QuoteManagement $quoteManagement
     * @param \Magento\Sales\Model\Service\OrderService $orderService
     * @param JsondataInterfaceFactory $jsondataFactory
     */
    public function __construct(
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCardAccountFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Sales\Model\Service\OrderService $orderService,
        JsondataInterfaceFactory $jsondataFactory
    ) {
        $this->giftCardAccountFactory = $giftCardAccountFactory;
        $this->storeManager = $storeManager;
        $this->customerRepository=$customerRepository;
        $this->customerFactory=$customerFactory;
        $this->addressFactory=$addressFactory;
        $this->_product = $product;
        $this->_formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->orderService = $orderService;
        $this->jsondataFactory = $jsondataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function checkGiftCard($giftCardCode) {
        $giftCard = $this->giftCardAccountFactory->create();
        $giftCard->loadByCode($giftCardCode);
        try {
            $returnReponse = false;
            $check = $giftCard->isValid(true, true, true, false);
            if($check){
                $existingAmount = $giftCard->getBalance();
                //if($amount <= $existingAmount)
                $returnReponse = $existingAmount;
            }

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $returnReponse = false;
        }
        return $returnReponse;
    }

    /**
     * @param \Kensium\CalculatorWebService\Api\Kensium\CalculatorWebService\Api\JsondataInterface $orderData
     * @param \Kensium\CalculatorWebService\Api\Kensium\CalculatorWebService\Api\AddressdataInterface $customerAddress
     * @param \Kensium\CalculatorWebService\Api\Kensium\CalculatorWebService\Api\BillingdataInterface $billingAddress
     * @param \Kensium\CalculatorWebService\Api\Kensium\CalculatorWebService\Api\ShippingdataInterface $shippingAddress
     * @param \Kensium\CalculatorWebService\Api\Kensium\CalculatorWebService\Api\ItemsdataInterface $items
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createorder($orderData, $customerAddress, $billingAddress, $shippingAddress,$items)
    {
        $customerType = $orderData->getCustomerType();
        $orderData= array (
            'currency_id'  => $orderData->getCurrencyId(),
            'email'        => $orderData->getEmail(),
            'customer_type'=> $customerType,
            'customer_address' => array (
                'street'                => $customerAddress->getStreet(),
                'city'                  => $customerAddress->getCity(),
                'country_id'            => $customerAddress->getCountryId(),
                'region'                => $customerAddress->getRegion(),
                'postcode'              => $customerAddress->getPostcode(),
                'telephone'             => $customerAddress->getTelephone(),
                'fax'                   => $customerAddress->getFax(),
                'is_default_shipping'   => $customerAddress->getIsDefaultShipping(),
                'is_default_billing'    => $customerAddress->getIsDefaultBilling()
            ),
            'shipping_address' => array (
                'firstname'             => $shippingAddress->getFirstName(), //address Details
                'lastname'              => $shippingAddress->getLastName(),
                'street'                => $shippingAddress->getStreet(),
                'city'                  => $shippingAddress->getCity(),
                'country_id'            => $shippingAddress->getCountryId(),
                'region'                => $shippingAddress->getRegion(),
                'postcode'              => $shippingAddress->getPostcode(),
                'telephone'             => $shippingAddress->getTelephone(),
                'fax'                   => $shippingAddress->getFax(),
                'save_in_address_book'  => $shippingAddress->getSaveInAddressBook()
            ),
            'billing_address' => array (
                'firstname'             => $billingAddress->getFirstName(), //address Details
                'lastname'              => $billingAddress->getLastName(),
                'street'                => $billingAddress->getStreet(),
                'city'                  => $billingAddress->getCity(),
                'country_id'            => $billingAddress->getCountryId(),
                'region'                => $billingAddress->getRegion(),
                'postcode'              => $billingAddress->getPostcode(),
                'telephone'             => $billingAddress->getTelephone(),
                'fax'                   => $billingAddress->getFax(),
                'save_in_address_book'  => $billingAddress->getSaveInAddressBook()
            ),
            'coupon_code'       => $orderData->getCouponCode(),
            'gift_card_code'    => $orderData->getGiftcard(),
            'shipping_method'   => $orderData->getShippingMethod(),
            'payment_method'    => $orderData->getPaymentMethod(),
            'items'=> array(
                array(
                    'sku'   => $items->getSku(),
                    'qty'   => $items->getQty(),
                    'price' => $items->getPrice()
                )
            )
        );
        $store=$this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderData['email']);// load customer by email address

        if(!$customer->getEntityId() && $customerType == 'customer') {
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteId)
                ->setStore($store)
                ->setFirstname($orderData['shipping_address']['firstname'])
                ->setLastname($orderData['shipping_address']['lastname'])
                ->setEmail($orderData['email'])
                ->setPassword($orderData['shipping_address']['firstname']);

            try {
                $customer->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                return "Invalid customer address";//$e->getMessage();
            }

            try {
                $address = $this->addressFactory->create()->addData($orderData['customer_address']);
                $customer->addAddress($address)
                    ->setId($customer->getId())->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                return "Invalid customer address";//$e->getMessage();
            }

        }

        $quote=$this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote

        // if you have already buyer id then you can load customer directly 
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();

        // Apply Coupon Code to the order
        if($orderData['coupon_code'] != '') {
            $quote->setCouponCode($orderData['coupon_code']);
        }

        $quote->assignCustomer($customer); //Assign quote to customer
        // add items in quote
        foreach($orderData['items'] as $item){
            //echo '<pre>';  echo $item['sku']; print_r($item['params']);
            $productId=$this->_product->getIdBySku($item['sku']);
            if($productId){
                $product = $this->_product->load($productId);//exit;
                $product->setPrice($item['price']);
                $quote->addProduct(
                    $product,
                    intval($item['qty'])
                );
            }else{
                return "Product doesn't exists";
            }

        }
        // Apply Gift Card Code
        $giftCard = $this->giftCardAccountFactory->create();
        $cards = explode(',',trim($orderData['gift_card_code']));
        $countCards = count($cards);
        $i = 0;
        foreach($cards as $giftCode){
            $i++;
            $giftcardAccount = $giftCard->loadByCode(trim($giftCode));
            try {
                $check = $giftcardAccount->isValid(true, true, true, true);
                if($check) {
                    try {
                        if($countCards == $i){
                            $giftcardAccount->addToCart(true, $quote);
                        }else{
                            $giftcardAccount->addToCart(false, $quote);
                        }
                    } catch (Exception $e) {
                        return 'Invalid Giftcard';//return $e->getMessage();
                    }
                } else {
                    return 'Invalid Giftcard';
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                return 'Invalid Giftcard';//return $e->getMessage();
            }
        }


        //Set Address to quote
        try{
            $quote->getBillingAddress()->addData($orderData['billing_address']);
        } catch(Exception $e){
            return 'Invalid billing address';
        }

        try{
            $quote->getShippingAddress()->addData($orderData['shipping_address']);
        } catch(Exception $e){
            return 'Invalid shipping address';
        }

        // Collect Rates and Set Shipping & Payment Method
        try {
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true)
                ->collectShippingRates()
                ->setShippingMethod($orderData['shipping_method']); //shipping method
        } catch( Exception $e){
            return 'Invalid shipping method';
        }

        try {
            $quote->setPaymentMethod($orderData['payment_method']); //payment method
        }catch (Exception $e){
            return 'Invalid payment method';
        }
        $quote->setInventoryProcessed(false); //not effect inventory
        $quote->save(); //Now Save quote and your quote is ready

        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => $orderData['payment_method']]);

        // Collect Totals & Save Quote
        $quote->collectTotals()->save();

        // Create Order From Quote
        $order = $this->quoteManagement->submit($quote);

        $order->setEmailSent(0);
        $increment_id = $order->getRealOrderId();
        if($order->getEntityId()){
            $result['order_id']= $order->getRealOrderId();
        }else{
            $result=['error'=>1,'msg'=>'There was a problem while creating the order. Please check the data.'];
        }

        return $result['order_id'];
    }
}
