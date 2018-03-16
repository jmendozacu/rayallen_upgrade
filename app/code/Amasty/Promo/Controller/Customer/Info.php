<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Amasty\Promo\Controller\Customer;

/**
 * @codeCoverageIgnore
 */
class Info extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Escaper $escaper
     *
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }
    /**
     * Info Action
     *
     * @return void
     */
    public function execute()
    {

        if( preg_match("/(iPad)/i", $_SERVER["HTTP_USER_AGENT"]) ) {
            $deviceType = "t";
        } elseif( preg_match("/(Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk)/i", $_SERVER["HTTP_USER_AGENT"]) ) {
            $deviceType = "m";
        } else {
            $deviceType = "d";
        }

        $_customer = $this->_customerSession->getCustomer();
            if($_customer->getEmail()) {
                $email = $_customer->getEmail();
            } else {
                $email = ' ';
            }
	if($this->getRequest()->getParam('ajax')) {
	    $account = $this->getRequest()->getParam('account');
	}
        if($this->getRequest()->getParam('ajax')== 'viewHome') {
            $_customer = $this->_customerSession->getCustomer();
            if($_customer->getEmail()) {
                $email = $_customer->getEmail();
            } else {
                $email = ' ';
            }
            echo '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
                                        <script type="text/javascript">
                                        window.criteo_q = window.criteo_q || [];
                                window.criteo_q.push(
                                                 { event: "setAccount", account: '.$account.' },
                                                 { event: "setSiteType", type: "'.$deviceType.'" },
                                                 { event: "setEmail", email: "'.$email.'"},         
                                                 { event: "viewHome"}
                                 );
                        </script>';
            exit;
        }

        if($this->getRequest()->getParam('ajax')== 'productView') {
            $sku = $this->getRequest()->getParam('sku');
            echo '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
                    <script type="text/javascript">
                    window.criteo_q = window.criteo_q || [];
                    window.criteo_q.push(
                        { event: "setAccount", account: '.$account.' },
                        { event: "setSiteType", type: "'.$deviceType.'" },
                        { event: "setEmail", email: "'.$email.'" },
                        { event: "viewItem", item: "'.$sku.'" }
                    );
                    </script>';
            exit;
        }

        if($this->getRequest()->getParam('ajax')== 'checkoutCartView') {
            $_customer = $this->_customerSession->getCustomer();
            if($_customer->getEmail()) {
                $email = $_customer->getEmail();
            } else {
                $email = ' ';
            }
            $viewBasket = $this->getRequest()->getParam('viewBasket');


            echo '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
                     <script type="text/javascript">
                     window.criteo_q = window.criteo_q || [];
                     window.criteo_q.push(
                     { event: "setAccount", account: '.$account.' },
                     { event: "setSiteType", type: "'.$deviceType.'" },
                     { event: "setEmail", email: "'.$email.'" },
                     { event: "viewBasket", item: ['.$viewBasket.']}
                     );
                     </script>';
            exit;
        }

        if($this->getRequest()->getParam('ajax')== 'productListView') {
            $_customer = $this->_customerSession->getCustomer();
            if($_customer->getEmail()) {
                $email = $_customer->getEmail();
            } else {
                $email = ' ';
            }
            $criteoPids = $this->getRequest()->getParam('criteoPids');

            echo '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
                     <script type="text/javascript">
                     window.criteo_q = window.criteo_q || [];
                     window.criteo_q.push(
                     { event: "setAccount", account: '.$account.' },
                     { event: "setSiteType", type: "'.$deviceType.'" },
                     { event: "setEmail", email: "'.$email.'" },
                     { event: "viewList", item: ['.$criteoPids.']}
                     );
                     </script>';
            exit;
        }

        if($this->getRequest()->getParam('ajax')== 'SuccessView') {

            $email = $this->getRequest()->getParam('email');
            $lastTransId = $this->getRequest()->getParam('lastTransId');
            $orderItems = $this->getRequest()->getParam('orderItems');

            echo '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
                     <script type="text/javascript">
                     window.criteo_q = window.criteo_q || [];
                     window.criteo_q.push(
                     { event: "setAccount", account: '.$account.' },
                     { event: "setSiteType", type: "'.$deviceType.'" },
                     { event: "setEmail", email: "'.$email.'" },
                     { event: "trackTransaction", id: "'.$lastTransId.'", item: ['.$orderItems.']}
                     );
                     </script>';
            exit;
        }


        $_customer = $this->_customerSession->getCustomer();
       // $this->_coreRegistry->register('current_reward', $this->_getReward());
        if($_customer->getId()) {
            $this->_view->loadLayout();
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Redeem Points'));

            // Add breadcrumb
            /** @var \Magento\Theme\Block\Html\Breadcrumbs */
            $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');
            $breadcrumbs->addCrumb('home', ['label' => __('Home'), 'title' => __('Home'), 'link' => $this->_url->getUrl('')]);
            $breadcrumbs->addCrumb('promo', ['label' => __('Redeem Points'), 'title' => __('Redeem Points')]);

            $this->_view->renderLayout();
        }else{
            $this->_redirect('customer/account/login');
        }
    }
}
