<?php
namespace Iglobal\Stores\Model;


class Rest extends \Magento\Framework\Model\AbstractModel
{
    protected $_entryPoint = 'https://api.iglobalstores.com/';
    protected $_store = 3; // default store ID
    protected $_key = '';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }


    protected function _construct()
    {
		//set store ID
		if ($this->scopeConfig->getValue('iglobal_integration/apireqs/iglobalid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $this->_store = $this->scopeConfig->getValue('iglobal_integration/apireqs/iglobalid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
		//Set API Key
		if ($this->scopeConfig->getValue('iglobal_integration/apireqs/secret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $this->_key = $this->scopeConfig->getValue('iglobal_integration/apireqs/secret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
    }

    protected function callApi($path, $data, $headers=array())
    {
        $client = new \Zend_Http_Client($this->_entryPoint . $path);
        $data = json_encode(array_merge($data, array('store' => $this->_store, 'secret' => $this->_key)));
        $response = $client
            ->setRawData($data, 'application/json')
            ->setHeaders($headers)
            ->request('POST');

        if ($response->isSuccessful())
        {
            return json_decode($response->getBody());
        }
        return false;

    }

    public function getAllOrders()
    {
        $data = array('sinceDate'=>'20100101');
        return $this->callApi('v1/orderNumbers', $data);
    }

    public function getAllOrdersSinceDate($data)
    {
        $data = array('sinceDate'=>$data);
        return $this->callApi('v1/orderNumbers', $data);
    }

    public function getOrder($order)
    {
        $data = array('orderId' => $order);
        $response = $this->callApi('v2/orderDetail', $data);
        return $response;
    }

    public function sendMagentoOrderId($igc_order_id, $magento_order_id) {
        $data = array('orderId' => $igc_order_id, 'merchantOrderId' => $magento_order_id);
        return $this->callApi('v1/updateMerchantOrderId', $data);
    }

    public function createTempCart(array $data)
    {
        return $this->callApi('v1/createTempCart', $data);
    }

    public function getRegionId($countryId, $region, $orderid)
    {
        $data = array('countryCode'=> $countryId,'region'=> $region, 'orderId' => $orderid);
        return $this->callApi(
            'v1/magento-region',
            $data,
            array("serviceToken" => "31ae7155-5b9e-461f-8353-9a8c3f8ae35974ffec3a-88dc-4acb-8420-270df7967338")
        );
    }
}
