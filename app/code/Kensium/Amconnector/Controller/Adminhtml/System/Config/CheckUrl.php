<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;

class CheckUrl extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Kensium\Amconnector\Helper\Sync
     */
    protected $syncHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $config;

    /**
     * @var \Kensium\Amconnector\Model\Connection
     */
    protected $amconnectorConnection;

    /**
     * @var
     */
    protected $clientHelper;

    /**
     * @var
     */
    protected $xmlHelper;
	/**
     * @var
     */    
	protected $urlHelper;
	/**
     * @var
     */
    protected $baseDirPath;
	/**
     * @var
     */
    protected $syncResourceModel;
    /**
     * @var
     */
    protected $_messageManager;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Kensium\Amconnector\Helper\Sync $syncHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Config\Model\ResourceModel\Config $config
     * @param \Kensium\Amconnector\Model\Connection $amconnectorConnection
     * @param \Kensium\Amconnector\Helper\Client $clientHelper
     * @param \Kensium\Amconnector\Helper\Xml $xmlHelper
     * @param \Kensium\Amconnector\Helper\Url $urlHelper
     * @param \Magento\Framework\App\Filesystem\DirectoryList $baseDirPath
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Config\Model\ResourceModel\Config $config,
        \Kensium\Amconnector\Model\Connection $amconnectorConnection,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Magento\Framework\App\Filesystem\DirectoryList $baseDirPath,
        \Kensium\Amconnector\Model\ResourceModel\Sync   $syncResourceModel
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->syncHelper = $syncHelper;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->config = $config;
        $this->amconnectorConnection = $amconnectorConnection;
        $this->clientHelper = $clientHelper;
        $this->xmlHelper = $xmlHelper;
        $this->urlHelper = $urlHelper;
        $this->baseDirPath = $baseDirPath;
        $this->syncResourceModel = $syncResourceModel;
        $this->_messageManager = $context->getMessageManager();
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    protected function _check()
    {
        $serverUrl = $this->getRequest()->getParam('serverUrl');
        if (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $serverUrl)) {
            $headers = @get_headers($serverUrl);
            if (strpos($headers[0], '404') === false) {
                $validUrl = true;
            } else {
                $validUrl = false;
            }
        } else {
            $validUrl = false;
        }

        if ($validUrl)
            $checkUrl = '1';
        elseif ($serverUrl == '')
            $checkUrl = '0';
        else
            $checkUrl = '2';

        return $checkUrl;
    }

    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        echo $result = $this->_check();
    }
}
