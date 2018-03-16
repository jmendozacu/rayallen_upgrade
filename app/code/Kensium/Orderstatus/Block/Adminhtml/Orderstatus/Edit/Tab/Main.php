<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

// @codingStandardsIgnoreFile

namespace Kensium\Orderstatus\Block\Adminhtml\Orderstatus\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;



class Main extends Generic implements TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Status Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Status Information');
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
        $model = $this->_coreRegistry->registry('current_kensium_orderstatus_orderstatus');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('orderstatus_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Status Information')]);
        if ($model->getId()) {
            $fieldset->addField('orderstatus_id', 'hidden', ['name' => 'orderstatus_id']);
        }
        $fieldset->addField(
            'status_label',
            'text',
            ['name' => 'status_label', 'label' => __('Status Label'), 'title' => __('Status Label'), 'required' => true]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => ['0' => __('Enabled'), '1' => __('Disabled'),]
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
