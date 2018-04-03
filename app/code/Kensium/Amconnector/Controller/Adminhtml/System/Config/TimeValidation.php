<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\Json as JsonFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Kensium\Amconnector\Helper\Time;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Kensium\Amconnector\Model\ResourceModel\Time\Collection;

class TimeValidation extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Time
     */
    protected $timeHelper;

    /**
     * @var Collection
     */
    protected $timeCollection;

    /**
     * @var DateTime
     */
    protected $date;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Time $timeHelper
     * @param DateTime $date
     * @param Collection $timeCollection
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Time $timeHelper,
        DateTime $date,
        Collection $timeCollection
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_storeManager = $storeManager;
        $this->timeHelper = $timeHelper;
        $this->date = $date;
        $this->timeCollection = $timeCollection;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     *
     * $syncStatus values represents..
     * 0 == There is no record in "amconnector_server_timing" table and need to "Verify"
     * 1 == There is 1 record in "amconnector_server_timing" table and Time is in Sync
     * 2 == There is 1 record in "amconnector_server_timing" table and Time is not in Sync
     */
    public function execute()
    {
        $syncStatus = 0; // button_label == Verify
        $timeRecordsCount = $this->timeCollection->count();
        if($timeRecordsCount) {
            $magentoTime = $this->timeHelper->getMagentoTime();
            $magentoTimeStamp = $this->date->timestamp($magentoTime);
            //acumatica Time
            $storeId = $this->_storeManager->getStore()->getStoreId();
            $resAcumaticaTime = $this->timeHelper->getAcumaticaTime($storeId);
            if ($resAcumaticaTime != 0 and is_array($resAcumaticaTime)) {
                $acumaticaTime = $resAcumaticaTime['time'];
                $acumaticaTimeStamp = $this->date->timestamp($acumaticaTime);
                //check time difference
                if($magentoTimeStamp > $acumaticaTimeStamp) {
                    $diff = ($magentoTimeStamp - $acumaticaTimeStamp);
                }else{
                    $diff = ($acumaticaTimeStamp - $magentoTimeStamp);
                }
                $second = 1;
                $minute = 60 * $second;
                $hour = 60 * $minute;
                $day = 24 * $hour;
                $getResult["day"] = floor($diff / $day);
                $getResult["hour"] = floor(($diff % $day) / $hour);
                $getResult["minute"] = floor((($diff % $day) % $hour) / $minute);
                $getResult["second"] = floor(((($diff % $day) % $hour) % $minute) / $second);
                if ($getResult["day"] == 0 && $getResult["hour"] == 0 && $getResult["minute"] == 0 && $getResult["second"] <= 10) {
                    $syncStatus = 1;   // button_label == Verify
                }else{
                    $syncStatus = 2;   // button_label == Sync Now
                }
            }
        }
        $resultJson = $this->resultJsonFactory;
        $result = $resultJson->setData([
            'timeSyncStatus' => $syncStatus
        ]);
        return $result;
    }
}
