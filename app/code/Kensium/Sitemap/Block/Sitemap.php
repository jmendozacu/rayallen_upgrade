<?php
namespace Kensium\Sitemap\Block;

class Sitemap extends \Magento\Framework\View\Element\Template
{
    protected $categoryFactory;
    protected $sitemapCategoryFactory;
    protected $_urlInterface;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $sitemapCategoryFactory,
        \Magento\Cms\Model\Page $page,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        array $data = []
    ) {
        $this->categoryFactory=$categoryFactory;
        $this->sitemapCategoryFactory=$sitemapCategoryFactory;
        $this->_catalogLayer = $layerResolver->get();
        $this->_page = $page;
        $this->_pageFactory = $pageFactory;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);

    }

    /**
     * Get current store name.
     *
     * @return string
     */
    public function getCurrentStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }
    /**
     * Get current store id.
     *
     * @return string
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }


    public function getInfo()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $rootCatId = $this->_storeManager->getStore()->getRootCategoryId();

        $categories = $this->categoryFactory->create()->load($rootCatId);
        $childCategories=$categories->getChildrenCategories();

        //$categories = $this->_catalogLayer->getCurrentCategory()->getChildrenCategories();
        foreach ($childCategories as $_category) {
            //echo "<pre>";print_r($_category->getData());
            $sub_cat = $this->categoryFactory->create()->load($_category->getId());
            $name = $sub_cat->getName();
            $url = $sub_cat->getUrl();
            $data[] = array('name' => $name, 'url' => $url);
        }
        return $data;
    }

    public function getPageInfo()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $pages = $this->_pageFactory->create()->getCollection($storeId);
        foreach($pages as $page){
            //echo "<pre>";print_r($page->getData());
            $pages_list = $this->_pageFactory->create()->setStoreId($this->_storeManager->getStore()->getId())->load($page->getPageId());
            if($page->getPageId() != '1' && $pages_list->getIdentifier() !='' && $pages_list->getIdentifier() !='no-route-2' && $pages_list->getIdentifier() !='service-unavailable' && $pages_list->getIdentifier() !='private-sales' && $pages_list->getIdentifier() !='privacy-policy-cookie-restriction-mode' && $pages_list->getIdentifier() !='enable-cookies' && $pages_list->getIdentifier() !='community' && $pages_list->getIdentifier() !='gift-certificates' && $pages_list->getIdentifier() !='image' && $pages_list->getIdentifier() !='porto_home_2' && $pages_list->getIdentifier() !='signaturek9-home' && $pages_list->getIdentifier() !='reward-point' && $pages_list->getIdentifier() !='field-tested'){
                $pageTiltle = $pages_list->getTitle();
                $pageIdentifier = $pages_list->getIdentifier();
                $pagedata[] = array('title' => $pageTiltle, 'identifier' => $pageIdentifier);
            }
        }
        return $pagedata;
    }



}
?>

