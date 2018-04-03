<?php
/**
 * Copyright © 2015 Kensium. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Kensium\Attributemanager\Block\Adminhtml\Customer\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;



class Main extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Eav\Model\EntityFactory
     */
    protected $entityFactory;

    /**
     * @var
     */
    protected $registry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Eav\Model\EntityFactory $entityFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, []);
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_coreRegistry = $registry;
        $this->entityFactory = $entityFactory;
        $this->setup = $setup;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Customer Attribute Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Customer Attribute Information');
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
        $model = $this->_coreRegistry->registry('attributemanager_data');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('attributes_');



            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Attribute Details')]);
        
        if ($model->getId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }
        $this->_addElementTypes($fieldset);


        $yesno = array(
            array(
                'value' => 0,
                'label' => 'No'
            ),
            array(
                'value' => 1,
                'label' => 'Yes'
            ));

       /* $fieldset->addField('attribute_code', 'text', array(
            'name'  => 'attribute_code',
            'label' => 'Attribute Code',
            'title' => 'Attribute Code',
            'note'  => 'For internal use. Must be unique with no spaces',
            'class' => 'validate-code',
            'required' => true,
        ));*/

        $validateClass = sprintf(
            'validate-code validate-length maximum-length-%d',
            \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
        );

        $fieldset->addField(
            'attribute_code',
            'text',
            [
                'name' => 'attribute_code',
                'label' => __('Attribute Code'),
                'title' => __('Attribute Code'),
                'note' => __(
                    'This is used internally. Make sure you don\'t use spaces or more than %1 symbols.',
                    \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
                ),
                'class' => $validateClass
            ]
        );

        $scopes = array(
            \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE =>'Store View',
            \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE =>'Website',
            \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL =>'Global',
        );

        if ($model->getAttributeCode() == 'status' || $model->getAttributeCode() == 'tax_class_id') {
        unset($scopes[\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE]);
        }

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => 'Scope',
            'title' => 'Scope',
            'note'  => 'Declare attribute value saving scope',
            'values'=> $scopes
        ));

        $inputTypes = array(
            array(
                'value' => 'text',
                'label' => 'Text Field'
            ),
            array(
                'value' => 'textarea',
                'label' => 'Text Area'
            ),
            array(
                'value' => 'date',
                'label' => 'Date'
            ),
            array(
                'value' => 'boolean',
                'label' => 'Yes/No'
            ),
            array(
                'value' => 'multiselect',
                'label' => 'Multiple Select'
            ),
            array(
                'value' => 'select',
                'label' => 'Dropdown'
            ),
        );

        if($this->getRequest ()->getParam ( 'type' )==="catalog_category"){
            $inputTypes[]=   array(
                'value' => 'image',
                'label' => 'Image'

            );
        }

        $response = new \Magento\Framework\DataObject();
        $response->setTypes(array());

        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $inputTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
        $this->_coreRegistry->register('attribute_type_hidden_fields', $_hiddenFields);
        $this->_coreRegistry->register('attribute_type_disabled_types', $_disabledTypes);

        $fieldset->addField('frontend_input', 'select', array(
            'name' => 'frontend_input',
            'label' => 'Catalog Input Type for Store Owner',
            'title' => 'Catalog Input Type for Store Owner',
            'value' => 'text',
            'values'=> $inputTypes
        ));

        $fieldset->addField('entity_type_id', 'hidden', array(
            'name' => 'entity_type_id',
            'value' => $this->entityFactory->create()->setType($this->getRequest ()->getParam ( 'type' ))->getTypeId()
        ));

        $fieldset->addField('is_user_defined', 'hidden', array(
            'name' => 'is_user_defined',
            'value' => 1
        ));

        $fieldset->addField('attribute_set_id', 'hidden', array(
            'name' => 'attribute_set_id',
            'value' => $this->entityFactory->create()->setType($this->getRequest ()->getParam ( 'type' ))->getTypeId()
        ));

        $fieldset->addField('attribute_group_id', 'hidden', array(
            'name' => 'attribute_group_id',
            'value' => $this->entityFactory->create()->setType($this->getRequest ()->getParam ( 'type' ))->getTypeId()
        ));

        /*******************************************************/
        $fieldset->addField('is_unique', 'select', array(
            'name' => 'is_unique',
            'label' => 'Unique Value',
            'title' => 'Unique Value (not shared with other products)',
            'note'  => 'Not shared with other products',
            'values' => $yesno,
        ));

        $fieldset->addField('is_required', 'select', array(
            'name' => 'is_required',
            'label' => 'Values Required',
            'title' => 'Values Required',
            'values' => $yesno,
        ));

        $fieldset->addField('is_visible', 'select', array(
            'name' => 'is_visible',
            'label' => 'Is Visible',
            'title' => 'Is Visible',
            'values' => $yesno,
        ));

        $fieldset->addField('frontend_class', 'select', array(
            'name'  => 'frontend_class',
            'label' => 'Input Validation for Store Owner',
            'title' => 'Input Validation for Store Owner',
            'values'=>  array(
                array(
                    'value' => '',
                    'label' => 'None'
                ),
                array(
                    'value' => 'validate-number',
                    'label' => 'Decimal Number'
                ),
                array(
                    'value' => 'validate-digits',
                    'label' => 'Integer Number'
                ),
                array(
                    'value' => 'validate-email',
                    'label' => 'Email'
                ),
                array(
                    'value' => 'validate-url',
                    'label' => 'Url'
                ),
                array(
                    'value' => 'validate-alpha',
                    'label' => 'Letters'
                ),
                array(
                    'value' => 'validate-alphanum',
                    'label' => 'Letters(a-zA-Z) or Numbers(0-9)'
                ),
            )
        ));

        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset',
            array('legend'=>'Frontend Properties'));

        if($model->getId())
        {
            $this->setup->startSetup();
            $write = $this->setup->getConnection('core_write');
            $readresult=$write->query("SELECT * from ".$this->setup->getTable('customer_form_attribute')." WHERE attribute_id=".$model->getId());
            while ($row = $readresult->fetch() ) {
                $customerForm[$row['form_code']] = 'checked';
            }
        }
        $fieldset->addField('adminhtml_customer', 'checkbox', array(
            'name' => 'customer_form[adminhtml_customer]',
            'label' => 'Adminhtml Customer',
            'value' => 'adminhtml_customer',
            'checked' => isset($customerForm['adminhtml_customer'])?$customerForm['adminhtml_customer']:'',
        ));
        $fieldset->addField('customer_account_create', 'checkbox', array(
            'name' => 'customer_form[customer_account_create]',
            'label' => 'Customer Account Create',
            'value' => 'customer_account_create',
            'checked' => isset($customerForm['customer_account_create'])?$customerForm['customer_account_create']:'',
        ));
        $fieldset->addField('customer_address_edit', 'checkbox', array(
            'name' => 'customer_form[customer_address_edit]',
            'label' => 'Customer Address Edit',
            'value' => 'customer_address_edit',
            'checked' => isset($customerForm['customer_address_edit'])?$customerForm['customer_address_edit']:'',
        ));
        $fieldset->addField('checkout_register', 'checkbox', array(
            'name' => 'customer_form[checkout_register]',
            'label' => 'Checkout Register',
            'value' => 'checkout_register',
            'checked' => isset($customerForm['checkout_register'])?$customerForm['checkout_register']:'',
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => 'Order',
            'title' => 'Order in form',
            'note' => 'order of attribute in form edit/create. Leave blank for form bottom.',
            'class' => 'validate-digits',
            'value' => $model->getAttributeSetInfo()
        ));

        if ($model->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);

            if (isset($disableAttributeFields[$model->getAttributeCode()])) {
                foreach ($disableAttributeFields[$model->getAttributeCode()] as $field) {
                    $form->getElement($field)->setDisabled(1);
                }
            }
        }
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return ['apply' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Apply'];
    }
}
