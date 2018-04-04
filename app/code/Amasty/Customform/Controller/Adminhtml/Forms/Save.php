<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Controller\Adminhtml\Forms;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Model\Layout\Update\ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var \Amasty\Customform\Model\FormFactory
     */
    private $formFactory;

    /**
     * @var \Amasty\Customform\Model\FormRepository
     */
    private $formRepository;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory,
        \Amasty\Customform\Model\FormFactory $formFactory,
        \Amasty\Customform\Model\FormRepository $formRepository
    ) {
        parent::__construct($context);
        $this->validatorFactory = $validatorFactory;
        $this->formFactory = $formFactory;
        $this->formRepository = $formRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Customform::page');
    }

    public function validate($data)
    {
        $errorNo = true;
        if (!empty($data['layout_update_xml'])) {
            /** @var $validatorCustomLayout \Magento\Framework\View\Model\Layout\Update\Validator */
            $validatorCustomLayout = $this->validatorFactory->create();
            if (!empty($data['layout_update_xml']) && !$validatorCustomLayout->isValid($data['layout_update_xml'])) {
                $errorNo = false;
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->messageManager->addErrorMessage($message);
            }
        }
        return $errorNo;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            /** @var \Amasty\Customform\Model\Form $model */
            $model = $this->formFactory->create();

            $id = $this->getRequest()->getParam('form_id');
            if ($id) {
                $model = $this->formRepository->get($id);
            }

            $this->prepareData($data);
            $model->setData($data);
/*
            if ($useConfig = $this->getRequest()->getPost('use_config')) {
                foreach ($useConfig as $attributeCode) {
                    $model->setData($attributeCode, null);
                }
            }*/

//            if (!$this->validate($data)) {
//                return $resultRedirect->setPath('*/*/edit', ['form_id' => $model->getId(), '_current' => true]);
//            }

            try {

                $this->validateFormCode($model);
                $this->_getSession()->setFormData($data);
                $this->formRepository->save($model);
                $this->_getSession()->setFormData(false);
                $this->messageManager->addSuccessMessage(__('You saved this form.'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['form_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the page.'));
            }

            return $resultRedirect->setPath('*/*/edit', ['form_id' => $this->getRequest()->getParam('form_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param \Amasty\Customform\Model\Form $formModel
     * @throws LocalizedException
     */
    protected function validateFormCode(\Amasty\Customform\Model\Form $formModel)
    {
        $exist = false;
        if ($formModel->getCode()) {
            $model = $this->formRepository->getByFormCode($formModel->getCode(), $formModel->getFormId());

            if ($model && $model->getFormId()) {

                if ($formModel->getFormId() && ($model->getFormId() != $formModel->getFormId())) {
                    $exist = true;
                }

                if (!$formModel->getFormId()) {
                    $exist = true;
                }
            }

            if ($exist) {
                throw new LocalizedException(__('Entity with code %1 already exist.', $formModel->getCode()));
            }

        } else {
            throw new LocalizedException(__('Form code not found'));
        }
    }

    private function prepareData(&$data)
    {
        if (!empty($data['customer_group'])) {
            $data['customer_group'] =
                implode(',', $data['customer_group']);
        } else {
            $data['customer_group'] = '';
        }

        if (!empty($data['store_id'])) {
            $data['store_id'] =
                implode(',', $data['store_id']);
        } else {
            $data['store_id'] = '0';
        }


    }
}
