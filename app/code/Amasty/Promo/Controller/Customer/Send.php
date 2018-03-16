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
class Send extends \Magento\Framework\App\Action\Action
{
    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

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
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->_customerSession = $customerSession;
        $this->customerFactory  = $customerFactory;
        parent::__construct($context);
    }

    /**
     * Post user question
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $this->inlineTranslation->suspend();

        $_customer = $this->_customerSession->getCustomer();
        if($_customer->getId()) {
            try {

                $websiteId = $this->storeManager->getWebsite()->getWebsiteId();

                // Instantiate object (this is the most important part)
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($websiteId);
                $customer->load($_customer->getId());
                $post = array('name' => $customer->getName(), 'email' => $customer->getEmail(), 'guid' => "" /*$customer->getGigyaUid()*/);
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($post);

                $sender = [
                    'name' => $this->_escaper->escapeHtml($post['name']),
                    'email' => $this->_escaper->escapeHtml($post['email']),
                    'guid' => $this->_escaper->escapeHtml($post['guid']),
                ];
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier('send_redeem_template')// this code we have mentioned in the email_templates.xml
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($sender)
                    ->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                    ->getTransport();

                $transport->sendMessage();;
                $this->inlineTranslation->resume();
                $this->messageManager->addSuccess(
                    __('We got your request. We\'ll respond to you very soon.')
                );
                $this->_redirect('amasty_promo/customer/info');
                return;
            } catch (\Exception $e) {
                $this->inlineTranslation->resume();
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.' . $e->getMessage())
                );
                $this->_redirect('amasty_promo/customer/info');
                return;
            }
        }else {
            $this->_redirect('customer/account/login');
        }
    }
}
