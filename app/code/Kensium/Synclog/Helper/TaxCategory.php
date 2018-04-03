<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\ObjectManagerInterface;

class TaxCategory extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Kensium\Synclog\Model\CronScheduleFactory
     */
    protected $cronScheduleFactory;

    /**
     * @var null
     */
    protected $cronSchedule = null;

    /**
     * @var
     */
    protected $taxCategoryLog;

    /**
     * @var
     */
    protected $taxCategorySyncLogDetailsFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var
     */
    protected $data;

    /**
     * @var
     */
    protected $transportBuilder;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory
     * @param \Kensium\Synclog\Model\TaxcategorysynclogFactory $taxCategoryLogFactory
     * @param \Kensium\Synclog\Model\TaxcategorysynclogdetailsFactory $taxCategorySyncLogDetailsFactory
     * @param DateTime $date
     * @param \Kensium\Amconnector\Helper\Data $data
     * @param ObjectManagerInterface $objectManagerInterface
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
                                \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory,
                                \Kensium\Synclog\Model\TaxcategorysynclogFactory $taxCategoryLogFactory,
                                \Kensium\Synclog\Model\TaxcategorysynclogdetailsFactory $taxCategorySyncLogDetailsFactory,
                                DateTime $date,
                                \Kensium\Amconnector\Helper\Data $data,
                                ObjectManagerInterface $objectManagerInterface
    )
    {
        parent::__construct($context);
        $this->cronScheduleFactory = $cronScheduleFactory;
        $this->taxcategoryLog = $taxCategoryLogFactory;
        $this->taxcategorysynclogdetailsFactory = $taxCategorySyncLogDetailsFactory;
        $this->date = $date;
        $this->helperData = $data;
        $this->_transportBuilder = $transportBuilder;
        $this->_objectManager = $objectManagerInterface;
        $this->logger = $context->getLogger();
        $this->scopeConfigInterface = $context->getScopeConfig();
    }


    /*
     * function for saving manual sync record in cron scheduler
     */

    public function taxCategoryManualSync($taxcategoryArray = array())
    {
        if (!$this->cronSchedule)
            $this->cronSchedule = $this->cronScheduleFactory->create();
        try {
            if (isset($taxcategoryArray['job_code']))
                $this->cronSchedule->setJobCode($taxcategoryArray['job_code']);

            if (isset($taxcategoryArray['schedule_id']))
                $this->cronSchedule->setScheduleId($taxcategoryArray['schedule_id']);

            if (isset($taxcategoryArray['status']))
                $this->cronSchedule->setStatus($taxcategoryArray['status']);

            if (isset($taxcategoryArray['messages']))
                $this->cronSchedule->setMessages($taxcategoryArray['messages']);

            if (isset($taxcategoryArray['created_at']))
                $this->cronSchedule->setCreatedAt($taxcategoryArray['created_at']);

            if (isset($taxcategoryArray['scheduled_at']))
                $this->cronSchedule->setScheduledAt($taxcategoryArray['scheduled_at']);

            if (isset($taxcategoryArray['executed_at']))
                $this->cronSchedule->setExecutedAt($taxcategoryArray['executed_at']);

            if (isset($taxcategoryArray['finished_at']))
                $this->cronSchedule->setFinishedAt($taxcategoryArray['finished_at']);

            if (isset($taxcategoryArray['runMode']))
                $this->cronSchedule->setRunMode($taxcategoryArray['runMode']);

            if (isset($taxcategoryArray['autoSync']))
                $this->cronSchedule->setAutoSync($taxcategoryArray['autoSync']);

            if (isset($taxcategoryArray['store_id']))
                $this->cronSchedule->setStoreId($taxcategoryArray['store_id']);

            $this->cronSchedule->save();
            return $this->cronSchedule->getId();

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * function for saving taxCategory sync Logs
     */

    public function taxCategorySyncLogs($taxCategoryArray = array(),$storeId)
    {
        $currentDate = $this->date->date('Y-m-d H:i:s', time());
        $schedulerId = $taxCategoryArray['schedule_id'];

        /*
         * if runmode is not manual then we need to get the Primary key (ID) from scheduler table
         * and need to pass that id to log the taxCategory data.
         */
        if (strtolower($taxCategoryArray['runMode']) != "manual") {
            $collection = $this->cronScheduleFactory->create()->getCollection(); // get it from Amconnector cron table
            $collection->addFieldToFilter('schedule_id', $taxCategoryArray['schedule_id']);
            $result = $collection->getFirstItem();
            if (null !== $result->getId()) {
                $schedulerId = $result->getId();
            }
        }
        $taxCategoryLogModel = $this->_objectManager->create('\Kensium\Synclog\Model\Taxcategorysynclog');
        $taxCategoryLogModel->setSyncExecId($schedulerId)
            ->setCreatedAt($currentDate)
            ->setAcumaticaAttributeCode($taxCategoryArray['acumatica_attribute_code'])
            ->setMagentoAttributeCode($taxCategoryArray['magento_attribute_code'])
            ->setDescription($taxCategoryArray['description'])
            ->setMessageType($taxCategoryArray['messageType'])
            ->setSyncDirection($taxCategoryArray['syncDirection']);
        $taxCategoryLogModel->save();

        $lastAddedTaxCategoryId = $taxCategoryLogModel->getId();
        $taxCategoryLogDetailsModel = $this->_objectManager->create('\Kensium\Synclog\Model\Taxcategorysynclogdetails');
        $taxCategoryLogDetailsModel->setSyncRecordId($lastAddedTaxCategoryId);
        if(isset($taxCategoryArray['longMessage']))
            $taxCategoryLogDetailsModel->setLongMessage($taxCategoryArray['longMessage']);
        $taxCategoryLogDetailsModel->save();

        /* Start of Failure log email*/

        if($storeId==0){
            $scopeType = 'default';
        }else{
            $scopeType = 'stores';
        }
        $sendLogReport = $this->scopeConfigInterface->getValue('amconnectorcommon/log_setting/log_report',$scopeType,$storeId);
        if($sendLogReport && !empty($taxCategoryArray['longMessage'])) {
            if ($sendLogReport) {
                $this->helperData->errorLogEmail('Tax Category', $taxCategoryArray['longMessage']);
            }
        }
    }


    /*
     * function for updating latest saving manual sync record in cron scheduler
     */

    public function taxCategoryManualSyncUpdate($taxCategoryArray = array())
    {
        $collection = $this->cronScheduleFactory->create()->getCollection();
        $collection->addFieldToFilter('id', $taxCategoryArray['id']);
        $result = $collection->getFirstItem();

        if (null !== $result->getId()) {
            $collection = $this->cronScheduleFactory->create()->load($taxCategoryArray['id']);
            try {
                if (isset($taxCategoryArray['status']))
                    $collection->setStatus($taxCategoryArray['status']);

                if (isset($taxCategoryArray['messages']))
                    $collection->setMessages($taxCategoryArray['messages']);

                if (isset($taxCategoryArray['scheduled_at']))
                    $collection->setScheduledAt($taxCategoryArray['scheduled_at']);

                if (isset($taxCategoryArray['executed_at']))
                    $collection->setExecutedAt($taxCategoryArray['executed_at']);

                if (isset($taxCategoryArray['finished_at']))
                    $collection->setFinishedAt($taxCategoryArray['finished_at']);

                $collection->save();

            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else
            return "Sync Record not found";
    }



}