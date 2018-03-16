<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Csvenvelopes\Block\Adminhtml\Csvenvelopes\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Main
 * @package Kensium\Csvenvelopes\Block\Adminhtml\Csvenvelopes\Edit\Tab
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, []);
        $this->_wysiwygConfig = $wysiwygConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Csv Envelopes Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Csv Envelopes Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_csvenvelopes');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('csvenvelopes_');


        if ($model->getId()) {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Csv Envelopes Information '."(".$model->getEnvcode().")")]);
            $fieldset->addField('csvenvelopes_id', 'hidden', ['name' => 'csvenvelopes_id']);
            $type = 'label';
        }
        else{
            $type = 'text';
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Csv Envelopes Information ')]);
        }

        $fieldset->addField('envcode',$type, array(
            'label'     => __('Envelope code'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'envcode',
            'index'     => 'envcode',
        ));

        $fieldset->addField('enventity', 'select', array(
            'label'     => __('Envelope entity'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'enventity',
            'index'    => 'enventity',
            'options' => array(
                '' => 'Please Select',
                'Customer' => 'Customer',
                'Product' => 'Product',
                'Order' => 'Order',
                'Item Sales Category' => 'Item Sales Category',
                'Basic Web Development' => 'Basic Web Development',
                'Count' => 'Count',
                'SIE' => 'SIE',
            ),
        ));

        $fieldset->addField('envtype', 'select', array(
            'label'     => __('Envelope type'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'envtype',
            'index'    => 'envtype',
            'options' => array(
                '' => 'Please Select',
                'WebService' => 'WebService',
                'EndPoint' => 'EndPoint',
            ),
        ));

        $fieldset->addField('envversion', 'text', array(
            'label'     => __('Envelope version'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'envversion',
            'index'    => 'envversion',
        ));

        $fieldset->addField('acumaticaversion', 'text', array(
            'label'     => __('Acumatica version'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'acumaticaversion',
            'index'    => 'acumaticaversion',
        ));

        $fieldset->addField('envname', 'text', array(
            'label'     => __('Envelope name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'envname',
            'index'    => 'envname',
        ));

        $fieldset->addField('methodname', 'text', array(
            'label'     => __('Method name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'methodname',
            'index'    => 'methodname',
        ));

        $fieldset->addField('envelope', 'textarea', array(
            'label'     => __('Envelope'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'envelope',
            'index'    => 'envelope',
            'style'      => 'width:600px;',
        ));
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
