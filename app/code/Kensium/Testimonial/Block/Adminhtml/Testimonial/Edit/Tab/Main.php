<?php
/**
 * Copyright © 2015 Kensium. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Kensium\Testimonial\Block\Adminhtml\Testimonial\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;



class Main extends Generic implements TabInterface
{
    /**
     * Banner config
     *
     * @var \Magento\Banner\Model\Config
     */
    protected $_testimonialConfig;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;



    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Banner\Model\Config $testimonialConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Banner\Model\Config $testimonialConfig,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_testimonialConfig = $testimonialConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Testimonial Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Testimonial Information');
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
        $model = $this->_coreRegistry->registry('current_testimonial');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('testimonial_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Testimonial Information')]);
        if ($model->getId()) {
            $fieldset->addField('testimonial_id', 'hidden', ['name' => 'testimonial_id']);
        }
        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );

        $fieldset->addField(
            'testimonial',
            'editor',
            [
                'name' => 'testimonial',
                'label' => __('Testimonial'),
                'title' => __('Testimonial'),
                'style' => 'height:36em',
                'required' => true,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => ['0' => __('Pending'),'1' => __('Approved'), '2' => __('Rejected')]
            ]
        );

        $options = [];
        foreach ($this->_storeManager->getWebsites() as $website) {
            $options[] = ['label' => $website->getName(), 'value' => $website->getId()];
        }

        $fieldset->addField(
            'store_id',
            'select',
            [
                'name'  => 'store_id',
                'label' => __('Websites'),
                'title' => __('Websites'),
                'required' => true,
                'values' => $options
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
