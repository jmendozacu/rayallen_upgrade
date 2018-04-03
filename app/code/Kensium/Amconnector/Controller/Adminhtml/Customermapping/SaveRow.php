<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Customermapping;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class SaveRow extends \Magento\Backend\App\Action
{
    protected $session;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
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
        $entityTypeId = $_GET['entityTypeId'];
        $attributeValue = $_GET['attributeValue'];
        $coulmnAttr = $_GET['coulmnAttr'];
        $session = $this->session->getData();
        $gridSession = $session['gridData'];
        if ($entityTypeId == 2) {
            $gridSession['BILLADD_' . $attributeCode][$coulmnAttr] = $attributeValue;
        } else {
            $gridSession[$attributeCode][$coulmnAttr] = $attributeValue;
        }
        $gridSession[$attributeCode][$coulmnAttr] = $attributeValue;
        $this->session->setData('gridData', $gridSession);
    }

}
