<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Attributemanager\Block\Adminhtml\Address\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class System extends Generic
{
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('entity_attribute');
        

            $form = new \Magento\Framework\DataObject();
            $fieldset = $form->addFieldset('base_fieldset', array('legend'=>'System Properties'));

            if ($model->getAttributeId()) {
                $fieldset->addField('attribute_id', 'hidden', array(
                    'name' => 'attribute_id',
                ));
            }

            $yesno = array(
                array(
                    'value' => 0,
                    'label' => 'No'
                ),
                array(
                    'value' => 1,
                    'label' => 'Yes'
                ));

            /*$fieldset->addField('attribute_model', 'text', array(
                'name' => 'attribute_model',
                'label' => 'Attribute Model'),
                'title' => 'Attribute Model'),
            ));
    
            $fieldset->addField('backend_model', 'text', array(
                'name' => 'backend_model',
                'label' => 'Backend Model'),
                'title' => 'Backend Model'),
            ));*/

            $fieldset->addField('backend_type', 'select', array(
                'name' => 'backend_type',
                'label' => 'Data Type for Saving in Database',
                'title' => 'Data Type for Saving in Database',
                'options' => array(
                    'text'      => 'Text',
                    'varchar'   => 'Varchar',
                    'static'    => 'Static',
                    'datetime'  => 'Datetime',
                    'decimal'   => 'Decimal',
                    'int'       => 'Integer',
                ),
            ));

            $fieldset->addField('is_global', 'select', array(
                'name'  => 'is_global',
                'label' => 'Globally Editable',
                'title' => 'Globally Editable',
                'values'=> $yesno,
            ));
            $form->setValues($model->getData());

            if ($model->getAttributeId()) {
                $form->getElement('backend_type')->setDisabled(1);
                if ($model->getIsGlobal()) {
                    #$form->getElement('is_global')->setDisabled(1);
                }
            } else {
            }        

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
