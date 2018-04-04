<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Model\Form\Element;

use Amasty\Customform\Api\Data\Form\Element\OptionInterface;

class Option extends \Magento\Framework\Model\AbstractModel implements OptionInterface
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Customform\Model\ResourceModel\Form\Element\Option');
        $this->setIdFieldName('option_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionId()
    {
        return $this->_getData(OptionInterface::OPTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptionId($optionId)
    {
        $this->setData(OptionInterface::OPTION_ID, $optionId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getElementId()
    {
        return $this->_getData(OptionInterface::ELEMENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setElementId($elementId)
    {
        $this->setData(OptionInterface::ELEMENT_ID, $elementId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionName()
    {
        return $this->_getData(OptionInterface::OPTION_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptionName($optionName)
    {
        $this->setData(OptionInterface::OPTION_NAME, $optionName);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionValue()
    {
        return $this->_getData(OptionInterface::OPTION_VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptionValue($optionValue)
    {
        $this->setData(OptionInterface::OPTION_VALUE, $optionValue);

        return $this;
    }
}
