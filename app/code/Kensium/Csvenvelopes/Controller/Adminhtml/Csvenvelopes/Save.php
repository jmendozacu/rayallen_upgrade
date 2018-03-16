<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Csvenvelopes\Controller\Adminhtml\Csvenvelopes;

class Save extends \Kensium\Csvenvelopes\Controller\Adminhtml\Csvenvelopes
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_objectManager->create('Kensium\Csvenvelopes\Model\Csvenvelopes');
                $data = $this->getRequest()->getPostValue();
                $model->setData($data);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();
                $this->messageManager->addSuccess(__('You saved the item.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('kensium_csvenvelopes/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('kensium_csvenvelopes/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('kensium_csvenvelopes/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('kensium_csvenvelopes/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('kensium_csvenvelopes/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('kensium_csvenvelopes/*/');
    }
}
