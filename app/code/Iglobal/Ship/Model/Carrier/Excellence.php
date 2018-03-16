<?php
namespace Iglobal\Ship\Model\Carrier;
use \Magento\Shipping\Model\Rate\Result;

class Excellence extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface{
	protected $_code = 'excellence';

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
     * @var \Magent\Shipping\Model\Rate\Result|null
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
    $show = $this->_registry->registry('shipping_cost');
    if($show){
      $method = $this->_rateMethodFactory->create();
      $method->setCarrier($this->_code);
      $method->setMethod($this->_code);
      $method->setCarrierTitle($this->_registry->registry('shipping_carriertitle'));
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
		return array('excellence'=>$this->getConfigData('name'));
	}
}
