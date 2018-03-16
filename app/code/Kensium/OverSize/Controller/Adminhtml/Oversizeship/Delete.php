<?php
/**
 * Kensium_OverSize extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_OverSize
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\OverSize\Controller\Adminhtml\Oversizeship;

class Delete extends \Kensium\OverSize\Controller\Adminhtml\Oversizeship
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('oversizeship_id');
        if ($id) {
            $sku = "";
            try {
                /** @var \Kensium\OverSize\Model\Oversizeship $oversizeship */
                $oversizeship = $this->oversizeshipFactory->create();
                $oversizeship->load($id);
                $sku = $oversizeship->getSku();
                $oversizeship->delete();
                $this->messageManager->addSuccess(__('The Over Size Ship has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_kensium_oversize_oversizeship_on_delete',
                    ['sku' => $sku, 'status' => 'success']
                );
                $resultRedirect->setPath('kensium_oversize/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_kensium_oversize_oversizeship_on_delete',
                    ['sku' => $sku, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('kensium_oversize/*/edit', ['oversizeship_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Over Size Ship to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('kensium_oversize/*/');
        return $resultRedirect;
    }
}
