<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Productimport\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Productimport extends \Magento\Backend\App\Action
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
     * Load Productimport from request
     *
     * @param string $idFieldName
     * @return \Kensium\Productimport\Model\Productimport $model
     */
    protected function _initProductimport($idFieldName = 'productimport_id')
    {
        $productimportId = (int)$this->getRequest()->getParam($idFieldName);
        $model = $this->_objectManager->create('Kensium\Productimport\Model\Productimport');
        if ($productimportId) {
            $model->load($productimportId);
        }
        if (!$this->_registry->registry('current_productimport')) {
            $this->_registry->register('current_productimport', $model);
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
        return $this->_authorization->isAllowed('Kensium_Productimport::kensium_productimport');
    }
}
