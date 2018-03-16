<?php
namespace Iglobal\Stores\Block;

class Includes extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Iglobal\Stores\Helper\Data
    */
    protected $_helper;

    /**
     * @var \Magento\Customer\Model\Session
    */
    protected $_customerSession;

    public function __construct(
        \Iglobal\Stores\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->_helper = $helper;
        $this->_customerSession = $customerSession;
    }

    public function getStoreManager(){
      return $this->_storeManager;
    }

    public function getScopeConfig(){
      return $this->_scopeConfig;
    }
    public function getConfigValue($configPath, $default){
        $value = $this->_scopeConfig->getValue('iglobal_integration/'. $configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($value) {
            return $value;
        }
        return $default;
    }

    public function getHelper(){
      return $this->_helper;
    }

    public function getCustomerSession(){
      return $this->_customerSession;
    }
}
