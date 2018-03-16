<?php
namespace Iglobal\Stores\Model;


class Order extends \Magento\Framework\Model\AbstractModel
{

    const STATUS_FRAUD      = 'IGLOBAL_FRAUD_REVIEW';
    const STATUS_IN_PROCESS = 'IGLOBAL_ORDER_IN_PROCESS';
    const STATUS_HOLD       = 'IGLOBAL_ORDER_ON_HOLD';
    const STATUS_CANCELED   = 'IGLOBAL_ORDER_CANCELED';

    /**
     * Quote instance
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote = null;
    protected $iglobal_order_id = null;
    protected $iglobal_order = null;
    protected $rest = null;

    /**
     * @var \Iglobal\Stores\Model\RestFactory
     */
    protected $storesFactory;

    /**
     * @var \Iglobal\Stores\Model\CarrierFactory
     */
    protected $carrierFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
    * @var \Magento\Checkout\Model\Session
    */
    protected $checkoutSession;

    /**
    * @var \Magento\Directory\Model\Region
    */
    protected $directoryRegion;

    /**
    * @var \Magento\Catalog\Model\Product
    */
    protected $catalogProduct;

    /**
    * @var \Magento\Sales\Model\Order
    */
    protected $salesOrder;

    /**
    * @var \Magento\Sales\Model\Order\Payment\Transaction
    */
    protected $salesOrderPaymentTransaction;

    /**
    * @var \Magento\Framework\DB\Transaction
    */
    protected $dbTransaction;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
    */
    protected $quoteManager;

    /**
     * @var \Magento\Customer\Model\Session
    */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
    */
    protected $emailSender;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Iglobal\Stores\Model\RestFactory $storesFactory,
        \Iglobal\Stores\Model\CarrierFactory $carrierFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Directory\Model\Region $directoryRegion,
        \Magento\Catalog\Model\Product $catalogProduct,
        \Magento\Sales\Model\Order $salesOrder,
        \Magento\Sales\Model\Order\Payment\Transaction $salesOrderPaymentTransaction,
        \Magento\Framework\DB\Transaction $dbTransaction,
        \Magento\Quote\Model\QuoteManagement $quoteManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $emailSender,
        // \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->storesFactory = $storesFactory;
        $this->carrierFactory = $carrierFactory;
        $this->registry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->directoryRegion = $directoryRegion;
        $this->catalogProduct = $catalogProduct;
        $this->salesOrder = $salesOrder;
        $this->salesOrderPaymentTransaction = $salesOrderPaymentTransaction;
        $this->dbTransaction = $dbTransaction;
        $this->quoteManager = $quoteManager;
        $this->customerSession = $customerSession;
        $this->emailSender = $emailSender;
        $this->logger = $context->getLogger();
        // $this->logger = $logger;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }


