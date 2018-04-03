<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Attributemanager\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class ExportCsv
 * @package Kensium\Attributemanager\Controller\Adminhtml\Customer
 */
class ExportCsv extends \Magento\Backend\App\Action
{
    /**
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
            \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
            PageFactory $resultPageFactory,
            Context $context
        ){
            $this->_fileFactory = $fileFactory;
            $this->_resultPageFactory = $resultPageFactory;
            parent::__construct($context);
        }

    public function execute()
    {
        $fileName = 'customer_attributes.csv';

        /** @var \\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()->createBlock('Kensium\Attributemanager\Block\Adminhtml\Customer\Grid')->getCsv();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
