<?php
namespace Kensium\Redirects\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;


class SetRedirectAction implements ObserverInterface
{  
    protected $_dataHelper;
	protected $_productloader;
	protected $_requestPram;
	protected $_urlBuilder;
	protected $categoryRepository;
	protected $_response;
	protected $_storeManager;
    public function __construct(
        \Kensium\Redirects\Helper\Data $dataHelper,
		\Magento\Catalog\Model\ProductFactory $_productloader,
		\Magento\Framework\App\Request\Http $requestPram,
		\Magento\Framework\UrlInterface $urlBuilder,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository,
		\Magento\Framework\App\ResponseInterface $response
    ) {
        $this->_dataHelper = $dataHelper;
		$this->_productloader = $_productloader;
		$this->_requestPram = $requestPram;
		$this->_urlBuilder = $urlBuilder;
		$this->categoryRepository = $categoryRepository;
		$this->_response = $response;
	}

    public function execute(EventObserver $observer)
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $currentStore = $storeManager->getStore();  
        $baseUrl = $currentStore->getBaseUrl();
		$request = $observer->getEvent()->getControllerAction()->getRequest();
        $actionName = $request->getControllerName();
		$requestUrl = rtrim($request->getScheme() . '://' . $request->getHttpHost() . $request->getRequestUri(), '/');
		if (($actionName == 'noroute' && $this->_dataHelper->isEnabled())) {
            $this->_response->setRedirect($baseUrl, '301');
            $this->_response->sendResponse();
            exit;
        }
		return;
    }
}
