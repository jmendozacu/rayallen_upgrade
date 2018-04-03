<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Synclog\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Productprice extends \Magento\Framework\App\Helper\AbstractHelper
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
     * @var \Kensium\Synclog\Model\productpricesynclogFactory
     */
    protected $productPriceLogFactory;

    /**
     * @var \Kensium\Synclog\Model\productpricesynclogdetailsFactory
     */
    protected $productPricesynclogdetailsFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory
     * @param \Kensium\Synclog\Model\ProductpricesynclogFactory $productPriceLogFactory
     * @param \Kensium\Synclog\Model\ProductpricesynclogdetailsFactory $productPricesynclogdetailsFactory
     * @param DateTime $date
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory,
                                \Kensium\Synclog\Model\ProductpricesynclogFactory $productPriceLogFactory,
                                \Kensium\Synclog\Model\ProductpricesynclogdetailsFactory $productPricesynclogdetailsFactory,
                                DateTime $date
    )
    {
        parent::__construct($context);
        $this->cronScheduleFactory = $cronScheduleFactory;
        $this->productPriceLogFactory = $productPriceLogFactory;
        $this->productPricesynclogdetailsFactory = $productPricesynclogdetailsFactory;
        $this->date = $date;
        $this->scopeConfigInterface = $context->getScopeConfig();
    }

    /**
     * @param array $productPriceArray
     * @return bool|string
     */
    public function productPriceSyncSuccessLogs($productPriceArray = array())
    {

        $this->productPriceLog = $this->productPriceLogFactory->create();
        $this->productPriceLogDetails = $this->productPricesynclogdetailsFactory->create();

        try {
            if (isset($productPriceArray['schedule_id']))
                $this->productPriceLog->setSyncExecId($productPriceArray['schedule_id']);

            if (isset($productPriceArray['created_at']))
                $this->productPriceLog->setCreatedAt($productPriceArray['created_at']);

            if (isset($productPriceArray['product_id']))
                $this->productPriceLog->setProductId($productPriceArray['product_id']);

            if (isset($productPriceArray['acumatica_attribute_code']))
                $this->productPriceLog->setAcumaticaAttributeCode($productPriceArray['acumatica_attribute_code']);

            if (isset($productPriceArray['messages']))
                $this->productPriceLog->setDescription($productPriceArray['messages']);

            if (isset($productPriceArray['action']))
                $this->productPriceLog->setAction($productPriceArray['action']);

            if (isset($productPriceArray['sync_action']))
                $this->productPriceLog->setSyncAction($productPriceArray['sync_action']);

            if (isset($productPriceArray['status']))
                $this->productPriceLog->setMessageType($productPriceArray['status']);

            if (isset($productPriceArray['sync_direction']))
                $this->productPriceLog->setSyncDirection($productPriceArray['sync_direction']);
            $this->productPriceLog->save();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        try {

            $this->productPriceLogDetails->setSyncRecordId($this->productPriceLog->getId()); // get the last insert id to be inserted in the log details table

            if (isset($productPriceArray['long_message']))
                $this->productPriceLogDetails->setLongMessage($productPriceArray['long_message']);

            $this->productPriceLogDetails->save();

            return TRUE;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * function for saving manual sync record in cron scheduler
     * @param array $productPriceArray
     * @return mixed|string
     */

    public function ProductPriceManualSync($productPriceArray = array())
    {

        if (!$this->cronSchedule)
            $this->cronSchedule = $this->cronScheduleFactory->create();
        try {
            if (isset($productPriceArray['job_code']))
                $this->cronSchedule->setJobCode($productPriceArray['job_code']);

            if (isset($productPriceArray['status']))
                $this->cronSchedule->setStatus($productPriceArray['status']);

            if (isset($productPriceArray['messages']))
                $this->cronSchedule->setMessages($productPriceArray['messages']);

            if (isset($productPriceArray['created_at']))
                $this->cronSchedule->setCreatedAt($productPriceArray['created_at']);

            if (isset($productPriceArray['scheduled_at']))
                $this->cronSchedule->setScheduledAt($productPriceArray['scheduled_at']);

            if (isset($productPriceArray['executed_at']))
                $this->cronSchedule->setExecutedAt($productPriceArray['executed_at']);

            if (isset($productPriceArray['finished_at']))
                $this->cronSchedule->setFinishedAt($productPriceArray['finished_at']);

            if (isset($productPriceArray['runMode']))
                $this->cronSchedule->setRunMode($productPriceArray['runMode']);

            if (isset($productPriceArray['autoSync']))
                $this->cronSchedule->setAutoSync($productPriceArray['autoSync']);

            if (isset($productPriceArray['store_id']))
                $this->cronSchedule->setStoreId($productPriceArray['store_id']);

            $this->cronSchedule->save();

            return $this->cronSchedule->getId();

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}