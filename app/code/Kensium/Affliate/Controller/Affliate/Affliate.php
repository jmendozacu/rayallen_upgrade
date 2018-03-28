<?php

namespace Kensium\Affliate\Controller\Affliate;


class Affliate extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
    
	   $this->_view->loadLayout();
	   // Add breadcrumb
	   /** @var \Magento\Theme\Block\Html\Breadcrumbs */
           $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Affiliate Application'));
	   //echo 'sdffd';exit;
           $this->_view->renderLayout();
    }
}
