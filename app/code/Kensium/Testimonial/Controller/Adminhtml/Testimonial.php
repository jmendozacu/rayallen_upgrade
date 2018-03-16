<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Testimonial\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Testimonial extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $registry)
    {
        $this->_registry = $registry;
        parent::__construct($context);
    }

    /**
     * Load Testimonial from request
     *
     * @param string $idFieldName
     * @return \Kensium\Testimonial\Model\Testimonial $model
     */
    protected function _initTestimonial($idFieldName = 'testimonial_id')
    {
        $testimonialId = (int)$this->getRequest()->getParam($idFieldName);
        $model = $this->_objectManager->create('Kensium\Testimonial\Model\Testimonial');
        if ($testimonialId) {
            $model->load($testimonialId);
        }
        if (!$this->_registry->registry('current_testimonial')) {
            $this->_registry->register('current_testimonial', $model);
        }
        return $model;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Kensium_Testimonial::kensium_testimonial');
    }
}
