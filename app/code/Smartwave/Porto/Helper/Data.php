<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Smartwave\Porto\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_objectManager;
	protected $categoryFactory;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager= $objectManager;
		$this->categoryFactory=$categoryFactory;
        parent::__construct($context);
    }
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getModel($model) {
        return $this->_objectManager->create($model);
    }
    public function getCurrentStore() {
        return $this->_storeManager->getStore();
    }

	public function getInfo($catId)
    {
        $category=$this->categoryFactory->create()->load($catId);
        $childCategories=$category->getChildrenCategories();//->limit(8);
        $data=array();
        $i=0;
        foreach($childCategories as $cat) {
            //if($i==10) break;
            $category=$this->categoryFactory->create()->load($cat->getId());          
            $name=$category->getName();
            $data[]=array('name' =>$name,'url' =>$category->getUrl(),'image'=>$category->getImageUrl());
            //$i++;
        }
        return $data;
    }
}
