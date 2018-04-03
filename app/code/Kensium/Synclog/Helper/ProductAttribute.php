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
 * Class ProductAttribute
 * @package Kensium\Synclog\Helper
 */
class ProductAttribute extends \Magento\Framework\App\Helper\AbstractHelper
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
    protected $productLog = null;


    /**
     * @var null
     */
    protected $productLogDetails = null;
    /**
     * @var \Kensium\Synclog\Model\CustomerFactory
     */
    protected $productAttributeLogFactory;

    /**
     * @var \Kensium\Synclog\Model\ordersynclogdetailsFactory
     */
    protected $productAttributesynclogdetailsFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory
     * @param \Kensium\Synclog\Model\ProductattributesynclogFactory $productAttributeLogFactory
     * @param \Kensium\Synclog\Model\ProductattributesynclogdetailsFactory $productAttributesynclogdetailsFactory
     * @param DateTime $date
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory,
                                \Kensium\Synclog\Model\ProductattributesynclogFactory $productAttributeLogFactory,
                                \Kensium\Synclog\Model\ProductattributesynclogdetailsFactory $productAttributesynclogdetailsFactory,
                                DateTime $date
    )
    {
        parent::__construct($context);
        $this->cronScheduleFactory = $cronScheduleFactory;
        $this->productLogFactory = $productAttributeLogFactory;
        $this->productsynclogdetailsFactory = $productAttributesynclogdetailsFactory;
        $this->date = $date;
        $this->scopeConfigInterface = $context->getScopeConfig();
    }


    /**
     * @param array $dataArray
     * @return bool|string
     */
    public function productAttributeSyncSuccessLogs($dataArray = array())
    {
        $this->productLog = $this->productLogFactory->create();
        $this->productLogDetails = $this->productsynclogdetailsFactory->create();
        try {
            if (isset($dataArray['schedule_id']))
                $this->productLog->setSyncExecId($dataArray['schedule_id']);

            if (isset($dataArray['created_at']))
                $this->productLog->setCreatedAt($dataArray['created_at']);

            if (isset($dataArray['acumatica_attribute_code']))
                $this->productLog->setAcumaticaAttributeCode($dataArray['acumatica_attribute_code']);

            if (isset($dataArray['description']))
                $this->productLog->setDescription($dataArray['description']);

            if (isset($dataArray['messageType']))
                $this->productLog->setMessageType($dataArray['messageType']);

            if (isset($dataArray['syncDirection']))
                $this->productLog->setSyncDirection($dataArray['syncDirection']);

            $this->productLog->save();



        } catch (Exception $e) {
            return $e->getMessage();
        }

        try {

            $this->productLogDetails->setSyncRecordId($this->productLog->getId()); // get the last insert id to be inserted in the log details table

            if (isset($dataArray['long_message']))
                $this->productLogDetails->setLongMessage($dataArray['long_message']);

            $this->productLogDetails->save();

            return TRUE;

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
    public function productAttributeManualSync($dataArray = array())
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