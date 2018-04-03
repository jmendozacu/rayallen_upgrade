<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;

class SaveRow extends Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Context $context
     * @param Session $session
     */
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $attributeCode = $_GET['attribute'];
        $attributeValue = $_GET['attributeValue'];
        $columnAttr = $_GET['coulmnAttr'];
        $session = $this->session->getData();
        $gridSession = $session['gridData'];
        $gridSession[$attributeCode][$columnAttr] = $attributeValue;
        $this->session->setData('gridData', $gridSession);
    }

}
