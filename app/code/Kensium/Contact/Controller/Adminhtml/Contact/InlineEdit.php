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

abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * JSON Factory
     * 
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * Contact Factory
     * 
     * @var \Kensium\Contact\Model\ContactFactory
     */
    protected $contactFactory;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Kensium\Contact\Model\ContactFactory $contactFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Kensium\Contact\Model\ContactFactory $contactFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->jsonFactory    = $jsonFactory;
        $this->contactFactory = $contactFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($postItems) as $contactId) {
            /** @var \Kensium\Contact\Model\Contact $contact */
            $contact = $this->contactFactory->create()->load($contactId);
            try {
                $contactData = $postItems[$contactId];//todo: handle dates
                $contact->addData($contactData);
                $contact->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithContactId($contact, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithContactId($contact, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithContactId(
                    $contact,
                    __('Something went wrong while saving the Contact.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Contact id to error message
     *
     * @param \Kensium\Contact\Model\Contact $contact
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithContactId(\Kensium\Contact\Model\Contact $contact, $errorText)
    {
        return '[Contact ID: ' . $contact->getId() . '] ' . $errorText;
    }
}
