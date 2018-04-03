<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Order extends \Magento\Framework\App\Helper\AbstractHelper
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
    protected $orderLogFactory;

    /**
     * @var \Kensium\Synclog\Model\ordersynclogdetailsFactory
     */
    protected $ordersynclogdetailsFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory
     * @param \Kensium\Synclog\Model\OrderFactory $orderLogFactory
     * @param \Kensium\Synclog\Model\OrdersynclogdetailsFactory $ordersynclogdetailsFactory
     * @param DateTime $date
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Kensium\Synclog\Model\CronScheduleFactory $cronScheduleFactory,
                                \Kensium\Synclog\Model\OrderFactory $orderLogFactory,
                                \Kensium\Synclog\Model\OrdersynclogdetailsFactory $ordersynclogdetailsFactory,
                                \Kensium\Amconnector\Helper\Data $helperData,
                                DateTime $date
    )
    {
        parent::__construct($context);
        $this->cronScheduleFactory = $cronScheduleFactory;
        $this->orderLogFactory = $orderLogFactory;
        $this->ordersynclogdetailsFactory = $ordersynclogdetailsFactory;
        $this->date = $date;
        $this->helperData = $helperData;
        $this->scopeConfigInterface = $context->getScopeConfig();
    }

    public function orderSyncSuccessLogs($orderArray = array())
    {
        //die(print_r($orderArray));
        //$this->cronSchedule = $this->cronScheduleFactory->create();
        $this->orderLog = $this->orderLogFactory->create();
        $this->orderLogDetails = $this->ordersynclogdetailsFactory->create();

        try {
            if (isset($orderArray['schedule_id']))
                $this->orderLog->setSyncExecId($orderArray['schedule_id']);

            if (isset($orderArray['created_at']))
                $this->orderLog->setCreatedAt($orderArray['created_at']);

            if (isset($orderArray['order_id']))
                $this->orderLog->setOrderId($orderArray['order_id']);

            if (isset($orderArray['acumatica_order_id']))
                $this->orderLog->setAcumaticaOrderId($orderArray['acumatica_order_id']);

            if (isset($orderArray['description']))
                $this->orderLog->setDescription($orderArray['description']);

            if (isset($orderArray['action']))
                $this->orderLog->setAction($orderArray['action']);

            if (isset($orderArray['sync_action']))
                $this->orderLog->setSyncAction($orderArray['sync_action']);

            if (isset($orderArray['run_mode']))
                $this->orderLog->setRunMode($orderArray['run_mode']);

            if (isset($orderArray['message_type']))
                $this->orderLog->setMessageType($orderArray['message_type']);

            if (isset($orderArray['store_id']))
                $this->orderLog->setStoreId($orderArray['store_id']);

            if (isset($orderArray['customer_email']))
                $this->orderLog->setCustomerEmail($orderArray['customer_email']);

            $this->orderLog->save();



        } catch (Exception $e) {
            return $e->getMessage();
        }

        try {

            $this->orderLogDetails->setSyncRecordId($this->orderLog->getId()); // get the last insert id to be inserted in the log details table

            if (isset($orderArray['long_message']))
                $this->orderLogDetails->setLongMessage($orderArray['long_message']);

            $this->orderLogDetails->save();

            /* Start of Failure log email*/
            $storeId = $orderArray['store_id'];
            if($storeId==0){
                $scopeType = 'default';
            }else{
                $scopeType = 'stores';
            }
            $sendLogReport = $this->scopeConfigInterface->getValue('amconnectorcommon/log_setting/log_report',$scopeType,$storeId);
            if($sendLogReport && !empty($orderArray['long_message'])) {
                if ($sendLogReport) {
                    $this->helperData->errorLogEmail('Order', $orderArray['long_message']);
                }
            }
            return TRUE;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * function for saving manual sync record in cron scheduler
     */

    public function orderManualSync($orderArray = array())
    {
        if (!$this->cronSchedule)
            $this->cronSchedule = $this->cronScheduleFactory->create();
        try {
            if (isset($orderArray['job_code']))
                $this->cronSchedule->setJobCode($orderArray['job_code']);

            if (isset($orderArray['status']))
                $this->cronSchedule->setStatus($orderArray['status']);

            if (isset($orderArray['messages']))
                $this->cronSchedule->setMessages($orderArray['messages']);

            if (isset($orderArray['created_at']))
                $this->cronSchedule->setCreatedAt($orderArray['created_at']);

            if (isset($orderArray['scheduled_at']))
                $this->cronSchedule->setScheduledAt($orderArray['scheduled_at']);

            if (isset($orderArray['executed_at']))
                $this->cronSchedule->setExecutedAt($orderArray['executed_at']);

            if (isset($orderArray['finished_at']))
                $this->cronSchedule->setFinishedAt($orderArray['finished_at']);

            if (isset($orderArray['run_mode']))
                $this->cronSchedule->setRunMode($orderArray['run_mode']);

            if (isset($orderArray['auto_sync']))
                $this->cronSchedule->setAutoSync($orderArray['auto_sync']);

            if (isset($orderArray['store_id']))
                $this->cronSchedule->setStoreId($orderArray['store_id']);

            $this->cronSchedule->save();

            return $this->cronSchedule->getId();

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}