<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Block\Adminhtml\Form\Edit\Tab;

class Content extends AbstractTab
{
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

        $fieldset = $form->addFieldset('content_fieldset', ['legend' => __('Form Content')]);

        $fieldset->addField(
            'submit_button',
            'textarea',
            [
                'name' => 'submit_button',
                'label' => __('Submit Button'),
                'title' => __('Submit Button'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'success_message',
            'textarea',
            [
                'name' => 'success_message',
                'label' => __('Success Message'),
                'title' => __('Success Message')
            ]
        );

        $data = $model->getData();
        if (empty($data['success_message'])) {
            $data['success_message'] = __('Thanks for contacting us. Your request was saved successfully.');
        }
        if (empty($data['submit_button'])) {
            $data['submit_button'] = __('Submit');
        }

        $form->setValues($data);
        $this->setForm($form);

        parent::_prepareForm();
        return $this;
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Form Content');
    }
}
