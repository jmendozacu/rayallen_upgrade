<?php

namespace Kensium\Catalogrequest\Controller\Catalogrequest;


class Index extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
    
	   $this->_view->loadLayout();
       //$this->_view->getPage()->getConfig()->getTitle()->prepend(__('Request a Quote Online'));
	   // Add breadcrumb
	   /** @var \Magento\Theme\Block\Html\Breadcrumbs */
	   $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');     
	   $breadcrumbs->addCrumb('home',['label' => __('Home'),'title' => __('Home'),'link' => $this->_url->getUrl('')]);
	   $breadcrumbs->addCrumb('quote',['label' => __('Online Catalog Version'),'title' => __('Online Catalog Version')]);
       $this->_view->renderLayout();
    }
}
