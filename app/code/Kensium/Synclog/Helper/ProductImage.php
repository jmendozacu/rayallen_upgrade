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
 * Class ProductImage
 * @package Kensium\Synclog\Helper
 */
class ProductImage extends \Magento\Framework\App\Helper\AbstractHelper
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
     * @var \Kensium\Synclog\Model\productimagesynclogFactory
     */
    protected $productImageLogFactory;

    /**
     * @var \Kensium\Synclog\Model\productimagesynclogdetailsFactory
     */
    protected $productImagesynclogdetailsFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $helperData;

    /**
     * @var null
     */
    protected $productImageLog = null;


    /**
     * @var null
     */
    protected $productImageLogDetails = null;

    /**
     * @var
     */
    protected $objectManagerInterface;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory
     * @param \Kensium\Synclog\Model\ProductimagesynclogFactory $productImageLogFactory
     * @param \Kensium\Synclog\Model\ProductimagesynclogdetailsFactory $productImagesynclogdetailsFactory
     * @param \Kensium\Amconnector\Helper\Data $helperData
     * @param \Magento\Framework\ObjectManagerInterface $objectManagerInterface
     * @param DateTime $date
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory,
                                \Kensium\Synclog\Model\ProductimagesynclogFactory $productImageLogFactory,
                                \Kensium\Synclog\Model\ProductimagesynclogdetailsFactory $productImagesynclogdetailsFactory,
                                \Kensium\Amconnector\Helper\Data $helperData,
                                \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
                                DateTime $date
    )
    {
        parent::__construct($context);
        $this->cronScheduleFactory = $cronScheduleFactory;
        $this->productImageLogFactory = $productImageLogFactory;
        $this->productImagesynclogdetailsFactory = $productImagesynclogdetailsFactory;
        $this->date = $date;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->helperData = $helperData;
        $this->_objectManager = $objectManagerInterface;
    }

    /**
     * @param array $productImageArray
     * @return bool|string
     */
    public function productImageSyncSuccessLogs($productImageArray = array())
    {
        $productImageLog = $this->_objectManager->create('\Kensium\Synclog\Model\Productimagesynclog');
        $productImageLogDetails = $this->_objectManager->create('\Kensium\Synclog\Model\Productimagesynclogdetails');
        $storeId = $productImageArray['storeId'];
        $currentDate = $this->date->date('Y-m-d H:i:s', time());
        $schedulerId = $productImageArray['schedule_id'];

        /*
         * if runmode is not manual then we need to get the Primary key (ID) from scheduler table
         * and need to pass that id to log the category data.
         */
        if (strtolower($productImageArray['runMode']) != "manual") {
            $collection = $this->cronScheduleFactory->create()->getCollection(); // get it from Amconnector cron table
            $collection->addFieldToFilter('schedule_id', $productImageArray['schedule_id']);
            $result = $collection->getFirstItem();
            if (null !== $result->getId()) {
                $schedulerId = $result->getId();
            }
        }

        $productImageLog->setSyncExecId($schedulerId)
            ->setCreatedAt($currentDate)
            ->setAcumaticaAttributeCode($productImageArray['acumatica_attribute_code'])
            ->setDescription($productImageArray['description'])
            ->setSyncDirection($productImageArray['syncDirection'])
            ->setMessageType($productImageArray['messageType'])
            ->setStoreId($storeId);

        $productImageLog->save();

        $lastAddedRecordId = $productImageLog->getId();
        $productImageLogDetails->setSyncRecordId($lastAddedRecordId);
        if(isset($productImageArray['longMessage'])){
            $productImageLogDetails->setLongMessage($productImageArray['longMessage']);
        }
        $productImageLogDetails->save();

        /* Start of Failure log email*/

        if($storeId==0){
            $scopeType = 'default';
        }else{
            $scopeType = 'stores';
        }
        $sendLogReport = $this->scopeConfigInterface->getValue('amconnectorcommon/log_setting/log_report',$scopeType,$storeId);
        if($sendLogReport && !empty($productImageArray['longMessage'])) {
            if ($sendLogReport) {
                $this->helperData->errorLogEmail('Product Image', $productImageArray['longMessage']);
            }
        }
    }

    /*
     * function for saving manual sync record in cron scheduler
     * @param array $productImageArray
     * @return mixed|string
     */

    public function productImageManualSync($productImageArray = array())
    {
//print_r($productImageArray); exit;
        if (!$this->cronSchedule)
            $this->cronSchedule = $this->cronScheduleFactory->create();
        try {
            if (isset($productImageArray['job_code']))
                $this->cronSchedule->setJobCode($productImageArray['job_code']);

            if (isset($productImageArray['status']))
                $this->cronSchedule->setStatus($productImageArray['status']);

            if (isset($productImageArray['messages']))
                $this->cronSchedule->setMessages($productImageArray['messages']);

            if (isset($productImageArray['created_at']))
                $this->cronSchedule->setCreatedAt($productImageArray['created_at']);

            if (isset($productImageArray['scheduled_at']))
                $this->cronSchedule->setScheduledAt($productImageArray['scheduled_at']);

            if (isset($productImageArray['executed_at']))
                $this->cronSchedule->setExecutedAt($productImageArray['executed_at']);

            if (isset($productImageArray['finished_at']))
                $this->cronSchedule->setFinishedAt($productImageArray['finished_at']);

            if (isset($productImageArray['runMode']))
                $this->cronSchedule->setRunMode($productImageArray['runMode']);

            if (isset($productImageArray['autoSync']))
                $this->cronSchedule->setAutoSync($productImageArray['autoSync']);

            if (isset($productImageArray['storeId']))
                $this->cronSchedule->setStoreId($productImageArray['storeId']);

            $this->cronSchedule->save();
            return $this->cronSchedule->getId();

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param array $dataArray
     * @return string
     */
    public function productImageManualSyncUpdate($dataArray = array())
    {
        $collection = $this->cronScheduleFactory->create()->getCollection();
        $collection->addFieldToFilter('id', $dataArray['id']);
        $result = $collection->getFirstItem();

        if (null !== $result->getId()) {
            $collection = $this->cronScheduleFactory->create()->load($dataArray['id']);
            try {
                if (isset($dataArray['status']))
                    $collection->setStatus($dataArray['status']);

                if (isset($dataArray['messages']))
                    $collection->setMessages($dataArray['messages']);

                if (isset($dataArray['scheduled_at']))
                    $collection->setScheduledAt($dataArray['scheduled_at']);

                if (isset($dataArray['executed_at']))
                    $collection->setExecutedAt($dataArray['executed_at']);

                if (isset($dataArray['finished_at']))
                    $collection->setFinishedAt($dataArray['finished_at']);

                $collection->save();

            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else
            return "Sync Record not found";
    }
}