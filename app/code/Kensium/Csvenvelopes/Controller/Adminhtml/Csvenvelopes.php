<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Csvenvelopes\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Csvenvelopes extends \Magento\Backend\App\Action
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
     * Load Csvenvelopes from request
     *
     * @param string $idFieldName
     * @return \Kensium\Csvenvelopes\Model\Csvenvelopes $model
     */
    protected function _initCsvenvelopes($idFieldName = 'csvenvelopes_id')
    {
        $csvenvelopesId = (int)$this->getRequest()->getParam($idFieldName);
        $model = $this->_objectManager->create('Kensium\Csvenvelopes\Model\Csvenvelopes');
        if ($csvenvelopesId) {
            $model->load($csvenvelopesId);
        }
        if (!$this->_registry->registry('current_csvenvelopes')) {
            $this->_registry->register('current_csvenvelopes', $model);
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
        return $this->_authorization->isAllowed('Kensium_Csvenvelopes::kensium_csvenvelopes');
    }
}
