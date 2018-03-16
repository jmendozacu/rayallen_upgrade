<?php

namespace Kensium\TrackOrder\Controller\Trackorder;


class Index extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $om->get('Magento\Framework\App\RequestInterface');
        $data=$request->getParams();
        $order = $om->get('\Magento\Sales\Model\Order')->load($data['orderId']);
        $coreRegistry = $om->get('\Magento\Framework\Registry');
        $coreRegistry->register('current_order', $order);
	    $this->_view->loadLayout();
       //$this->_view->getPage()->getConfig()->getTitle()->prepend(__('Request a Quote Online'));
	   // Add breadcrumb
	   /** @var \Magento\Theme\Block\Html\Breadcrumbs */
	   $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');     
	   $breadcrumbs->addCrumb('home',['label' => __('Home'),'title' => __('Home'),'link' => $this->_url->getUrl('')]);
	   $breadcrumbs->addCrumb('quote',['label' => __('Track Order'),'title' => __('Track Order')]);
       $this->_view->renderLayout();
    }
}
