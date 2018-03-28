<?php

namespace Kensium\Testimonial\Controller\Testimonial;


class Testimonial extends \Magento\Framework\App\Action\Action
{
    
    public function execute()
    {
	   $this->_view->loadLayout();
       $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Testimonials'));

		// Add breadcrumb
		/** @var \Magento\Theme\Block\Html\Breadcrumbs */
		$breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');     
		$breadcrumbs->addCrumb('home',['label' => __('Home'),'title' => __('Home'),'link' => $this->_url->getUrl('')]);
		$breadcrumbs->addCrumb('testimonials',['label' => __('Testimonials'),'title' => __('Testimonials')]);
		$this->_view->renderLayout();
    }
}
