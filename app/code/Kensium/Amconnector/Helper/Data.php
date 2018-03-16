<?php
/**
 *
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface as Logger;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Stdlib\DateTime\Timezone;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var  \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected  $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $syncResourceModel;

    /**
     * @var \Magento\Framework\Filesystem\File
     */
    protected $file;

    protected $timezone;
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    protected $clientHelper;

    /**
     * @param Context $context
     * @param DateTime $date
     * @param Timezone $timezone
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param Client $clientHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     */
    public function __construct(
        Context $context,
        DateTime $date,
        Timezone $timezone,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
    )
    {
        parent::__construct($context);
        $this->date = $date;
        $this->timezone = $timezone;
        $this->file = $file;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->syncResourceModel=$syncResourceModel;
        $this->clientHelper = $clientHelper;
        $this->scopeConfigInterface = $context->getScopeConfig();
    }


    /**
     * Check for duplicate job
     * @param $entity
     * @return boolean
     *
     */
    public function chkforDuplicateJob($entity)
    {

        $syncDirectory = BP . "/amconnector/lock/";
        $lockFile = $syncDirectory . $entity . ".lock";
        if (file_exists($lockFile)) {
            # check if it's stale
            $lockingPID = trim(file_get_contents($lockFile));

            # Get all active PIDs.
            $pids = explode("\n", trim(`ps -e | awk '{print $1}'`));

            # If PID is still active, return true
            if (in_array($lockingPID, $pids)) {
                return true;
            } else {
                # Lock-file is stale, so kill it.  Then move on to re-creating it.
                // echo "Removing stale lock file.\n";
                unlink($lockFile);
                return false;
            }
        }
    }


    /**
     * @param $syncId
     * @param $jobCode
     * @param $tobeInsertedId
     * @return string
     * @throws \Exception
     */
    public function syncLogFile($syncId,$jobCode,$tobeInsertedId)
    {
        $logPath = $this->clientHelper->getLogPath();

        if(empty($tobeInsertedId))
            $tobeInsertedId = $this->syncResourceModel->beforeCheckConnectionFlag($syncId, $jobCode);
        $logFileName = $jobCode."_". $syncId . "_" . $tobeInsertedId . ".log";
        $filePath  = BP.$logPath.$jobCode."/".$this->date->date('Y-m-d');
        $this->file->checkAndCreateFolder($filePath);
        //@chmod(BP."/var/", DriverInterface::WRITEABLE_DIRECTORY_MODE);

        $htacessFile = BP.$logPath.$jobCode."/.htaccess";
        if (!file_exists($htacessFile)) {
            $content = 'Order allow,deny' . "\n" . "Allow from all" . "\n";
            file_put_contents($htacessFile, $content . "\n");
        }

        $logViewFileName = BP.$logPath.$jobCode."/".$this->date->date('Y-m-d') ."/" . $logFileName;
        if(!($this->file->fileExists($logViewFileName))) {
            umask(002);
            $fp = fopen($logViewFileName, 'a');
            //$this->file->chmod($logViewFileName,'777');
        }
        return  $logViewFileName;
    }

    /**
     * @param $syncName
     * @param $errorMsg
     * To send error log Email
     */
    public function errorLogEmail($syncName, $errorMsg)
    {
        $sendLogReport = $this->scopeConfigInterface->getValue('amconnectorcommon/log_setting/log_report');
        $emailReceipient = $this->scopeConfigInterface->getValue('amconnectorcommon/log_setting/log_email_recipient');
        $explodedemailReceipient = explode(",", $emailReceipient);

        if($sendLogReport == 1){

            $templateOptions = array('area' =>  \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 'store' => $this->storeManager->getStore()->getId());
            $templateVars = array(
                'store' => $this->storeManager->getStore(),
                'sync_name' => $syncName,
                'message'   => $errorMsg
            );
            $from = array(
                'email' => "support@kensium.com",
                'name' => "Acumart:Connector Support"
            );
            $this->inlineTranslation->suspend();

            if (is_array($explodedemailReceipient)) {
                for ($i = 0; $i < count($explodedemailReceipient); $i++) {
                    $to = $explodedemailReceipient[$i];
                    $transport = $this->_transportBuilder->setTemplateIdentifier('error_log_email_template')
                        ->setTemplateOptions($templateOptions)
                        ->setTemplateVars($templateVars)
                        ->setFrom($from)
                        ->addTo($to)
                        ->getTransport();
                    $transport->sendMessage();
                }
            }
            $this->inlineTranslation->resume();
        }
    }
    public function writeLogToFile($logViewFileName,$txt){
        $txt = $this->timezone->date(time())->format('Y-m-d H:i:s')." : ".$txt;
        //@chmod($logViewFileName, DriverInterface::WRITEABLE_FILE_MODE);
        @file_put_contents($logViewFileName, $txt . PHP_EOL,FILE_APPEND);
    }

    /**
     * Get Log file path from configuration
     */
    public function getLogPath()
    {
        /**
         * Path will take from common configuration if available
         * If not available then it takes static "/var/logs/" from root magento folder
         */
        $path = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/log_setting/default_logpath',NULL,NULL);
        if($path == ''){
            return $path = "/var/log/";
        }else{
            $path = rtrim($path, '/') . '/';
            return '/'.ltrim($path,'/');
        }
    }


    public function getConfigParameters($storeId = NULL) {
        $scope = 'stores';
        if($storeId == 0){
           $scope = 'default';
        }
        $userName = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/userName',$scope,$storeId);
        $password = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/password',$scope,$storeId);
        $company = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/companyName',$scope,$storeId);
        $configParameters = array('name'=> $userName,'password'=> $password,'company'=> $company,'local'=>'en-gb');
        return $configParameters;
    }

    public function getConfigParametersOrder($storeId = NULL) {
        $scope = 'stores';
        if($storeId == 0){
           $scope = 'default';
        }
        $userName = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/userName',$scope,$storeId);
        $password = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/password',$scope,$storeId);
        $company = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/companyName',$scope,$storeId);
        $configParameters = array('name'=> $userName."@".$company,'password'=> $password,'company'=> $company,'local'=>'en-gb');
        return $configParameters;
    }

    /**
     * @return string
     * Technical support email for error logs
     */
    public function logErrorSenderEmail()
    {
        $data = array(
            'name' => 'Technical Support',
            'email' => 'support@kensium.com'
        );
        return $data;
    }
    /**
     * For clear cache
     */
    public function clearCache()
    {
        $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
