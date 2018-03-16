<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Kensium\Amconnector\Model\LicensecheckFactory;
use DateTime as phpDateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\AdminNotification\Model\Inbox;
class LicenseValidation
{

    protected $licenseHelper;

    protected $licenseCheckFactory;

    protected $date;

    protected  $_scopeConfig;

    protected  $storeManagerInterface;

    protected $inlineTranslation;

    protected $_transportBuilder;

    protected $adminNotification;

    /**
     * @param Helper\Licensecheck\Proxy $licenseHelper
     * @param LicensecheckFactory $licenseCheckFactory
     * @param DateTime $date
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManagerInterface
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $_transportBuilder
     */
    public function __construct(
        \Kensium\Amconnector\Helper\Licensecheck\Proxy $licenseHelper,
        LicensecheckFactory $licenseCheckFactory,
        DateTime $date,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManagerInterface,
        StateInterface $inlineTranslation,
        TransportBuilder    $_transportBuilder,
        Inbox $adminNotification
    )
    {
        $this->licenseHelper = $licenseHelper;
        $this->date = $date;
        $this->licenseCheckFactory = $licenseCheckFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $_transportBuilder;
        $this->adminNotification = $adminNotification;
    }


    /**
     * CRON FOR VALIDATING LICENSE
     */
    public function licenseValidation()
    {
        $current_date = $this->date->date('Y-m-d');
        $licenseKeyCollection = $this->licenseCheckFactory->create()->getCollection();

        foreach ($licenseKeyCollection as $licenseRecord) {
            $verified_date = $licenseRecord->getData('verified_date');
            $date_arr = explode(' ', $verified_date);
            if ($date_arr[0] != $current_date) {
                $license_key = base64_decode($licenseRecord->getData('license_key'));
                $store_id = $licenseRecord->getData('store_id');
                $status = $this->licenseHelper->checkLicense($license_key, 1, $store_id);
                Mage::log('store id:' . $store_id . ':' . $status);
            } else {
                Mage::log("Today verified already");
            }
        }
    }

    // Send Expire Notification email && add notification in admin side
    public function licenseExpireNotification()
    {
        $current_date = $this->date->date('Y-m-d');
        $current_date = new phpDateTime($current_date);
        $admin_email =  $this->_scopeConfig->getValue('trans_email/ident_general/email');
        $admin_name =  $this->_scopeConfig->getValue('trans_email/ident_general/name');
        $licensekeycollection = Mage::getModel('amconnector/licensecheck')->getCollection();
        foreach ($licensekeycollection as $licenserecord) {

            $response = base64_decode($licenserecord->getData('request_data'));
            $data = json_decode($response);
            $customData = $data->LicenseDetails['0']->CustomData;
            $customDataExplode = explode('||-||', $customData);
            $licenseType = str_replace('LicenseTypeValue=', '', $customDataExplode[4]);
            if ($licenseType == 1) {
                $licenseTypeValue = 'Annual';
            } else if ($licenseType == 2) {
                $licenseTypeValue = 'Perpetual';
            } else {
                $licenseTypeValue = 'Trial';
            }
            if ($licenseTypeValue == 'Annual' || $licenseTypeValue == 'Trial') {

                $date_expires = $data->LicenseDetails['0']->DateExpires;

                $date_expires = new phpDateTime($date_expires);
                $dDiff = $current_date->diff($date_expires);
                $days_expire = $dDiff->days;
                $from = array('email' => $admin_email, 'name' => $admin_name);
                if ($days_expire == 30 || $days_expire == 7 || $days_expire == 3 || $days_expire == 2 || $days_expire == 1) {
                    $store_id = $licenserecord->getData('store_id');
                    $store = $this->storeManagerInterface->getStore($store_id);
                    $store_name = $store->getName();
                    $supportUrl ='';
                    //$supportUrl = Mage::helper("adminhtml")->getUrl('adminhtml/support_support/new');
                    if($days_expire == 30 || $days_expire == 7){
                        $notification_text = "Amconnector license will expire in " . $days_expire . " days for " . $store_name . ". Please Contact Amconnector <a href='". $supportUrl ."'>support team</a> to extend the license";
                        $title = "Amconnector license will expire in " . $days_expire . " days for " . $store_name ;
                        $this->adminNotification->add("notice",$title,$notification_text,$supportUrl);
                        Mage::getModel('adminnotification/inbox')->addNotice($title, $notification_text);
                        try {
                            $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' =>1);
                            $this->inlineTranslation->suspend();
                            $to = array('amits@kensium.com', $title);
                            $emailTemplateVariables = array();
                            $emailTemplateVariables['days_to_expire'] = $days_expire;
                            $emailTemplateVariables['store_id'] = $store_id;
                            $transport = $this->_transportBuilder->setTemplateIdentifier('license_request')
                                ->setTemplateOptions($templateOptions)
                                ->setTemplateVars($emailTemplateVariables)
                                ->setFrom($from)
                                ->addTo($to)
                                ->getTransport();
                            $transport->sendMessage();
                            $this->inlineTranslation->resume();
                        }catch (\Exception $ex){
                            //echo $ex->getMessage();exit;
                        }
                    }

                    elseif($days_expire == 3 || $days_expire == 2 ){
                        $notification_text = "Amconnector license will expire in " . $days_expire . " days for " . $store_name . ". Please Contact Amconnector <a href='".$supportUrl."'>support team</a> to extend the license";
                        $title = "Amconnector license will expire in " . $days_expire . " days for " . $store_name ;
                        try {
                            $this->adminNotification->add("notice",$title,$notification_text,$supportUrl);
                            $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' =>1);
                            $this->inlineTranslation->suspend();
                            $to = array('amits@kensium.com', $title);
                            $emailTemplateVariables = array();
                            $emailTemplateVariables['days_to_expire'] = $days_expire;
                            $emailTemplateVariables['store_id'] = $store_id;
                            $transport = $this->_transportBuilder->setTemplateIdentifier('license_request')
                                ->setTemplateOptions($templateOptions)
                                ->setTemplateVars($emailTemplateVariables)
                                ->setFrom($from)
                                ->addTo($to)
                                ->getTransport();
                            $transport->sendMessage();
                            $this->inlineTranslation->resume();
                        }catch (\Exception $ex){
                            //echo $ex->getMessage();exit;
                        }
                    }

                    elseif($days_expire == 1){
                        $notification_text = "Amconnector license will expire in " . $days_expire . " days for " . $store_name . ". Please Contact Amconnector <a href='". $supportUrl ."'>support team</a> to extend the license";
                        $title = "Amconnector license will expire in " . $days_expire . " days for " . $store_name ;
                        try {
                            $this->adminNotification->add("critical",$title,$notification_text,$supportUrl);
                            $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' =>1);
                            $this->inlineTranslation->suspend();
                            $to = array('amits@kensium.com', $title);
                            $emailTemplateVariables = array();
                            $emailTemplateVariables['days_to_expire'] = $days_expire;
                            $emailTemplateVariables['store_id'] = $store_id;
                            $transport = $this->_transportBuilder->setTemplateIdentifier('license_request')
                                ->setTemplateOptions($templateOptions)
                                ->setTemplateVars($emailTemplateVariables)
                                ->setFrom($from)
                                ->addTo($to)
                                ->getTransport();
                            $transport->sendMessage();
                            $this->inlineTranslation->resume();
                        }catch (\Exception $ex){
                            //echo $ex->getMessage();exit;
                        }
                    }
                }
            };
        }

    }
}
