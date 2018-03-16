<?php

/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Attributemanager\Model;

/**
 * Class Attributemanager
 * @package Kensium\Attributemanager\Model
 */
class Attributemanager extends \Magento\Eav\Model\Entity\Attribute
{
    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        if ( $this->getFrontendInput()=="image"){
            $this->setBackendModel('Magento\Catalog\Model\Category\Attribute\Backend\Image');
            $this->setBackendType('varchar');
        }

        if ( $this->getFrontendInput()=="date"){
            $this->setBackendModel('Magento\Eav\Model\Entity\Attribute\Backend\Datetime');
            $this->setBackendType('datetime');
        }

        if ( $this->getFrontendInput()=="textarea" ){

            $this->setBackendType('text');
        }

        if ( $this->getFrontendInput()=="text" ){

            $this->setBackendType('varchar');
        }

        if ( ($this->getFrontendInput()=="multiselect" || $this->getFrontendInput()=="select") ){
            $this->setData('source_model', 'Magento\Eav\Model\Entity\Attribute\Source\Table');
            $this->setBackendType('varchar');
        }

        if ($this->getFrontendInput()=="boolean"){
            $this->setFrontendInput("select");
            $this->setBackendType('int');
            $this->setData('source_model', 'Magento\Eav\Model\Entity\Attribute\Source\Boolean');
        }
        return parent::beforeSave();
    }
}