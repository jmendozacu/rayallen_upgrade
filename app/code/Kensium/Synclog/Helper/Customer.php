<?php
/**
 *
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Kensium\Synclog\Model\CronScheduleFactory
     */
    protected $cronScheduleFactory;

    /**
     * @var null
     */
    protected $cronSchedule = null;

    /**
     * @var \Kensium\Synclog\Model\CustomerFactory
     */
    protected $customerLogFactory;

    /**
     * @var \Kensium\Synclog\Model\CustomersynclogdetailsFactory
     */
    protected $customersynclogdetailsFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory
     * @param \Kensium\Synclog\Model\CustomerFactory $customerLogFactory
     * @param \Kensium\Synclog\Model\CustomersynclogdetailsFactory $customersynclogdetailsFactory
     * @param DateTime $date
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory,
                                \Kensium\Synclog\Model\CustomerFactory $customerLogFactory,
                                \Kensium\Synclog\Model\CustomersynclogdetailsFactory $customersynclogdetailsFactory,
                                DateTime $date
    )
    {
        parent::__construct($context);
        $this->cronScheduleFactory = $cronScheduleFactory;
        $this->customerLogFactory = $customerLogFactory;
        $this->customersynclogdetailsFactory = $customersynclogdetailsFactory;
        $this->date = $date;
        $this->scopeConfigInterface = $context->getScopeConfig();
    }


    /*
     * function for saving customer sync success Logs
     */

    public function customerSyncSuccessLogs($customerArray = array())
    {

        try {
            $currentDate = $this->date->date('Y-m-d H:i:s', time());
            if ($this->cronSchedule)
                $schedulerId = $this->cronSchedule->getId();

            /*
             * if runmode is not manual then we need to get the Primary key (ID) from scheduler table
             * and need to pass that id to log the customer data.
             */
            // for now we are commenting this code for clarification
            /*if (strtolower($customerArray['runMode']) != "manual") {
                $collection = $this->cronScheduleFactory->create()->getCollection(); // get it from Amconnector cron table
                $collection->addFieldToFilter('schedule_id', $customerArray['schedule_id']);
                $result = $collection->getFirstItem();
                if (null !== $result->getId()) {
                    $schedulerId = $result->getId();
                }
            }*/
            $customerLogModel = $this->customerLogFactory->create();

            $customerLogModel->setSyncExecId($schedulerId)
                ->setCreatedAt($currentDate)
                ->setCustomerId($customerArray['customerId']);
            if (isset($customerArray['email']))
                $customerLogModel->setEmail($customerArray['email']);

            $customerLogModel->setDescription($customerArray['description'])
                ->setAction($customerArray['action'])
                ->setSyncAction($customerArray['syncAction'])
                ->setAccumaticaCustomerId($customerArray['accumaticaCustomerId'])
                ->setSyncDirection($customerArray['syncDirection']);

            if (isset($customerArray['before_change']))
                $customerLogModel->setBeforeChange($customerArray['before_change']);
            if (isset($customerArray['after_change']))
                $customerLogModel->setAfterChange($customerArray['after_change']);
            $customerLogModel->setMessageType($customerArray['messageType']);
            $customerLogModel->save();
            $lastaddedCustomerId = $customerLogModel->getId();
            $customerLogDetailsModel = $this->customersynclogdetailsFactory->create();
            $customerLogDetailsModel->setSyncRecordId($lastaddedCustomerId);
            $customerLogDetailsModel->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /*
     * function for saving customer sync failure Logs
     */

    public function customerSyncFailureLogs($customerArray = array())
    {
        $logLevel = $this->scopeConfigInterface->getValue('common/logs/log_level');
        $reportLevel = $this->scopeConfigInterface->getValue('common/logs/level_report');
        $explodeErrorCode = array();
        if(isset($customerArray['error_code']))
            $explodeErrorCode = explode(",", $customerArray['error_code']);
        if ($this->cronSchedule)
            $schedulerId = $this->cronSchedule->getId();
        /*
         * if runmode is not manual then we need to get the Primary key (ID) from scheduler table
         * and need to pass that id to log the customer data.
         */
        if (strtolower($customerArray['runMode']) != "manual") {
            $collection = $this->cronScheduleFactory->create()->getCollection(); // get it from Amconnector cron table
            $collection->addFieldToFilter('schedule_id', $customerArray['schedule_id']);
            $result = $collection->getFirstItem();
            if (null !== $result->getId()) {
                $schedulerId = $result->getId();
            }
        }

        $currentDate = $this->date->date('Y-m-d H:i:s', time());
        for ($ie = 0; $ie <= count($explodeErrorCode); $ie++) {
            if (isset($explodeErrorCode[$ie]) && $explodeErrorCode[$ie] >= $logLevel) {

                $customerLogModel = $this->customerLogFactory->create();
                $customerLogModel->setSyncExecId($schedulerId)
                    ->setCreatedAt($currentDate)
                    ->setCustomerId($customerArray['customerId'])
                    ->setDescription($customerArray['description'])
                    ->setAction($customerArray['action'])
                    ->setSyncAction($customerArray['syncAction'])
                    ->setAccumaticaCustomerId($customerArray['accumaticaCustomerId'])
                    ->setSyncDirection($customerArray['syncDirection']);
                if (isset($customerArray['before_change']))
                    $customerLogModel->setBeforeChange($customerArray['before_change']);
                if (isset($customerArray['after_change']))
                    $customerLogModel->setAfterChange($customerArray['after_change']);
                $customerLogModel->setMessageType($customerArray['messageType']);
                $customerLogModel->save();
                $lastaddedCustomerId = $customerLogModel->getId();

                $customerLogDetailsModel = $this->customersynclogdetailsFactory->create();
                $customerLogDetailsModel->setSyncRecordId($lastaddedCustomerId)
                    ->setLongMessage($customerArray['longMessage']);
                $customerLogDetailsModel->save();

            }
        }
        $sendLogReport = $this->scopeConfigInterface->getValue('common/logs/log_report');
        if ($sendLogReport) {
            $emailTemplate = $this->scopeConfigInterface->getValue('common/logs/log_email');
            $emailTemplate = Mage::getModel('core/email_template')->loadDefault('synclog_report');
            $emailSender = $this->scopeConfigInterface->getValue('common/logs/log_email_sender');
            $emailReceipient = $this->scopeConfigInterface->getValue('common/logs/log_email_recipient');
            $explodedemailReceipient = explode(",", $emailReceipient);
            if ($emailSender == "general") {
                $senderEmailName = $this->scopeConfigInterface->getValue('trans_email/ident_general/name');
                $senderEmailId = $this->scopeConfigInterface->getValue('trans_email/ident_general/email');
            } else if ($emailSender == "sales") {
                $senderEmailName = $this->scopeConfigInterface->getValue('trans_email/ident_sales/name');
                $senderEmailId = $this->scopeConfigInterface->getValue('trans_email/ident_sales/email');
            } else if ($emailSender == "support") {
                $senderEmailName = $this->scopeConfigInterface->getValue('trans_email/ident_support/name');
                $senderEmailId = $this->scopeConfigInterface->getValue('trans_email/ident_support/email');
            } else if ($emailSender == "custom1") {
                $senderEmailName = $this->scopeConfigInterface->getValue('trans_email/ident_custom1/name');
                $senderEmailId = $this->scopeConfigInterface->getValue('trans_email/ident_custom1/email');
            } else if ($emailSender == "custom2") {
                $senderEmailName = $this->scopeConfigInterface->getValue('trans_email/ident_custom2/name');
                $senderEmailId = $this->scopeConfigInterface->getValue('trans_email/ident_custom2/email');
            }

            for ($ie = 0; $ie < count($explodeErrorCode); $ie++) {
                if (isset($explodeErrorCode[$ie]) && $explodeErrorCode[$ie] >= $logLevel) {
                    $emailTemplateVariables = array();
                    $emailTemplate->setSenderName($senderEmailName);
                    $emailTemplate->setSenderEmail($senderEmailId);
                    $emailTemplate->setType('html');
                    $emailTemplate->setTemplateSubject('Acumatica - Report Level');

                    if (is_array($explodedemailReceipient)) {
                        for ($i = 0; $i < count($explodedemailReceipient); $i++) {
                            if(isset($explodedemailReceipient[$i])){
                                $emailTemplate->send($explodedemailReceipient[$i]);
                            }
                        }
                    }
                }
            }
        }
    }

    /*
     * function for saving manual sync record in cron scheduler
     */

    public function customerManualSync($customerArray = array())
    {
        if (!$this->cronSchedule)
            $this->cronSchedule = $this->cronScheduleFactory->create();
        try {
            if (isset($customerArray['job_code']))
                $this->cronSchedule->setJobCode($customerArray['job_code']);

            if (isset($customerArray['schedule_id']))
                $this->cronSchedule->setScheduleId($customerArray['schedule_id']);

            if (isset($customerArray['status']))
                $this->cronSchedule->setStatus($customerArray['status']);

            if (isset($customerArray['messages']))
                $this->cronSchedule->setMessages($customerArray['messages']);

            if (isset($customerArray['created_at']))
                $this->cronSchedule->setCreatedAt($customerArray['created_at']);

            if (isset($customerArray['scheduled_at']))
                $this->cronSchedule->setScheduledAt($customerArray['scheduled_at']);

            if (isset($customerArray['executed_at']))
                $this->cronSchedule->setExecutedAt($customerArray['executed_at']);

            if (isset($customerArray['finished_at']))
                $this->cronSchedule->setFinishedAt($customerArray['finished_at']);

            if (isset($customerArray['runMode']))
                $this->cronSchedule->setRunMode($customerArray['runMode']);

            if (isset($customerArray['autoSync']))
                $this->cronSchedule->setAutoSync($customerArray['autoSync']);

            if (isset($customerArray['store_id']))
                $this->cronSchedule->setStoreId($customerArray['store_id']);

            $this->cronSchedule->save();

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    /*
     * function for saving customer attribute sync success Logs
     */

    public function customerAttributeSyncSuccessLogs($customerAttributeArray = array())
    {
        $customerAttributeSyncModel = Mage::getModel("synclog/customerattributelog");
        try {
            $customerSyncCronScheduleId = $customerAttributeSyncModel->saveSyncSuccessLogs($customerAttributeArray);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * function for saving customer Attribute sync failure Logs
     */

    public function customerAttributeSyncFailureLogs($customerAttributeArray = array())
    {
        $customerAttributeSyncModel = Mage::getModel("synclog/customerattributelog");
        try {
            $customerAttributeSyncModel->saveSyncFailureLogs($customerAttributeArray);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * function for saving manual sync record in cron scheduler
     */

    public function customerAttributeManualSync($customerAttributeArray = array())
    {
        $customerAttributeSyncModel = Mage::getModel("synclog/customerattributelog");
        try {
            $customerAttributeSyncCronScheduleId = $customerAttributeSyncModel->saveManualSyncData($customerAttributeArray);
            return $customerAttributeSyncCronScheduleId;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * function for updating latest saving manual sync record in cron scheduler
     */

    public function customerAttributeManualSyncUpdate($customerAttributeArray = array())
    {
        $customerAttributeSyncModel = Mage::getModel("synclog/customerattributelog");
        try {
            $customerSyncCronScheduleId = $customerAttributeSyncModel->saveManualSyncDataUpdate($customerAttributeArray);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


}