<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;


class Category extends \Magento\Framework\App\Helper\AbstractHelper
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
     * @var \Kensium\Synclog\Model\CategoryFactory
     */
    protected $categoryLogFactory;

    /**
     * @var \Kensium\Synclog\Model\CategorysynclogdetailsFactory
     */
    protected $categorysynclogdetailsFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;
    /**
     * @var \Kensium\Synclog\Model\Category
     */
    protected $categoryLogModel;
    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory
     * @param \Kensium\Synclog\Model\CategoryFactory $categoryLogFactory
     * @param \Kensium\Synclog\Model\Category $categoryLogModel
     * @param \Kensium\Synclog\Model\CategorysynclogdetailsFactory $categorysynclogdetailsFactory
     * @param DateTime $date
     * @param \Kensium\Amconnector\Helper\Data $helperData
     * @param ObjectManagerInterface $objectManagerInterface
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory,
                                \Kensium\Synclog\Model\CategoryFactory $categoryLogFactory,
                                \Kensium\Synclog\Model\Category $categoryLogModel,
                                \Kensium\Synclog\Model\CategorysynclogdetailsFactory $categorysynclogdetailsFactory,
                                DateTime $date,
                                \Kensium\Amconnector\Helper\Data $helperData,
                                ObjectManagerInterface $objectManagerInterface
    )
    {
        parent::__construct($context);
        $this->cronScheduleFactory = $cronScheduleFactory;
        $this->categoryLogFactory = $categoryLogFactory;
        $this->categorysynclogdetailsFactory = $categorysynclogdetailsFactory;
        $this->date = $date;
        $this->helperData = $helperData;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->_objectManager = $objectManagerInterface;
        $this->categoryLogModel = $categoryLogModel;
    }

    /**
     * @param array $categoryArray
     * @return string
     */
    public function categoryManualSync($categoryArray = array())
    {
        if (!$this->cronSchedule)
            $this->cronSchedule = $this->cronScheduleFactory->create();
        try {
            if (isset($categoryArray['job_code']))
                $this->cronSchedule->setJobCode($categoryArray['job_code']);

            if (isset($categoryArray['schedule_id']))
                $this->cronSchedule->setScheduleId($categoryArray['schedule_id']);

            if (isset($categoryArray['status']))
                $this->cronSchedule->setStatus($categoryArray['status']);

            if (isset($categoryArray['messages']))
                $this->cronSchedule->setMessages($categoryArray['messages']);

            if (isset($categoryArray['created_at']))
                $this->cronSchedule->setCreatedAt($categoryArray['created_at']);

            if (isset($categoryArray['scheduled_at']))
                $this->cronSchedule->setScheduledAt($categoryArray['scheduled_at']);

            if (isset($categoryArray['executed_at']))
                $this->cronSchedule->setExecutedAt($categoryArray['executed_at']);

            if (isset($categoryArray['finished_at']))
                $this->cronSchedule->setFinishedAt($categoryArray['finished_at']);

            if (isset($categoryArray['runMode']))
                $this->cronSchedule->setRunMode($categoryArray['runMode']);

            if (isset($categoryArray['autoSync']))
                $this->cronSchedule->setAutoSync($categoryArray['autoSync']);

            if (isset($categoryArray['store_id']))
                $this->cronSchedule->setStoreId($categoryArray['store_id']);

            $this->cronSchedule->save();
            return $this->cronSchedule->getId();

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * function for updating latest saving manual sync record in cron scheduler
     */

    public function categoryManualSyncUpdate($categoryArray = array())
    {
        $collection = $this->cronScheduleFactory->create()->getCollection();
        $collection->addFieldToFilter('id', $categoryArray['id']);
        $result = $collection->getFirstItem();

        if (null !== $result->getId()) {
            $collection = $this->cronScheduleFactory->create()->load($categoryArray['id']);
            try {
                if (isset($categoryArray['status']))
                    $collection->setStatus($categoryArray['status']);

                if (isset($categoryArray['messages']))
                    $collection->setMessages($categoryArray['messages']);

                if (isset($categoryArray['scheduled_at']))
                    $collection->setScheduledAt($categoryArray['scheduled_at']);

                if (isset($categoryArray['executed_at']))
                    $collection->setExecutedAt($categoryArray['executed_at']);

                if (isset($categoryArray['finished_at']))
                    $collection->setFinishedAt($categoryArray['finished_at']);

                $collection->save();

            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else
            return "Sync Record not found";
    }

    /*
     * function for saving category sync Logs
     */

    public function categorySyncLogs($categoryArray = array())
    {
        $storeId = $categoryArray['storeId'];
        $currentDate = $this->date->date('Y-m-d H:i:s', time());
        $schedulerId = $categoryArray['schedule_id'];

        /*
         * if runmode is not manual then we need to get the Primary key (ID) from scheduler table
         * and need to pass that id to log the category data.
         */
        if (strtolower($categoryArray['runMode']) != "manual") {
            $collection = $this->cronScheduleFactory->create()->getCollection(); // get it from Amconnector cron table
            $collection->addFieldToFilter('schedule_id', $categoryArray['schedule_id']);
            $result = $collection->getFirstItem();
            if (null !== $result->getId()) {
                $schedulerId = $result->getId();
            }
        }
        $categoryLogModel = $this->_objectManager->create('\Kensium\Synclog\Model\Category');
        $categoryLogModel->setSyncExecId($schedulerId)
            ->setCreatedAt($currentDate)
            ->setCatId($categoryArray['catId'])
            ->setAcumaticaCategoryName($categoryArray['acumaticaCategoryName'])
            ->setAcumaticaCategoryId($categoryArray['acumaticaCategoryId'])
            ->setDescription($categoryArray['description'])
            ->setAction($categoryArray['action'])
            ->setMessageType($categoryArray['messageType'])
            ->setSyncAction($categoryArray['syncAction']);

        if(isset($categoryArray['Failure'])){
            $categoryLogModel->setMessageType($categoryArray['Failure']);
        }
        if(isset($categoryArray['before_change'])){
            $categoryLogModel->setBeforeChange($categoryArray['before_change']);
        }
        if(isset($categoryArray['after_change'])){
            $categoryLogModel->setAfterChange($categoryArray['after_change']);
        }
        $categoryLogModel->save();

        $lastAddedcategoryId = $categoryLogModel->getId();
        $categoryLogDetailsModel = $this->_objectManager->create('\Kensium\Synclog\Model\Categorysynclogdetails');
        $categoryLogDetailsModel->setSyncRecordId($lastAddedcategoryId);
        if(isset($categoryArray['longMessage'])){
            $categoryLogDetailsModel->setLongMessage($categoryArray['longMessage']);
        }
        $categoryLogDetailsModel->save();

        /* Start of Failure log email*/

        if($storeId==0){
            $scopeType = 'default';
        }else{
            $scopeType = 'stores';
        }
        $sendLogReport = $this->scopeConfigInterface->getValue('amconnectorcommon/log_setting/log_report',$scopeType,$storeId);
        if($sendLogReport && !empty($categoryArray['longMessage'])) {
            if ($sendLogReport) {
                $this->helperData->errorLogEmail('Category', $categoryArray['longMessage']);
            }
        }
    }

}