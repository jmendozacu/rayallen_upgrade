<?php
namespace Iglobal\Stores\Model;

class Carrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface{
	protected $_code = 'ig';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Whether this carrier has fixed rates calculation
     *
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Rate result data
     *
     * @var \Magento\Shipping\Model\Rate\Result|null
     */
    protected $_result = null;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory
     */
    protected $_rateErrorFactory;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;


  /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Registry $registry,
     * @param array $data
  */
  public function __construct(
      \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
      \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
      \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
      \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
      \Psr\Log\LoggerInterface $logger,
      \Magento\Framework\Registry $registry,
      array $data = []
  ) {
      parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
      $this->_registry = $registry;
      $this->_rateFactory = $rateFactory;
      $this->_rateMethodFactory = $rateMethodFactory;
  }

  public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request){
    $this->setRequest($request);
    if(!$this->isActive()){
          return false;
    }

  	$price = $this->getConfigData('price'); // set a default shipping price maybe 0
    $result = $this->_rateFactory->create();
    $method = $this->_rateMethodFactory->create();
    $show = $this->_registry->registry('shipping_carriertitle');
    if($show){
      $method = $this->_rateMethodFactory->create();
      $method->setCarrier($this->_code);
      $method->setMethod($this->_registry->registry('shipping_carriertitle'));
      $method->setCarrierTitle($this->_code);
      $method->setMethodTitle($this->_registry->registry('shipping_methodtitle'));
      if($this->_registry->registry('shipping_cost')){
 			  $method->setPrice($this->_registry->registry('shipping_cost'));
     		$method->setCost($this->_registry->registry('shipping_cost'));
     	} else {
     		$method->setPrice($price);
     		$method->setCost($price);
     	}
      $result->append($method);
    }
    else{
      $error = $this->_rateErrorFactory->create();
      $error->setCarrier($this->_code);
      $error->setCarrierTitle($this->getConfigData('name'));
    	$error->setErrorMessage($this->getConfigData('specificerrmsg'));
      $result->append($error);
    }
    $this->_result = $result;
    return $result;
  }

	public function getAllowedMethods()
	{
        return array($this->_code =>$this->getConfigData('name'),
            'DHL_EXPRESS' => 'Express',
            'DHL_GLOBAL_MAIL' => 'Global Mail',
            'FEDEX_ECONOMY' => 'Economy',
            'FEDEX_GROUND' => 'Ground',
            'FEDEX_IPD' => 'FedEx IPD',
            'FEDEX_PRIORITY' => 'Priority',
            'UPS_2ND_DAY_AIR' => 'UPS 2 Day Air',
            'UPS_3_DAY_AIR' => 'UPS 3 Day Air',
            'UPS_3_DAY_SELECT' => 'UPS_3_DAY_SELECT',
            'UPS_EXPEDITED' => 'Expedited',
            'UPS_EXPRESS' => 'Express',
            'UPS_EXPRESS_SAVER' => 'Express Saver',
            'UPS_FREIGHT' => 'UPS Freight',
            'UPS_GROUND' => 'Canada Ground',
            'UPS_NEXT_DAY_AIR_SAVER' =>'UPS Next Day Air Saver',
            'UPS_SAVER' => 'UPS_SAVER',
            'UPS_STANDARD' => 'Canada Standard',
            'UPS_WORLDEASE' => 'UPS WorldEase',
            'UPS_WORLDWIDE_EXPEDITED' => 'Expedited',
            'UPS_WORLDWIDE_EXPRESS' => 'Express',
            'USPS_EXPRESS_1' => 'Express 1 Mail',
            'USPS_FIRST_CLASS' => 'First Class Mail',
            'USPS_FIRST_CLASS_MAIL_INTERNATIONAL' => 'First Class Mail, International',
            'USPS_FIRST_CLASS_PACKAGE_INTL_SERVICE' => 'First Class Mail, International',
            'USPS_PRIORITY' => 'USPS Priority',
            'USPS_PRIORITY_DOMESTIC' =>'USPS Priority Domestic',
            'USPS_PRIORITY_EXPRESS' => 'USPS Priority Express',
            'USPS_PRIORITY_EXPRESS_INTL' => 'USPS Priority Express',
            'USPS_PRIORITY_INTL' => 'USPS Priority',
            'USPS_PRIORITY_MAIL_EXPRESS_INTERNATIONAL' => 'Priority Mail Express, International',
            'USPS_PRIORITY_MAIL_INTERNATIONAL' => 'Priority Mail, International',
        );
	}
}
