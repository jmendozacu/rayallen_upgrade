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
namespace Kensium\Contact\Controller\Adminhtml;

abstract class Contact extends \Magento\Backend\App\Action
{
    /**
     * Contact Factory
     * 
     * @var \Kensium\Contact\Model\ContactFactory
     */
    protected $contactFactory;

    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     * 
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * constructor
     * 
     * @param \Kensium\Contact\Model\ContactFactory $contactFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Kensium\Contact\Model\ContactFactory $contactFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->contactFactory        = $contactFactory;
        $this->coreRegistry          = $coreRegistry;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Init Contact
     *
     * @return \Kensium\Contact\Model\Contact
     */
    protected function initContact()
    {
        $contactId  = (int) $this->getRequest()->getParam('contact_id');
        /** @var \Kensium\Contact\Model\Contact $contact */
        $contact    = $this->contactFactory->create();
        if ($contactId) {
            $contact->load($contactId);
        }
        $this->coreRegistry->register('kensium_contact_contact', $contact);
        return $contact;
    }
}