    public function setQuote($quote)
    {
        $this->quote = $quote;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function checkStatus($order)
    {
        if (!$this->iglobal_order)
        {
            $this->setIglobalOrder($order->getIgOrderNumber());
        }
        $status = $this->iglobal_order->orderStatus;
        if (($status == self::STATUS_FRAUD || $status == self::STATUS_HOLD || $status == self::STATUS_CANCELED) && $order->canHold()) {
            $order->hold();
            $order->addStatusHistoryComment("Order Set to {$status} by iGlobal", false);
            $order->save();
        } elseif ($status == self::STATUS_IN_PROCESS && $order->canUnHold()) {
            $order->unHold();
            $order->addStatusHistoryComment("Order Set to {$status} by iGlobal", false);
            $order->save();
        }
    }
    public function setIglobalOrder($orderid)
    {
        $this->iglobal_order_id = $orderid;
        if (!$this->iglobal_order)
        {
            $this->rest = $this->storesFactory->create();
            $this->iglobal_order = $this->rest->getOrder($this->iglobal_order_id)->order;
        }
    }

    public function processOrder($orderid, $quote=NULL)
    {
        $this->setIglobalOrder($orderid);
        if ($this->iglobal_order->merchantOrderId)
        {
            return false;
        }
        // check the if this is the same quote that was sent.
        if ($quote)
        {
            $this->quote = $quote;
        } elseif (!$this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }

        if ($this->iglobal_order->referenceId && $this->iglobal_order->referenceId != $this->quote->getId())
        {
            $this->quote->load($this->iglobal_order->referenceId);
        }

        // Set the duty_tax for the address total collection to use
        $this->registry->register('duty_tax', $this->iglobal_order->dutyTaxesTotal);
        $shippingAddress = $this->setContactInfo();
        $this->setItems();
        $shippingAddress = $this->setShipping($shippingAddress);
        $this->setPayment($shippingAddress);
        $order = $this->createOrder();
        $this->registry->unregister('duty_tax');
        $this->registry->unregister('shipping_cost');
        $this->registry->unregister('shipping_carriertitle');
        $this->registry->unregister('shipping_methodtitle');
        return $order;
    }
    public function regionId($state, $countryCode){
        $region = $this->directoryRegion->loadbyName($state, $countryCode);
        if (!$region->getId())
        {
            // Lookup region from iGlobalstores
            $regionId = $this->rest->getRegionId($countryCode, $state, $this->iglobal_order_id);
            if(property_exists($regionId, "magentoRegionId")) {
                $region->load($regionId->magentoRegionId);
            }
            if (!$region->getId())
            {
                $regionData = array(
                  'country_id' => $countryCode,
                  'default_name' => $state,
                );
                if(property_exists($regionId,"isoCode")) {
                    $regionData['code'] = $regionId->isoCode;
                }
                // Create a new region
                $region->setData($regionData)->save();
            }
        }
        return $region->getId();
    }
    protected function setContactInfo()
    {
        //set customer info

        //check if logged in
        if($this->customerSession->isLoggedIn()){
          $this->quote->setCustomerIsGuest(false);
        }else{
          $this->quote->setCustomerIsGuest(true);
        }

        $this->quote->setCustomerEmail($this->iglobal_order->email);

        $_name = explode(' ', $this->iglobal_order->name, 2);
        if ($this->iglobal_order->testOrder) {
            $name_first = "TEST ORDER! DO NOT SHIP! - " . array_shift($_name);
            $name_last = array_pop($_name);
        } else {
            $name_first = array_shift($_name);
            $name_last = array_pop($_name);
        }

        $this->quote->setCustomerFirstname($name_first);
        $this->quote->setCustomerLastname($name_last);

        $street = $this->iglobal_order->address1;
        if ($this->iglobal_order->address2)
        {
            $street = array($street, $this->iglobal_order->address2);
        }

        if($this->iglobal_order->countryCode == 'PR') {
            $this->iglobal_order->countryCode = 'US';
            $this->iglobal_order->state = 'Puerto Rico';
        }
        if($this->iglobal_order->countryCode == 'VI') {
            $this->iglobal_order->countryCode = 'US';
            $this->iglobal_order->state = 'Virgin Islands';
        }
        if($this->iglobal_order->billingCountryCode == 'PR') {
            $this->iglobal_order->billingCountryCode = 'US';
            $this->iglobal_order->billingState = 'Puerto Rico';
        }
        if($this->iglobal_order->billingCountryCode == 'VI') {
            $this->iglobal_order->billingCountryCode = 'US';
            $this->iglobal_order->billingState = 'Virgin Islands';
        }
        $addressData = array(
            'firstname' => $name_first,
            'lastname' => $name_last,
            'street' => $street,
            'city' => $this->iglobal_order->city ?  $this->iglobal_order->city : $this->iglobal_order->state,
            'postcode' => $this->iglobal_order->zip,
            'telephone' => $this->iglobal_order->phone,
            'region' => $this->iglobal_order->state,
            'region_id' => $this->regionId($this->iglobal_order->state, $this->iglobal_order->countryCode),
            'country_id' => $this->iglobal_order->countryCode,
            'company' => '',//$this->iglobal_order->company,
        );

        if (!empty($this->iglobal_order->billingAddress1)) {
            $_nameBilling = explode(' ', $this->iglobal_order->billingName, 2);
            if ($this->iglobal_order->testOrder) {
                $name_first_billing = "TEST ORDER! DO NOT SHIP! - " . array_shift($_nameBilling);
                $name_last_billing = array_pop($_nameBilling);
            } else {
                $name_first_billing = array_shift($_nameBilling);
                $name_last_billing = array_pop($_nameBilling);
            }

            $streetBilling = $this->iglobal_order->billingAddress1;
            if ($this->iglobal_order->billingAddress2) {
                $streetBilling = array($streetBilling, $this->iglobal_order->billingAddress2);
            }

            $billingAddressData = array(
                'firstname' => $name_first_billing,
                'lastname' => $name_last_billing,
                'street' => $streetBilling,
                'city' => $this->iglobal_order->billingCity ? $this->iglobal_order->billingCity : $this->iglobal_order->billingState,
                'postcode' => $this->iglobal_order->billingZip,
                'telephone' => $this->iglobal_order->billingPhone,
                'region' => $this->iglobal_order->billingState,
                'region_id' => $this->regionId($this->iglobal_order->billingState, $this->iglobal_order->billingCountryCode),
                'country_id' => $this->iglobal_order->billingCountryCode,
            );

        } else {
            $billingAddressData = $addressData;
        }
        if ($this->scopeConfig->getValue('iglobal_integration/igjq/iglogging', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
        {
            $this->logger->log('INFO', 'address data for order {$this->iglobal_order_id}: '.print_r($addressData,true));
            $this->logger->log('INFO', 'billing address data for order {$this->iglobal_order_id}: '.print_r($billingAddressData,true));
        }

        $this->quote->getBillingAddress()->addData($billingAddressData);
        $shippingAddress = $this->quote->getShippingAddress()->addData($addressData);
        return $shippingAddress;
    }

    protected function setItems()
    {
        $quote_items = array();
        $ig_items = array();
        foreach($this->quote->getAllVisibleItems() as $item) {
            if($item->getOptionByCode("simple_product")) {
                $quote_items[$item->getOptionByCode("simple_product")->getValue()] = $item;
            } else {
                $quote_items[$item->getProductId()] = $item;
            }

        }
        foreach ($this->iglobal_order->items as $item) {
            if ($item->productId) { // discounts do not have a productId set
                $ig_items[$item->productId] = $item;
            }
        }

        $missing = array_diff_key($ig_items, $quote_items);
        $extra = array_diff_key($quote_items, $ig_items);
        foreach ($missing as $pid => $item)
        {
            // Add the product to the quote
            $product = $this->catalogProduct->load($pid);
            if ($product->getId())
            {
                $product->setPrice($item->unitPrice);
                $this->quote->addProduct($product, $item->quantity);
            } else {
                $this->logger->log('WARNING', "Missing sku `{$item->sku}' for {$this->iglobal_order_id}");
            }
        }
        foreach($extra as $item)
        {
            $this->quote->deleteItem($item);
            $item->delete();
        }
    }

    protected function setShipping($shippingAddress)
    {
        $shippers = $this->carrierFactory->create()->getAllowedMethods();
        $carrierMethod = $this->iglobal_order->shippingCarrierServiceLevel;
        if (!$carrierMethod || !array_key_exists($carrierMethod, $shippers)) {
            $carrierMethod = 'ig';
        }
        $shippingMethod = $this->iglobal_order->customerSelectedShippingName;
        if(!$shippingMethod) {
            $shippingMethod = "International shipping";
        }

        //Add things to the register so they can be used by the shipping method
        $this->registry->register('shipping_cost', $this->iglobal_order->shippingTotal);
        $this->registry->register('shipping_carriertitle', $carrierMethod);
        $this->registry->register('shipping_methodtitle', $shippingMethod);
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('ig_'.$carrierMethod);
        return $shippingAddress;
    }

    protected function setPayment($address)
    {
        //updates payment type in Magento Admin area
        if (isset($this->iglobal_order->paymentProcessing)) {
            $data = (array) $this->iglobal_order->paymentProcessing;
        } else {
            $data = array();
        }
        if(isset( $this->iglobal_order->paymentProcessing->paymentGateway)) {
            $paymentMethod = $this->iglobal_order->paymentProcessing->paymentGateway;
        } else if (isset($this->iglobal_order->paymentProcessing->paymentProcessor)) {
            $paymentMethod = $this->iglobal_order->paymentProcessing->paymentProcessor;
        } else {
            $paymentMethod = 'iGlobal';
        }
        switch($paymentMethod) {
            case 'iGlobal_CC':
            case 'AUTHORIZE_NET':
            case 'BRAINTREE':
            case 'CYBERSOURCE':
            case 'INTERPAY':
            case 'PAYPAL_CC':
            case 'PAY_FLOW':
            case 'STRIPE':
            case 'USA_EPAY':
                if(isset($this->iglobal_order->paymentProcessing->cardType) &&
                    $this->iglobal_order->paymentProcessing->cardType == 'PURCHASE_ORDER') {
                    $data['method'] = 'iGlobal';
                } else {
                    $data['method'] = 'iGlobalCreditCard';
                }
                break;
            case 'iGlobal PayPal':
            case 'INTERPAY_PAYPAL':
            case 'PAYPAL_EXPRESS':
            case 'PAYPAL':
                $data['method'] = 'iGlobalPaypal';
                break;
            default:
                $data['method'] = 'iGlobal';
        }
        $address->setPaymentMethod($data['method']);
        $this->quote->getPayment()->importData($data);
    }

    protected function setTransactionInfo($order){
        //add trans ID
        try {
            $transaction_id = $this->iglobal_order->paymentProcessing->transactionId;
        } catch (\Exception $e){
            $transaction_id = '34234234234';
        }
        if(!$transaction_id) {
            $transaction_id = '34234234234';
        }
        $transaction = $this->salesOrderPaymentTransaction;
        $transaction->setOrderId($order->getId());
        $transaction->setPaymentId($order->getPayment()->getId());
        //$transaction->setOrderPaymentObject($order->getPayment());
        $transaction->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH);
        $transaction->setTxnId($transaction_id);
        if(isset($this->iglobal_order->paymentProcessing)) {
            try {
                $transaction->setAdditionalInformation(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, (array)$this->iglobal_order->paymentProcessing);
                if ($this->iglobal_order->paymentProcessing->transactionType == "AUTH_CAPTURE") {
                    $transaction->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
                }
            } catch (\Exception $e) {

            }
        }
        $transaction->save();
    }

    protected function createOrder()
    {

        $this->quote->collectTotals()->save();
        $order = $this->quoteManager->submit($this->quote);

        $this->checkoutSession->setLastSuccessQuoteId($this->quote->getId());
        $this->checkoutSession->setLastQuoteId($this->quote->getId());
        $this->checkoutSession->clearHelperData();

        $this->_eventManager->dispatch(
            'checkout_type_onepage_save_order_after',
            ['order' => $order, 'quote' => $this->quote]
        );

        $id = $order->getEntityId();

        if ($this->scopeConfig->getValue('iglobal_integration/apireqs/send_order_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $this->emailSender->send($order, $forceSyncMode=true);
        }

        //Save Order Invoice as paid
        $commentMessage = 'Order automatically imported from iGlobal order ID: '. $this->iglobal_order_id;

        try {
            $order = $this->salesOrder->load($id);
            $this->setTransactionInfo($order);


            $invoices = $order->getInvoiceCollection()->addAttributeToFilter('order_id', array('eq'=>$order->getId()));
            $invoices->getSelect()->limit(1);
            if ((int)$invoices->count() == 0 && $order->getState() == \Magento\Sales\Model\Order::STATE_NEW) {
                if(!$order->canInvoice()) {
                    $order->addStatusHistoryComment($commentMessage, false);
                    $order->addStatusHistoryComment('iGlobal: Order cannot be invoiced', false);
                    $order->save();
                } else {
                    $order->addStatusHistoryComment($commentMessage, false);
                    $invoice = $order->prepareInvoice();
                    $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
                    $invoice->register();
                    $invoice->getOrder()->setCustomerNoteNotify(false);
                    $invoice->getOrder()->setIsInProcess(true);
                    $order->addStatusHistoryComment('Automatically INVOICED by iGlobal', false);
                    $transactionSave = $this->dbTransaction
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());
                    $transactionSave->save();
                }
            }
            $this->checkStatus($order);

            // add customer notes
            if($this->iglobal_order->notes){
                foreach ($this->iglobal_order->notes as $note) {
                  if($note->customerNote) {
                      $order->addStatusHistoryComment($note->note, false);
                  }
                }
            }
            $extraNote = "";
            if($this->iglobal_order->birthDate) {
                $extraNote .= "Birthdate: " . $this->iglobal_order->birthDate . "\n";
            }
            if($this->iglobal_order->nationalIdentifier) {
                $extraNote .= "National Identifier: " . $this->iglobal_order->nationalIdentifier . "\n";
            }
            if($this->iglobal_order->boxCount) {
                $extraNote .= "Boxes: " . $this->iglobal_order->boxCount . "\n";
            }

            if($extraNote) {
                $order->addStatusHistoryComment($extraNote, false);
            }
        } catch (\Exception $e) {
            $order->addStatusHistoryComment('iGlobal Invoicer: Exception occurred during automatically invoicing. Exception message: '.$e->getMessage(), false);
            $order->save();
        }
        if ($this->iglobal_order->testOrder) {
            $order->setIglobalTestOrder(1);
        }

        $redirectUrl = $this->quote->getPayment()->getOrderPlaceRedirectUrl();

        $order->setIgOrderNumber($this->iglobal_order_id);
        $order->setInternationalOrder(1);
        $order->save();

        //Send the magento id to iGlobal
        $this->rest->sendMagentoOrderId($this->iglobal_order_id, $order->getIncrementId());

        $this->checkoutSession->setLastOrderId($order->getId());
        $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
        $this->checkoutSession->setLastOrderStatus($order->getStatus());
        $this->checkoutSession->setRedirectUrl($redirectUrl);

        $this->_eventManager->dispatch(
            'checkout_submit_all_after',
            [
                'order' => $order,
                'quote' => $this->quote
            ]
        );

        return $order;
    }
}
