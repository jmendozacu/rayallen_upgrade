<?php
namespace Iglobal\Stores\Model;

class Tax extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
	protected $_code = 'tax';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
    }
    public function setCode($code)
    {
			// store the international fee in the tax field.
			return $this;
    }

	public function collect(
		\Magento\Quote\Model\Quote $quote,
		\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
		\Magento\Quote\Model\Quote\Address\Total $total
	){
		if($this->scopeConfig->getValue('iglobal_integration/apireqs/ice_toggle', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
		{
			$fee = $this->registry->registry('duty_tax');
			if ($fee)
			{
				$this->_setAddress($shippingAssignment->getShipping()->getAddress());
                $this->_setTotal($total);
				$this->_setAmount($fee);
				$this->_setBaseAmount($fee);
			}
		}
		return $this;
	}
}
