<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Attributemanager\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Catalog\Model\Entity\Attribute;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Class Front
 * @package Kensium\Attributemanager\Block\Adminhtml\Category\Edit\Tab
 */
class Front extends Generic
{
    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * @var PropertyLocker
     */
    private $propertyLocker;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNo
     * @param PropertyLocker $propertyLocker
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        PropertyLocker $propertyLocker,
        $data = []
    ) {
        $this->_yesNo = $yesNo;
        $this->propertyLocker = $propertyLocker;
        parent::__construct($context, $registry, $formFactory, []);
    }

    /**
     * {@inheritdoc}
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('attributemanager_data');

        $form = new \Magento\Framework\DataObject(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>'Frontend Properties'));

        $yesno = array(
            array(
                'value' => 0,
                'label' => 'No'
            ),
            array(
                'value' => 1,
                'label' => 'Yes'
            ));


        $fieldset->addField('is_searchable', 'select', array(
            'name' => 'is_searchable',
            'label' => 'Use in quick search',
            'title' => 'Use in quick search',
            'values' => $yesno,
        ));

        $fieldset->addField('is_visible_in_advanced_search', 'select', array(
            'name' => 'is_visible_in_advanced_search',
            'label' => 'Use in advanced search',
            'title' => 'Use in advanced search',
            'values' => $yesno,
        ));

        $fieldset->addField('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => 'Comparable on Front-end',
            'title' => 'Comparable on Front-end',
            'values' => $yesno,
        ));


        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => "Use In Layered Navigation<br/>(Can be used only with catalog input type 'Dropdown')",
            'title' => 'Can be used only with catalog input type Dropdown',
            'values' => array(
                array('value' => '0', 'label' => 'No'),
                array('value' => '1', 'label' => 'Filterable (with results)'),
                array('value' => '2', 'label' => 'Filterable (no results)'),
            ),
        ));

//        if ($model->getIsUserDefined() || !$model->getId()) {
        $fieldset->addField('is_visible_on_front', 'select', array(
            'name' => 'is_visible_on_front',
            'label' => 'Visible on Catalog Pages on Front-end',
            'title' => 'Visible on Catalog Pages on Front-end',
            'values' => $yesno,
        ));
//        }


        $this->setForm($form);
        $form->setValues($model->getData());
        $this->propertyLocker->lock($form);
        return parent::_prepareForm();
    }
}
