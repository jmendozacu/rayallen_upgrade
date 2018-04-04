<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Api\Data\Form;

interface ElementInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ELEMENT_ID = 'element_id';
    const FORM_ID = 'form_id';
    const NAME = 'name';
    const TYPE = 'type';
    const TITLE = 'title';
    const VALIDATION = 'validation';
    const REQUIRED = 'required';
    /**#@-*/

    /**
     * @return int
     */
    public function getElementId();

    /**
     * @param int $elementId
     *
     * @return \Amasty\Customform\Api\Data\Form\ElementInterface
     */
    public function setElementId($elementId);

    /**
     * @return int
     */
    public function getFormId();

    /**
     * @param int $formId
     *
     * @return \Amasty\Customform\Api\Data\Form\ElementInterface
     */
    public function setFormId($formId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \Amasty\Customform\Api\Data\Form\ElementInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return \Amasty\Customform\Api\Data\Form\ElementInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return \Amasty\Customform\Api\Data\Form\ElementInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getValidation();

    /**
     * @param string $validation
     *
     * @return \Amasty\Customform\Api\Data\Form\ElementInterface
     */
    public function setValidation($validation);
}
