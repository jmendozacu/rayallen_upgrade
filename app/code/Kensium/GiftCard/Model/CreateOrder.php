<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\GiftCard\Model;

use Kensium\GiftCard\Api\CreateOrderInterface;
use Kensium\GiftCard\Api\JsondataInterfaceFactory;
use Kensium\GiftCard\Api\AddressdataInterfaceFactory;
use Kensium\GiftCard\Api\BillingdataInterfaceFactory;
use Kensium\GiftCard\Api\ShippingdataInterfaceInterfaceFactory;
use Magento\TestFramework\Inspection\Exception;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Framework\Exception\LocalizedException;
use Magento\AdvancedCheckout\Model\Cart;
class CreateOrder implements CreateOrderInterface
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
     * @var OrderFactory
     */
    protected $orderFactory;

    protected $resource;

    protected $connection;

    protected $invoiceService;

    protected $transaction;
//
//    /**
//     * @var InvoiceSender
//     */
    protected $invoiceSender;

    /**
     * Cart Model
     */
    protected  $cartModel;

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
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param InvoiceSender $invoiceSender
     * @param Cart $cartModel
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
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        InvoiceSender $invoiceSender,
        Cart $cartModel
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
        $this->orderFactory = $orderFactory;
        $this->resource = $resource;
        $this->invoiceService = $invoiceService;
        $this->invoiceSender = $invoiceSender;
        $this->transaction = $transaction;
        $this->cartModel = $cartModel;
    }

    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->resource->getConnection('core_write');
        }
        return $this->connection;
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

        } catch (LocalizedException $e) {
            $returnReponse = false;
        }
        return $returnReponse;
    }


    /**
     * @param \Kensium\GiftCard\Api\JsondataInterface $jsonOrderData
     * @param \Kensium\GiftCard\Api\AddressdataInterface $customerAddress
     * @param \Kensium\GiftCard\Api\BillingdataInterface $billingAddress
     * @param \Kensium\GiftCard\Api\ShippingdataInterface $shippingAddress
     * @param \Kensium\GiftCard\Api\ItemsdataInterface $items
     * @return array|string
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws bool
     */
    public function createOrder($jsonOrderData, $customerAddress, $billingAddress, $shippingAddress,$items)
    {
        $invalidType = array();
        $invalidSku = array();
        $invalidAmount = array();
        $invalidStore = array();
        $invalidInventoryInfo = array();
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
        //echo $storeId;die;
        //Gift Card Inventory Details
        $arrGiftSkus = explode(",",$items->getSkus());
        $arrGiftQtys = explode(",",$items->getQtys());
        $arrGiftPrices = explode(",",$items->getPrices());
        $arrGiftSenderNames = explode(",",$items->getSenderNames());
        $arrGiftSenderEmails = explode(",",$items->getSenderEmails());
        $arrGiftReceiverNames = explode(",",$items->getReceiverNames());
        $arrGiftReceiverEmails = explode(",",$items->getReceiverEmails());
        $arrGiftMessages = explode(",",$items->getMessages());

        $store = $this->storeManager->getStore($storeId);
        $websiteId = $store->getWebsiteId();
        //Check for validation
        foreach($arrGiftSkus as $key=>$giftSku) {
            $productId = $this->_product->getIdBySku($giftSku);
            //echo $productId;exit;
            if(!isset($arrGiftQtys) || !isset($arrGiftPrices) || !isset($arrGiftSenderNames) || !isset($arrGiftSenderEmails) || !isset($arrGiftReceiverNames) || !isset($arrGiftMessages) )
                $invalidInventoryInfo[] = $giftSku;
            if ($productId) {
                $product = $this->_product->load($productId);
                //Check product type
                if(!in_array($storeId,$product->getStoreIds())){
                    $invalidStore[] = $giftSku;
                }else if ($product->getTypeId() != "giftcard") {
                    $invalidType[] = $giftSku;
                } else {
                    //Check product amount is correctly matching if gift amount inputs
                    $giftCardPrices = $this->getConnection()->fetchAll('SELECT value from magento_giftcard_amount where entity_id = ' . $productId);
                    $giftAmounts = array();
                    foreach ($giftCardPrices as $gAmount) {
                        $giftAmounts[] = $gAmount['value'];
                    }
                    $giftSkuPrice = $arrGiftPrices[$key];
                    if (!in_array($giftSkuPrice, $giftAmounts)) {
                        $invalidAmount[] = $giftSku;
                    }
                }
            } else {
                $invalidSku[] = $giftSku;
            }
        }
        if(count($invalidType) > 0  || count($invalidSku) > 0 || count($invalidAmount) > 0 || count($invalidInventoryInfo) > 0 || count($invalidStore) > 0){
            $errorMessage = '';
            if(count($invalidType) > 0)
                $errorMessage[] = 'Invalid Type for products '.implode(", ",$invalidType);
            if(count($invalidSku) > 0)
                $errorMessage[] = 'Invalid Skus '.implode(", ",$invalidSku);
            if(count($invalidAmount) > 0)
                $errorMessage[] = 'Invalid Amount for SKUs '.implode(", ",$invalidAmount);
            if(count($invalidInventoryInfo) > 0)
                $errorMessage[] = 'Invalid Inventory for SKUs '.implode(", ",$invalidInventoryInfo);
            if(count($invalidStore) > 0)
                $errorMessage[] = 'Invalid Store for SKUs '.implode(", ",$invalidStore);
            return ['error'=>FALSE,'msg'=>$errorMessage];//$e->getMessage();
        }

        $arrGiftCodes = array();
        $customerType = $jsonOrderData->getCustomerType();
        $orderData = array(
            'currency_id' => $jsonOrderData->getCurrencyId(),
            'email' => $jsonOrderData->getEmail(),
            'customer_type' => $customerType,
            'customer_address' => array(
                'street' => $billingAddress->getStreet(),
                'city' => $billingAddress->getCity(),
                'country_id' => $billingAddress->getCountryId(),
                'region' => $billingAddress->getRegion(),
                'postcode' => $billingAddress->getPostcode(),
                'telephone' => $billingAddress->getTelephone(),
                'fax' => $billingAddress->getFax(),
                'is_default_shipping' => 1,
                'is_default_billing' => 1,
                'firstname' => $billingAddress->getFirstName(), //address Details
                'lastname'  => $billingAddress->getLastName()
            ),
            'shipping_address' => array(
                'firstname' => $shippingAddress->getFirstName(), //address Details
                'lastname' => $shippingAddress->getLastName(),
                'street' => $shippingAddress->getStreet(),
                'city' => $shippingAddress->getCity(),
                'country_id' => $shippingAddress->getCountryId(),
                'region' => $shippingAddress->getRegion(),
                'postcode' => $shippingAddress->getPostcode(),
                'telephone' => $shippingAddress->getTelephone(),
                'fax' => $shippingAddress->getFax(),
                'save_in_address_book' => $shippingAddress->getSaveInAddressBook()
            ),
            'billing_address' => array(
                'firstname' => $billingAddress->getFirstName(), //address Details
                'lastname' => $billingAddress->getLastName(),
                'street' => $billingAddress->getStreet(),
                'city' => $billingAddress->getCity(),
                'country_id' => $billingAddress->getCountryId(),
                'region' => $billingAddress->getRegion(),
                'postcode' => $billingAddress->getPostcode(),
                'telephone' => $billingAddress->getTelephone(),
                'fax' => $billingAddress->getFax(),
                'save_in_address_book' => $billingAddress->getSaveInAddressBook()
            ),
            'coupon_code' => $jsonOrderData->getCouponCode(),
            'shipping_method' => $jsonOrderData->getShippingMethod(),
            'payment_method' => $jsonOrderData->getPaymentMethod(),
            'acm_ref_no' => $jsonOrderData->getAcmRefNo()
        );

        // load customer by email address
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderData['email']);

        if (!$customer->getEntityId()) {
            //If not available then create this customer
            $customer->setWebsiteId($websiteId)
                ->setStore($store)
                ->setFirstname($orderData['shipping_address']['firstname'])
                ->setLastname($orderData['shipping_address']['lastname'])
                ->setEmail($orderData['email'])
		->setPassword(rand(888888,999999999));

            try {
                $customer->save();
                $this->updateCustomerAttribute("acumatica_customer_id",$jsonOrderData->getCustomerId(),$customer->getId());
            } catch (LocalizedException $e) {
                return ['error'=>FALSE,'msg'=> $e->getMessage()];
            }

            try {
                $address = $this->addressFactory->create()->addData($orderData['customer_address']);
                $customer->addAddress($address)
                    ->setId($customer->getId())->save();
            } catch (LocalizedException $e) {
                return ['error'=>FALSE,'msg'=>$e->getMessage()];
            }
        }
        foreach($arrGiftSkus as $key=>$giftSku) {
            $productId = $this->_product->setStoreId($storeId)->getIdBySku($giftSku);
            $giftAmount = $arrGiftPrices[$key];
            $giftQty = $arrGiftQtys[$key];
            $giftSenderName = $arrGiftSenderNames[$key];
            $giftSenderEmail = $arrGiftSenderEmails[$key];
            $giftReceiverName = $arrGiftReceiverNames[$key];
            $giftReceiverEmail = $arrGiftReceiverEmails[$key];
            $giftMessage = $arrGiftMessages[$key];
            if ($productId) {
                $request = new DataObject();
                $params = array("product" => $productId, "qty" => $giftQty, "giftcard_amount" => $giftAmount, "giftcard_sender_email" => $giftSenderEmail, "giftcard_recipient_email" => $giftReceiverEmail, "giftcard_sender_name" => $giftSenderName, "giftcard_recipient_name" => $giftReceiverName, "giftcard_message" => $giftMessage);
                $request->setData($params);
                $this->cartModel->addProduct($productId, $request);
            }
        }
        $cart = $this->cartModel->saveQuote();
        $quote = $cart->getQuote();
        //set store for which you create quote
        $quote->setStore($store);
        //Set Address to quote
        // if you have already buyer id then you can load customer directly
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        // Apply Coupon Code to the order
        if ($orderData['coupon_code'] != '') {
            $quote->setCouponCode($orderData['coupon_code']);
        }
        $quote->assignCustomer($customer);
        try {
            $quote->getBillingAddress()->addData($orderData['billing_address']);
        } catch (\Exception $e) {
            return ['error'=>FALSE,'msg'=>'Invalid billing address'];
        }

        try {
            $quote->getShippingAddress()->addData($orderData['shipping_address']);
        } catch (\Exception $e) {
            return ['error'=>FALSE,'msg'=>'Invalid shipping address'];
        }
        // Collect Rates and Set Shipping & Payment Method
        try {
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true);
                $shippingAddress->collectShippingRates()
                ->setShippingMethod($orderData['shipping_method']); //shipping method
        } catch( \Exception $e){
            return 'Invalid shipping method';
        }
        try {
            $quote->setPaymentMethod($orderData['payment_method']); //payment method
        } catch (\Exception $e) {
            return ['error'=>FALSE,'msg'=>'Invalid payment method'];
        }
        $quote->setInventoryProcessed(false); //not effect inventory
        $quote->save(); //Now Save quote and your quote is ready
        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => $orderData['payment_method']]);
        // Collect Totals & Save Quote
        $quote->collectTotals()->save();
        // Create Order From Quote
        $order = $this->quoteManagement->submit($quote);
        if (isset($order)) {
            $order->setEmailSent(0);
            //$order->setData('acumatica_order_id', $jsonOrderData->getAcmRefNo());
            $order->save();
            /**
             * Generate Invoice programatically
             */
            if ($order->canInvoice()) {
                $invoice = $this->invoiceService->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                $this->transaction->addObject($invoice)->addObject($invoice->getOrder())->save();
                $this->invoiceSender->send($invoice);
            }
            $items = $order->getItems();
            foreach($items as $item) {
                $codes = $item->getProductOptionByCode('giftcard_created_codes');
                foreach($codes as $code) {
                    $arrGiftCodes[] = $item->getSku() . '|' . $code . '|' . $item->getPrice();
                }
            }
        } else {
            return $result = ['error' => FALSE, 'msg' => 'There was a problem while creating the order. Please check the data.'];
        }
        $boolSuccess = FALSE;
        if(count($arrGiftCodes) > 0)
            $boolSuccess = TRUE;
        return array($boolSuccess,$arrGiftCodes);
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
