<?php

namespace Kensium\Quote\Controller\Quote;


class Quote extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
    
	   $this->_view->loadLayout();
	   // Add breadcrumb
	   /** @var \Magento\Theme\Block\Html\Breadcrumbs */
	   $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');     
	   $breadcrumbs->addCrumb('home',['label' => __('Home'),'title' => __('Home'),'link' => $this->_url->getUrl('')]);
	   $breadcrumbs->addCrumb('quote',['label' => __('Request a Quote'),'title' => __('Request a Quote')]);
       $this->_view->renderLayout();
    }
}
