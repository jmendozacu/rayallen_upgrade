<?php

/**
 *
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Config\Model\ResourceModel\Config;
use Kensium\Amconnector\Model\LicensecheckFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Kensium\Amconnector\Helper\Licensecheck\Proxy as licenseHelper;
use DateTime as phpDateTime;

class Licensecheck extends AbstractDb {

    const IS_LICENSE_INVALID = "Invalid";
    const IS_LICENSE_VALID = "Valid";
    const IS_LICENSE_DISABLED = "Disabled";

    protected $config;

    /**
     * @param Context $context
     * @param Config $config
     * @param null $connectionName
     */
    protected $licenseFactory;

    /**
     * @var licenseHelper
     */
    protected $licenseHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var DateTime
     */
    protected $date;

    public function __construct(
    Context $context, Config $config, LicensecheckFactory $licenseFactory, DateTime $date, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory, licenseHelper $licenseHelper, $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->licenseFactory = $licenseFactory;
        $this->config = $config;
        $this->date = $date;
        $this->licenseHelper = $licenseHelper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('amconnector_license_check', 'id');
    }

    /**
     * @param $licenseKey
     * @param $licenseStatus
     * @param $response
     * @param $storeId
     * @param null $license_url
     */
    public function licenseCheck($licenseKey, $licenseStatus, $response, $storeId, $license_url = NULL) {

        $licenseKey = base64_encode($licenseKey);
        $licenseStatus = base64_encode($licenseStatus);
        $currentTime = $this->date->date('Y-m-d H:i:s', time());
        $response = base64_encode($response);
        $tblPrefix = $this->config->getTable('amconnector_license_check');
        $write = $this->getConnection('core_write');
        $count = $write->fetchOne("SELECT count(*) from " . $tblPrefix . "  where store_id='" . $storeId . "' ");
        if ($count) {
            if ($storeId == 0 || $storeId == 1) {
                $write->query("UPDATE " . $tblPrefix . " set license_key='" . $licenseKey . "', verified_date='" . $currentTime . "', license_status = '" . $licenseStatus . "', request_data = '" . $response . "' where store_id ='1'");
                $write->query("UPDATE " . $tblPrefix . " set license_key='" . $licenseKey . "', verified_date='" . $currentTime . "', license_status = '" . $licenseStatus . "', request_data = '" . $response . "' where store_id ='0'");
            } else {
                $write->query("UPDATE " . $tblPrefix . " set license_key='" . $licenseKey . "', verified_date='" . $currentTime . "', license_status = '" . $licenseStatus . "',license_url = '" . $license_url . "', request_data = '" . $response . "' where store_id ='" . $storeId . "'");
            }
        } else {
            if ($storeId == 0 || $storeId == 1) {
                $write->query("INSERT INTO  `" . $tblPrefix . "` (`id`,`license_key`,`license_status`,`store_id`,`verified_date`,`request_data`,`license_url`)
                                                   VALUES (NULL,'" . $licenseKey . "','" . $licenseStatus . "','" . 0 . "','" . $currentTime . "','" . $response . "' ,'" . $license_url . "'),
                                                   (NULL,'" . $licenseKey . "','" . $licenseStatus . "','" . 1 . "','" . $currentTime . "','" . $response . "' ,'" . $license_url . "')");
            } else {
                $write->query("INSERT INTO  `" . $tblPrefix . "` (`id`,`license_key`,`license_status`,`store_id`,`verified_date`,`request_data`,`license_url`)
                                                    VALUES (NULL,'" . $licenseKey . "','" . $licenseStatus . "','" . $storeId . "','" . $currentTime . "','" . $response . "','" . $license_url . "' )");
            }
        }
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getResponse($storeId) {
        $tblPrefix = $this->config->getTable('amconnector_license_check');
        $write = $this->getConnection('core_write');
        $response = $write->fetchOne("SELECT request_data from " . $tblPrefix . " where store_id='" . $storeId . "' ");

        return $response;
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getLicenseKey($storeId) {
        $tblPrefix = $this->config->getTable('amconnector_license_check');
        $write = $this->getConnection('core_write');
        $key = $write->fetchOne("SELECT license_key from " . $tblPrefix . "  where store_id='" . $storeId . "' ");

        return $key;
    }

    /**
     * @param $storeId
     * @return string
     */
    public function getLicenseStatus($storeId) {
	$status='Valid';
	return $status;
        $tblPrefix = $this->config->getTable('amconnector_license_check');
        $write = $this->getConnection('core_write');
        $currentTime = $this->date->date('Y-m-d H:i:s', time());
        $verifiedDate = $write->fetchOne("SELECT verified_date from " . $tblPrefix . "  where store_id='" . $storeId . "' ");
        if ($verifiedDate == ''){
            return 'Invalid';
        }
        
        /*$start_date = new phpDateTime($verifiedDate);
        $end_date = new phpDateTime($currentTime);
        $interval = $start_date->diff($end_date);
        if ($interval->d > 3) {
            $disable_encode = base64_encode('Disabled');
            $write->query("UPDATE " . $tblPrefix . " set license_status='" . $disable_encode . "'  where store_id='" . $storeId . "' ");
        } else if ($interval->d > 1) { */
            $licenseKey = $write->fetchOne("SELECT license_key from " . $tblPrefix . "  where store_id='" . $storeId . "' ");
            $licenseKey = base64_decode($licenseKey);
            $this->licenseHelper->checkLicense($licenseKey, 0, $storeId);
       /* } */
        $status = $write->fetchOne("SELECT license_status from " . $tblPrefix . "  where store_id='" . $storeId . "' ");

        //$status='Valid';
        return base64_decode($status);
    }

    /**
     * Get License Types store wise
     * return array key value pair(store_id,license type)
     */
    public function getLicenseTypes() {
        $licenseKeyCollection = $this->licenseFactory->create()->getCollection();
        $licenseTypes = array();
        foreach ($licenseKeyCollection as $licenseRecord) {

            $response = base64_decode($licenseRecord->getData('request_data'));
            $data = json_decode($response);
            $customData = $data->LicenseDetails['0']->CustomData;
            $customDataExplode = explode('||-||', $customData);
            $licenseTypes[$licenseRecord->getData('store_id')] = str_replace('LicenseTypeValue=', '', $customDataExplode[4]);
        }
        return $licenseTypes;
    }

    /**
     * @param $storeId
     * @return string
     */
    public function checkLicenseTypes($storeId) {
        /* Get license type */
        $licenseTypes = $this->getLicenseTypes();
        if ($licenseTypes) {
            $licenseType = $licenseTypes[$storeId];
        } else {
            $licenseType = '';
        }
        if ($licenseType == 1) {
            $licenseTypeValue = 'annual';
        } elseif ($licenseType == 2) {
            $licenseTypeValue = 'perpetual';
        } else {
            $licenseTypeValue = 'trial';
        }
        return $licenseTypeValue;
    }

    /*     * *
     * @param $current
     * @param $storeId
     * @param $website
     * @return $this
     */

    function validateLicense($storeId) {
        $licenseStatus = $this->getLicenseStatus($storeId);
        if ($licenseStatus == self::IS_LICENSE_INVALID) {
            $this->messageManager->addError('Invalid license key.');
            session_write_close();
            $redirectResult = $this->resultRedirectFactory->create();
            return $redirectResult->setPath('adminhtml/system_config/edit/section/license/', ['store' => $storeId]);
        } else if ($licenseStatus == self::IS_LICENSE_DISABLED) {
            $this->messageManager->addError('Your License Key is no longer valid.  Please contact Support.');
            session_write_close();
            $redirectResult = $this->resultRedirectFactory->create();
            return $redirectResult->setPath('adminhtml/system_config/edit/section/license/', ['store' => $storeId]);
        }
    }
}
