<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Api\Data\Form\Element;

interface OptionInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const OPTION_ID = 'option_id';
    const ELEMENT_ID = 'element_id';
    const OPTION_NAME = 'name';
    const OPTION_VALUE = 'value';
    /**#@-*/

    /**
     * @return int
     */
    public function getOptionId();

    /**
     * @param int $optionId
     *
     * @return \Amasty\Customform\Api\Data\Form\Element\OptionInterface
     */
    public function setOptionId($optionId);

    /**
     * @return int
     */
    public function getElementId();

    /**
     * @param int $elementId
     *
     * @return \Amasty\Customform\Api\Data\Form\Element\OptionInterface
     */
    public function setElementId($elementId);

    /**
     * @return string
     */
    public function getOptionName();

    /**
     * @param string $optionName
     *
     * @return \Amasty\Customform\Api\Data\Form\Element\OptionInterface
     */
    public function setOptionName($optionName);

    /**
     * @return string
     */
    public function getOptionValue();

    /**
     * @param string $optionValue
     *
     * @return \Amasty\Customform\Api\Data\Form\Element\OptionInterface
     */
    public function setOptionValue($optionValue);
}
