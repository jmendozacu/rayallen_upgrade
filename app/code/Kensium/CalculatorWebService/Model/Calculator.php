<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\CalculatorWebService\Model;

use Kensium\CalculatorWebService\Api\CalculatorInterface;
use Kensium\CalculatorWebService\Api\JsondataInterfaceFactory;
use Kensium\CalculatorWebService\Api\AddressdataInterfaceFactory;
use Kensium\CalculatorWebService\Api\BillingdataInterfaceFactory;
use Kensium\CalculatorWebService\Api\ShippingdataInterfaceInterfaceFactory;
use Magento\TestFramework\Inspection\Exception;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order;
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
     * Cart Model
     */
    protected $resource;

    protected  $cartModel;

    protected $invoiceService;

    protected $transaction;

    protected $invoiceSender;

    protected $orderModel;

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
        JsondataInterfaceFactory $jsondataFactory,
        \Magento\AdvancedCheckout\Model\Cart $cartModel,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        InvoiceSender $invoiceSender,
        \Magento\Framework\App\ResourceConnection $resource,
        Order $orderModel
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
        $this->cartModel = $cartModel;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->orderModel = $orderModel;
        $this->resource = $resource;
    }

    protected function getConnection()
    {
        $this->connection = $this->resource->getConnection('core_write');
        return $this->connection;
    }

    /**
     * @param string $giftCardCode
     * @param string $branchName
     * @return array|bool
     */
    public function checkGiftCard($giftCardCode,$branchName) {
        $gfCards = explode(",", $giftCardCode);
        $responseArray = array();
        $stores = array("1"=>"RA","2"=>"JJ","3"=>"GD");
        $storeId = '';
        if(!in_array($branchName,$stores))
            return $responseArray = [ FALSE,$branchName.'|0|Invalid Branch'];
        else{
            foreach($stores as $sid=>$store){
                if($store == $branchName){
                    $storeId = $sid;
                }
            }
        }
        $store = $this->storeManager->getStore($storeId);
        $websiteId = $store->getWebsiteId();
        foreach($gfCards as $gfCard) {
            $giftCard = $this->giftCardAccountFactory->create();
            $giftCard->loadByCode($gfCard);
            try {
                $check = $giftCard->isValid(true, true, false, false);
                if($giftCard->getWebsiteId() != $websiteId) {
                    $responseArray[] = $gfCard."|0|Gift card doesn't not found in this branch";
                }elseif ($check) {
                    $existingAmount = $giftCard->getBalance();
                    $responseArray[] = $gfCard.'|1|'.$existingAmount;
                }else{
                    $responseArray[] = $gfCard.'|0|'.NULL;
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $responseArray[] = $gfCard.'|0|Invalid Giftcard';
            }
        }
        return $responseArray;
    }

    
    /**
     * @param \Kensium\CalculatorWebService\Api\JsondataInterface $jsonOrderData
     * @param \Kensium\CalculatorWebService\Api\AddressdataInterface $customerAddress
     * @param \Kensium\CalculatorWebService\Api\BillingdataInterface $billingAddress
     * @param \Kensium\CalculatorWebService\Api\ShippingdataInterface $shippingAddress
     * @param \Kensium\CalculatorWebService\Api\ItemsdataInterface $items
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createorder($jsonOrderData, $customerAddress, $billingAddress, $shippingAddress,$items)
    {
        $skus = $qtys = $price = $arrayItems = array();
        $skus = explode(",", $items->getSku());
        $qtys = explode(",", $items->getQty());
        $price = explode(",", $items->getPrice());
        $stores = array("1"=>"RA","2"=>"JJ","3"=>"GD");
        $storeId = '';
        if(!in_array($jsonOrderData->getBranchName(),$stores))
            return ['error'=>FALSE,'msg'=>"Invalid Branch"];
        else{
            foreach($stores as $sid=>$store){
                if($store == $jsonOrderData->getBranchName()){
                    $storeId = $sid;
                }
            }
        }
        $count = count($skus);
        if($count > 0){
            for($i=0; $i < $count;$i++){
                $arrayItems[] = array(
                    'sku'   => $skus[$i],
                    'qty'   => $qtys[$i],
                    'price' => $price[$i]
                );
            }
        }
        $customerType = $jsonOrderData->getCustomerType();
        $orderData= array (
            'currency_id'  => $jsonOrderData->getCurrencyId(),
            'email'        => $jsonOrderData->getEmail(),
            'customer_type'=> $customerType,
            'customer_address' => array (
                'street'                => $billingAddress->getStreet(),
                'city'                  => $billingAddress->getCity(),
                'country_id'            => $billingAddress->getCountryId(),
                'region'                => $billingAddress->getRegion(),
                'postcode'              => $billingAddress->getPostcode(),
                'telephone'             => $billingAddress->getTelephone(),
                'fax'                   => $billingAddress->getFax(),
                'firstname'             => $billingAddress->getFirstName(), //address Details
                'lastname'              => $billingAddress->getLastName(),
                'is_default_shipping'   => 1,
                'is_default_billing'    => 1
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
            'coupon_code'       => $jsonOrderData->getCouponCode(),
            'gift_card_code'    => $jsonOrderData->getGiftcard(),
            'shipping_method'   => 'tablerate_bestway',//$jsonOrderData->getShippingMethod(),
	    'shipping_amount'   => $jsonOrderData->getShippingAmount(),
            'payment_method'    => $jsonOrderData->getPaymentMethod(),
            'items'             => $arrayItems
        );
        $store = $this->storeManager->getStore($storeId);
        $websiteId = $store->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderData['email']);// load customer by email address

        if(!$customer->getEntityId()) {
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteId)
                ->setStore($store)
                ->setFirstname($orderData['shipping_address']['firstname'])
                ->setLastname($orderData['shipping_address']['lastname'])
                ->setEmail($orderData['email'])
                ->setPassword(rand(888888,999999999));

            try {
                $customer->save();
                $this->updateCustomerAttribute("acumatica_customer_id",$jsonOrderData->getCustomerId(),$customer->getId());
                //$customer->setData("acumatica_customer_id",$jsonOrderData->getCustomerId());

            } catch (\Magento\Framework\Exception\LocalizedException $e) {

                return ['error'=>FALSE,'msg'=>$e->getMessage()];
            }

            try {
                $address = $this->addressFactory->create()->addData($orderData['customer_address']);
                $customer->addAddress($address)
                    ->setId($customer->getId())->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                return ['error'=>FALSE,'msg'=>$e->getMessage()];//$e->getMessage();
            }

        }
        
        //Create object of quote
        foreach($orderData['items'] as $item){
            $productId = $this->_product->getIdBySku($item['sku']);
            if($productId > 0){
                $product = $this->_product->load($productId);//exit;
                //Check product type
                if(!in_array($storeId,$product->getStoreIds())) {
                    return ['error'=>FALSE,'msg'=>"Product doesn't exist in the given store"];
                }else{
                    $qty = intval($item['qty']);
                    // add items in quote
                    $this->cartModel->addProduct($productId, $qty);
                }
            }else{
                return ['error'=>FALSE,'msg'=>"Product doesn't exists"];
            }
        }

        $cart = $this->cartModel->saveQuote();
        $quote = $cart->getQuote();
        //set store for which you create quote
        $quote->setStore($store);

        // if you have already buyer id then you can load customer directly
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();

        // Apply Coupon Code to the order
        if($orderData['coupon_code'] != '') {
            $quote->setCouponCode($orderData['coupon_code']);
        }

        $quote->assignCustomer($customer); //Assign quote to customer
        // Apply Gift Card Code
        $giftCard = $this->giftCardAccountFactory->create();
        $cards = explode(',',trim($orderData['gift_card_code']));
        $countCards = count($cards);
        $i = 0;
	try {
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true)
                ->collectShippingRates()
                ->setShippingAmount($orderData['shipping_amount'])
                ->setShippingMethod($orderData['shipping_method']); //shipping method
        } catch( Exception $e){
            return ['error'=>FALSE,'msg'=>'Invalid shipping method'];
        }
        foreach($cards as $giftCode){
            $i++;
            $giftcardAccount = $giftCard->loadByCode(trim($giftCode));
            //print_r($giftcardAccount->getData());die;
            try {
                $check = $giftcardAccount->isValid(true, true, $websiteId, true);
                if($check) {
                    try {
                        if($countCards == $i){
                            $giftcardAccount->addToCart(true, $quote);
                        }else{
                            $giftcardAccount->addToCart(false, $quote);
                        }
                    } catch (Exception $e) {
                        return ['error'=>FALSE,'msg'=>'Invalid Giftcard'];//return $e->getMessage();
                    }
                } else {
                    return ['error'=>FALSE,'msg'=>'Invalid Giftcard'];
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                return ['error'=>FALSE,'msg'=>$e->getMessage()];//return $e->getMessage();
            }
        }
        //Set Address to quote
        try{
            $quote->getBillingAddress()->addData($orderData['billing_address']);
        } catch(\Magento\Framework\Exception\LocalizedException $e){
            return ['error'=>FALSE,'msg'=>'Invalid billing address'.$e->getMessage()];
        }

        try{
            $quote->getShippingAddress()->addData($orderData['shipping_address']);
        } catch(\Magento\Framework\Exception\LocalizedException $e){
            return ['error'=>FALSE,'msg'=>'Invalid shipping address'.$e->getMessage()];
        }

        // Collect Rates and Set Shipping & Payment Method
        /*try {
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true)
                ->collectShippingRates()
		->setShippingAmount($orderData['shipping_amount'])
                ->setShippingMethod($orderData['shipping_method']); //shipping method
        } catch( Exception $e){
            return ['error'=>FALSE,'msg'=>'Invalid shipping method'];
        }*/
        //If there is extra amount in order than giftcard pay by checkmo
        try {
            $quote->setPaymentMethod($orderData['payment_method']); //payment method
        }catch (Exception $e){
            return ['error'=>FALSE,'msg'=>'Invalid payment method'];
        }
	try {
        $quote->setInventoryProcessed(false); //not effect inventory
        $quote->save(); //Now Save quote and your quote is ready
	}catch (\Magento\Framework\Exception\LocalizedException $e){
            return ['error'=>FALSE,'msg'=>$e->getMessage()];
        }
        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => $orderData['payment_method']]);

        // Collect Totals & Save Quote
        $quote->collectTotals()->save();

        // Create Order From Quote
        $order = $this->quoteManagement->submit($quote);

        $order->setEmailSent(0);
        $increment_id = $order->getRealOrderId();
        if($order->getEntityId()){
            if ($order->canInvoice()) {
                $invoice = $this->invoiceService->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                $this->transaction->addObject($invoice)->addObject($invoice->getOrder())->save();
                $this->invoiceSender->send($invoice);
            }
            $result['order_id']= $order->getRealOrderId();
        }else{
            $result=['error'=>1,'msg'=>'There was a problem while creating the order. Please check the data.'];
        }

        return [TRUE,$result['order_id']];
    }

    /**
     * @param \Kensium\CalculatorWebService\Api\JsonOrderInterface $orderData
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateOrder($orderData)
    {
        $orderId = $orderData->getOrderIncrementId();
        $acumaticaOrderRef = $orderData->getAcumaticaOrderReference();
        $order = $this->orderModel->load($orderId, "increment_id");
        if ($order->getId()!=''){
            //acm_order_number
            $order->setData("acumatica_order_id", $acumaticaOrderRef);
            $order->save();
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * @param $attributeCode
     * @param $value
     * @param $entityId
     */
    public function updateCustomerAttribute($attributeCode,$value,$entityId)
    {
        $attributeId = $this->getConnection()->fetchOne("SELECT attribute_id FROM eav_attribute where attribute_code = '".$attributeCode."' and entity_type_id=1");

        if($attributeId)
        {
            $checkAttributeValue = $this->getConnection()->fetchOne("SELECT value_id FROM customer_entity_varchar where attribute_id = '".$attributeId."' and entity_id = $entityId ");
            if($checkAttributeValue)
            {
                $this->getConnection()->query("UPDATE customer_entity_varchar set value='".$value."' where attribute_id='".$attributeId."' and entity_id= '".$entityId."' ");
            }else
            {
                $this->getConnection()->query("INSERT INTO `customer_entity_varchar`(`value_id`, `attribute_id`, `entity_id`, `value`)
               VALUES (NULL, '" . $attributeId . "', '" . $entityId . "', '" . $value . "')" );
            }
        }
    }
}
