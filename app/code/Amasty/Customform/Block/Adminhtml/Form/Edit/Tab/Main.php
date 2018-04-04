<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Block\Adminhtml\Form\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Element\Dependence;

/**
 * Form page edit form main tab
 */
class Main extends AbstractTab
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    private $systemStore;

    /**
     * @var \Amasty\Customform\Model\Config\Source\CustomerGroup
     */
    private $groupSourceFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\Email\Template
     */
    private $emailTemplateSource;

    /**
     * @var \Magento\Config\Model\Config\Source\YesnoFactory
     */
    private $yesNoFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Amasty\Customform\Model\Config\Source\CustomerGroup $groupSourceFactory,
        \Magento\Config\Model\Config\Source\Email\Template $emailTemplateSource,
        \Magento\Config\Model\Config\Source\YesnoFactory $yesNoFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->systemStore = $systemStore;
        $this->groupSourceFactory = $groupSourceFactory;
        $this->emailTemplateSource = $emailTemplateSource;
        $this->yesNoFactory = $yesNoFactory;
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Amasty\Customform\Model\Form $model */
        $model = $this->_coreRegistry->registry('amasty_customform_form');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('form_');

        $fieldSet = $form->addFieldset('base_fieldset', ['legend' => __('Form Information')]);

        if ($model->getId()) {
            $fieldSet->addField('form_id', 'hidden', ['name' => 'form_id']);
        }

        $fieldSet->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
            ]
        );
        $fieldSet->addField(
            'code',
            'text',
            [
                'name' => 'code',
                'label' => __('Code'),
                'title' => __('Code'),
                'required' => true,
            ]
        );

        $fieldSet->addField(
            'success_url',
            'text',
            [
                'name' => 'success_url',
                'label' => __('Success Url'),
                'title' => __('Success Url'),
                'note' => __('Leave empty to redirect on homepage. Use "/" to redirect on previous page.'),
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldSet->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'store_id[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true),
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldSet->addField(
                'store_id',
                'hidden',
                ['name' => 'store_id[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $groupValues = $this->groupSourceFactory->toOptionArray();
        $preselectedGroupValues =  array_column($groupValues, 'value');
        $fieldSet->addField('customer_group', 'multiselect', [
            'name'      => 'customer_group[]',
            'label'     => ('Customer Groups'),
            'title'     => ('Customer Groups'),
            'values'    => $groupValues,
        ]);

        $fieldSet->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'values' => $model->getAvailableStatuses()
            ]
        );

        $yesNo = $this->yesNoFactory->create()->toOptionArray();
        $sendNotification = $fieldSet->addField(
            'send_notification',
            'select',
            [
                'label' => __('Send notification to email'),
                'title' => __('Send notification to email'),
                'name' => 'send_notification',
                'values'    => $yesNo,
            ]
        );

        $sendTo = $fieldSet->addField(
            'send_to',
            'text',
            [
                'name' => 'send_to',
                'label' => __('Recipients email'),
                'title' => __('Recipients email'),
                'comment' => __('Comma separated Emails, no spaces.')
            ]
        );
        
        $emailTemplates = $this->emailTemplateSource->setPath('amasty/customform/email/template')->toOptionArray();
        $emailTemplate = $fieldSet->addField(
            'email_template',
            'select',
            [
                'label' => __('Email Template'),
                'title' => __('Email Template'),
                'name' => 'email_template',
                'values'    => $emailTemplates
            ]
        );

        /* default values */
        if (!$model->getId()) {
            $model->setData('status', \Amasty\Customform\Model\Form::STATUS_ENABLED);
            $model->setData('customer_group', $preselectedGroupValues);
            $model->setData('store_id', '0');
        }

        /** @var Dependence $dependence */
        $dependence = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
        $this->addDependencies($dependence, $sendNotification, $sendTo);
        $this->addDependencies($dependence, $sendNotification, $emailTemplate);
        $this->setChild('form_after', $dependence);

        $form->setValues($model->getData());
        $this->setForm($form);

        parent::_prepareForm();
        return $this;
    }

    /**
     * define field dependencies
     */
    protected function addDependencies(Dependence $dependence, $parent, $depend)
    {
        $dependence->addFieldMap($parent->getHtmlId(), $parent->getName())
            ->addFieldMap($depend->getHtmlId(), $depend->getName())
            ->addFieldDependence(
                $depend->getName(),
                $parent->getName(),
                '1'
            );
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Form Information');
    }
}
