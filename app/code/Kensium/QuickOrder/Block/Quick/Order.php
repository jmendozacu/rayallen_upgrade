<?php
/**
 * Copyright © 2015 Kensium . All rights reserved.
 */
namespace Kensium\QuickOrder\Block\Quick;
use Kensium\QuickOrder\Block\BaseBlock;
class Order extends BaseBlock
{
    
    public $sessionGeneric;
    public $multipleLimit;
    protected $scopeConfig;
    public function __construct(\Kensium\QuickOrder\Block\Context $context,
            \Magento\Framework\Session\Generic $sessionGeneric,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
            )
    {
        $this->scopeConfig = $scopeConfig;
        $this->sessionGeneric = $sessionGeneric;
        $this->multipleLimit =  $this->scopeConfig->getValue('quickorder/kensium_quickorder/multiple_products_limit');
       
        parent::__construct($context);
//         die("Quick Order Limit".$this->_scopeConfig->getValue('quickorder/kensium_quickorder/multiple_products_limit'));
    }

    public function testCode()
    {
      echo  $this->_storeManager->getStore()->getBaseUrl();
    }
	
    
    public function urlBase(){
      return  $this->_storeManager->getStore()->getBaseUrl();        
    }

}
