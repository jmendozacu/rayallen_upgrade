<?php
namespace Iglobal\Stores\Controller\Checkout;

//
// Load the iGlobal hosted checkout in an iframe, cause it's awesome
//
class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Iglobal\Stores\Helper\Data
     */
    protected $storesHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Iglobal\Stores\Model\International
     */
    protected $storesInternational;

    /**
     * @var \Magento\Customer\Model\Session
    */
    protected $customerSession;

    /**
     * @var  \Magento\Framework\App\Response\Http
    */
    protected $response;

    /**
     * @var \Magento\Checkout\Model\Cart
    */
    protected $cart;

    /**
     * @var \Magento\Framework\Url
    */
    protected $url;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Iglobal\Stores\Helper\Data $storesHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Iglobal\Stores\Model\International $storesInternational,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\Url $url,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->storesHelper = $storesHelper;
        $this->scopeConfig = $scopeConfig;
        $this->storesInternational = $storesInternational;
        $this->customerSession = $customerSession;
        $this->response = $response;
        $this->url = $url;
        $this->cart = $cart;
        parent::__construct(
            $context
        );
    }

    public function execute()
    {
  		//check to see if they are domestic and then redirect to domestic checkout
  		$helper = $this->storesHelper;
  		if($helper->isDomestic()){
  			$this->_redirect('checkout/');
  			return;
  		}

  		if ($this->scopeConfig->getValue('iglobal_integration/apireqs/force_login', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) && !$this->customerSession->isLoggedIn()) {
  			$this->customerSession->setBeforeAuthUrl($this->url->getUrl('iglobal/checkout'));
  			$this->response->setRedirect($this->url->getUrl('customer/account/login'));
  		}
      $cartQty = (int) $this->cart->getQuote()->getItemsQty();

      if (!$cartQty) {
          $this->_redirect('checkout/cart');
         return;
      }

  		if(!$this->storesInternational){
            $this->_redirect('checkout/cart');
            return;
  		}

  		$cartId = $this->storesInternational->getTempCartId();
  		$url = $helper->getCheckoutUrl($cartId);

  		if($this->scopeConfig->getValue('iglobal_integration/apireqs/use_iframe', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
  			$domCode = '<html><head><title>International Checkout</title>';
  			$domCode .= '<style type="text/css">body,html{margin:0;padding:0;height:100%;overflow:hidden;}#content{position:absolute;left:0;right:0;bottom:0;top:0;}</style>';
  			$domCode .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
  			$domCode .= '</head><body>';
  			$domCode .= '<div id="content"><iframe width="100%" height="100%" frameborder="0" src="'.$url.'"/></div>';
  			$domCode .= '</body></html>';
  			$this->response->setContent($domCode);
  		} else {
  			$this->_redirect($url);
  		}
  	}

}
