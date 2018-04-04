<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Block\Widget\Form\Renderer;

use Magento\Backend\Block\Template\Context;

class Creator extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
{
    protected $_template = 'Amasty_Customform::widget/form/renderer/fieldset.phtml';

    private $elementTypeConnection = [
      'textinput'   => 'input',
      'textarea'    => 'input',
     // 'hidden'      => 'input',
      'number'      => 'input',
      'date'        => 'select',
      'time'        => 'select',
      'datetime'    => 'select',
      'file'        => 'select',
      //'DateRange'   => 'select',
      'dropdown'    => 'options',
      'listbox'     => 'options',
      'checkbox'    => 'options',
      'checkboxtwo' => 'options',
      'radio'       => 'options',
      'radiotwo'    => 'options',
      'text'        => 'other',
      'hone'        => 'other',
      'htwo'        => 'other',
      'hthree'      => 'other'
    ];

    private $types = [
        'input'     => 'Input',
        'select'    => 'Select',
        'options'   => 'Options',
        'other'     => 'Other',
    ];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Amasty\Customform\Helper\Messages
     */
    private $messagesHelper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    public function __construct(
        Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Customform\Helper\Messages $messagesHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->objectManager = $objectManager;
        $this->messagesHelper = $messagesHelper;
        $this->jsonEncoder = $jsonEncoder;
    }

    public function getElementTypes()
    {
        return $this->types;
    }

    public function getElementsByType($type)
    {
        $array = array_keys($this->elementTypeConnection, $type);

        return $array;
    }

    public function getTabsHtml()
    {
        $i = 0;
        $html = '';

        $types = $this->getElementTypes();
        foreach ($types as $type => $typeName) {
            $html .=
                '<li><a href="#amelement-tabs-' . (++$i) . '">' .
                    $typeName .
                '</a></li>';
        }

        return $html;
    }

    public function getFrmbFieldsJson()
    {
        $result = [];
        foreach ($this->elementTypeConnection as $key => $type) {
            $element = $this->_createElement($key);
            if ($element) {
                $data = $element->getElementData($key, $type);
                $result[] = $data;
            }
        }
        return $this->jsonEncoder->encode($result);
    }

    public function getMessagesJson()
    {
        return $this->messagesHelper->getMessages();
    }

    public function getTypeFieldsJson()
    {
        $result = [];
        foreach ($this->types as $key => $value) {
            $result[]= [
                'type' => $key,
                'title' => $value
            ];
        }
        return $this->jsonEncoder->encode($result);
    }

    protected function _createElement($name)
    {
        $className = 'Amasty\Customform\Block\Widget\Form\Element\\'  . ucfirst($name);
        if (class_exists($className)) {
            $element = $this->objectManager->create($className);
            return $element;
        }

        return false;
    }
}
