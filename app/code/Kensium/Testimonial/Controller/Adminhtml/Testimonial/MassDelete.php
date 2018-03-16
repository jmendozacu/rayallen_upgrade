<?php
 
namespace Kensium\Testimonial\Controller\Adminhtml\Testimonial;
 
class MassDelete extends \Kensium\Testimonial\Controller\Adminhtml\Testimonial
{
   /**
    * @return void
    */
   public function execute()
   {
       $ids = $this->getRequest()->getParam('testimonial');
       if (!is_array($ids)) {
           $this->messageManager->addError(__('Please select a testimonial(s).'));
       } else {
           try {
               foreach ($ids as $id) {
                   $model = $this->_objectManager->create('Kensium\Testimonial\Model\Testimonial')->load($id);
                   $model->delete();
               }

               $this->messageManager->addSuccess(__('You deleted %1 record(s).', count($ids)));
           } catch (\Magento\Framework\Exception\LocalizedException $e) {
               $this->messageManager->addError($e->getMessage());
           } catch (\Exception $e) {
               $this->messageManager->addError(
                   __('Something went wrong while mass-deleting testimonials. Please review the action log and try again.')
               );
               $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
               return;
           }
       }
 
        $this->_redirect('kensium_testimonial/*/');
   }
}
