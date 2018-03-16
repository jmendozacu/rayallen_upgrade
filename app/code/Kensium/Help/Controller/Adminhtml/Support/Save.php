<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Help\Controller\Adminhtml\Support;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * Class Save
 * @package Kensium\Help\Controller\Adminhtml\Support
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var
     */
    protected $data;

    /**
     * @var
     */
    protected $email;

    /**
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Kensium\Help\Helper\Data $data
     * @param \Kensium\Help\Helper\Email $email
     * @param Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Kensium\Help\Helper\Data $data,
        \Kensium\Help\Helper\Email $email,
        Context $context
    ) {
        parent::__construct($context);
        $this->authSession = $authSession;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->storeManager = $storeManager;
        $this->helper =$data;
        $this->emailHelper = $email;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($this->getRequest()->getParams()) {
            $data=$this->getRequest()->getParams();
            try {

                $type = trim($data['type']);
                $name  = trim($data['firstname']);
                $email =trim($data['email']);
                $subject =trim($data['subject']);
                $priority =trim($data['priority']);
                $message =trim($data['message']);
                $store_name = $this->storeManager->getStore()->getName();
                $acumaticaUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
                $base_url = $this->helper->getBaseUrl();
                $acumatica_instance = $this->helper->getAcumaticaVersion();
                $kems_version = '2.1';

                // it depends on the template variables
                $emailTemplateVariables = array();
                $emailTemplateVariables['type'] = $type;
                $emailTemplateVariables['name'] = $name;
                $emailTemplateVariables['email'] = $email;
                $emailTemplateVariables['subject'] = $type.' - '.$subject;
                $emailTemplateVariables['priority'] = $priority;
                $emailTemplateVariables['message'] = $message;
                $emailTemplateVariables['store_name'] = $store_name;
                $emailTemplateVariables['domain'] = $base_url;
                $emailTemplateVariables['acumatica_instance'] = $acumaticaUrl."(Version - ".$acumatica_instance.")";
                $emailTemplateVariables['amconnector_version'] = $kems_version;

                $data = $this->getRequest()->getParams();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $user = $this->authSession->getUser();
                $senderInfo = [
                    'email' =>  $user->getEmail(),
                    'name' =>  $user->getUsername()
                ];

                $recieverInfo = [
                    'email' => $email,
                    'name' => $name
                ];
                $storeId = $this->storeManager->getStore();
                $this->emailHelper->sendEmail($storeId,$emailTemplateVariables,$senderInfo,$recieverInfo);

                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                }
                $this->messageManager->addSuccess(
                    __('request send succesfully')
                );
                $this->_redirect('kensium_help/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __($e->getMessage())
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('kensium_help/support/index', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('kensium_help/*/');
    }
}
