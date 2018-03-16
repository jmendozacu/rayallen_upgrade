<?php
/**
 * Kensium_OverSize extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_OverSize
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\OverSize\Block\Adminhtml\Oversizeship\Edit\Tab;

class Oversizeship extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Kensium\OverSize\Model\Oversizeship $oversizeship */
        $oversizeship = $this->_coreRegistry->registry('kensium_oversize_oversizeship');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('oversizeship_');
        $form->setFieldNameSuffix('oversizeship');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Oversized Item Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        if ($oversizeship->getId()) {
            $fieldset->addField(
                'oversizeship_id',
                'hidden',
                ['name' => 'oversizeship_id']
            );
        }
        $fieldset->addField(
            'sku',
            'text',
            [
                'name'  => 'sku',
                'label' => __('Sku'),
                'title' => __('Sku'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'price',
            'text',
            [
                'name'  => 'price',
                'label' => __('Price'),
                'title' => __('Price'),
                'required' => true,
            ]
        );

        $oversizeshipData = $this->_session->getData('kensium_oversize_oversizeship_data', true);
        if ($oversizeshipData) {
            $oversizeship->addData($oversizeshipData);
        } else {
            if (!$oversizeship->getId()) {
                $oversizeship->addData($oversizeship->getDefaultValues());
            }
        }
        $form->addValues($oversizeship->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Oversized Item');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
