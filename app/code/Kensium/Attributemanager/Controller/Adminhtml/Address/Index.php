<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Attributemanager\Controller\Adminhtml\Address;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Kensium\Attributemanager\Controller\Adminhtml\Address
 */
class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Eav\Model\EntityFactory
     */
    protected $entityFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Eav\Model\EntityFactory $entityFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->entityFactory = $entityFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Customer Address Attributes'), __('Customer Address Attributes
	'));
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Address Attributes
	'));
        return $resultPage;
    }
}
