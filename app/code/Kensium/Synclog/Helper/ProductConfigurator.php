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

class ProductConfigurator extends \Magento\Framework\App\Helper\AbstractHelper
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
    protected $productLogFactory;

    /**
     * @var \Kensium\Synclog\Model\ordersynclogdetailsFactory
     */
    protected $inventorysynclogdetailsFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $helperData;

    /**
     * @var
     */
    protected $objectManagerInterface;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory
     * @param \Kensium\Synclog\Model\ProductconfiguratorsynclogFactory $productconfiguratorsyncLogFactory
     * @param \Kensium\Synclog\Model\ProductconfiguratorsynclogdetailsFactory $productconfiguratorsynclogdetailsFactory
     * @param \Kensium\Amconnector\Helper\Data $helperData
     * @param DateTime $date
     * @param ObjectManagerInterface $objectManagerInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory,
        \Kensium\Synclog\Model\ProductconfiguratorsynclogFactory $productconfiguratorsyncLogFactory,
        \Kensium\Synclog\Model\ProductconfiguratorsynclogdetailsFactory $productconfiguratorsynclogdetailsFactory,
        \Kensium\Amconnector\Helper\Data $helperData,
        DateTime $date,
        ObjectManagerInterface $objectManagerInterface
    )
    {
        parent::__construct($context);
        $this->cronScheduleFactory = $cronScheduleFactory;
        $this->productconfiguratorsyncLogFactory = $productconfiguratorsyncLogFactory;
        $this->productconfiguratorsynclogdetailsFactory = $productconfiguratorsynclogdetailsFactory;
        $this->date = $date;
        $this->helperData = $helperData;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->_objectManager = $objectManagerInterface;
    }

    /**
     * @param array $dataArray
     */
    public function productConfiguratorSyncSuccessLogs($dataArray = array())
    {
        $storeId = $dataArray['storeId'];
        $currentDate = $this->date->date('Y-m-d H:i:s', time());
        $schedulerId = $dataArray['schedule_id'];

        /*
         * if runmode is not manual then we need to get the Primary key (ID) from scheduler table
         * and need to pass that id to log the category data.
         */
        if (strtolower($dataArray['runMode']) != "manual") {
            $collection = $this->cronScheduleFactory->create()->getCollection(); // get it from Amconnector cron table
            $collection->addFieldToFilter('schedule_id', $dataArray['schedule_id']);
            $result = $collection->getFirstItem();
            if (null !== $result->getId()) {
                $schedulerId = $result->getId();
            }
        }
        $productLogModel = $this->_objectManager->create('\Kensium\Synclog\Model\Productconfiguratorsynclog');
        $productLogModel->setSyncExecId($schedulerId)
            ->setCreatedAt($currentDate)
            ->setProductId($dataArray['productId'])
            ->setAcumaticaStockItem($dataArray['acumaticaStockItem'])
            ->setDescription($dataArray['description'])
            ->setAction($dataArray['action'])
            ->setMessageType($dataArray['messageType'])
            ->setSyncAction($dataArray['syncAction']);

        if(isset($dataArray['messageType']) == 'Failure'){
            $productLogModel->setMessageType($dataArray['messageType']);
        }
        if(isset($dataArray['before_change'])){
            $productLogModel->setBeforeChange($dataArray['before_change']);
        }
        if(isset($dataArray['after_change'])){
            $productLogModel->setAfterChange($dataArray['after_change']);
        }
        $productLogModel->save();

        $lastAddedcategoryId = $productLogModel->getId();
        $productLogDetailsModel = $this->_objectManager->create('\Kensium\Synclog\Model\Productconfiguratorsynclogdetails');
        $productLogDetailsModel->setSyncRecordId($lastAddedcategoryId);
        if(isset($categoryArray['longMessage'])){
            $productLogDetailsModel->setLongMessage($dataArray['longMessage']);
        }
        $productLogDetailsModel->save();

        /* Start of Failure log email*/

        if($storeId==0){
            $scopeType = 'default';
        }else{
            $scopeType = 'stores';
        }
        $sendLogReport = $this->scopeConfigInterface->getValue('amconnectorcommon/log_setting/log_report',$scopeType,$storeId);
        if($sendLogReport && !empty($dataArray['longMessage'])) {
            if ($sendLogReport) {
                $this->helperData->errorLogEmail('Product', $dataArray['longMessage']);
            }
        }
    }

    /*
     * function for saving manual sync record in cron scheduler
     */

    public function productManualSync($dataArray = array())
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

    public function productManualSyncUpdate($dataArray = array())
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