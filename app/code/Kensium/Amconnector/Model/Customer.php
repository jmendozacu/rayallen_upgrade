<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Model;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Config\Definition\Exception\Exception;
use Kensium\Amconnector\Helper\Sync as AmSync;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Kensium\Lib;

class Customer extends \Magento\Framework\Model\AbstractModel
{
    public $errorCheckInMagento = array();
    public $errorCheckInAcumatica = array();
    public $streetLength = 45;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var Timezone
     */
    protected $timeZone;

    /**
     * @var ScopeConfigInterface
     */

    protected  $scopeConfigInterface;
    /**
     * @var Sync
     */
    protected $helper;

    /**
     * @var ResourceConnection|\Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;


    /**
     * @var \Kensium\Amconnector\Model\TimeFactory
     */
    protected $timeFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Customer
     */
    protected $resource;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $resourceModelSync;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Sales\Model\Order\AddressFactory
     */
    protected $orderAddressFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Kensium\Amconnector\Helper\Client
     */
    protected $clientHelper;
    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Kensium\Amconnector\Helper\Customer
     */
    protected $amconnectorCustomerHelper;

    /**
     * @var Xml
     */
    protected $xmlHelper;

    protected $branchCodes;

    protected $urlHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param ResourceModel\Customer\Collection $resourceCollection
     * @param DateTime $date
     * @param AmSync $helper
     * @param Timezone $timezone
     * @param \Kensium\Amconnector\Helper\Customer $amconnectorCustomerHelper
     * @param \Kensium\Amconnector\Helper\Xml $xmlHelper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param TimeFactory $timeFactory
     * @param ResourceModel\Customer $resource
     * @param ResourceModel\Sync $resourceModelSync
     * @param \Kensium\Amconnector\Helper\Data $amconnectorHelper
     * @param \Kensium\Amconnector\Helper\Url $urlHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Order\AddressFactory $orderAddressFactory
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Kensium\Amconnector\Helper\Client $clientHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScopeConfigInterface $scopeConfigInterface,
        \Kensium\Amconnector\Model\ResourceModel\Customer\Collection $resourceCollection = null,
        DateTime $date,
        AmSync $helper,
        Timezone $timezone,
        \Kensium\Amconnector\Helper\Customer $amconnectorCustomerHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Model\TimeFactory $timeFactory,
        \Kensium\Amconnector\Model\ResourceModel\Customer $resource,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        \Kensium\Amconnector\Helper\Data $amconnectorHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\AddressFactory $orderAddressFactory,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        Lib\Common $common,
        $data = array()
    )
    {

        $this->date = $date;
        $this->helper = $helper;
        $this->amconnectorCustomerHelper = $amconnectorCustomerHelper;
        $this->timeFactory = $timeFactory;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->resource = $resource;
        $this->resourceModelSync = $resourceModelSync;
        $this->amconnectorHelper = $amconnectorHelper;
        $this->xmlHelper = $xmlHelper;
        $this->timezone = $timezone;
        $this->logger = $context->getLogger();
        $this->urlHelper = $urlHelper;
        $this->messageManager = $messageManager;
        $this->customerRepository=$customerRepository;
        $this->customerFactory=$customerFactory;
        $this->orderFactory=$orderFactory;
        $this->orderAddressFactory=$orderAddressFactory;
        $this->storeRepository = $storeRepository;
        $this->addressFactory=$addressFactory;
        $this->orderRepository=$orderRepository;
        $this->clientHelper=$clientHelper;
        $this->common = $common;
        //$this->branchCodes =  array(1=>'MAIN',2=>"EAST",3=>"NORTH",4=>"SOUTH",5=>"WEST",6=>"RAPIDBYTE");
        $this->branchCodes =  array(1=>'RA',2=>"JJ",3=>"RA", 4=>"GN", 5=>"RA");
        parent::__construct($context,$registry,$resource,$resourceCollection,$data = array());
    }

    /**
     * constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Kensium\Amconnector\Model\ResourceModel\Customer');
    }

    /**
     * @param $aData
     * @param $mappingAttributes
     * @param $customerLog
     * @param $storeId
     * @param null $logViewFileName
     * @param $customerLogHelper
     * @param $directionFlag
     * @return array
     */
    public function syncToMagento($aData, $mappingAttributes, $customerLog,  $storeId, $logViewFileName = NULL,$customerLogHelper,$directionFlag)
    {
        /**
         * Here we are preparing an array based on the mapping attribute
         */
        if (!is_array($aData)) {
            $aData = json_decode(json_encode($aData), 1);
        }
        $websiteId =  $this->storeRepository->getById($storeId)->getWebsiteId();
        $syncData = array();
        $billingData = array();
        $shippingData = array();
        /**
         * if both magento id and acumatica id not null then check for sync direction
         */
        if (isset($aData['BillingContactSameAsMain']['Value']) && $aData['BillingContactSameAsMain']['Value'] != 'true') {
            $billingCompany = $aData['BillingContactSameAsMain']['Value'];
        }

        if (isset($aData['ShippingContactSameAsMain']['Value']) && $aData['ShippingContactSameAsMain']['Value'] != 'true') {
            $shippingCompany = $aData['ShippingContactSameAsMain']['Value'];
        }
        foreach ($mappingAttributes as $key => $value) {
            $mappingData = explode('|',$value);
            if($directionFlag && $mappingData[1] == 'Bi-Directional (Magento Wins)'){
                continue;
            }
            if(isset($mappingData[0]) && $mappingData[0] != '') {
                $acumaticaLabel =  $this->resource->getAcumaticaAttrCode($mappingData[0]);
            }
            $acumaticaAttrCode = explode(" ", $acumaticaLabel); //array[0] will be section and array[1] will be attribute code
            if (isset($acumaticaAttrCode[0]) &&  $acumaticaAttrCode[0] == "CustomerSchema") {
                if (isset($aData[$acumaticaAttrCode[1]]['Value'])) {
                    $acumaticaFieldValue = $aData[$acumaticaAttrCode[1]]['Value'];
                    if (isset($aData['BillingAddressSameAsMain']['Value']) && $aData['BillingAddressSameAsMain']['Value'] != 'true') {
                        $billing = $aData[$acumaticaAttrCode[1]]['Value'];
                    } else {
                        $billing = '';
                    }
                    if (isset($aData['ShippingAddressSameAsMain']['Value']) && $aData['ShippingAddressSameAsMain']['Value'] != 'true') {
                        $shipping = $aData[$acumaticaAttrCode[1]]['Value'];
                    } else {
                        $shipping = '';
                    }
                } else {
                    $acumaticaFieldValue = '';
                }
            } elseif (isset($acumaticaAttrCode[0]) && $acumaticaAttrCode[0] == "MainAddress") {
                if (isset($aData['MainContact']['Address'][$acumaticaAttrCode[1]]['Value'])) {
                    $acumaticaFieldValue = $aData['MainContact']['Address'][$acumaticaAttrCode[1]]['Value'];
                    if (isset($aData['BillingAddressSameAsMain']['Value']) && $aData['BillingAddressSameAsMain']['Value'] != 'true') {
                        $billing = $aData['BillingContact']['Address'][$acumaticaAttrCode[1]]['Value'];
                    } else {
                        $billing = '';
                    }
		    $shipping = '';
                    if (isset($aData['ShippingAddressSameAsMain']['Value']) && $aData['ShippingAddressSameAsMain']['Value'] != 'true') {
			if(isset($aData['ShippingContact']['Address'][$acumaticaAttrCode[1]]['Value'])){
                           $shipping = $aData['ShippingContact']['Address'][$acumaticaAttrCode[1]]['Value'];
			}
          	}
                }else {
                    $acumaticaFieldValue = '';
                }
                if(isset($aData['BillingContact']['Address'][$acumaticaAttrCode[1]]['Value']) || isset($aData['ShippingContact']['Address'][$acumaticaAttrCode[1]]['Value']))
                {
                    if (isset($aData['BillingAddressSameAsMain']['Value']) && $aData['BillingAddressSameAsMain']['Value'] != 'true') {
                        $billing = $aData['BillingContact']['Address'][$acumaticaAttrCode[1]]['Value'];
                    } else {
                        $billing = '';
                    }
		    $shipping = '';
                    if (isset($aData['ShippingAddressSameAsMain']['Value']) && $aData['ShippingAddressSameAsMain']['Value'] != 'true') {
			if(isset($aData['ShippingContact']['Address'][$acumaticaAttrCode[1]]['Value'])){
                           $shipping = $aData['ShippingContact']['Address'][$acumaticaAttrCode[1]]['Value'];
                        }
                    }
                }else{
                    if(!isset($aData['BillingContact']['Address'][$acumaticaAttrCode[1]]['Value']))
                        $billing = '';

                   if(!isset($aData['ShippingContact']['Address'][$acumaticaAttrCode[1]]['Value']))
                        $shipping = '';
                }
            } elseif (isset($acumaticaAttrCode[0]) && $acumaticaAttrCode[0] == "MainContact") {
                if($acumaticaAttrCode[1] == "CompanyName")
                    $acumaticaAttrCode[1] = "DisplayName";
                if(isset($aData[$acumaticaAttrCode[0]][$acumaticaAttrCode[1]]['Value']))
                    $acumaticaFieldValue = $aData[$acumaticaAttrCode[0]][$acumaticaAttrCode[1]]['Value'];
                if (isset($aData['BillingAddressSameAsMain']['Value']) && $aData['BillingAddressSameAsMain']['Value'] != 'true') {
                    if(isset($aData['BillingContact'][$acumaticaAttrCode[1]]['Value'])){
                        $billing = $aData['BillingContact'][$acumaticaAttrCode[1]]['Value'];
                    }
                } else {
                    if(isset($aData[$acumaticaAttrCode[0]][$acumaticaAttrCode[1]]['Value']))
                        $billing = $aData[$acumaticaAttrCode[0]][$acumaticaAttrCode[1]]['Value'];
                }
                if (isset($aData['ShippingAddressSameAsMain']['Value']) && $aData['ShippingAddressSameAsMain']['Value'] != 'true') {
                    if(isset($aData['ShippingContact'][$acumaticaAttrCode[1]]['Value'])) {
                        $shipping = $aData['ShippingContact'][$acumaticaAttrCode[1]]['Value'];
                    }
                } else {
                    if(isset($aData[$acumaticaAttrCode[0]][$acumaticaAttrCode[1]]['Value']))
                        $shipping = $aData[$acumaticaAttrCode[0]][$acumaticaAttrCode[1]]['Value'];
                }
            } else {
                if (isset($aData[$acumaticaAttrCode[0]][$acumaticaAttrCode[1]]['Value'])) {
                    $acumaticaFieldValue = $aData[$acumaticaAttrCode[0]][$acumaticaAttrCode[1]]['Value'];
                    if (isset($aData['BillingAddressSameAsMain']['Value']) && $aData['BillingAddressSameAsMain']['Value'] != 'true') {
                        $billing = $aData['BillingContact'][$acumaticaAttrCode[1]]['Value'];
                    } else {
                        $billing = '';
                    }
                    if (isset($aData['ShippingAddressSameAsMain']['Value']) && $aData['ShippingAddressSameAsMain']['Value'] != 'true') {
                        $shipping = $aData['ShippingContact'][$acumaticaAttrCode[1]]['Value'];
                    } else {
                        $shipping = '';
                    }
                } else {
                    $acumaticaFieldValue = '';
                }
            }
            if ($key == "firstname") {
                if (isset($acumaticaFieldValue) && $acumaticaFieldValue != '') {
                    $firstName = explode(' ', $acumaticaFieldValue);
                    $acumaticaFieldValue = $firstName[0];
                } else {
                    $acumaticaFieldValue = "";
                }
                if (isset($billing) && $billing != '') {
                    $firstName = explode(' ', $billing);
                    $billing = $firstName[0];
                } else {
                    $billing = "";
                }
                if (isset($shipping) && $shipping != '') {
                    $firstName = explode(' ', $shipping);
                    $shipping = $firstName[0];
                } else {
                    $shipping = "";
                }
            }
            if ($key == "lastname") {
                if (isset($acumaticaFieldValue) && $acumaticaFieldValue != '') {
                    $lastName = explode(' ', $acumaticaFieldValue);
                    if (count($lastName) == 1) {
                        $acumaticaFieldValue = ".";
                    } else {
                        $position = strpos($acumaticaFieldValue, ' ');
                        $name = substr($acumaticaFieldValue, $position);
                        $acumaticaFieldValue = trim($name);
                    }
                } else {
                    $acumaticaFieldValue = "";
                }
                if (isset($billing) && $billing != '') {
                    $lastName = explode(' ', $billing);
                    if (count($lastName) == 1) {
                        $billing = ".";
                    } else {
                        $position = strpos($billing, ' ');
                        $name = substr($billing, $position);
                        $billing = trim($name);
                    }
                } else {
                    $billing = "";
                }
                if (isset($shipping) && $shipping != '') {
                    $lastName = explode(' ', $shipping);
                    if (count($lastName) == 1) {
                        $shipping = ".";
                    } else {
                        $position = strpos($shipping, ' ');
                        $name = substr($shipping, $position);
                        $shipping = trim($name);
                    }
                } else {
                    $shipping = "";
                }
            }
            $syncData[$key] = $acumaticaFieldValue;
            if (isset($aData['BillingAddressSameAsMain']['Value']) && $aData['BillingAddressSameAsMain']['Value'] != 'true') {
                $billingData[$key] = $billing;
            }
            if (isset($aData['ShippingAddressSameAsMain']['Value']) && $aData['ShippingAddressSameAsMain']['Value'] != 'true') {
                $shippingData[$key] = $shipping;
            }
        }
        if (isset($aData['CustomerID']['Value']) ) {
            $syncData['acumatica_customer_id'] = $aData['CustomerID']['Value'];
        }

        $statusFlg = false;
        if(isset($aData['Status']['Value']) && strtolower($aData['Status']['Value']) == "active")
        {
            $statusFlg = true;
        }
        $customerEmail = $customerFirtsName = $customerLastName = '';
        if(isset($syncData['email']))
            $customerEmail = $syncData['email'];
        if(isset($syncData['firstname']))
            $customerFirtsName = $syncData['firstname'];
        if(isset($syncData['lastname']))
            $customerLastName = $syncData['lastname'];

        if( isset($syncData['acumatica_customer_id']) && $syncData['acumatica_customer_id'] && count($syncData) > 1 && $statusFlg){
            if ($customerEmail == '' || $customerFirtsName == '' || $customerLastName == '' ) {
                /**
                 * logs here for customer required fields
                 */
                $customerLog['description'] = "Required Fields Are Empty To Sync Customer"; //Descripton
                $customerLog['syncAction'] = "Customer Not Synced";
                $customerLog['accumaticaCustomerId'] = $syncData['acumatica_customer_id'];
                $customerLog['syncDirection'] = "syncToMagento";
                $customerLog['messageType'] = "Failure";
                $customerLogHelper->customerSyncSuccessLogs($customerLog);
                $txt = $this->timezone->date(time())->format('Y-m-d H:i:s')  . " : Error : Accumatica Customer ID : " . $customerLog['accumaticaCustomerId'] . " : " . $customerLog['description'];
                file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                $this->errorCheckInMagento[] = 1;
            } else {
                try {
                    $syncData['website_id'] = $websiteId;
                    $customerId = $this->resource->getcustomerIdByAcmId($syncData['acumatica_customer_id'],$attributeCode='acumatica_customer_id',$websiteId);
                    if(!isset($customerId) || $customerId == '')
                    {
                        $customerId =  $this->resource->getCustomerIdByEmail($customerEmail, $websiteId);
                    }
                    $customAttributes = array('1'=>'custsum_customerid','2'=>'compinfo_businessname','3'=>'mainaddr_addressline1','4'=>'acstng_custclass','5'=>'shippstr_shpbrch','6'=>'billtoinfo_attention','7'=>'acumatica_customer_id','8'=>'wwss');
                    if ($customerId) {
                        $customer = $this->updateCustomerToMagento($customerId, $customerFirtsName, $customerLastName, $syncData, $customerLog,  $logViewFileName,$customerLogHelper);
                    } else {
                        $customer = $this->createCustomerToMagento($syncData, $customerLog,  $logViewFileName,$customerLogHelper);
                    }
                    if ($customer->getId()) {
                        /**
                         * Need to add condition that if any required address field is blank then stop sync particular customer address
                         * Need to get the State/Province id from the code
                         */
                        /*if ($syncData['street'] != '' && $syncData['city'] != '' && $syncData['country_id'] != '' && $syncData['postcode'] != '' && $syncData['telephone'] != '' && $syncData['region_id'] != '')
                        {*/
                        if(isset($syncData['region_id']))
                        {
                            $getStateId = $this->resourceModelSync->getStateId($syncData['region_id']);
                            if ($getStateId) {
                                $syncData['region_id'] = $getStateId;
                            }
                        }
                            /**
                             * here we need to map the addressline 2
                             */
                            if(!empty($aData['MainContact']['Address']['AddressLine2']['Value']))
                            {
                                $addressLine2 = $aData['MainContact']['Address']['AddressLine2']['Value'];
                                $syncData['street'] = $syncData['street']."\n".$addressLine2;

                            }
                            $before = json_encode($customer->getData()); //Before data
                            $customerLog['before_change'] = $before;
                            if (!empty($billingData)) {
                                $getBillingStateId =$this->resourceModelSync->getStateId($billingData['region_id']);
                                $billingData['region_id'] = $getBillingStateId;
                                if(!empty($aData['BillingContact']['Address']['AddressLine2']['Value']))
                                {
                                    $billingAddressLine2 = $aData['BillingContact']['Address']['AddressLine2']['Value'];

                                    $billingData['street'] = $billingData['street']."\n".$billingAddressLine2;

                                }
                                unset($billingData['acumatica_customer_id']);
                                $customerBillingAddress = $billingData;
                            } else {
                                if(isset($billingCompany) && $billingCompany=='false'){
                                    $syncData['company'] = $aData['BillingContact']['DisplayName']['Value'];
                                }
                                $customerBillingAddress = $syncData;
                            }
                            if (!empty($shippingData)) {
                                $getShippingStateId = $this->resourceModelSync->getStateId($shippingData['region_id']);
                                $shippingData['region_id'] = $getShippingStateId;
                                if(!empty($aData['ShippingContact']['Address']['AddressLine2']['Value']))
                                {
                                    $shippingAddressLine2 = $aData['ShippingContact']['Address']['AddressLine2']['Value'];
                                    $shippingData['street'] = $shippingData['street']."\n".$shippingAddressLine2;
                                }
                                unset($shippingData['acumatica_customer_id']);
                                $customerShippingAddress = $shippingData;
                            } else {
                                if(isset($shippingCompany) && $shippingCompany=='false'){
                                    $syncData['company'] = $aData['ShippingContact']['DisplayName']['Value'];
                                }
                                $customerShippingAddress = $syncData;
                            }
                            $getBillingAddress = $customer->getPrimaryBillingAddress();
                            $billingEntityId = '';
                            if($getBillingAddress){
                                $billingEntityId = $getBillingAddress->getId();
                            }
                            $getShippingAddress = $customer->getPrimaryShippingAddress();
                            $shippingEntityId = '';
                            if($getShippingAddress){
                                $shippingEntityId = $getShippingAddress->getId();
                            }

                            // Check whether the address is there or not
                            $isNewBillingAddress = false;
                            $isNewShippingAddress = false;
                            $billingAddressLogFlag = false;
                            $shippingAddressLogFlag = false;
                            $updatedAddress = false;
                            /**
                             * Checking the billing address is updated or not in magento
                             */
                            if ($getBillingAddress && $billingEntityId != '') {
                                if ($getBillingAddress->getData('street') != $customerBillingAddress['street']) {
                                    $billingAddressLogFlag = true;
                                }
                                if ($getBillingAddress->getData('city') != $customerBillingAddress['city']) {
                                    $billingAddressLogFlag = true;
                                }
                                if ($getBillingAddress->getData('country_id') != $customerBillingAddress['country_id']) {
                                    $billingAddressLogFlag = true;
                                }
                                if ($getBillingAddress->getData('postcode') != $customerBillingAddress['postcode']) {
                                    $billingAddressLogFlag = true;
                                }
                                if ($getBillingAddress->getData('telephone') != $customerBillingAddress['telephone']) {
                                    $billingAddressLogFlag = true;
                                }
                                if ($getBillingAddress->getData('region_id') != $customerBillingAddress['region_id']) {
                                    $billingAddressLogFlag = true;
                                }
                                if ($getBillingAddress->getData('fax') != $customerBillingAddress['fax']) {
                                    $billingAddressLogFlag = true;
                                }
                                if ($getBillingAddress->getData('company') != $customerBillingAddress['company']) {
                                    $billingAddressLogFlag = true;
                                }
                            }

                            /**
                             * Checking the shipping address is updated or not in magento
                             */
                            if ($getShippingAddress && $shippingEntityId != '') {
                                if ($getShippingAddress->getData('street') != $customerShippingAddress['street']) {
                                    $shippingAddressLogFlag = true;
                                }
                                if ($getShippingAddress->getData('city') != $customerShippingAddress['city']) {
                                    $shippingAddressLogFlag = true;
                                }
                                if ($getShippingAddress->getData('country_id') != $customerShippingAddress['country_id']) {
                                    $shippingAddressLogFlag = true;
                                }
                                if ($getShippingAddress->getData('postcode') != $customerShippingAddress['postcode']) {
                                    $shippingAddressLogFlag = true;
                                }
                                if ($getShippingAddress->getData('telephone') != $customerShippingAddress['telephone']) {
                                    $shippingAddressLogFlag = true;
                                }
                                if ($getShippingAddress->getData('region_id') != $customerShippingAddress['region_id']) {
                                    $shippingAddressLogFlag = true;
                                }
                                if ($getShippingAddress->getData('fax') != $customerShippingAddress['fax']) {
                                    $shippingAddressLogFlag = true;
                                }
                                if ($getShippingAddress->getData('company') != $customerShippingAddress['company']) {
                                    $shippingAddressLogFlag = true;
                                }
                            }
                            /**
                             * When billing or shipping address have changes
                             */
                            if($shippingAddressLogFlag || $billingAddressLogFlag){

                                /**
                                 * When customer have address in magento
                                 * then update default address with billing address
                                 * delete shipping address if exists and create new shipping address
                                 */

                                /* Update defaul address with billing address*/
                                try {
                                    if ($getBillingAddress) {
                                        foreach ($customerBillingAddress as $_key => $_value) {
                                            if (!empty($_value)) {
                                                $_keyset = 'set' . ucfirst($_key);
                                                $getBillingAddress->$_keyset($_value);
                                            }
                                        }
                                        $getBillingAddress->save();

                                        $customerLog['customerId'] = $customer->getId(); //Current customer ID
                                        $customerLog['email'] = $customer->getEmail();
                                        $customerLog['description'] = "Customer Id:" . $customer->getAcumaticaCustomerId() . " Billing address synced with Magento"; //Descripton
                                        $customerLog['syncAction'] = "Customer Address Updated Into Magento";
                                        $customerLog['accumaticaCustomerId'] = $customer->getAcumaticaCustomerId();
                                        $customerLog['syncDirection'] = "syncToMagento";
                                        $customerLog['messageType'] = "Success";
                                        $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                        $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
                                        file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                                    }
                                } catch(\Magento\Framework\Validator\Exception $e) {
                                    /**
                                     *logs here to print exception
                                     */
                                    /*$errorMsg = $e->getMessage()."<br />".$e->getTraceAsString();
                                    $syncName = 'Customer Sync';
                                    $this->amconnectorHelper->errorLogEmail($syncName, $errorMsg);*/
                                    $customerLog['description'] = json_encode($e->getMessage()); //Descripton
                                    $customerLog['customerId'] = ''; //Descripton
                                    $customerLog['syncAction'] = "Customer Billing Address Not Synced";
                                    $customerLog['accumaticaCustomerId'] = $syncData['acumatica_customer_id'];
                                    $customerLog['syncDirection'] = "syncToMagento";
                                    $customerLog['messageType'] = "Failure";
                                    $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Error : ".$syncData['acumatica_customer_id']." : Billing address not synced";
                                    file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                                    $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                    $this->errorCheckInMagento[] = 1;
                                }

                                try {
                                    /* Create new shipping address*/
                                    $getShippingAddress = '';
                                    if (!$getShippingAddress) {
                                        $getShippingAddress = $this->addressFactory->create();
                                        $getShippingAddress->setCustomer($customer);
                                        $isNewShippingAddress = true;
                                    }
                                    if ($getShippingAddress) {
                                        foreach ($customerShippingAddress as $_key => $_value) {
                                            if (!empty($_value)) {
                                                $_keyset = 'set' . ucfirst($_key);
                                                $getShippingAddress->$_keyset($_value);
                                            }
                                        }
                                        $getShippingAddress->save();
                                        if ($isNewShippingAddress) {
                                            // Add address to customer and save
                                            $customer->addAddress($getShippingAddress)
                                                ->setDefaultShipping($getShippingAddress->getId())
                                                ->save();
                                        }
                                    }
                                    if (isset($shippingEntityId) && $billingEntityId != $shippingEntityId) {
                                        $address1 = $this->addressFactory->create()->load($shippingEntityId);
                                        $address1->delete();
                                    }
                                    $customerLog['customerId'] = $customer->getId(); //Current customer ID
                                    $customerLog['email'] = $customer->getEmail();
                                    $customerLog['description'] = "Customer Id:" . $customer->getAcumaticaCustomerId() . " Shipping address synced with Magento"; //Descripton
                                    $customerLog['syncAction'] = "Customer Address Updated Into Magento";
                                    $customerLog['accumaticaCustomerId'] = $customer->getAcumaticaCustomerId();
                                    $customerLog['syncDirection'] = "syncToMagento";
                                    $customerLog['messageType'] = "Success";
                                    $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                    $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
                                    file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                                } catch(\Magento\Framework\Validator\Exception $e) {
                                    $updatedAddress = false;
                                    $isNewShippingAddress = false;
                                    /**
                                     *logs here to print exception
                                     */
                                    /*$errorMsg = $e->getMessage()."<br />".$e->getTraceAsString();
                                    $syncName = 'Customer Sync';
                                    $this->amconnectorHelper->errorLogEmail($syncName, $errorMsg);*/
                                    $customerLog['description'] = json_encode($e->getMessage()); //Descripton
                                    $customerLog['customerId'] = ''; //Descripton
                                    $customerLog['syncAction'] = "Customer Shipping Address Not Synced";
                                    $customerLog['accumaticaCustomerId'] = $syncData['acumatica_customer_id'];
                                    $customerLog['syncDirection'] = "syncToMagento";
                                    $customerLog['messageType'] = "Failure";
                                    $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Error : ".$syncData['acumatica_customer_id']." : Shipping address not synced";
                                    file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                                    $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                    $this->errorCheckInMagento[] = 1;
                                }
                            }else{
                                /**
                                 * When customer have no address in magento
                                 * Create separate billing and shipping address in magento
                                 */
                                try {
                                    if (!$getBillingAddress) {
                                        $getBillingAddress = $this->addressFactory->create();
                                        $getBillingAddress->setCustomer($customer);
                                        $isNewBillingAddress = true;
                                    }
                                    if ($getBillingAddress) {
                                        foreach ($customerBillingAddress as $_key => $_value) {
                                            if (!empty($_value)) {
                                                $_keyset = 'set' . ucfirst($_key);
                                                $getBillingAddress->$_keyset($_value);
                                            }
                                        }
                                        $getBillingAddress->save();
                                        if ($isNewBillingAddress) {
                                            // Add address to customer and save
                                            $customer->addAddress($getBillingAddress)
                                                ->setDefaultBilling($getBillingAddress->getId())
                                                ->save();
                                        }
                                        $customerLog['customerId'] = $customer->getId(); //Current customer ID
                                        $customerLog['email'] = $customer->getEmail();
                                        $customerLog['description'] = "Customer Id:" . $customer->getAcumaticaCustomerId() . " Billing address synced with Magento"; //Descripton
                                        $customerLog['syncAction'] = "Customer Address Updated Into Magento";
                                        $customerLog['accumaticaCustomerId'] = $customer->getAcumaticaCustomerId();
                                        $customerLog['syncDirection'] = "syncToMagento";
                                        $customerLog['messageType'] = "Success";
                                        $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                        $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
                                        file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                                    }
                                } catch(\Magento\Framework\Validator\Exception $e) {
                                    $isNewBillingAddress = false;
                                    /**
                                     *logs here to print exception
                                     */
                                    /*$errorMsg = $e->getMessage()."<br />".$e->getTraceAsString();
                                    $syncName = 'Customer Sync';
                                    $this->amconnectorHelper->errorLogEmail($syncName, $errorMsg);*/
                                    $customerLog['description'] = json_encode($e->getMessage()); //Descripton
                                    $customerLog['customerId'] = ''; //Descripton
                                    $customerLog['syncAction'] = "Customer Billing Address Not Synced";
                                    $customerLog['accumaticaCustomerId'] = $syncData['acumatica_customer_id'];
                                    $customerLog['syncDirection'] = "syncToMagento";
                                    $customerLog['messageType'] = "Failure";
                                    $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Error : ".$syncData['acumatica_customer_id']." : Billing address not synced";
                                    file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                                    $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                    $this->errorCheckInMagento[] = 1;
                                }

                                try {
                                    if (!$getShippingAddress) {
                                        $getShippingAddress = $this->addressFactory->create();
                                        $getShippingAddress->setCustomer($customer);
                                        $isNewShippingAddress = true;
                                    }
                                    if ($getShippingAddress) {
                                        foreach ($customerShippingAddress as $_key => $_value) {
                                            if (!empty($_value)) {
                                                $_keyset = 'set' . ucfirst($_key);
                                                $getShippingAddress->$_keyset($_value);
                                            }
                                        }
                                        $getShippingAddress->save();
                                        if ($isNewShippingAddress) {
                                            // Add address to customer and save
                                            $customer->addAddress($getShippingAddress)
                                                ->setDefaultShipping($getShippingAddress->getId())
                                                ->save();
                                        }
                                    }
                                    $customerLog['customerId'] = $customer->getId(); //Current customer ID
                                    $customerLog['email'] = $customer->getEmail();
                                    $customerLog['description'] = "Customer Id:" . $customer->getAcumaticaCustomerId() . " Shipping address synced with Magento"; //Descripton
                                    $customerLog['syncAction'] = "Customer Address Updated Into Magento";
                                    $customerLog['accumaticaCustomerId'] = $customer->getAcumaticaCustomerId();
                                    $customerLog['syncDirection'] = "syncToMagento";
                                    $customerLog['messageType'] = "Success";
                                    $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                    $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
                                    file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                                } catch(\Magento\Framework\Validator\Exception $e) {
                                    $updatedAddress = false;
                                    $isNewShippingAddress = false;
                                    /**
                                     *logs here to print exception
                                     */
                                    /*$errorMsg = $e->getMessage()."<br />".$e->getTraceAsString();
                                    $syncName = 'Customer Sync';
                                    $this->amconnectorHelper->errorLogEmail($syncName, $errorMsg);*/
                                    $customerLog['description'] = json_encode($e->getMessage()); //Descripton
                                    $customerLog['customerId'] = ''; //Descripton
                                    $customerLog['syncAction'] = "Customer Shipping Address Not Synced";
                                    $customerLog['accumaticaCustomerId'] = $syncData['acumatica_customer_id'];
                                    $customerLog['syncDirection'] = "syncToMagento";
                                    $customerLog['messageType'] = "Failure";
                                    $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Error : ".$syncData['acumatica_customer_id']." : Shipping address not synced";
                                    file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                                    $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                    $this->errorCheckInMagento[] = 1;
                                }
                            }


                            /**
                             * logs here for address sync success
                             */
                            if ($updatedAddress) {
                                $customerLog['customerId'] = $customer->getId(); //Current customer ID
                                $customerLog['email'] = $customer->getEmail();
                                $customerLog['description'] = "Customer Id:" . $customer->getAcumaticaCustomerId() . " Address synced with Magento"; //Descripton
                                $customerLog['syncAction'] = "Customer Address Updated Into Magento";
                                $customerLog['accumaticaCustomerId'] = $customer->getAcumaticaCustomerId();
                                $customerLog['syncDirection'] = "syncToMagento";
                                $customerLog['messageType'] = "Success";
                                $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
                                file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                            }elseif($isNewBillingAddress && $isNewShippingAddress){
                                $customerLog['customerId'] = $customer->getId(); //Current customer ID
                                $customerLog['email'] = $customer->getEmail();
                                $customerLog['description'] = "Customer Id:" . $customer->getAcumaticaCustomerId() . " Address synced with Magento"; //Descripton
                                $customerLog['syncAction'] = "Customer Address Inserted Into Magento";
                                $customerLog['accumaticaCustomerId'] = $customer->getAcumaticaCustomerId();
                                $customerLog['syncDirection'] = "syncToMagento";
                                $customerLog['messageType'] = "Success";
                                $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                $txt = $this->timezone->date(time())->format('Y-m-d H:i:s')  . " : Info : " . $customerLog['description'];
                                file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                            }
                        //}

                        $updateCustomAttributes = array();
                        foreach ($syncData as $key => $value) {
                            if (in_array($key, $customAttributes)) {
                                $updateCustomAttributes[] = $key;
                            }
                        }

                        if(!empty($updateCustomAttributes))
                        {
                            foreach($updateCustomAttributes as $updateCustomAttribute)
                            {

                                $this->resource->updateCustomerAttribute($updateCustomAttribute,$syncData[$updateCustomAttribute],$customer->getId());
                            }
                        }

                    }
                }catch (Exception $e) {
                    /**
                     *logs here to print exception
                     */
                    /*$errorMsg = $e->getMessage()."<br />".$e->getTraceAsString();
                    $syncName = 'Customer Sync';
                    $this->amconnectorHelper->errorLogEmail($syncName, $errorMsg);*/
                    $customerLog['description'] = $e->getMessage(); //Descripton
                    $customerLog['syncAction'] = "Customer Not Synced";
                    $customerLog['accumaticaCustomerId'] = $syncData['acumatica_customer_id'];
                    $customerLog['syncDirection'] = "syncToMagento";
                    $customerLog['messageType'] = "Failure";
                    $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['syncAction'];
                    file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                    $customerLogHelper->customerSyncSuccessLogs($customerLog);
                    $this->errorCheckInMagento[] = 1;
                }
            }
        }

        return $this->errorCheckInMagento;
    }

    public function createCustomerToMagento($syncData,  $customerLog, $logViewFileName,$customerLogHelper)
    {
        /**
         * creating customer
         */
        $customer = $this->customerFactory->create();

        foreach ($syncData as $key => $value) {
            $keyset = 'set' . ucfirst($key);
            $customer->$keyset($syncData[$key]);
        }
        try {
            $customer->save();
        } catch (\Magento\Framework\Validator\Exception $e) {
            $customerLog['customerId'] = '';
            $customerLog['accumaticaCustomerId'] = $syncData['acumatica_customer_id'];
            $customerLog['description'] = $e->getMessage(); //Descripton
            $customerLog['syncAction'] = "Customer Not Synced";
            $customerLog['syncDirection'] = "syncToMagento";
            $customerLog['messageType'] = "Failure";
            $customerLogHelper->customerSyncSuccessLogs($customerLog);
            $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
            file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
        }catch (Exception $e) {
            $customerLog['customerId'] = '';
            $customerLog['accumaticaCustomerId'] = $syncData['acumatica_customer_id'];
            $customerLog['description'] = $e->getMessage(); //Descripton
            $customerLog['syncAction'] = "Customer Not Synced";
            $customerLog['syncDirection'] = "syncToMagento";
            $customerLog['messageType'] = "Failure";
            $customerLogHelper->customerSyncSuccessLogs($customerLog);
            $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
            file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
        }

        /**
         * logs here for inserting customer
         */
        if ($customer->getId()) {
            $customerLog['customerId'] = $customer->getId();
            $customerLog['email'] = $customer->getEmail();
            $customerLog['description'] = "Customer Id:" . $customer->getAcumaticaCustomerId() . " Synced To Magento"; //Descripton
            $customerLog['syncAction'] = "Customer Inserted Into Magento";
            $customerLog['accumaticaCustomerId'] = $customer->getAcumaticaCustomerId();
            $customerLog['syncDirection'] = "syncToMagento";
            $customerLog['messageType'] = "Success";
            $customerLogHelper->customerSyncSuccessLogs($customerLog);
            $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
            file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
        }
        return $customer;
    }

    public function updateCustomerToMagento($customerId, $customerFirtsName, $customerLastName, $syncData,  $customerLog,  $logViewFileName,$customerLogHelper)
    {
        /**
         * updating customer
         */
        $updateFlag = false;
        $customer = $this->customerFactory->create()->load($customerId);
        $customerLog['before_change'] = json_encode($customer->getData());
        if ($customerFirtsName != $customer->getFirstname() || $customerLastName != $customer->getLastname() || $customer->getEmail() != $syncData['email']) {
            if($customer->getEmail() != $syncData['email'])
            {
                $customerCheck = $this->customerFactory->create()->setWebsiteId($syncData['website_id'])->loadByEmail($syncData['email']);
                if(!$customerCheck->getId())
                {
                    $updateFlag = true;
                }else
                {
                    $customerLog['customerId'] = $customer->getId(); //Current customer ID
                    $customerLog['email'] = $customer->getEmail();
                    $customerLog['description'] = "Customer Id:" . $syncData['acumatica_customer_id'] . " not Synced To Magento. A customer with email ".$syncData['email']." already exists"; //Descripton
                    $customerLog['syncAction'] = "Customer Updated Into Magento";
                    $customerLog['accumaticaCustomerId'] = $syncData['acumatica_customer_id'];
                    $customerLog['syncDirection'] = "syncToMagento";
                    $customerLog['messageType'] = "Error";
                    $customerLogHelper->customerSyncSuccessLogs($customerLog);
                    $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Error : " . $customerLog['description'];
                    file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                }
            }else{
                $updateFlag = true;
            }
            if($updateFlag)
            {
                foreach ($syncData as $key => $value) {
                    $keyset = 'set' . ucfirst($key);
                    $customer->$keyset($syncData[$key]);
                }
                try {
                    $customer->save();
                    /**
                     * logs here for updating customer
                     */
                    $customerLog['customerId'] = $customer->getId(); //Current customer ID
                    $customerLog['email'] = $customer->getEmail();
                    $customerLog['description'] = "Customer Id:" . $syncData['acumatica_customer_id'] . " Synced To Magento"; //Descripton
                    $customerLog['syncAction'] = "Customer Updated Into Magento";
                    $customerLog['accumaticaCustomerId'] = $syncData['acumatica_customer_id'];
                    $customerLog['syncDirection'] = "syncToMagento";
                    $customerLog['messageType'] = "Success";
                    $customerLog['after_change'] = json_encode($customer->getData());
                    $customerLogHelper->customerSyncSuccessLogs($customerLog);
                    $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
                    file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                } catch (Exception $e) {
                    $customerLog['description'] = $e->getMessage(); //Descripton
                    $customerLog['syncAction'] = "Customer Not Synced";
                    $customerLog['syncDirection'] = "syncToMagento";
                    $customerLog['messageType'] = "Failure";
                    $customerLogHelper->customerSyncSuccessLogs($customerLog);

                }
            }
        }
        return $customer;
    }

    /**
     * @param $aData
     * @param $mappingAttributes
     * @param $customerLog
     * @param $storeId
     * @param null $logViewFileName
     * @param $customerLogHelper
     * @param $directionFlag
     * @param $flag
     * @return array|string
     */

    public function syncToAcumatica($aData, $mappingAttributes, $customerLog, $storeId, $logViewFileName = NULL, $customerLogHelper, $directionFlag, $flag)
    {
        if ($customerId = $aData['magento_id']) {
            /*From Order*/
            if ($flag == 'ORDER') {
                $customer = $this->orderFactory->create()->load($customerId);
                /**
                 * Here we need to get store Id from order to pass it as a branch
                 */
                $storeIdFromOrder = $customer['store_id'];
                $branchName = '';
                if(isset($storeIdFromOrder) && !empty($storeIdFromOrder))
		//if (isset($storeIdFromCustomer) && !empty($storeIdFromCustomer))
				{
					$branchName = $this->branchCodes[$storeIdFromOrder];
				}
                //$branchName = $this->branchCodes[$storeIdFromOrder];
                $billAdd = $this->orderAddressFactory->create()->load($customer['billing_address_id']);
                $data[$customerId]['billing'] = $data[$customerId]['main'] = $billAdd->getData();
                $data[$customerId]['shipping'] = $this->orderAddressFactory->create()->load($customer['shipping_address_id'])->getData();
                $data[$customerId]['firstname'] = $billAdd->getFirstname();
                //$data[$customerId]['lastname'] = $billAdd->getLastname();
		        $lastName = $billAdd->getLastname();
                $data[$customerId]['lastname'] = str_replace("<missing>","",$lastName);
                $data[$customerId]['email'] = $billAdd->getEmail();
            } else {
                   $customer = $this->resource->getCustomerById($customerId);
                    $data[$customerId] = $customer;
                    $storeIdFromCustomer = $customer['store_id'];
                    $branchName = '';
                    if (isset($storeIdFromCustomer) && !empty($storeIdFromCustomer)) {
                        $branchName = $this->branchCodes[$storeIdFromCustomer];
                    }
                if ($customer['default_billing']) {
                    $data[$customerId]['billing'] = $data[$customerId]['main'] = $this->resource->getCustomerPrimaryAddress($customer['default_billing']);
                } elseif (isset($aData['order_id'])) {
                    $customerOrder = $this->orderFactory->create()->load($aData['order_id']);
                    if ($primaryBillAdd = $this->orderAddressFactory->create()->load($customerOrder['billing_address_id'])) {
                        $data[$customerId]['billing'] = $data[$customerId]['main'] = $primaryBillAdd->getData();
                    }
                }
                if ($customer['default_shipping']) {
                    $data[$customerId]['shipping'] = $this->resource->getCustomerPrimaryAddress($customer['default_shipping']);
                } elseif (isset($aData['order_id'])) {
                    $customerOrder = $this->orderFactory->create()->load($aData['order_id']);
                    if ($primaryShipAdd = $this->orderAddressFactory->create()->load($customerOrder['shipping_address_id'])) {
                        $data[$customerId]['shipping'] = $primaryShipAdd->getData();
                    }
                }
            }
            /*End Order*/
            $syncData = array();
            foreach ($mappingAttributes as $key => $value) {
                $mappingData = explode('|', $value);
                if ($directionFlag && $mappingData[1] == "Bi-Directional (Acumatica Wins)") {
                    continue;
                }
                if (isset($mappingData[0]) && $mappingData[0] != '') {
                    $acumaticaLabel = $this->resource->getAcumaticaAttrCode($mappingData[0]);
                }
                $acumaticaAttrCode = explode(" ", $acumaticaLabel); //array[0] will be section and array[1] will be attribute code
                if (isset($acumaticaAttrCode[0])) {
                    switch ($acumaticaAttrCode[0]) {
                        case "CustomerSchema":
                            if (isset($data[$customerId][$key]) && $data[$customerId][$key] != '') {
                                $syncData[$acumaticaAttrCode[1]] = $data[$customerId][$key];
                                if ($key == 'firstname' || $key == 'lastname') {
                                    if($key == 'firstname')
                                        $name[0] = $data[$customerId][$key];
                                    else
                                        $name[1] = $data[$customerId][$key];
                                }
                            }
                            break;
                        case "MainAddress":
                            if ($acumaticaAttrCode[1] == 'State') {
                                if (isset($data[$customerId]['billing'][$key]))
                                    $syncData['MainContact']['Address'][$acumaticaAttrCode[1]] = $syncData['BillingContact']['Address'][$acumaticaAttrCode[1]] = $this->resourceModelSync->getStateCodeById($data[$customerId]['billing'][$key]);

                                if (isset($data[$customerId]['shipping'][$key]))
                                    $syncData['ShippingContact']['Address'][$acumaticaAttrCode[1]] = $this->resourceModelSync->getStateCodeById($data[$customerId]['shipping'][$key]);
                            } else {
                                if (isset($data[$customerId]['billing'][$key]))
                                    $syncData['MainContact']['Address'][$acumaticaAttrCode[1]] = $syncData['BillingContact']['Address'][$acumaticaAttrCode[1]] = $data[$customerId]['billing'][$key];

                                if (isset($data[$customerId]['shipping'][$key]))
                                    $syncData['ShippingContact']['Address'][$acumaticaAttrCode[1]] = $data[$customerId]['shipping'][$key];
                            }

                            break;
                        case "MainContact":
                            if ($acumaticaAttrCode[1] == "Email") {
                                $syncData[$acumaticaAttrCode[0]][$acumaticaAttrCode[1]] = $syncData['BillingContact'][$acumaticaAttrCode[1]] = $syncData['ShippingContact'][$acumaticaAttrCode[1]] = $data[$customerId][$key];
                            } else {
                                if (isset($data[$customerId]['main'][$key])) {
                                    $syncData[$acumaticaAttrCode[0]][$acumaticaAttrCode[1]] = $data[$customerId]['main'][$key];
                                    if (isset($data[$customerId]['billing'][$key]))
                                        $syncData['BillingContact'][$acumaticaAttrCode[1]] = $data[$customerId]['billing'][$key];
                                    if (isset($data[$customerId]['shipping'][$key]))
                                        $syncData['ShippingContact'][$acumaticaAttrCode[1]] = $data[$customerId]['shipping'][$key];
                                }
                            }

                            break;
                        case "BillingContact":
                            if (isset($data[$customerId]['billing'][$key])) {
                                $syncData['MainContact'][$acumaticaAttrCode[1]] = $syncData['BillingContact'][$acumaticaAttrCode[1]] = $data[$customerId]['billing'][$key];
                            }

                            break;
                        case "BillingAddress":
                            if ($acumaticaAttrCode[1] == 'State') {
                                if (isset($data[$customerId]['billing'][$key])) {
                                    $getStateCode = $this->resourceModelSync->getStateCodeById($data[$customerId]['billing'][$key]);
                                    $syncData['MainContact']['Address'][$acumaticaAttrCode[1]] = $syncData['BillingContact']['Address'][$acumaticaAttrCode[1]] = $getStateCode;
                                }
                            } else {
                                if (isset($data[$customerId]['billing'][$key])) {
                                    $syncData['MainContact']['Address'][$acumaticaAttrCode[1]] = $syncData['BillingContact']['Address'][$acumaticaAttrCode[1]] = $data[$customerId]['billing'][$key];
                                }
                            }

                            break;
                        case "ShipToContact":
                            if (isset($data[$customerId]['shipping'][$key])) {
                                $syncData['MainContact'][$acumaticaAttrCode[1]] = $syncData['ShippingContact'][$acumaticaAttrCode[1]] = $data[$customerId]['shipping'][$key];
                            }

                            break;
                        case "ShipToAddress":
                            if ($acumaticaAttrCode[1] == 'State') {
                                if (isset($data[$customerId]['shipping'][$key])) {
                                    $getStateCode = $this->resourceModelSync->getStateCodeById($data[$customerId]['shipping'][$key]);
                                    $syncData['MainContact']['Address'][$acumaticaAttrCode[1]] = $syncData['ShippingContact']['Address'][$acumaticaAttrCode[1]] = $getStateCode;
                                }
                            } else {
                                if (isset($data[$customerId]['shipping'][$key])) {
                                    $syncData['MainContact']['Address'][$acumaticaAttrCode[1]] = $syncData['ShippingContact']['Address'][$acumaticaAttrCode[1]] = $data[$customerId]['shipping'][$key];
                                }
                            }

                            break;
                    }
                }

            }
            if (!empty($name)) {
                ksort($name);
                $syncData['CustomerName'] = implode(' ', $name);
            }
            $acumaticaCustId = $this->resource->getAcmCustomerId($customer['entity_id']);//$customer->getAcumaticaCustomerId();
            if (!empty($syncData)) {
                if (isset($syncData['MainContact']['Email']))
                    $customerEmail = $syncData['MainContact']['Email'];
                else
                    $customerEmail = '';
                $autoNumber = false;
                if (isset($customerEmail) && $customerEmail != '' && isset($syncData['CustomerName']) && $syncData['CustomerName'] != '') {

                    /**
                     * assigning Default customer class,Terms and Statement Cycle Id
                     */
                    if ($this->scopeConfigInterface->getValue('amconnectorsync/customersync/customerclass', 'stores', $storeId))
                        $customerClass = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/customerclass', 'stores', $storeId);
                    else
                        $customerClass = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/customerclass');

                    if ($this->scopeConfigInterface->getValue('amconnectorsync/customersync/customerterms', 'stores', $storeId))
                        $customerTerms = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/customerterms', 'stores', $storeId);
                    else
                        $customerTerms = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/customerterms');

                    if ($this->scopeConfigInterface->getValue('amconnectorsync/customersync/customerstatementcycle', 'stores', $storeId))
                        $customerStatementCycleId = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/customerstatementcycle', 'stores', $storeId);
                    else
                        $customerStatementCycleId = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/customerstatementcycle');

		    //if(isset($customerOrder['ig_order_number'])) {
                   	 //mail("akashc@kensium.com","IGLOBAL ORDER NUMBER",$customerOrder['ig_order_number']); 
		    //}
                    //Customization//
                    if(isset($customerOrder['ig_order_number']))
                        $syncData['CustomerClass'] = 'IGLOBAL';
                    else
                        $syncData['CustomerClass'] = $customerClass;

                    //Customization//
                    //mail("akashc@kensium.com","CUSTOMER CLASS",$syncData['CustomerClass']);

                    if ($customerTerms) {
                        $syncData['Terms'] = $customerTerms;
                    }
                    if ($customerStatementCycleId) {
                        $syncData['StatementCycleID'] = $customerStatementCycleId;
                    }

                    /**
                     * Creating customer envelope for Acumatica
                     * Customer Id will be created based on segmentation key
                     * if key is return == auto; then no need to send the customer Id
                     * if key is return == manual; then check the pattern and create a customer id
                     * Note: As of default product we are considering default pattern which is 9 digit
                     * Note: get Segmentation from acumatica
                     */
                    if ($acumaticaCustId != '') {
                        $customerAvailable = $this->amconnectorCustomerHelper->getAcumaticaCustomerById($acumaticaCustId, $storeId);
                    } else {
                        $customerAvailable = '';//$this->amconnectorCustomerHelper->getAcumaticaCustomerByEmail($customerEmail, $storeId);
                    }
                    if ($customerAvailable != '') {
                        $acuCustId = $customerAvailable;
                    } else {
                        /**
                         * get segmentation key
                         */
                        if ($this->scopeConfigInterface->getValue('amconnectorsync/customersync/segmentation', 'stores', $storeId))
                            $segmentation = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/segmentation', 'stores', $storeId);
                        else
                            $segmentation = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/segmentation');

                        if ($segmentation == 1) {
                            $acuCustId = '&lt;NEW&gt;';
                            $autoNumber == 'true';
                        } else {

                            if ($this->scopeConfigInterface->getValue('amconnectorsync/customersync/manualsegmention', 'stores', $storeId))
                                $manualSegmentation = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/manualsegmention', 'stores', $storeId);
                            else
                                $manualSegmentation = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/manualsegmention');

                            if ($manualSegmentation != '') {
                                $length = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/manualsegmention');
                            } else {
                                $length = '9';
                            }
                            $firstName = '';
                            if ($flag == "ORDER") {
                                $firstName = $data[$customerId]['firstname'];
                            } else {
                                $firstName = $customer->getFirstname();
                            }
                            $customerValue = $firstName . $customerId;

                            if (strlen($customerValue) <= $length) {
                                $acuCustId = $customerValue;
                            } else {
                                $diff = strlen($customerValue) - $length;
                                $acuCustId = substr($customerValue, $diff);
                            }
                        }
                    }
                    /**
                     * need to add zipcode validation
                     * Main Contact
                     * Billing Contact
                     * Shipping Contact
                     */
                    if (!isset($syncData['MainContact']['Address']['PostalCode']))
                        $syncData['MainContact']['Address']['PostalCode'] = '';
                    if (!isset($syncData['BillingContact']['Address']['PostalCode']))
                        $syncData['BillingContact']['Address']['PostalCode'] = '';
                    if (!isset($syncData['ShippingContact']['Address']['PostalCode']))
                        $syncData['ShippingContact']['Address']['PostalCode'] = '';
                    if (isset($syncData['MainContact']['Address']) && $syncData['MainContact']['Address']['PostalCode'] != '' && $syncData['MainContact']['Address']['Country'] == 'US') {
                        $mainZipCode = trim($syncData['MainContact']['Address']['PostalCode']);
                        $syncData['MainContact']['Address']['PostalCode'] = $this->validateUSAZip($mainZipCode);
                    }
                    if (isset($syncData['BillingContact']['Address']) && $syncData['BillingContact']['Address']['PostalCode'] != '' && $syncData['BillingContact']['Address']['Country'] == 'US') {
                        $billingZipCode = trim($syncData['BillingContact']['Address']['PostalCode']);
                        $syncData['BillingContact']['Address']['PostalCode'] = $this->validateUSAZip($billingZipCode);
                    }
                    if (isset($syncData['ShippingContact']['Address']) && $syncData['ShippingContact']['Address']['PostalCode'] != '' && $syncData['ShippingContact']['Address']['Country'] == 'US') {
                        $shippingZipCode = trim($syncData['ShippingContact']['Address']['PostalCode']);
                        $syncData['ShippingContact']['Address']['PostalCode'] = $this->validateUSAZip($shippingZipCode);
                    }
                    /**
                     * Need Address line 2 validation
                     * Main Contact
                     * Billing Contact
                     * Shipping Contact
                     */
                    if (!(isset($syncData['MainContact']['Address']['AddressLine1'])))
                        $syncData['MainContact']['Address']['AddressLine1'] = '';
                    if (!(isset($syncData['BillingContact']['Address']['AddressLine1'])))
                        $syncData['BillingContact']['Address']['AddressLine1'] = '';
                    if (!(isset($syncData['ShippingContact']['Address']['AddressLine1'])))
                        $syncData['ShippingContact']['Address']['AddressLine1'] = '';

                    if (isset($syncData['MainContact']['Address']) && $syncData['MainContact']['Address']['AddressLine1'] != '') {
                        $mainAddresStreet = explode("\n", $syncData['MainContact']['Address']['AddressLine1']);
                        $mainStreet1 = $mainStreet2 = '';
                        if (isset($mainAddresStreet)) {
                            if (isset($mainAddresStreet[0]))
                                $mainStreet1 = $mainAddresStreet[0];
                            if (isset($mainAddresStreet[1]))
                                $mainStreet2 = $mainAddresStreet[1];
                        }
                        if (strlen($mainStreet1) > $this->streetLength) {
                            $mainAdressStreet1 = substr($mainStreet1, 0, $this->streetLength);
                            $syncData['MainContact']['Address']['AddressLine1'] = $mainAdressStreet1;
                        } else {
                            $syncData['MainContact']['Address']['AddressLine1'] = $mainStreet1;
                        }
                        if (strlen($mainStreet2) > $this->streetLength) {
                            $mainAdressStreet2 = substr($mainStreet2, 0, $this->streetLength);
                            $syncData['MainContact']['Address']['AddressLine2'] = $mainAdressStreet2;
                        } else {
                            if (strlen($mainStreet1) > $this->streetLength) {
                                $mainAddressStreet3 = substr($mainStreet1, $this->streetLength, $this->streetLength);
                                $syncData['MainContact']['Address']['AddressLine2'] = $mainAddressStreet3;
                            } else {
                                $syncData['MainContact']['Address']['AddressLine2'] = $mainStreet2;
                            }
                        }
                    }

                    if (isset($syncData['BillingContact']['Address']) && $syncData['BillingContact']['Address']['AddressLine1'] != '') {
                        $billingAddresStreet = explode("\n", $syncData['BillingContact']['Address']['AddressLine1']);
                        $billingStreet1 = $billingStreet2 = '';
                        if (isset($billingAddresStreet)) {
                            if (isset($billingAddresStreet[0]))
                                $billingStreet1 = $billingAddresStreet[0];
                            if (isset($billingAddresStreet[1]))
                                $billingStreet2 = $billingAddresStreet[1];
                        }
                        if (strlen($billingStreet1) > $this->streetLength) {
                            $billingAdressStreet1 = substr($billingStreet1, 0, $this->streetLength);
                            $syncData['BillingContact']['Address']['AddressLine1'] = $billingAdressStreet1;
                        } else {
                            $syncData['BillingContact']['Address']['AddressLine1'] = $billingStreet1;
                        }
                        if (strlen($billingStreet2) > $this->streetLength) {
                            $billingAdressStreet2 = substr($billingStreet2, 0, $this->streetLength);
                            $syncData['BillingContact']['Address']['AddressLine2'] = $billingAdressStreet2;
                        } else {
                            if (strlen($billingStreet1) > $this->streetLength) {
                                $billingAddressStreet3 = substr($billingStreet1, $this->streetLength, $this->streetLength);
                                $syncData['BillingContact']['Address']['AddressLine2'] = $billingAddressStreet3;
                            } else {
                                $syncData['BillingContact']['Address']['AddressLine2'] = $billingStreet2;
                            }
                        }
                    }

                    if (isset($syncData['ShippingContact']['Address']) && $syncData['ShippingContact']['Address']['AddressLine1'] != '') {
                        $shippingAddresStreet = explode("\n", $syncData['ShippingContact']['Address']['AddressLine1']);
                        $shippingStreet1 = $shippingStreet2 = '';
                        if (isset($shippingAddresStreet)) {
                            if (isset($shippingAddresStreet[0]))
                                $shippingStreet1 = $shippingAddresStreet[0];
                            if (isset($shippingAddresStreet[1]))
                                $shippingStreet2 = $shippingAddresStreet[1];
                        }
                        if (strlen($shippingStreet1) > $this->streetLength) {
                            $shippingAdressStreet1 = substr($shippingStreet1, 0, $this->streetLength);
                            $syncData['ShippingContact']['Address']['AddressLine1'] = $shippingAdressStreet1;
                        } else {
                            $syncData['ShippingContact']['Address']['AddressLine1'] = $shippingStreet1;
                        }
                        if (strlen($shippingStreet2) > $this->streetLength) {
                            $shippingAdressStreet2 = substr($shippingStreet2, 0, $this->streetLength);
                            $syncData['ShippingContact']['Address']['AddressLine2'] = $shippingAdressStreet2;
                        } else {
                            if (strlen($shippingStreet1) > $this->streetLength) {
                                $shippingAddressStreet3 = substr($shippingStreet1, $this->streetLength, $this->streetLength);
                                $syncData['ShippingContact']['Address']['AddressLine2'] = $shippingAddressStreet3;
                            } else {
                                $syncData['ShippingContact']['Address']['AddressLine2'] = $shippingStreet2;
                            }
                        }
                    }
		    $syncData['CustomerName'] = str_replace("<missing>","",$syncData['CustomerName']);
                    $createCustomerEnvelope = '';
                    if ($autoNumber == 'true') {
                        $createCustomerEnvelope .= '<CustomerID xsi:nil="true" />';
                    } else {
                        if (!isset($syncData['CustomerID'])) {
                            $createCustomerEnvelope .= '<CustomerID><Value>' . $acuCustId . '</Value><HasError>false</HasError></CustomerID>';
                        }
                    }
                    foreach ($syncData as $key => $_value) {
                        if ($key == 'MainContact') {
                            $createCustomerEnvelope .= '<MainContact> <ID xsi:nil="true" ></ID><Delete>false</Delete>';
                            foreach ($_value as $_key => $_data) {
                                if ($_key == 'Address') {
                                    if (array_filter($_data)) {
                                        $createCustomerEnvelope .= '<Address> <ID xsi:nil="true" ></ID><Delete>false</Delete>';
                                        foreach ($_data as $_akey => $_addrData) {
                                            if ($_akey == 'State' && empty($_addrData)) continue;
                                            $createCustomerEnvelope .= '<' . $_akey . '><Value><![CDATA[' . htmlspecialchars( trim(strtoupper($_addrData))) . ']]></Value><HasError>false</HasError></' . $_akey . '>';
                                        }
                                        $createCustomerEnvelope .= '</Address>';
                                    }
                                } else {
                                    if ($_key == "CompanyName") {
                                        $_key = "DisplayName";
                                    }
                                    if ($_key != "Email") {
                                        $_data = trim($_data);
                                    }
                                    if($_key == "Email"){
                                        $createCustomerEnvelope .= '<' . $_key . '><Value><![CDATA[' . htmlspecialchars( trim($_data)) . ']]></Value><HasError>false</HasError></' . $_key . '>';
                                    }
                                    else {
                                        $createCustomerEnvelope .= '<' . $_key . '><Value><![CDATA[' . htmlspecialchars( trim(strtoupper($_data))) . ']]></Value><HasError>false</HasError></' . $_key . '>';
                                    }
                                }
                            }
                            if ($syncData['CustomerName'] != '') {
                                $createCustomerEnvelope .= '<Position><Value><![CDATA[' . htmlspecialchars( trim(strtoupper($syncData['CustomerName']))) . ']]></Value><HasError>false</HasError></Position>';
                            }
                            $createCustomerEnvelope .= '</MainContact>';
                        } elseif ($key == 'ShippingContact') {
                            $createCustomerEnvelope .= '<ShippingContact> <ID xsi:nil="true" ></ID><Delete>false</Delete>';
                            foreach ($_value as $_key => $_data) {
                                if ($_key == 'Address') {
                                    if (array_filter($_data)) {
                                        $createCustomerEnvelope .= '<Address> <ID xsi:nil="true" ></ID><Delete>false</Delete>';
                                        foreach ($_data as $_akey => $_addrData) {
                                            if ($_akey == 'State' && empty($_addrData)) continue;
                                            $createCustomerEnvelope .= '<' . $_akey . '><Value><![CDATA[' . htmlspecialchars( trim(strtoupper($_addrData))) . ']]></Value><HasError>false</HasError></' . $_akey . '>';
                                        }
                                        $createCustomerEnvelope .= '</Address>';
                                    }
                                } else {
                                    if ($_key == "CompanyName") {
                                        $_key = "DisplayName";
                                    }
                                    if ($_key != "Email") {
                                        $_data = trim($_data);
                                    }
                                    if($_key == "Email"){
                                        $createCustomerEnvelope .= '<' . $_key . '><Value><![CDATA[' . htmlspecialchars( trim($_data)) . ']]></Value><HasError>false</HasError></' . $_key . '>';
                                    }
                                    else {
                                        $createCustomerEnvelope .= '<' . $_key . '><Value><![CDATA[' . htmlspecialchars( trim(strtoupper($_data))) . ']]></Value><HasError>false</HasError></' . $_key . '>';
                                    }
                                }

                            }
                            if ($syncData['CustomerName'] != '') {
                                $createCustomerEnvelope .= '<Position><Value><![CDATA[' . htmlspecialchars( trim(strtoupper($syncData['CustomerName']))) . ']]></Value><HasError>false</HasError></Position>';
                            }
                            $createCustomerEnvelope .= '</ShippingContact>';

                        } elseif ($key == 'BillingContact') {
                            $createCustomerEnvelope .= '<BillingContact> <ID xsi:nil="true" ></ID><Delete>false</Delete>';
                            foreach ($_value as $_key => $_data) {
                                if ($_key == 'Address') {
                                    $createCustomerEnvelope .= '<Address> <ID xsi:nil="true" ></ID><Delete>false</Delete>';
                                    foreach ($_data as $_akey => $_addrData) {
                                        if ($_akey == 'State' && empty($_addrData)) continue;
                                        if ($_addrData) {
                                            $createCustomerEnvelope .= '<' . $_akey . '><Value><![CDATA[' . htmlspecialchars( trim(strtoupper($_addrData))) . ']]></Value><HasError>false</HasError></' . $_akey . '>';
                                        }
                                    }
                                    $createCustomerEnvelope .= '</Address>';
                                } else {
                                    if ($_key == "CompanyName") {
                                        $_key = "DisplayName";
                                    }
                                    $createCustomerEnvelope .= '<' . $_key . '><Value><![CDATA[' . htmlspecialchars( trim($_data)) . ']]></Value><HasError>false</HasError></' . $_key . '>';
                                }
                            }
                            $createCustomerEnvelope .= '</BillingContact>';
                        } else {
                            $createCustomerEnvelope .= '<' . $key . '><Value><![CDATA[' . htmlspecialchars( trim(strtoupper($_value))) . ']]></Value><HasError>false</HasError></' . $key . '>';
                        }
                    }
		    //mail("akashc@kensium.com","Customer Envelope",$createCustomerEnvelope);
                    if (isset($data[$customerId]['shipping']) && array_filter($data[$customerId]['shipping'])) {
                        $createCustomerEnvelope .= '<ShippingAddressSameAsMain><Value>false</Value><HasError>false</HasError></ShippingAddressSameAsMain><ShippingContactSameAsMain><Value>false</Value><HasError>false</HasError></ShippingContactSameAsMain>';
                    } else {
                        $createCustomerEnvelope .= '<ShippingAddressSameAsMain><Value>true</Value><HasError>false</HasError></ShippingAddressSameAsMain><ShippingContactSameAsMain><Value>true</Value><HasError>false</HasError></ShippingContactSameAsMain>';
                    }
                    $csvCreateCustomer = $this->helper->getEnvelopeData('CREATECUSTOMER');
                    $envCreateCustomer = $csvCreateCustomer['envelope'];
                    $createCustomerEnvelope = str_replace('{{CREATECUSTOMER}}', $createCustomerEnvelope, $envCreateCustomer);
                    $action = $csvCreateCustomer['envName'] . '/' . $csvCreateCustomer['envVersion'] . '/' . $csvCreateCustomer['methodName'];
                    try {

                        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl', 'stores', $storeId);
                        if (!isset($serverUrl)) {
                            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
                        }
                        $url = $this->common->getBasicConfigUrl($serverUrl);
                        $configParams = $this->amconnectorHelper->getConfigParameters($storeId);
                        $XMLGetRequest = $createCustomerEnvelope;
                        $XMLGetRequest = str_replace("&", "&amp;", $XMLGetRequest);
                        $xml = $this->common->getAcumaticaResponse($configParams,$XMLGetRequest, $url, $action,$branchName);
                        if(isset($xml->Body->PutResponse->PutResult) && is_object($xml->Body->PutResponse->PutResult))
                        {
                            $data = $xml->Body->PutResponse->PutResult;
                            $totalData = $this->xmlHelper->xml2array($data);
                            $AcumaticaCustomerId = '';
                            if (isset($totalData['CustomerID']['Value']))
                                $AcumaticaCustomerId = $totalData['CustomerID']['Value'];
                            if ($AcumaticaCustomerId != '') {
                                $customerId = $aData['magento_id'];
                                if ($flag == 'ORDER') {

                                    /*
                                     * Here need to save email,acumatica customer id in amconnector_customer_order_mapping table
                                     * because if same customer can place order as a guest or normal
                                     * So we need to check that customer is exist in acumatica or not before run order sync
                                     */
                                    $emailOfCustomer = $totalData['MainContact']['Email']['Value'];
                                    $sendDatatoMapping = $this->resource->sendDataToMapping($emailOfCustomer, $AcumaticaCustomerId, $storeId);
                                } else {

                                    $this->resource->updateCustomerAttribute('acumatica_customer_id', $AcumaticaCustomerId, $customer['entity_id']);
                                }

                                $customerLog['customerId'] = $customer['entity_id']; //Current customer ID
                                $customerLog['email'] = $customer['email']; //Current customer Email
                                $customerLog['description'] = "Customer Id:" . $AcumaticaCustomerId . " Synced To Acumatica"; //Descripton
                                $customerLog['syncAction'] = "Customer Synced To Acumatica";
                                $customerLog['accumaticaCustomerId'] = $AcumaticaCustomerId;
                                $customerLog['syncDirection'] = "syncToAcumatica";
                                $customerLog['messageType'] = "Success";
                                $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
                                file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                            } else {
                                $customerLog['customerId'] = $customer->getId(); //Current customer ID
                                $customerLog['email'] = $customer->getEmail(); //Current customer Email
                                $customerLog['description'] = json_encode($xml); //Descripton
                                $customerLog['syncAction'] = "Customer Not Synced";
                                if ($acumaticaCustId != '') {
                                    $customerLog['accumaticaCustomerId'] = $acumaticaCustId;
                                } else {
                                    $customerLog['accumaticaCustomerId'] = "";
                                }
                                $customerLog['syncDirection'] = "syncToAcumatica";
                                $customerLog['messageType'] = "Failure";
                                $customerLogHelper->customerSyncSuccessLogs($customerLog);
                                $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
                                file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                                $this->errorCheckInAcumatica[] = 1;
                            }
                        }else{
                            $customerLog['customerId'] = $customer['entity_id']; //Current customer ID
                            $customerLog['email'] = $customer['email']; //Current customer Email
                            $customerLog['description'] = json_encode($xml); //Descripton
                            $customerLog['syncAction'] = "Customer Not Synced";
                            if ($acumaticaCustId != '') {
                                $customerLog['accumaticaCustomerId'] = $acumaticaCustId;
                            } else {
                                $customerLog['accumaticaCustomerId'] = "";
                            }
                            $customerLog['syncDirection'] = "syncToAcumatica";
                            $customerLog['messageType'] = "Failure";
                            $customerLogHelper->customerSyncSuccessLogs($customerLog);
                            $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
                            file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                            $this->errorCheckInAcumatica[] = 1;
                        }
                    } catch (SoapFault $e) {
                        $customerLog['customerId'] = $customer['entity_id']; //Current customer ID
                        $customerLog['email'] = $customer['email']; //Current customer Email
                        $customerLog['description'] = $e->getMessage(); //Descripton

                        /* Send email when error in saving customer*/
                        $errorMsg = $e->getMessage() . "/n" . $e->getTraceAsString();
                        $syncName = 'Customer Sync';
                        $this->amconnectorHelper->errorLogEmail($syncName, $errorMsg);
                        $customerLog['syncAction'] = "Customer Not Synced";
                        if ($acumaticaCustId != '') {
                            $customerLog['accumaticaCustomerId'] = $acumaticaCustId;
                        } else {
                            $customerLog['accumaticaCustomerId'] = "";
                        }
                        $customerLog['syncDirection'] = "syncToAcumatica";
                        $customerLog['messageType'] = "Failure";
                        $customerLogHelper->customerSyncSuccessLogs($customerLog);
                        $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
                        file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                        $this->errorCheckInAcumatica[] = 1;
                    }
                } else {
                    /**
                     * logs here for customer required fields
                     */
                    $customerLog['customerId'] = $customer['entity_id']; //Current customer ID
                    $customerLog['email'] = $customer['email']; //Current customer Email
                    $customerLog['description'] = "Required Fields Are Empty To Sync Customer In Acumatica"; //Descripton
                    $customerLog['syncAction'] = "Customer Not Synced";
                    if ($acumaticaCustId != '') {
                        $customerLog['accumaticaCustomerId'] = $acumaticaCustId;
                    } else {
                        $customerLog['accumaticaCustomerId'] = "";
                    }
                    $customerLog['syncDirection'] = "syncToAcumatica";
                    $customerLog['messageType'] = "Failure";
                    $customerLogHelper->customerSyncSuccessLogs($customerLog);
                    $txt = $this->timezone->date(time())->format('Y-m-d H:i:s') . " : Info : " . $customerLog['description'];
                    file_put_contents($logViewFileName, $txt . PHP_EOL, FILE_APPEND);
                    $this->errorCheckInAcumatica[] = 1;
                }
            }
        }
        if (isset($this->errorCheckInAcumatica)) {

            return $this->errorCheckInAcumatica;
        } else {

            return '';
        }
    }


    /**
     * @param $zipcode
     * @return string
     * US zipcode validation
     */
    public function validateUSAZip($zipcode)
    {
        $zipCode = "";
        if(preg_match("/^([0-9]{5})(-[0-9]{4})?$/i",$zipcode)) {
            $zipCode =  $zipcode;
        } else {
            $count = substr_count($zipcode, '-');
            if($count == 0) {
                $zipCode = substr($zipcode, 0, 5);
            } else {
                $zipcodes = explode("-",$zipcode);
                $zipCode = substr($zipcodes[0], 0, 5)."-".substr($zipcodes[1], 0, 4);
            }
        }
        return $zipCode;
    }
}

