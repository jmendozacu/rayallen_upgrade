<?php

namespace Kensium\Testimonial\Controller\Adminhtml\Testimonial;

class Delete extends \Kensium\Testimonial\Controller\Adminhtml\Testimonial
{
    /**
     * @return void
     */
    public function execute()
    {
        // check if we know what should be deleted
        $testimonialId = $this->getRequest()->getParam('id');
        if ($testimonialId) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Kensium\Testimonial\Model\Testimonial');
                $model->load($testimonialId);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('You deleted the Testimonial.'));
                // go to grid
                $this->_redirect('kensium_testimonial/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while deleting testimonial data. Please review the action log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                // save data in session
                $this->_getSession()->setFormData($this->getRequest()->getParams());
                // redirect to edit form
                $this->_redirect('adminhtml/*/edit', ['id' => $testimonialId]);
                return;
            }
        }
        // display error message
        $this->messageManager->addError(__('We cannot find a testimonial to delete.'));

        $this->_redirect('kensium_testimonial/*/');
    }
}
