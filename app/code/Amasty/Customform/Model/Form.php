<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Model;

use Amasty\Customform\Api\Data\FormInterface;
use Amasty\Customform\Model\Form\ElementRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class Form extends \Magento\Framework\Model\AbstractModel implements FormInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var ElementRepository
     */
    private $elementRepository;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        ElementRepository $elementRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->jsonDecoder = $jsonDecoder;
        $this->elementRepository = $elementRepository;
        $this->jsonEncoder = $jsonEncoder;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Customform\Model\ResourceModel\Form');
        $this->setIdFieldName('form_id');
    }

    public function afterSave()
    {
        if (!$this->getData('json_saved')) {
            $formData = $this->getFormJson();
            $formData = $this->jsonDecoder->decode($formData);
            $formId = $this->getFormId();
            $existedElementsIds = [];

            foreach ($formData as &$element) {
                $existedElementsIds[] = $this->saveElement($element);
            }

            if ($formId) {
                $this->elementRepository->truncateByFormId($formId, $existedElementsIds);
            }

            $formData = $this->jsonEncoder->encode($formData);
            $this->setFormJson($formData);
            $this->setData('json_saved', true);
            $this->_getResource()->save($this);
        }

        return parent::afterSave();
    }

    private function saveElement(&$element)
    {
        if (!array_key_exists('name', $element)) {
            throw new NoSuchEntityException(
                __('Field attribute "Name" is required')
            );
        }

        $name = $element['name'];
        $name = explode('-', $name);
        $id = end($name);
        array_pop($name);
        $element['name'] = implode('-', $name);

        /* generate validation rules*/
        $element['validation'] = $this->generateValidation($element);

        $elementModel = $this->elementRepository->get($id);
        $elementModel->addData($element);

        $elementModel->setFormId((int)$this->getFormId());
        $elementModel = $this->elementRepository->save($elementModel);

        $element['name'] .= '-' . $elementModel->getElementId();

        return $elementModel->getElementId();
    }

    private function generateValidation($element)
    {
        $result = '';
        $validation = [];
        $validationFields = ['required', 'validation', 'allowed_extension', 'max_file_size'];

        foreach ($validationFields as $field) {
            if (array_key_exists($field, $element) && $element[$field]) {
                $validation[$field] = $element[$field];
            }
        }

        if ($validation) {
            $result = $this->jsonEncoder->encode($validation);
        }

        return $result;
    }

    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    public function isEnabled()
    {
        if ($this->getStatus() == self::STATUS_ENABLED) {
            return true;
        }

        return false;
    }

    /* Getters and setters implementation:*/

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return $this->_getData(FormInterface::FORM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setFormId($formId)
    {
        $this->setData(FormInterface::FORM_ID, $formId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->_getData(FormInterface::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->setData(FormInterface::CODE, $code);

        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_getData(FormInterface::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->setData(FormInterface::TITLE, $title);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessUrl()
    {
        return $this->_getData(FormInterface::SUCCESS_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setSuccessUrl($successUrl)
    {
        $this->setData(FormInterface::SUCCESS_URL, $successUrl);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_getData(FormInterface::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->setData(FormInterface::STATUS, $status);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->_getData(FormInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(FormInterface::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroup()
    {
        return $this->_getData(FormInterface::CUSTOMER_GROUP);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroup($customerGroup)
    {
        $this->setData(FormInterface::CUSTOMER_GROUP, $customerGroup);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->_getData(FormInterface::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        $this->setData(FormInterface::STORE_ID, $storeId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSendNotification()
    {
        return $this->_getData(FormInterface::SEND_NOTIFICATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setSendNotification($sendNotification)
    {
        $this->setData(FormInterface::SEND_NOTIFICATION, $sendNotification);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSendTo()
    {
        return $this->_getData(FormInterface::SEND_TO);
    }

    /**
     * {@inheritdoc}
     */
    public function setSendTo($sendTo)
    {
        $this->setData(FormInterface::SEND_TO, $sendTo);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailTemplate()
    {
        return $this->_getData(FormInterface::EMAIL_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailTemplate($emailTemplate)
    {
        $this->setData(FormInterface::EMAIL_TEMPLATE, $emailTemplate);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubmitButton()
    {
        return $this->_getData(FormInterface::SUBMIT_BUTTON);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubmitButton($submitButton)
    {
        $this->setData(FormInterface::SUBMIT_BUTTON, $submitButton);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessMessage()
    {
        return $this->_getData(FormInterface::SUCCESS_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSuccessMessage($successMessage)
    {
        $this->setData(FormInterface::SUCCESS_MESSAGE, $successMessage);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormJson()
    {
        return $this->_getData(FormInterface::FORM_JSON);
    }

    /**
     * {@inheritdoc}
     */
    public function setFormJson($json)
    {
        $this->setData(FormInterface::FORM_JSON, $json);

        return $this;
    }
}
