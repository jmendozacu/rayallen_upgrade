<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Testimonial\Block\Adminhtml\Testimonial\Edit;

/**
 * Class \Magento\Testimonial\Block\Adminhtml\Testimonial\Edit\Form
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('kensium_testimonial/testimonial/save'),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
