<?php
namespace Iglobal\Stores\Model;



class International extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Iglobal\Stores\Helper\Data
     */
    protected $storesHelper;

    /**
     * @var \Iglobal\Stores\Model\Rest
     */
    protected $storesFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Cart
    */
    protected $cart;

    /**
     * @var \Magento\Framework\Url
    */
    protected $url;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
    */
    protected $moduleList;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Iglobal\Stores\Helper\Data $storesHelper,
        \Iglobal\Stores\Model\RestFactory $storesFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Url $url,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storesHelper = $storesHelper;
        $this->storesFactory = $storesFactory;
        $this->scopeConfig = $scopeConfig;
        $this->cart = $cart;
        $this->url = $url;
        $this->moduleList = $moduleList;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

	public function getTempCartId ()
	{

		//get all the items in the cart
		$quote = $this->cart->getQuote();
		$items = array();
		$helper = $this->storesHelper;
		foreach ($quote->getAllVisibleItems() as $item) {
			$items[] = $helper->getItemDetails($item);
		}

		// Check for discounts to add as a negative line item
        $discount_totals = 0;
        foreach($quote->getAllItems() as $item){
            $discount_totals += $item->getTotalDiscountAmount();
        }
        if($discount_totals > 0)
        {
            $items[] = array(
                'description' => "Discount",
                'quantity' => 1,
                'unitPrice' => $discount_totals * -1
            );
        }

		$rest = $this->storesFactory->create();
		$response = $rest->createTempCart(array(
			"storeId" => $this->scopeConfig->getValue('iglobal_integration/apireqs/iglobalid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
			"referenceId" => $quote->getId(),
			"externalConfirmationPageURL" => $this->url->getUrl('iglobal/success', array('_secure'=> true)),
			// "misc6" => "iGlobal v".$this->moduleList->getOne(self::MODULE_NAME)['setup_version']. ", Magento v".\Magento\Framework\AppInterface::VERSION,
			"items" => $items,));
		return $response->tempCartUUID;
	}
}
