<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Log;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

abstract class AbstractLog extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    protected $clientHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\Io\File $file,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->resultPageFactory = $resultPageFactory;
        $this->date = $date;
        $this->clientHelper = $clientHelper;
        $this->backendHelper = $context->getHelper();
        $this->storeManager = $storeManager;
        $this->file = $file;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function createLogFile()
    {

        $jobCode = $this->getRequest()->getParam('code', NULL);
        $syncId = $this->getRequest()->getParam('syncId', NULL);
        $logPath = $this->clientHelper->getLogPath();

        $tobeInsertedId = $this->getRequest()->getParam('tobeInsertedId', NULL);
        $logFileName = $jobCode."_". $syncId . "_" . $tobeInsertedId . ".log";
        $filePath  = BP.$logPath.$jobCode."/".$this->date->date('Y-m-d');
        $this->file->checkAndCreateFolder($filePath);

        $htacessFile = BP.$logPath.$jobCode."/.htaccess";
        if (!file_exists($htacessFile)) {
            $content = 'Order allow,deny' . "\n" . "Allow from all" . "\n". "SetEnv no-gzip 1" . "\n";
            file_put_contents($htacessFile, $content . "\n");
        }

        $logViewFileName = BP.$logPath.$jobCode."/".$this->date->date('Y-m-d') ."/" . $logFileName;
        $fp = fopen($logViewFileName, 'a+');

        $data['logUrl'] = str_replace("index.php/", "", $this->storeManager->getStore()->getBaseUrl() .ltrim($logPath,'/').$jobCode."/".$this->date->date('Y-m-d')."/".$logFileName);
        return $data['logUrl'];

    }

    public function createBaiscLogFile($fileName)
    {   
        $logFileName = $fileName.".log";
        $logPath = $this->clientHelper->getLogPath();
        $filePath  = BP.$logPath.$fileName."/".$this->date->date('Y-m-d');
        $this->file->checkAndCreateFolder($filePath);

        $htacessFile = BP.$logPath.$fileName."/.htaccess";
        if (!file_exists($htacessFile)) {
            $content = 'Order allow,deny' . "\n" . "Allow from all" . "\n". "SetEnv no-gzip 1" . "\n";
            file_put_contents($htacessFile, $content . "\n");
        }

        $logViewFileName = BP.$logPath.$fileName."/".$this->date->date('Y-m-d') ."/" . $logFileName;
        $fp = fopen($logViewFileName, 'w');

        $data['logUrl'] = str_replace("index.php/", "", $this->storeManager->getStore()->getBaseUrl() .ltrim($logPath,'/').$fileName."/".$this->date->date('Y-m-d')."/".$logFileName);
        return $data['logUrl'];
    }
    
    public function getLogPath()
    {
        $jobCode = $this->getRequest()->getParam('code', NULL);
        $syncId = $this->getRequest()->getParam('syncId', NULL);
        $logPath = $this->clientHelper->getLogPath();
        $tobeInsertedId = $this->getRequest()->getParam('tobeInsertedId', NULL);
        $logFileName = $jobCode."_". $syncId . "_" . $tobeInsertedId . ".log";
        $logViewFileName = BP.$logPath.$jobCode."/".$this->date->date('Y-m-d') ."/" . $logFileName;
        return $logViewFileName;
    }
    public function createBaiscLogPath($fileName)
    {   
        $logFileName = $fileName.".log";
        $logPath = $this->clientHelper->getLogPath();
        $logViewFileName = BP.$logPath.$fileName."/".$this->date->date('Y-m-d') ."/" . $logFileName;
        return $logViewFileName;
    }

}
