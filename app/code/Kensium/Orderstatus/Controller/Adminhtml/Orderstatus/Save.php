<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Orderstatus\Controller\Adminhtml\Orderstatus;

class Save extends \Kensium\Orderstatus\Controller\Adminhtml\Orderstatus
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_objectManager->create('Kensium\Orderstatus\Model\Orderstatus');
                $data = $this->getRequest()->getPostValue();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $orderstatus_id = $this->getRequest()->getParam('orderstatus_id');
                if ($orderstatus_id) {
                    $model->load($orderstatus_id);
                    if ($orderstatus_id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
                $statusCollection = $objectManager->create('Kensium\Orderstatus\Model\ResourceModel\Orderstatus\Collection');
                /** Apply filters here */
                $statusCollection->load();

                foreach($statusCollection as $newOrderStatus) {
                    if ($orderstatus_id) {
                        if($orderstatus_id != $newOrderStatus->getOrderstatusId()){
                            $existingStatus[] = strtolower($newOrderStatus->getStatusLabel());
                        }
                    }else{
                        $existingStatus[] = strtolower($newOrderStatus->getStatusLabel());
                    }
                }

                $newStatus = strtolower($data['status_label']);

                if (in_array($newStatus, $existingStatus)) {
                    $this->messageManager->addError(
                        __('Order status already exist.')
                    );

                    if (!empty($orderstatus_id)) {
                        $this->_redirect('kensium_orderstatus/*/edit', ['orderstatus_id' => $orderstatus_id]);
                    } else {
                        $this->_redirect('kensium_orderstatus/*/new');
                    }
                }else{
                    if(empty($orderstatus_id) || 1){
                        $date = date('Y-m-d H:i:s');
                        $data['created_time'] = $date;// \Magento\Framework\Stdlib\DateTime\TimezoneInterface::formatDate();
                    }

                    $model->setData($data);
                    $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                    $session->setPageData($model->getData());
                    $model->save();
                    $this->messageManager->addSuccess(__('You saved the item.'));
                    $session->setPageData(false);

                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('kensium_orderstatus/*/edit', ['id' => $model->getId()]);
                        return;
                    }
                    $this->_redirect('kensium_orderstatus/*/');
                    return;
                }

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $orderstatus_id = (int)$this->getRequest()->getParam('orderstatus_id');
                if (!empty($orderstatus_id)) {
                    $this->_redirect('kensium_orderstatus/*/edit', ['orderstatus_id' => $orderstatus_id]);
                } else {
                    $this->_redirect('kensium_orderstatus/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('kensium_orderstatus/*/edit', ['orderstatus_id' => $this->getRequest()->getParam('orderstatus_id')]);
                return;
            }
        }
        $this->_redirect('kensium_orderstatus/*/');
    }
}
