<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Controller\Form;

use Amasty\Customform\Model\Answer;
use Amasty\Customform\Model\Form;
use Amasty\Customform\Model\Form\Element;
use Amasty\Customform\Model\ResourceModel\Form\Element\Option\CollectionFactory as OptionCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\MediaStorage\Model\File\Uploader;
use Amasty\Customform\Helper\Data;
use Magento\Framework\App\Filesystem\DirectoryList;
use Amasty\Customform\Api\Data\Form\ElementInterface;
use Amasty\Customform\Api\Data\Form\Element\OptionInterface;
use Magento\Framework\Filesystem\Driver\File;

class Submit extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Amasty\Customform\Helper\Data
     */
    private $helper;

    /**
     * @var \Amasty\Customform\Model\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Amasty\Customform\Model\FormRepository
     */
    private $formRepository;

    /**
     * @var \Amasty\Customform\Model\AnswerFactory
     */
    private $answerFactory;

    /**
     * @var \Amasty\Customform\Model\AnswerRepository
     */
    private $answerRepository;

    /**
     * @var \Amasty\Customform\Model\ResourceModel\Form\Element\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var OptionCollectionFactory
     */
    private $optionCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $fileDriver;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Amasty\Customform\Model\FormRepository $formRepository,
        \Amasty\Customform\Model\AnswerRepository $answerRepository,
        \Amasty\Customform\Model\AnswerFactory $answerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Customform\Model\Template\TransportBuilder $transportBuilder,
        \Amasty\Customform\Model\ResourceModel\Form\Element\CollectionFactory $collectionFactory,
        OptionCollectionFactory $optionCollectionFactory,
        \Amasty\Customform\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        File $fileDriver
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->formRepository = $formRepository;
        $this->answerFactory = $answerFactory;
        $this->answerRepository = $answerRepository;
        $this->collectionFactory = $collectionFactory;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->filesystem = $filesystem;
        $this->fileDriver = $fileDriver;
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $formId = (int)$this->getRequest()->getParam('form_id');
        if ($this->getRequest()->isPost() && $formId) {
            try {
                if (!$this->formKeyValidator->validate($this->getRequest())) {
                    throw new LocalizedException(
                        __('Form key is not valid. Please try to reload the page.')
                    );
                }

                /** @var Form $formModel */
                $formModel = $this->formRepository->get($formId);

                /** @var  Answer $model */
                $model = $this->answerFactory->create();
                $answerData = $this->generateAnswerData($formModel);
                $model->addData($answerData);
                $this->answerRepository->save($model);

                $message = $formModel->getSuccessMessage();
                if (!$message) {
                    $message = __('Thanks for contacting us. Your request was saved successfully.');
                }
                $this->messageManager->addSuccessMessage($message);
                $this->sendAdminNotification($formModel, $model);

                $url = $formModel->getSuccessUrl();
                if ($url === '/') {
                    $url = $this->_redirect->getRefererUrl();
                }

                $resultRedirect->setPath($url);
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath($this->_redirect->getRefererUrl());
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        $this->messageManager->addErrorMessage(
            __('Sorry. There is a problem with Your Form Request. Please try again or use Contact Us link in the menu.')
        );
        return $resultRedirect->setPath($this->_redirect->getRefererUrl());
    }

    private function generateAnswerData($formModel)
    {
        $json = $this->generateJson($formModel);

        return [
            'form_id'  => $formModel->getId(),
            'store_id' => $this->storeManager->getStore()->getId(),
            'ip'       => $this->helper->getCurrentIp(),
            'customer_id' => (int)$this->helper->getCurrentCustomerId(),
            'response_json' => $json
        ];
    }

    private function generateJson($formModel)
    {
        $formJson = $formModel->getFormJson();
        $fields = $this->helper->decode($formJson);
        $data = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $value = $this->getValidValue($field, $name);
            if ($value) {
                $type = $field['type'];
                switch ($type) {
                    case 'checkbox':
                    case 'checkboxtwo':
                    case 'dropdown':
                    case 'listbox':
                    case 'radio':
                    case 'radiotwo':
                        $elementId  = explode('-', $name)[1];
                        $options = $this->optionCollectionFactory->create()
                            ->getFieldsByElementId($elementId);
                        $tmpValue = [];
                        /** @var \Amasty\Customform\Model\Form\Element\Option $option */
                        foreach ($options as $option) {
                            if (is_array($value) && in_array($option->getValue(), $value)) {
                                $tmpValue[] = $option->getName();
                            }
                        }

                        $data[$name]['value'] = $tmpValue ? implode(', ', $tmpValue) : $value;
                        break;
                    default:
                        $value = $this->helper->escapeHtml($value);
                        $data[$name]['value'] = $value;
                }

                $data[$name]['label'] = $field['label'];
                $data[$name]['type'] = $type;
            }
        }

        return $this->helper->encode($data);
    }

    private function getValidValue($field, $name)
    {
        $result = $this->getRequest()->getParam($name, null);
        $fileValidation = [];
        $validation = $this->getRow($field, ElementInterface::VALIDATION);
        $fieldType = $this->getRow($field, ElementInterface::TYPE);
        $isFile = strcmp($fieldType, 'file') === 0;

        if ($validation && $validation !== 'None') {
            $validation = $this->helper->decode($validation);
            $valueNotExist = (!$isFile && !$result)
                || ($isFile && !array_key_exists($name, $this->getRequest()->getFiles()->toArray()));

            if (!array_key_exists(ElementInterface::REQUIRED, $validation)
                && $valueNotExist
            ) {
                return $result;
            }

            foreach ($validation as $key => $item) {
                switch ($key) {
                    case 'required':
                        if ($result === null && ($fieldType != 'file')) {
                            throw new LocalizedException(__('Please enter a %1.', $field->getTitle()));
                        }
                        break;
                    case 'validation':
                        if ($item == 'validate-email') {
                            if (!\Zend_Validate::is($result, 'EmailAddress')) {
                                throw new LocalizedException(__('Please enter a valid email address.'));
                            }
                        }
                        break;
                    case 'allowed_extension':
                    case 'max_file_size':
                        $fileValidation[$key] = $item;
                        break;
                }
            }
        }

        if ($isFile) {
            $result = $this->helper->saveFileField($name, $fileValidation);
        }

        return $result;
    }

    private function sendAdminNotification(Form $formModel, Answer $model)
    {
        $emailTo = trim($formModel->getSendTo());
        if (!$emailTo) {
            $emailTo = trim($this->helper->getModuleConfig('email/recipient_email'));
        }

        if ($emailTo && $this->helper->getModuleConfig('email/enabled') && $formModel->getSendNotification()) {
            $sender = $this->helper->getModuleConfig('email/sender_email_identity');
            $template = $formModel->getEmailTemplate();
            if (!$template) {
                $template = $this->helper->getModuleConfig('email/template');
            }

            $model->setFormTitle($formModel->getTitle());
            $customerData = $this->helper->getCustomerName($model->getCustomerId());

            try {
                $store = $this->storeManager->getStore();
                $data =  [
                    'website_name'  => $store->getWebsite()->getName(),
                    'group_name'    => $store->getGroup()->getName(),
                    'store_name'    => $store->getName(),
                    'response'      => $model,
                    'link'          => $this->helper->getAnswerViewUrl($model->getAnswerId()),
                    'submit_fields' => $this->getSubmitFields($model),
                    'customer_name' => $customerData['customer_name'],
                    'customer_link' => $customerData['customer_link'],
                ];

                if (strpos($emailTo, ',') !== false) {
                    $emailTo = explode(',', $emailTo);
                }

                $transport = $this->transportBuilder->setTemplateIdentifier(
                    $template
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getId()]
                )->setTemplateVars(
                    $data
                )->setFrom(
                    $sender
                )->addTo(
                    $emailTo
                )->getTransport();

                $transport->sendMessage();
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(__('Unable to send email.'));
            }
        }
    }

    private function getSubmitFields(Answer &$model)
    {
        $html = '<table cellpadding="7">';
        $formData = $model->getResponseJson();
        if ($formData) {
            $fields = $this->helper->decode($formData);

            foreach ($fields as $field) {
                $value = $this->getRow($field, OptionInterface::OPTION_VALUE);
                $fieldType = $this->getRow($field, ElementInterface::TYPE);
                if ($fieldType == 'file') {
                    $filePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath()
                        . Data::MEDIA_PATH . Uploader::getCorrectFileName($value);
                    $this->transportBuilder->addAttachment(
                        $this->fileDriver->fileGetContents($filePath),
                        $value
                    );
                }

                $html .= '<tr>' .
                    '<td style="width: 50%;">' . $field['label'] . '</td>' .
                    '<td>' . $value . '</td>' .
                    '</tr>';
            }
        }
        $html .= '</table>';

        return $html;
    }

    private function getRow($field, $type)
    {
        return isset($field[$type]) ? $field[$type] : null;
    }
}
