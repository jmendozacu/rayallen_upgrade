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

class Delete extends \Kensium\Contact\Controller\Adminhtml\Contact
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('contact_id');
        if ($id) {
            $fname = "";
            try {
                /** @var \Kensium\Contact\Model\Contact $contact */
                $contact = $this->contactFactory->create();
                $contact->load($id);
                $fname = $contact->getFname();
                $contact->delete();
                $this->messageManager->addSuccess(__('The Contact has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_kensium_contact_contact_on_delete',
                    ['fname' => $fname, 'status' => 'success']
                );
                $resultRedirect->setPath('kensium_contact/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_kensium_contact_contact_on_delete',
                    ['fname' => $fname, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('kensium_contact/*/edit', ['contact_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Contact to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('kensium_contact/*/');
        return $resultRedirect;
    }
}
