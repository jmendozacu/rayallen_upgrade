<?php
/**
 * Kensium_Contact extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Contact
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Contact\Controller\Adminhtml\Contact;

class Save extends \Kensium\Contact\Controller\Adminhtml\Contact
{
    /**
     * Backend session
     * 
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * constructor
     * 
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Kensium\Contact\Model\ContactFactory $contactFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Kensium\Contact\Model\ContactFactory $contactFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->backendSession = $backendSession;
        parent::__construct($contactFactory, $registry, $resultRedirectFactory, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('contact');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->filterData($data);
            $contact = $this->initContact();
            $contact->setData($data);
            $this->_eventManager->dispatch(
                'kensium_contact_contact_prepare_save',
                [
                    'contact' => $contact,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $contact->save();
                $this->messageManager->addSuccess(__('The Contact has been saved.'));
                $this->backendSession->setKensiumContactContactData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'kensium_contact/*/edit',
                        [
                            'contact_id' => $contact->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('kensium_contact/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Contact.'));
            }
            $this->_getSession()->setKensiumContactContactData($data);
            $resultRedirect->setPath(
                'kensium_contact/*/edit',
                [
                    'contact_id' => $contact->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('kensium_contact/*/');
        return $resultRedirect;
    }

    /**
     * filter values
     *
     * @param array $data
     * @return array
     */
    protected function filterData($data)
    {
        if (isset($data['opt_out'])) {
            if (is_array($data['opt_out'])) {
                $data['opt_out'] = implode(',', $data['opt_out']);
            }
        }
        return $data;
    }
}
