<?php
/**
 * Kensium_Catalogrequest extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Catalogrequest
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Catalogrequest\Controller\Adminhtml\Catalogrequest;

class Delete extends \Kensium\Catalogrequest\Controller\Adminhtml\Catalogrequest
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('catalogrequest_id');
        if ($id) {
            $fname = "";
            try {
                /** @var \Kensium\Catalogrequest\Model\Catalogrequest $catalogrequest */
                $catalogrequest = $this->catalogrequestFactory->create();
                $catalogrequest->load($id);
                $fname = $catalogrequest->getFname();
                $catalogrequest->delete();
                $this->messageManager->addSuccess(__('The Catalogrequest has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_kensium_catalogrequest_catalogrequest_on_delete',
                    ['fname' => $fname, 'status' => 'success']
                );
                $resultRedirect->setPath('kensium_catalogrequest/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_kensium_catalogrequest_catalogrequest_on_delete',
                    ['fname' => $fname, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('kensium_catalogrequest/*/edit', ['catalogrequest_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Catalogrequest to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('kensium_catalogrequest/*/');
        return $resultRedirect;
    }
}
