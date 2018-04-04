<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Model\Form;

use Amasty\Customform\Api\Data\Form\ElementInterface;
use Amasty\Customform\Model\Form\Element\OptionRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class Element extends \Magento\Framework\Model\AbstractModel implements ElementInterface
{
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        OptionRepository $optionRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->optionRepository = $optionRepository;
    }

    public function afterSave()
    {
        if (!$this->getData('option_saved') && $this->getValues()) {
            $options = $this->getValues();
            foreach ($options as &$option) {
                $this->saveOption($option);
            }
            $this->setData('option_saved', true);
        }

        return parent::afterSave();
    }

    private function saveOption($option)
    {
        if (!array_key_exists('value', $option)) {
            throw new NoSuchEntityException(
                __('Option attribute "Value" is required')
            );
        }
        $value = $option['value'];
        $option['name'] = $option['label'];
        $elementId = (int)$this->getElementId();
        $optionModel = $this->optionRepository->getByElementAndValue($elementId, $value);
        $optionModel->addData($option);

        $optionModel->setElementId($elementId);
        $this->optionRepository->save($optionModel);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Customform\Model\ResourceModel\Form\Element');
        $this->setIdFieldName('element_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getElementId()
    {
        return $this->_getData(ElementInterface::ELEMENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setElementId($elementId)
    {
        $this->setData(ElementInterface::ELEMENT_ID, $elementId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return $this->_getData(ElementInterface::FORM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setFormId($formId)
    {
        $this->setData(ElementInterface::FORM_ID, $formId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_getData(ElementInterface::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->setData(ElementInterface::NAME, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_getData(ElementInterface::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->setData(ElementInterface::TYPE, $type);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_getData(ElementInterface::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->setData(ElementInterface::TITLE, $title);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidation()
    {
        return $this->_getData(ElementInterface::VALIDATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setValidation($validation)
    {
        $this->setData(ElementInterface::VALIDATION, $validation);

        return $this;
    }
}
