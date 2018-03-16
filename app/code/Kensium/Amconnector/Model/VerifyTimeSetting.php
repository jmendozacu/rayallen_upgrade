<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface as PsrLogger;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\Message\ManagerInterface;
use Magento\Config\Model\ResourceModel\Config as ConnectorConfig;

class VerifyTimeSetting
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var PsrLogger
     */
    protected $logger;

    /**
     * @var Time
     */
    protected $timeHelper;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param PsrLogger $logger
     * @param Time $timeHelper
     * @param ManagerInterface $messageManager
     * @param Config $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        PsrLogger $logger,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        ManagerInterface $messageManager,
        ConnectorConfig $config
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->timeHelper = $timeHelper;
        $this->messageManager = $messageManager;
        $this->config = $config;
    }

    /**
     * @param $autoSyncStatus
     * @param $orgScope
     * @param $scopeId
     * @return int
     */
    public function verifyTimeSetting($autoSyncStatus,$orgScope,$scopeId)
    {
        $this->config->saveConfig('amconnector_time_configuration/amconnectortimeset/automatictimesync', $autoSyncStatus, $orgScope, $scopeId);
        if($autoSyncStatus == 0){
            $timeSyncStatus = $this->timeHelper->getVerifyTime($scopeId);
        }else {
            $timeSyncStatus = $this->timeHelper->getSyncTime($autoSyncStatus, $orgScope, $scopeId);
        }
        if($timeSyncStatus == 1){
            $this->logger->critical(new LocalizedException(__('Magento server time synced with Acumatica server successfully!')));
            $this->messageManager->addSuccessMessage( __('Magento server time synced with Acumatica server successfully!') );
        }else{
            $this->logger->critical(new LocalizedException(__('Magento server time sync failed.')));
            $this->messageManager->addErrorMessage( __('Magento server time sync failed.') );
        }
        return $timeSyncStatus;
    }
}
