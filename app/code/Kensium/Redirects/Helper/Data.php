<?php
namespace Kensium\Redirects\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_directoryList;
	
	const XML_PATH_ENABLED = 'redirects/general/enable';
	
	
	
	public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
		DirectoryList $directoryList
    ) {
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
		$this->_directoryList = $directoryList;
        parent::__construct($context);
    }
	
	public function isEnabled($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE, $store);
    }
		
	public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

}