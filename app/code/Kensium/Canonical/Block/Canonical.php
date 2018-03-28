<?php

namespace Kensium\Canonical\Block;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Cms\Model\Page;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Canonical extends Template
{
    /**
     * @var RequestInterface
     */
    protected $_appRequestInterface;

    /**
     * @var Page
     */
    protected $_modelPage;

    /**
     * @var UrlInterface
     */
    protected $_frameworkUrlInterface;


    /**
     * @var LayerFactory
     */
    protected $_modelLayerFactory;

    /**
     * @var CategoryFactory
     */
    protected $_modelCategoryFactory;


    /**
     * @var Status
     */
    protected $_productStatus;

    /**
     * @var Visibility
     */
    protected $_productVisibility;

    /**
     * @var ScopeConfigInterface
     */
    protected $_configScopeConfigInterface;

    /**
     * @var Registry
     */
    protected $_frameworkRegistry;

    /**
     * @var ReviewFactory
     */
    protected $_modelReviewFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_modelStoreManagerInterface;

    /**
     * @var ProductFactory
     */
    protected $_modelProductFactory;

    public function __construct(Context $context,
                                RequestInterface $appRequestInterface,
                                Page $modelPage,
                                UrlInterface $frameworkUrlInterface,
                                CategoryFactory $modelCategoryFactory,
                                Status $productStatus,
                                Visibility $productVisibility,
                                ScopeConfigInterface $configScopeConfigInterface,
                                Registry $frameworkRegistry,
                                ReviewFactory $modelReviewFactory,
                                StoreManagerInterface $modelStoreManagerInterface,
                                ProductFactory $modelProductFactory,
                                array $data = [])
    {
        $this->_appRequestInterface = $appRequestInterface;
        $this->_modelPage = $modelPage;
        $this->_frameworkUrlInterface = $frameworkUrlInterface;
        $this->_modelCategoryFactory = $modelCategoryFactory;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
        $this->_configScopeConfigInterface = $configScopeConfigInterface;
        $this->_frameworkRegistry = $frameworkRegistry;
        $this->_modelReviewFactory = $modelReviewFactory;
        $this->_modelStoreManagerInterface = $modelStoreManagerInterface;
        $this->_modelProductFactory = $modelProductFactory;

        parent::__construct($context, $data);
    }


    public function getLink()
    {
        $message = '';
        $modUrl='';
        $moduleName = $this->_appRequestInterface->getModuleName();
        $routeName = $this->_appRequestInterface->getRouteName();
        $controllerName = $this->_appRequestInterface->getControllerName();
        $currentPageUrl = $this->_frameworkUrlInterface->getCurrentUrl();
        $currentPageUrl = rtrim($currentPageUrl, '/');
        if ($controllerName == 'affliate' || $moduleName == 'contact' || $moduleName == 'Quote' || $this->_modelPage->getId()) {
            $pageURL = str_replace(array("/index.php", "https"), array("", "http"), $currentPageUrl);
            return '<link rel="canonical" href="' . $pageURL . '"/>';
        }

        if ($controllerName == 'result' && $moduleName == 'catalogsearch') {
            return '';
        }

        $pageNum = $this->_appRequestInterface->getParam('p');

        if (strpos($currentPageUrl, '?start') !== false || strpos($currentPageUrl, '&start') !== false ||
            strpos($currentPageUrl, '?p') !== false || strpos($currentPageUrl, '&p') !== false ||
            strpos($currentPageUrl, '?limit') !== false || strpos($currentPageUrl, '&limit') !== false || strpos($currentPageUrl, '?product_list_limit') !== false
        ) {
            $splitUrl = explode("?", $currentPageUrl);
            $modUrl = $splitUrl[0];
           // if ($pageNum != '' && $pageNum > 1) {
                $modUrl .= "?product_list_limit=all";
           // }
            return '<link rel="canonical" href="' . $modUrl . '"/>';

        }
        if ($controllerName == "category" && $moduleName != "blog") {
            $splitUrl = explode("?", $currentPageUrl);
            $modUrl = $splitUrl[0];
            $modUrl .= "?product_list_limit=all";
            $modUrl = str_replace("https", "http", $modUrl);
            return '<link rel="canonical" href="' . $modUrl . '"/>';
        }
        if ($controllerName == "product") {
            if ($routeName == "review") {
                $modUrl = $currentPageUrl;
                $modUrl .= "?product_list_limit=all";
            } else {
                $pageURL = rtrim($currentPageUrl, '/');
                $modUrl = str_replace("https", "http", $pageURL);
            }

        }else{
            $modUrl = str_replace("https", "http", $currentPageUrl);
        }

        if ($this->_modelPage->getIdentifier() != 'no-route') {
            $message = '<link rel="canonical" href="' . $modUrl . '"/>';
        }

        return $message;

    }

}
