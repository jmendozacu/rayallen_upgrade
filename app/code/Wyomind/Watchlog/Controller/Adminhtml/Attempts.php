<?php
/*
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\Watchlog\Controller\Adminhtml;

abstract class Attempts extends \Magento\Backend\App\Action
{

    protected $_resultPageFactory = null;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    /**
     * Does the menu is allowed
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Wyomind_Watchlog::attempts');
    }
    
    protected function _validatePostData($data)
    {
        $errorNo = true;
        if (!empty($data['layout_update_xml']) || !empty($data['custom_layout_update_xml'])) {
            /** @var $validatorCustomLayout \Magento\Core\Model\Layout\Update\Validator */
            $validatorCustomLayout = $this->_objectManager->create('Magento\Core\Model\Layout\Update\Validator');
            if (!empty($data['layout_update_xml']) && !$validatorCustomLayout->isValid($data['layout_update_xml'])) {
                $errorNo = false;
            }
            if (!empty($data['custom_layout_update_xml']) && !$validatorCustomLayout->isValid(
                $data['custom_layout_update_xml']
            )
            ) {
                $errorNo = false;
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->_messageManager->addError($message);
            }
        }
        return $errorNo;
    }
    
    abstract public function execute();
}
