<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class CustomerAttribute
 * @package Kensium\Synclog\Helper
 */
class CustomerAttribute extends \Magento\Framework\App\Helper\AbstractHelper
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
     * @var null
     */
    protected $customerLog = null;


    /**
     * @var null
     */
    protected $customerLogDetails = null;
    /**
     * @var \Kensium\Synclog\Model\CustomerFactory
     */
    protected $customerAttributeLogFactory;

    /**
     * @var
     */
    protected $customerAttributesynclogdetailsFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var
     */
    protected  $data;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory
     * @param \Kensium\Synclog\Model\CustomerattributesynclogFactory $customerAttributeLogFactory
     * @param \Kensium\Synclog\Model\CustomerattributesynclogdetailsFactory $customerAttributesynclogdetailsFactory
     * @param DateTime $date
     * @param \Kensium\Amconnector\Helper\Data $data
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory,
                                \Kensium\Synclog\Model\CustomerattributesynclogFactory $customerAttributeLogFactory,
                                \Kensium\Synclog\Model\CustomerattributesynclogdetailsFactory $customerAttributesynclogdetailsFactory,
                                DateTime $date,
                                \Kensium\Amconnector\Helper\Data $data
    )
    {
        parent::__construct($context);
        $this->cronScheduleFactory = $cronScheduleFactory;
        $this->customerLogFactory = $customerAttributeLogFactory;
        $this->customersynclogdetailsFactory = $customerAttributesynclogdetailsFactory;
        $this->date = $date;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->helperData = $data;
    }


    /**
     * @param array $dataArray
     * @return bool|string
     */
    public function customerAttributeSyncSuccessLogs($dataArray = array(),$storeId)
    {
        $this->customerLog = $this->customerLogFactory->create();
        $this->customerLogDetails = $this->customersynclogdetailsFactory->create();
        try {
            if (isset($dataArray['schedule_id']))
                $this->customerLog->setSyncExecId($dataArray['schedule_id']);

            if (isset($dataArray['created_at']))
                $this->customerLog->setCreatedAt($dataArray['created_at']);

            if (isset($dataArray['acumatica_attribute_code']))
                $this->customerLog->setAcumaticaAttributeCode($dataArray['acumatica_attribute_code']);

            if (isset($dataArray['description']))
                $this->customerLog->setDescription($dataArray['description']);

            if (isset($dataArray['messageType']))
                $this->customerLog->setMessageType($dataArray['messageType']);

            if (isset($dataArray['syncDirection']))
                $this->customerLog->setSyncDirection($dataArray['syncDirection']);

            $this->customerLog->save();


            $this->customerLogDetails->setSyncRecordId($this->customerLog->getId()); // get the last insert id to be inserted in the log details table

            if (isset($dataArray['long_message']))
                $this->customerLogDetails->setLongMessage($dataArray['long_message']);

            $this->customerLogDetails->save();


            /* Start of Failure log email*/

            if($storeId==0){
                $scopeType = 'default';
            }else{
                $scopeType = 'stores';
            }
            $sendLogReport = $this->scopeConfigInterface->getValue('amconnectorcommon/log_setting/log_report',$scopeType,$storeId);
            if($sendLogReport && !empty($dataArray['longMessage'])) {
                if ($sendLogReport) {
                    $this->helperData->errorLogEmail('Customer Attribute', $dataArray['longMessage']);
                }
            }

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * function for saving manual sync record in cron scheduler
     */
    /**
     * @param array $dataArray
     * @return mixed|string
     */
    public function customerAttributeManualSync($dataArray = array())
    {
        if (!$this->cronSchedule)
            $this->cronSchedule = $this->cronScheduleFactory->create();
        try {
            if (isset($dataArray['job_code']))
                $this->cronSchedule->setJobCode($dataArray['job_code']);

            if (isset($dataArray['status']))
                $this->cronSchedule->setStatus($dataArray['status']);

            if (isset($dataArray['messages']))
                $this->cronSchedule->setMessages($dataArray['messages']);

            if (isset($dataArray['created_at']))
                $this->cronSchedule->setCreatedAt($dataArray['created_at']);

            if (isset($dataArray['scheduled_at']))
                $this->cronSchedule->setScheduledAt($dataArray['scheduled_at']);

            if (isset($dataArray['executed_at']))
                $this->cronSchedule->setExecutedAt($dataArray['executed_at']);

            if (isset($dataArray['finished_at']))
                $this->cronSchedule->setFinishedAt($dataArray['finished_at']);

            if (isset($dataArray['runMode']))
                $this->cronSchedule->setRunMode($dataArray['runMode']);

            if (isset($dataArray['autoSync']))
                $this->cronSchedule->setAutoSync($dataArray['autoSync']);

            if (isset($dataArray['store_id']))
                $this->cronSchedule->setStoreId($dataArray['store_id']);

            $this->cronSchedule->save();

            return $this->cronSchedule->getId();

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}