<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Backend\Model\Session;
use Kensium\Amconnector\Helper\Licensecheck;

class ExportDomainLicense extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $config;

    protected $inlineTranslation;

    protected $_transportBuilder;

    protected $cacheTypeList;

    protected $backendSession;

    protected $licenseHelper;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Config\Model\ResourceModel\Config $config
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $_transportBuilder
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param Licensecheck $licenseHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Config\Model\ResourceModel\Config $config,
        StateInterface $inlineTranslation,
        TransportBuilder    $_transportBuilder,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        Licensecheck $licenseHelper
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_messageManager = $context->getMessageManager();
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $_transportBuilder;
        $this->config = $config;
        $this->cacheTypeList= $cacheTypeList;
        $this->backendSession = $context->getSession();
        $this->licenseHelper = $licenseHelper;
        parent::__construct($context);
    }


    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $isAllValuesSelected = $this->backendSession->getIsAllValuesSelected();
        if ($isAllValuesSelected) {
            $fileName = 'LicenseRequest.txt';
            $content = $this->licenseHelper->getNewLicenseDetails();
            $handle = fopen($fileName, 'w');
            fwrite($handle, $content);
            $this->licenseHelper->downloadLicense($fileName);
        } else {
            $this->_messageManager->addError('Please Select all required Fields');
            $this->_redirect('adminhtml/system_config/edit/section/license/');
        }
        $this->backendSession->unsIsAllValuesSelected();
    }
}
