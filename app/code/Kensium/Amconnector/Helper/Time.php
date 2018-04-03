<?php
/**
 *
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Webapi\Soap;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Locale\ListsInterface;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Kensium\Amconnector\Model\TimeFactory as TimeFactory;
use Kensium\Amconnector\Helper\Client;
use Kensium\Amconnector\Helper\Sync;
use Kensium\Amconnector\Helper\Url;
use Kensium\Amconnector\Helper\AmconnectorSoap;
use Kensium\Lib;

class Time extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var Timezone
     */
    protected $timeZone;

    /**
     * @var Sync
     */
    protected $syncHelper;

    /**
     * @var Url
     */
    protected $urlHelper;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var TimeFactory
     */
    protected $timeFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var ListsInterface
     */
    protected $localeLists;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Client
     */
    protected $clientHelper;

    /**
     * @var
     */
    protected $xmlHelper;

    const IS_TIME_VALID = "Valid";

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param DateTime $date
     * @param Timezone $timeZone
     * @param Sync $syncHelper
     * @param Url $urlHelper
     * @param Client $clientHelper
     * @param ResourceConnection $resourceConnection
     * @param TimeFactory $timeFactory
     * @param Logger $logger
     * @param ManagerInterface $messageManager
     * @param ModuleDataSetupInterface $setup
     * @param ListsInterface $localeLists
     * @param Config $config
     * @param Xml $xmlHelper
     */
    public function __construct(
        Context $context,
        DateTime $date,
        TimeZone $timeZone,
        Sync $syncHelper,
        Url $urlHelper,
        Client $clientHelper,
        ResourceConnection $resourceConnection,
        TimeFactory $timeFactory,
        ManagerInterface $messageManager,
        ListsInterface $localeLists,
        Config $config,
        \Kensium\Amconnector\Model\ResourceModel\Time $resourceTime,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        Lib\Common $common
    )
    {
        parent::__construct($context);
        $this->clientHelper = $clientHelper;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->date = $date;
        $this->timeZone = $timeZone;
        $this->syncHelper = $syncHelper;
        $this->urlHelper = $urlHelper;
        $this->resourceConnection = $resourceConnection;
        $this->timeFactory = $timeFactory;
        $this->logger = $context->getLogger();
        $this->messageManager = $messageManager;
        $this->localeLists = $localeLists;
        $this->config = $config;
        $this->xmlHelper = $xmlHelper;
        $this->resourceTime = $resourceTime;
        $this->common = $common;
        $this->amconnectorHelper = $dataHelper;
    }


    /**
     * @return string
     */
    public function getMagentoTime()
    {
        global $magentoTime;
        $magentoTime = '';
        if ($magentoTime == '') {
            $date = $this->timeZone->date()->format('Y-m-d H:i:s');
            return $date;
        }
    }

    /**
     * @return mixed|string
     */
    public function getMagentoTimeZone()
    {
        $timeZone = $this->timeZone->getConfigTimezone();
        return $timeZone;
    }

    /**
     * @return array|int
     */
    public function getAcumaticaTime($storeId)
    {
        $acumaticaConnection = 0;
        $timeStart = time();
        $userName = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/userName');
        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
        if(isset($serverUrl) && $serverUrl != '')
        {
            $amconnectorConfigUrl = $this->common->getBasicConfigUrl($serverUrl);
        }
        /*envelope*/
            $csvTime = $this->syncHelper->getEnvelopeData('GETTIMEZONE');
            $XMLGetRequest = $csvTime['envelope'];
            $XMLGetRequest = str_replace("{{USERNAME}}", $userName, $XMLGetRequest);
            $timeAction = $csvTime['envName'] . "/" . $csvTime['envVersion'] . "/" . $csvTime['methodName'];

            /* sending request for get time zone details */
            try {
                $configParameters = $this->amconnectorHelper->getConfigParameters($storeId);
                $response = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $amconnectorConfigUrl, $timeAction);
                $xml = $response;
                $responseTime = $xml->Body->GetResponse->GetResult->ServerTime->Value;
                $time = $this->date->date('Y-m-d H:i:s', $responseTime);
                //calculate actual acumaticaTime
                $acumaticaTimeStamp = $this->date->timestamp($time);
                $timeEnd = time();
                $timeDiff = $timeEnd - $timeStart;
                $actualAcumaticaTimeStamp = $acumaticaTimeStamp - $timeDiff;
                $modifiedAcumaticaTime = $this->date->date('Y-m-d H:i:s', $actualAcumaticaTimeStamp);
                $time = $modifiedAcumaticaTime;
                //timeZone
                $responseTimeZone = $xml->Body->GetResponse->GetResult->TimeZone->Value;
                $timeZone = $this->getTimeZoneValue($responseTimeZone);
                return array('time' => $time, 'timeZone' => $timeZone);
            } catch (Exception $e) {
                $err = 0;
                return $err;
            }
    }

    /**
     * @param $timeDescription
     * @return mixed
     */
    public function getTimeZoneValue($timeDescription)
    {
        $timeZoneList = array(
            '(GMT-11:00) Midway Island, Samoa' => 'Pacific/Midway',
            '(GMT-11:00) Samoa' => 'Pacific/Samoa',
            '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
            '(GMT-09:00) Alaska' => 'US/Alaska',
            '(GMT-08:00) Pacific Time (US & Canada)' => 'America/Los_Angeles',
            '(GMT-08:00) Tijuana, Baja California' => 'America/Tijuana',
            '(GMT-07:00) Arizona' => 'US/Arizona',
            '(GMT-07:00) Chihuahua, La Paz, Mazatlan' => 'America/Chihuahua',
            '(GMT-07:00) La Paz' => 'America/Chihuahua',
            '(GMT-07:00) Mazatlan' => 'America/Mazatlan',
            '(GMT-07:00) Mountain Time (US & Canada)' => 'US/Mountain',
            '(GMT-06:00) Central America' => 'America/Managua',
            '(GMT-06:00) Central Time (US & Canada)' => 'US/Central',
            '(GMT-06:00) Guadalajara, Mexico City, Monterrey' => 'America/Mexico_City',
            '(GMT-03:00) Montevideo' => 'America/Montevideo',
            '(GMT-06:00) Mexico City' => 'America/Mexico_City',
            '(GMT-06:00) Monterrey' => 'America/Monterrey',
            '(GMT-06:00) Saskatchewan' => 'Canada/Saskatchewan',
            '(GMT-05:00) Bogota, Lima, Quito, Rio Branco' => 'America/Bogota',
            '(GMT-05:00) Eastern Time (US & Canada)' => 'US/Eastern',
            '(GMT-04:00) Manaus' => 'America/Manaus',
            '(GMT-05:00) Indiana (East)' => 'US/East-Indiana',
            '(GMT-05:00) Lima' => 'America/Lima',
            '(GMT-05:00) Quito' => 'America/Bogota',
            '(GMT-04:00) Atlantic Time (Canada)' => 'Canada/Atlantic',
            '(GMT-04:30) Caracas' => 'America/Caracas',
            '(GMT-04:00) La Paz' => 'America/La_Paz',
            '(GMT-04:00) Santiago' => 'America/Santiago',
            '(GMT-03:30) Newfoundland' => 'Canada/Newfoundland',
            '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
            '(GMT-03:00) Buenos Aires, Georgetown' => 'America/Argentina/Buenos_Aires',
            '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
            '(GMT-03:00) Greenland' => 'America/Godthab',
            '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
            '(GMT-01:00) Azores' => 'Atlantic/Azores',
            '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
            '(GMT) Casablanca' => 'Africa/Casablanca',
            '(GMT+00:00) Edinburgh' => 'Europe/London',
            '(GMT+00:00) Greenwich Mean Time : Dublin' => 'Etc/Greenwich',
            '(GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London' => 'Europe/Dublin',
            '(GMT+00:00) Lisbon' => 'Europe/Lisbon',
            '(GMT+00:00) London' => 'Europe/London',
            '(GMT) Monrovia, Reykjavik' => 'Africa/Monrovia',
            '(GMT) Universal Standard Time' => 'UTC',
            '(GMT+00:00) UTC' => 'UTC',
            '(GMT+01:00) Amsterdam' => 'Europe/Amsterdam',
            '(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna' => 'Europe/Amsterdam',
            '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
            '(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague' => 'Europe/Belgrade',
            '(GMT+01:00) Berlin' => 'Europe/Berlin',
            '(GMT+01:00) Bern' => 'Europe/Berlin',
            '(GMT+01:00) Bratislava' => 'Europe/Bratislava',
            '(GMT+01:00) Brussels' => 'Europe/Brussels',
            '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris' => 'Europe/Brussels',
            '(GMT+01:00) Budapest' => 'Europe/Budapest',
            '(GMT+01:00) Copenhagen' => 'Europe/Copenhagen',
            '(GMT+01:00) Ljubljana' => 'Europe/Ljubljana',
            '(GMT+01:00) Madrid' => 'Europe/Madrid',
            '(GMT+01:00) Paris' => 'Europe/Paris',
            '(GMT+01:00) Prague' => 'Europe/Prague',
            '(GMT+01:00) Rome' => 'Europe/Rome',
            '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
            '(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb' => 'Europe/Sarajevo',
            '(GMT+01:00) Skopje' => 'Europe/Skopje',
            '(GMT+01:00) Stockholm' => 'Europe/Stockholm',
            '(GMT+01:00) Vienna' => 'Europe/Vienna',
            '(GMT+01:00) Warsaw' => 'Europe/Warsaw',
            '(GMT+01:00) West Central Africa' => 'Africa/Lagos',
            '(GMT+01:00) Zagreb' => 'Europe/Zagreb',
            '(GMT+02:00) Amman' => 'Asia/Amman',
            '(GMT+02:00) Athens' => 'Europe/Athens',
            '(GMT+02:00) Athens, Bucharest, Istanbul' => 'Europe/Athens',
            '(GMT+02:00) Beirut' => 'Asia/Beirut',
            '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
            '(GMT+02:00) Cairo' => 'Africa/Cairo',
            '(GMT+02:00) Harare' => 'Africa/Harare',
            '(GMT+02:00) Harare, Pretoria' => 'Africa/Harare',
            '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
            '(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius' => 'Europe/Helsinki',
            '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
            '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
            '(GMT+02:00) Kyiv' => 'Europe/Helsinki',
            '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
            '(GMT+02:00) Riga' => 'Europe/Riga',
            '(GMT+02:00) Sofia' => 'Europe/Sofia',
            '(GMT+02:00) Tallinn' => 'Europe/Tallinn',
            '(GMT+02:00) Vilnius' => 'Europe/Vilnius',
            '(GMT+02:00) Minsk' => 'Europe/Minsk',
            '(GMT+02:00) Windhoek' => 'Africa/Windhoek',
            '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
            '(GMT+03:00) Kuwait' => 'Asia/Kuwait',
            '(GMT+03:00) Kuwait, Riyadh' => 'Asia/Kuwait',
            '(GMT+03:00) Minsk' => 'Europe/Minsk',
            '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
            '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
            '(GMT+03:00) Volgograd' => 'Europe/Volgograd',
            '(GMT+03:30) Tehran' => 'Asia/Tehran',
            '(GMT+03:00) Moscow, St. Petersburg, Volgograd' => 'Europe/Moscow',
            '(GMT+03:00) Tbilisi' => 'Asia/Tbilisi',
            '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
            '(GMT+04:00) Abu Dhabi, Muscat' => 'Asia/Muscat',
            '(GMT+04:00) Baku' => 'Asia/Baku',
            '(GMT+04:00) Moscow' => 'Europe/Moscow',
            '(GMT+04:00) Muscat' => 'Asia/Muscat',
            '(GMT+04:00) St. Petersburg' => 'Europe/Moscow',
            '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
            '(GMT+04:00) Yerevan' => 'Asia/Yerevan',
            '(GMT+04:00) Caucasus Standard Time' => 'Asia/Yerevan',
            '(GMT+04:30) Kabul' => 'Asia/Kabul',
            '(GMT+05:00) Islamabad' => 'Asia/Karachi',
            '(GMT+05:00) Islamabad, Karachi' => 'Asia/Karachi',
            '(GMT+05:00) Karachi' => 'Asia/Karachi',
            '(GMT+05:00) Tashkent' => 'Asia/Tashkent',
            '(GMT+05:30) Chennai' => 'Asia/Calcutta',
            '(GMT+05:30) Kolkata' => 'Asia/Kolkata',
            '(GMT+05:30) Mumbai' => 'Asia/Calcutta',
            '(GMT+05:30) New Delhi' => 'Asia/Calcutta',
            '(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi' => 'Asia/Kolkata',
            '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
            '(GMT+06:00) Almaty' => 'Asia/Almaty',
            '(GMT+06:00) Almaty, Novosibirsk' => 'Asia/Almaty',
            '(GMT+06:00) Astana' => 'Asia/Dhaka',
            '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
            '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
            '(GMT+06:00) Ekaterinburg' => 'Asia/Yekaterinburg',
            '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
            '(GMT+06:30) Yangon (Rangoon)' => 'Asia/Rangoon',
            '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
            '(GMT+07:00) Bangkok, Hanoi, Jakarta' => 'Asia/Bangkok',
            '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
            '(GMT+07:00) Hanoi' => 'Asia/Bangkok',
            '(GMT+07:00) Jakarta' => 'Asia/Jakarta',
            '(GMT+07:00) Novosibirsk' => 'Asia/Novosibirsk',
            '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
            '(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi' => 'Asia/Hong_Kong',
            '(GMT+08:00) Chongqing' => 'Asia/Chongqing',
            '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
            '(GMT+08:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
            '(GMT+08:00) Kuala Lumpur' => 'Asia/Kuala_Lumpur',
            '(GMT+08:00) Kuala Lumpur, Singapore' => 'Asia/Kuala_Lumpur',
            '(GMT+08:00) Perth' => 'Australia/Perth',
            '(GMT+08:00) Singapore' => 'Asia/Singapore',
            '(GMT+08:00) Taipei' => 'Asia/Taipei',
            '(GMT+08:00) Ulaan Bataar' => 'Asia/Ulan_Bator',
            '(GMT+08:00) Irkutsk, Ulaan Bataar' => 'Asia/Irkutsk',
            '(GMT+08:00) Urumqi' => 'Asia/Urumqi',
            '(GMT+09:00) Irkutsk' => 'Asia/Irkutsk',
            '(GMT+09:00) Osaka' => 'Asia/Tokyo',
            '(GMT+09:00) Osaka, Sapporo, Tokyo' => 'Asia/Tokyo',
            '(GMT+09:00) Sapporo' => 'Asia/Tokyo',
            '(GMT+09:00) Seoul' => 'Asia/Seoul',
            '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
            '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
            '(GMT+09:30) Darwin' => 'Australia/Darwin',
            '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
            '(GMT+10:00) Canberra' => 'Australia/Canberra',
            '(GMT+10:00) Canberra, Melbourne, Sydney' => 'Australia/Canberra',
            '(GMT+10:00) Guam' => 'Pacific/Guam',
            '(GMT+10:00) Guam, Port Moresby' => 'Pacific/Guam',
            '(GMT+10:00) Hobart' => 'Australia/Hobart',
            '(GMT+10:00) Melbourne' => 'Australia/Melbourne',
            '(GMT+10:00) Port Moresby' => 'Pacific/Port_Moresby',
            '(GMT+10:00) Sydney' => 'Australia/Sydney',
            '(GMT+10:00) Yakutsk' => 'Asia/Yakutsk',
            '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
            '(GMT+11:00) Vladivostok' => 'Asia/Vladivostok',
            '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
            '(GMT+12:00) Auckland' => 'Pacific/Auckland',
            '(GMT+12:00) Fiji' => 'Pacific/Fiji',
            '(GMT+12:00) International Date Line West' => 'Pacific/Kwajalein',
            '(GMT+12:00) Kamchatka' => 'Asia/Kamchatka',
            '(GMT+12:00) Kamchatka, Marshall Is.' => 'Asia/Kamchatka',
            '(GMT+12:00) Magadan' => 'Asia/Magadan',
            '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
            '(GMT+12:00) New Caledonia' => 'Asia/Magadan',
            '(GMT+12:00) Solomon Is.' => 'Asia/Magadan',
            '(GMT+11:00) Solomon Is., New Caledonia' => 'Asia/Magadan',
            '(GMT+11:00) Magadan' => 'Asia/Magadan',
            '(GMT+12:00) Wellington' => 'Pacific/Auckland',
            '(GMT+12:00) Auckland, Wellington' => 'Pacific/Auckland',
            '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu',
        );

        foreach ($timeZoneList as $key => $val) {
            if ($timeDescription == $key) {
                return $val;
            }
        }
    }

    /**
     * @param $hms
     * @return int
     */
    public function hms2sec($hms)
    {
        list($h, $m, $s) = explode (":", $hms);
        $seconds = 0;
        $seconds += (intval($h) * 3600);
        $seconds += (intval($m) * 60);
        $seconds += (intval($s));
        return $seconds;
    }

    /**
     * @param null $storeId
     * @return string
     * Check Acumatica Time is synced with magento or not
     */
    public function timeSyncCheck($storeId = NULL)
    {
	return "Valid";
        if($storeId == NULL)
        {
            $storeId = $this->syncHelper->getCurrentStoreId();
        }
        if($storeId == 1)
            $storeId = 0;
        $timeStart = time();
        $timeMagento = $this->getMagentoTime();//magento Time
        $getAcumaticaTime = $this->getAcumaticaTime($storeId);// Acumatica Time By Store Wise
        if ($getAcumaticaTime != 0) {
            $dateTimeMagento = new \DateTime($timeMagento);
            $dateTimeAcumatica = new \DateTime($getAcumaticaTime['time']);

            $timeMagentoZone = $this->getMagentoTimeZone();// Magento Time Zone
            $acumaticaTimezone = $getAcumaticaTime['timeZone'];//Acumatic time zone
            $timeEnd = time();
            $timeDiff = ($timeEnd - $timeStart)/2;
            $magentoTimestamp = $dateTimeMagento->getTimestamp();
            $acumaticaTimestamp = $dateTimeAcumatica->getTimestamp() - $timeDiff;
            /**
             * need to modify the Acumatica time in order to match time with magento
             */
            $differenceString = substr($getAcumaticaTime['time'],-6);
            if (strpos($differenceString, '+') !== false)
            {
                $differenceData = explode('+',$differenceString);
                $acumaticaTimestamp = $acumaticaTimestamp + $this->hms2sec($differenceData[1]);
            }elseif(strpos($differenceString, '-') !== false)
            {
                $differenceData = explode('-',$differenceString);
                $acumaticaTimestamp = $acumaticaTimestamp - $this->hms2sec($differenceData[1]);
            }
            $actualAcumaticaTime = $this->date->date('Y-m-d H:i:s', $acumaticaTimestamp);
            if($magentoTimestamp > $acumaticaTimestamp) {
                $diff = ($magentoTimestamp - $acumaticaTimestamp);
            }else{
                $diff = ($acumaticaTimestamp - $magentoTimestamp);
            }
            $ActualAcumaticaTime = $this->date->date('Y-m-d H:i:s', $acumaticaTimestamp);
            $second = 1;
            $minute = 60 * $second;
            $hour = 60 * $minute;
            $day = 24 * $hour;
            $getResult["day"] = floor($diff / $day);
            $getResult["hour"] = floor(($diff % $day) / $hour);
            $getResult["minute"] = floor((($diff % $day) % $hour) / $minute);
            $getResult["second"] = floor(((($diff % $day) % $hour) % $minute) / $second);


            // check if amconnector_server_timing tableexists
            if ($this->resourceTime->isExists() == true) {
                $resultArray = $this->resourceTime->getData($storeId);
                if (is_array($resultArray)) {
                    $count = count($resultArray);
                }
            }

            if ($count && $getResult["day"] == 0 && $getResult["hour"] == 0 && $getResult["minute"] == 0 && $getResult["second"] <= 12)
            {
                $values = array('magento_time' => $timeMagento, 'magento_timezone' => $timeMagentoZone, 'accumatica_time' => $ActualAcumaticaTime, 'accumatica_timezone' => $acumaticaTimezone, 'verified_status' => 'synced');
                if($storeId == 0 || $storeId == 1)
                {
                    $this->resourceTime->updateTime($values,'update');
                }else {
                    $this->resourceTime->update($values,$storeId);
                }
                $timeStatus = "Valid";
                return $timeStatus;
            } else {
                $timeStatus = "Invalid";
                return $timeStatus;
            }
        }
    }


    /**
     * @param int $autoSyncStatus
     * @param string $orgScope
     * @param int $scopeId
     * @return int
     */
    public function getSyncTime($autoSyncStatus = 0, $orgScope = 'default', $scopeId = 0)
    {
        $timeStart = time();
        $resAcumaticaTime = $this->getAcumaticaTime($scopeId);
        $syncAction = 0;
        if ($resAcumaticaTime != 0) {
            //magento Time
            $magentoTime = $this->getMagentoTime();
            $magentoTimeZone = $this->getMagentoTimeZone();
            $magentoTimeStamp = $this->date->timestamp($magentoTime);
            $timeEnd = time();
            $timeDiff = ($timeEnd - $timeStart)/2;
            //acumatica Time
            if ($resAcumaticaTime != 0 and is_array($resAcumaticaTime)) {
                $acumaticaTime = $resAcumaticaTime['time'];
                $acumaticaTimeZone = $resAcumaticaTime['timeZone'];
                $acumaticaTimeStamp = $this->date->timestamp($acumaticaTime) - $timeDiff;
                //check time difference
                if($magentoTimeStamp > $acumaticaTimeStamp) {
                    $diff = ($magentoTimeStamp - $acumaticaTimeStamp);
                }else{
                    $diff = ($acumaticaTimeStamp - $magentoTimeStamp);
                }
                $acumaticaTime = $this->date->date('Y-m-d H:i:s', $acumaticaTimeStamp);
                $second = 1;
                $minute = 60 * $second;
                $hour = 60 * $minute;
                $day = 24 * $hour;
                $getResult["day"] = floor($diff / $day);
                $getResult["hour"] = floor(($diff % $day) / $hour);
                $getResult["minute"] = floor((($diff % $day) % $hour) / $minute);
                $getResult["second"] = floor(((($diff % $day) % $hour) % $minute) / $second);
                //get the details of Table "amconnector_server_timing" starts here
                $count=count($this->timeFactory->create()->getCollection());

                if ($getResult["day"] == 0 && $getResult["hour"] == 0 && $getResult["minute"] == 0 && $getResult["second"] <= 10) {
                    if ($count) {
                        $values = array('magento_time' => $magentoTime, 'magento_timezone' => $magentoTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_at' => '', 'verified_status' => 'synced', 'scope_id' => $scopeId);
                        if($scopeId == 0 || $scopeId == 1)
                        {
                            $this->resourceTime->updateTime($values,'update');
                        }else {
                            $this->resourceTime->update($values, $scopeId);
                        }
                    } else {
                        if($scopeId == 0 || $scopeId == 1)
                        {
                            $values = array('magento_time' => $magentoTime, 'magento_timezone' => $magentoTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_status' => 'synced', 'scope_id' => $scopeId);
                            $this->resourceTime->updateTime($values,'insert');

                        }else {
                            $values = array('magento_time' => $magentoTime, 'magento_timezone' => $magentoTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_status' => 'synced', 'scope_id' => $scopeId);
                            $this->resourceTime->insert($values);
                        }
                    }

                    $this->messageManager->addSuccess("Magento server time synced with Acumatica server successfully!");
                    $syncAction = "1";
                }
                else {
                    $flag = false;
                    $timeZones = $this->localeLists->getOptionTimezones();
                    if ($count) {


                        foreach ($timeZones as $timeZone) {
                            if ($timeZone['value'] == $acumaticaTimeZone) {
                                $this->config->saveConfig('general/locale/timezone', $timeZone['value'], $orgScope, $scopeId);
                                shell_exec('sudo date -s "' . $acumaticaTime . '" 2>&1');
                                if($scopeId == 0 || $scopeId == 1)
                                {
                                    $values = array('magento_time' => $acumaticaTime, 'magento_timezone' => $acumaticaTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_at' => '', 'verified_status' => 'synced');
                                    $this->resourceTime->update($values, $scopeId);

                                }else {
                                    $values = array('magento_time' => $acumaticaTime, 'magento_timezone' => $acumaticaTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_at' => '', 'verified_status' => 'synced', 'scope_id' => $scopeId);
                                    $this->resourceTime->insert($values);
                                }
                                $flag = true;
                                $syncAction = 1;
                            }
                        }

                    } else {
                        foreach ($timeZones as $timeZone) {
                            if ($timeZone['value'] == $acumaticaTimeZone) {
                                $this->config->saveConfig('general/locale/timezone', $timeZone['value'], $orgScope, $scopeId);
                                shell_exec('sudo date -s "' . $acumaticaTime . '" 2>&1');
                                if($scopeId == 0 || $scopeId == 1)
                                {

                                    $values = array('magento_time' => $magentoTime, 'magento_timezone' => $magentoTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_at' => '', 'verified_status' => 'synced');
                                    $this->resourceTime->update($values,$scopeId);

                                }else {
                                    $values = array('magento_time' => $magentoTime, 'magento_timezone' => $magentoTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_at' => '', 'verified_status' => 'synced', 'scope_id' => $scopeId);
                                    $this->resourceTime->insert($values);
                                }
                                $flag = true;
                                $syncAction = "1";
                            }
                        }
                    }
                    if (!$flag) {
                        $this->messageManager->addError("Magento server time does not match with Acumatica server time");
                        $syncAction = "0";
                    }else{
                        $this->messageManager->addSuccess("Magento server time synced with Acumatica server successfully!");
                    }
                }
            }
        }
        //$this->clientHelper->clearCache();
        return $syncAction;
    }

    /**
     * @param string $orgScope
     * @param int $scopeId
     * @return int
     */
    public function getVerifyTime($scopeId = 0,$flag = NULL)
    {
        $syncAction = 0;
        //magento Time
        $timeStart = time();
        $magentoTime = $this->getMagentoTime();
        $magentoTimeZone = $this->getMagentoTimeZone();
        $magentoTimeStamp = $this->date->timestamp($magentoTime);
        $timeEnd = time();
        //acumatica Time
        $timeDiff = ($timeEnd - $timeStart)/2;
        $resAcumaticaTime = $this->getAcumaticaTime($scopeId);
        if ($resAcumaticaTime != 0 and is_array($resAcumaticaTime)) {
            $acumaticaTime = $resAcumaticaTime['time'];
            $acumaticaTimeZone = $resAcumaticaTime['timeZone'];
            $acumaticaTimeStamp = $this->date->timestamp($acumaticaTime) - $timeDiff;
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
            //get the details of Table "amconnector_server_timing" starts here

            // check if amconnector_server_timing tableexists
            if ($this->resourceTime->isExists() == true) {
                $resultArray = $this->resourceTime->getData($scopeId);
                if (is_array($resultArray)) {
                    $count = count($resultArray);
                }
            }
            if ($getResult["day"] == 0 && $getResult["hour"] == 0 && $getResult["minute"] == 0 && $getResult["second"] <= 10) {
                if ($count) {
                    $values = array('magento_time' => $magentoTime, 'magento_timezone' => $magentoTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_status' => 'synced');
                    if($scopeId == 0 || $scopeId == 1)
                    {
                        $this->resourceTime->updateTime($values,'update');
                    }else {
                        $this->resourceTime->update($values, $scopeId);
                    }
                    if(empty($flag)) {
                        $this->messageManager->addSuccess('Magento server time verified with Acumatica server successfully!');
                    }

                    $syncAction = "1";
                } else {
                    if($scopeId == 0 || $scopeId == 1)
                    {

                        $values = array('magento_time' => $magentoTime, 'magento_timezone' => $magentoTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_status' => 'synced');
                        $this->resourceTime->updateTime($values,'insert');

                    }else {
                        $values = array('magento_time' => $magentoTime, 'magento_timezone' => $magentoTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_status' => 'synced', 'scope_id' => $scopeId);
                        $this->resourceTime->insert($values);
                    }
                    if(empty($flag)) {
                        $this->messageManager->addSuccess('Magento server time verified with Acumatica server successfully!');
                    }
                    $syncAction = "1";
                }
            }
            else {
                if ($count == 0) {
                    if($scopeId == 0 || $scopeId == 1)
                    {

                        $values = array('magento_time' => $magentoTime, 'magento_timezone' => $magentoTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_status' => 'not-synced');
                        $this->resourceTime->updateTime($values,'insert');

                    }else {
                        $values = array('magento_time' => $magentoTime, 'magento_timezone' => $magentoTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_at' => '', 'verified_status' => 'not-synced', 'scope_id' => $scopeId);
                        $this->resourceTime->insert($values);
                    }
                    if(empty($flag)) {
                        $this->messageManager->addError('Magento server time does not match with Acumatica server time');
                    }
                    $syncAction = "0";
                } else {
                    $values = array('magento_time' => $magentoTime, 'magento_timezone' => $magentoTimeZone, 'accumatica_time' => $acumaticaTime, 'accumatica_timezone' => $acumaticaTimeZone, 'verified_status' => 'not-synced');
                    if($scopeId == 0 || $scopeId == 1)
                    {

                        $this->resourceTime->updateTime($values,'update');;
                    }else {
                        $this->resourceTime->insert($values);
                    }
                    if(empty($flag)) {
                        $this->messageManager->addError('Magento server time does not match with Acumatica server time');
                    }
                    $syncAction = "0";
                }
            }
        }else {
            $this->messageManager->addError("Test connection failed. Please try again.");
            $syncAction = '0';
        }
        return $syncAction;
    }

}
