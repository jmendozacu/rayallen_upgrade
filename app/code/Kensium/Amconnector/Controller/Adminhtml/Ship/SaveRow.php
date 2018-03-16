<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Ship;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class SaveRow extends \Magento\Backend\App\Action
{
    protected $session;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     */
    public function __construct(
        Context $context

    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $attributeCode = $_GET['attribute'];
        $attributeValue = $_GET['attributeValue'];
        $coulmnAttr = $_GET['coulmnAttr'];
        $session = $this->session->getData();
        $gridSession = $session['gridData'];
        $gridSession[$attributeCode][$coulmnAttr] = $attributeValue;
        $this->session->setData('gridData', $gridSession);
    }

}
