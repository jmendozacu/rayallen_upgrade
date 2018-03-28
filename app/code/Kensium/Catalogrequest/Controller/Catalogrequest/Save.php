<?php

namespace Kensium\Catalogrequest\Controller\Catalogrequest;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Kensium\Catalogrequest\Model\CatalogrequestFactory
     */
    protected $catalogrequestFactory;

    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_helper;

    /**
     * @var CaptchaStringResolver
     */
    protected $captchaStringResolver;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Kensium\Catalogrequest\Model\CatalogrequestFactory $catalogrequestFactory
     * @param \Magento\Captcha\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Kensium\Catalogrequest\Model\CatalogrequestFactory $catalogrequestFactory,
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver
    )
    {
        $this->_helper = $helper;
        $this->catalogrequestFactory = $catalogrequestFactory;
        $this->captchaStringResolver = $captchaStringResolver;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPost();
        $data = $data->toArray();
        $formId = 'contact_us';
        $captcha = $this->_helper->getCaptcha($formId);


        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller = '';//$observer->getControllerAction();
        if (!$captcha->isCorrect($this->captchaStringResolver->resolve($this->getRequest(), $formId))) {
            $this->messageManager->addError(__('Incorrect CAPTCHA.'));
            $this->_redirect('catalogrequest');
            return;
        }


        foreach ($data as $key => $value) {
            if (!is_array($value))
                $data2[$key] = trim($value);
        }
        if (!empty($data['fname']) && !empty($data['lname']) && !empty($data['address']) && !empty($data['city']) && !empty($data['state']) && !empty($data['zip']) && !empty($data['country']) && !empty($data['email']) && !empty($data['phone'])) {
            $freecatalog = $this->catalogrequestFactory->create();

            if ($freecatalog->setData($data)->save()) {
                $this->messageManager->addSuccess("Catalog Request Submitted Successfully");
            }
        } else {
            $this->messageManager->addError("Catalog Request is not submitted");
        }
        $this->_redirect('catalogrequest');
    }
}
