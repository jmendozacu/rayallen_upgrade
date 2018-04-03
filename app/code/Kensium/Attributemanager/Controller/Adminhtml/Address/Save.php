<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Attributemanager\Controller\Adminhtml\Address;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Save
 * @package Kensium\Attributemanager\Controller\Adminhtml\Address
 */
class Save extends Edit
{
    public function execute(){

        if ($data = $this->_request->getParams()) {
            if(empty($data['frontend_label'][1])){
                if($this->_request->getParam('attribute_id')){
                    $this->messageManager->addError('Label may not be empty');
                    $this->_redirect($this->getUrl('attributemanager/address/edit', ['type' => $this->_type,'attribute_id' => $this->_request->getParam('attribute_id')]));
                    return;
                }else{
                    $this->messageManager->addError('Label may not be empty');
                    $this->_redirect($this->getUrl('attributemanager/address/edit', ['type' => $this->_type,'attribute_id' => 0]));
                    return;
                }
            }
            $model = $this->_objectManager->create('\Magento\Eav\Model\Entity\Attribute');
            $model->setData($data);
            if( $this->getRequest()->getParam('attribute_id') > 0 ) {

                $model->setId($this->getRequest()->getParam('attribute_id'));
            }

            try {

                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(time())
                        ->setUpdateTime(time());
                } else {
                    $model->setUpdateTime(time());
                }
                foreach ( $model['frontend_label'] as $key => $val ) {
                    $onearr[$key - 1] = $val;
                }
                $model['frontend_label'] = $onearr;
                $model['entity_type_id'] = 2;
                $model['is_user_defined'] = 1;
                $model['is_visible'] = 1;
                $model->save();
                $id=$model->getId();
                if(isset($data['customer_form'])) {
                    foreach($data['customer_form'] as $key1 => $val1){
                        $usedInForms[] = $key1;
                    }
                    if ($this->_block == 'customer' || $this->_block == 'address') {
                        $attribute = $this->attributeFactory->create()->load($id);
                            $this->eavConfig->getAttribute($this->_type, $attribute->getAttributeCode())
                            ->setData('used_in_forms', $usedInForms)
                            ->save();
                        }

                }

                $this->messageManager->addSuccess('Item was successfully saved');
                $this->adminSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect($this->getUrl('*/*/edit', array('type' => $this->_type,'attribute_id' => $id)));
                    $this->messageManager->addSuccess('Item was successfully saved');
                    return;
                }

                $this->_redirect($this->getUrl('attributemanager/address/index'));
                return;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->adminSession->setFormData($data);
                $this->_redirect($this->getUrl('attributemanager/address/edit'), array('type'=>$this->getRequest()->getParam('type'),'attribute_id' => $this->getRequest()->getParam('attribute_id')));
                return;
            }
        }
        $this->messageManager->addError('Unable to find item to save');
        $this->_redirect($this->getUrl('attributemanager/address/index'));
    }
}
