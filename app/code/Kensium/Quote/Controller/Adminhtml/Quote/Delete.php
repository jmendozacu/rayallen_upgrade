<?php
/**
 * Kensium_Quote extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Quote
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Quote\Controller\Adminhtml\Quote;

class Delete extends \Kensium\Quote\Controller\Adminhtml\Quote
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('quote_id');
        if ($id) {
            $fname = "";
            try {
                /** @var \Kensium\Quote\Model\Quote $quote */
                $quote = $this->quoteFactory->create();
                $quote->load($id);
                $fname = $quote->getFname();
                $quote->delete();
                $this->messageManager->addSuccess(__('The Quote has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_kensium_quote_quote_on_delete',
                    ['fname' => $fname, 'status' => 'success']
                );
                $resultRedirect->setPath('kensium_quote/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_kensium_quote_quote_on_delete',
                    ['fname' => $fname, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('kensium_quote/*/edit', ['quote_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Quote to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('kensium_quote/*/');
        return $resultRedirect;
    }
}
