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

class ProductInventory extends \Magento\Framework\App\Helper\AbstractHelper
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
    protected $inventoryLogFactory;
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
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory
     * @param \Kensium\Synclog\Model\ProductinventorysynclogFactory $inventoryLogFactory
     * @param \Kensium\Synclog\Model\ProductinventorysynclogdetailsFactory $inventorysynclogdetailsFactory
     * @param DateTime $date
     * @param ObjectManagerInterface $objectManagerInterface
     * @param \Kensium\Amconnector\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory,
        \Kensium\Synclog\Model\ProductinventorysynclogFactory $inventoryLogFactory,
        \Kensium\Synclog\Model\ProductinventorysynclogdetailsFactory $inventorysynclogdetailsFactory,
        DateTime $date,
        ObjectManagerInterface $objectManagerInterface,
        \Kensium\Amconnector\Helper\Data $helperData
    )
    {
        parent::__construct($context);
        $this->cronScheduleFactory = $cronScheduleFactory;
        $this->inventoryLogFactory = $inventoryLogFactory;
        $this->inventorysynclogdetailsFactory = $inventorysynclogdetailsFactory;
        $this->date = $date;
        $this->_objectManager = $objectManagerInterface;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->helperData = $helperData;
    }

    /**
     * @param array $inventoryArray
     * @return string
     */
    public function inventoryManualSync($inventoryArray = array())
    {
        if (!$this->cronSchedule)
            $this->cronSchedule = $this->cronScheduleFactory->create();
        try {
            if (isset($inventoryArray['job_code']))
                $this->cronSchedule->setJobCode($inventoryArray['job_code']);

            if (isset($inventoryArray['schedule_id']))
                $this->cronSchedule->setScheduleId($inventoryArray['schedule_id']);

            if (isset($inventoryArray['status']))
                $this->cronSchedule->setStatus($inventoryArray['status']);

            if (isset($inventoryArray['messages']))
                $this->cronSchedule->setMessages($inventoryArray['messages']);

            if (isset($inventoryArray['created_at']))
                $this->cronSchedule->setCreatedAt($inventoryArray['created_at']);

            if (isset($inventoryArray['scheduled_at']))
                $this->cronSchedule->setScheduledAt($inventoryArray['scheduled_at']);

            if (isset($inventoryArray['executed_at']))
                $this->cronSchedule->setExecutedAt($inventoryArray['executed_at']);

            if (isset($inventoryArray['finished_at']))
                $this->cronSchedule->setFinishedAt($inventoryArray['finished_at']);

            if (isset($inventoryArray['runMode']))
                $this->cronSchedule->setRunMode($inventoryArray['runMode']);

            if (isset($inventoryArray['autoSync']))
                $this->cronSchedule->setAutoSync($inventoryArray['autoSync']);

            if (isset($inventoryArray['store_id']))
                $this->cronSchedule->setStoreId($inventoryArray['store_id']);

            $this->cronSchedule->save();
            return $this->cronSchedule->getId();

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * function for updating latest saving manual sync record in cron scheduler
     */

    public function inventoryManualSyncUpdate($inventoryArray = array())
    {
        $collection = $this->cronScheduleFactory->create()->getCollection();
        $collection->addFieldToFilter('id', $inventoryArray['id']);
        $result = $collection->getFirstItem();

        if (null !== $result->getId()) {
            $collection = $this->cronScheduleFactory->create()->load($inventoryArray['id']);
            try {
                if (isset($inventoryArray['status']))
                    $collection->setStatus($inventoryArray['status']);

                if (isset($inventoryArray['messages']))
                    $collection->setMessages($inventoryArray['messages']);

                if (isset($inventoryArray['scheduled_at']))
                    $collection->setScheduledAt($inventoryArray['scheduled_at']);

                if (isset($inventoryArray['executed_at']))
                    $collection->setExecutedAt($inventoryArray['executed_at']);

                if (isset($inventoryArray['finished_at']))
                    $collection->setFinishedAt($inventoryArray['finished_at']);

                $collection->save();

            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else
            return "Sync Record not found";
    }

    /*
     * function for saving inventory sync Logs
     */

    public function inventorySyncLogs($inventoryArray = array())
    {
        $storeId = $inventoryArray['store_id'];
        $currentDate = $this->date->date('Y-m-d H:i:s', time());
        $schedulerId = $inventoryArray['schedule_id'];

        /*
         * if runmode is not manual then we need to get the Primary key (ID) from scheduler table
         * and need to pass that id to log the inventory data.
         */
        if (strtolower($inventoryArray['runMode']) != "manual") {
            $collection = $this->cronScheduleFactory->create()->getCollection(); // get it from Amconnector cron table
            $collection->addFieldToFilter('schedule_id', $inventoryArray['schedule_id']);
            $result = $collection->getFirstItem();
            if (null !== $result->getId()) {
                $schedulerId = $result->getId();
            }
        }
        $inventoryLogModel = $this->_objectManager->create('\Kensium\Synclog\Model\Productinventorysynclog');
        $inventoryLogModel->setSyncExecId($schedulerId)
            ->setCreatedAt($currentDate)
            ->setAcumaticaAttributeCode($inventoryArray['acumatica_attribute_code'])
            ->setDescription($inventoryArray['description'])
            ->setMessageType($inventoryArray['messageType'])
            ->setSyncDirection($inventoryArray['syncDirection']);

        $inventoryLogModel->save();

        $lastAddedInventoryId = $inventoryLogModel->getId();
        $inventoryLogDetailsModel = $this->_objectManager->create('\Kensium\Synclog\Model\Productinventorysynclogdetails');
        $inventoryLogDetailsModel->setSyncRecordId($lastAddedInventoryId);
        if(isset($inventoryArray['longMessage'])){
            $inventoryLogDetailsModel->setLongMessage($inventoryArray['longMessage']);
        }
        $inventoryLogDetailsModel->save();

        /* Start of Failure log email*/

        if($storeId==0){
            $scopeType = 'default';
        }else{
            $scopeType = 'stores';
        }
        $sendLogReport = $this->scopeConfigInterface->getValue('amconnectorcommon/log_setting/log_report',$scopeType,$storeId);
        if(!isset($sendLogReport )){
            $sendLogReport = $this->scopeConfigInterface->getValue('amconnectorcommon/log_setting/log_report');
        }
        if($sendLogReport && !empty($inventoryArray['longMessage'])) {
            if ($sendLogReport) {
                $this->helperData->errorLogEmail('Inventory', $inventoryArray['longMessage']);
            }
        }
    }
}