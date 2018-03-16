<?php
namespace Iglobal\Stores\Controller\Success;


class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Checkout\Model\Session
    */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order
    */
    protected $salesOrder;

    /**
     * @var \Iglobal\Stores\Model\Order
    */
    protected $storesOrder;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Checkout\Model\Cart
    */
    protected $cart;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order $salesOrder,
        \Iglobal\Stores\Model\Order $storesOrder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Checkout\Model\Cart $cart

    ) {
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->salesOrder = $salesOrder;
        $this->storesOrder = $storesOrder;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->resultPageFactory = $resultPageFactory;
        $this->cart = $cart;
        parent::__construct(
            $context
        );
    }

    public function execute()
    {
        $_order = $this->getRequest()->getParam('orderId', null);
        $lastOrderId = "";
        try
        {
            $quote = $this->checkoutSession->getQuote()->setStoreId($this->storeManager->getStore()->getId());
            $sales_order = $this->salesOrder->loadByAttribute('ig_order_number', $_order);

            if(!$_order) {
                header('Location: /');
                return;
            } else if($sales_order->getId()) {
                $this->storesOrder->checkStatus($sales_order);
                $lastOrderId = $sales_order->getId();
            } else {
                $this->storesOrder->processOrder($_order, $quote);
                if (!$this->_objectManager->get('Magento\Checkout\Model\Session\SuccessValidator')->isValid()) {
                    return $this->resultRedirectFactory->create()->setPath('checkout/cart');
                }
                $this->checkoutSession->clearQuote();
                $this->cart->save();
                $lastOrderId = $this->checkoutSession->getLastOrderId();
            }
        }
        catch(\Exception $e)
        {
            $adminEmail = false;
            //die($e);
            if ($this->scopeConfig->getValue('iglobal_integration/apireqs/admin_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                $adminEmail = $this->scopeConfig->getValue('iglobal_integration/apireqs/admin_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            }
            mail('monitoring@iglobalstores.com', 'Magento Integration Error - International order failed to import', 'International order# '. $_order .'.'. ' Exception Message: '.$e->getMessage());
            mail('magentomissedorders@iglobalstores.com', 'Magento Integration Error - International order failed to import', 'International order# '. $_order .'.'. ' Exception Message: '.$e->getMessage());
            if ($adminEmail) {
                mail($adminEmail, 'iGlobal Import Error - International order failed to import', 'iGlobal International order# '. $_order . " failed to import properly.  We've already received notice of the problem, and are probably working on it as you read this.  Until then, you may manually enter the order, or give us a call for help at 1-800-942-0721." );
            }
            $this->logger->log(\Monolog\Logger::ERROR, "International order #{$_order} failed to import!".$e);
        }

        $resultPage = $this->resultPageFactory->create();
        $this->_eventManager->dispatch('checkout_onepage_controller_success_action', ['order_ids' => [$lastOrderId]]);
        return $resultPage;
    }

}
