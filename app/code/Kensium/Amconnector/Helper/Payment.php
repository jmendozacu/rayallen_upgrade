<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Webapi\Soap;
use SoapFault;
use Symfony\Component\Config\Definition\Exception\Exception;
use Kensium\Lib;

class Payment extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var
     */
    protected $clientHelper;

    /**
     * @var
     */
    protected $xmlHelper;

    public function __construct(
        Context $context,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        Lib\Common $common,
        \Kensium\Amconnector\Helper\Data $dataHelper
    )
    {
        $this->context = $context;
        $this->syncHelper = $syncHelper;
        $this->clientHelper = $clientHelper;
        $this->xmlHelper = $xmlHelper;
        $this->common = $common;
        $this->amconnectorHelper = $dataHelper;
    }


    /**
     * @param $url
     * @param $storeId
     */
    public function getPaymentSchema($url, $storeId)
    {
        try {

            if ($storeId == 0 || $storeId == NULL) {
                $storeId = 1;
            }
            $csvPaymentSchemaData = $this->syncHelper->getEnvelopeData('GETPAYMENTSCHEMA');
            $XMLGetRequest = $csvPaymentSchemaData['envelope'];
            $action = $csvPaymentSchemaData['envName'] . '/' . $csvPaymentSchemaData['envVersion'] . '/' . $csvPaymentSchemaData['methodName'];
            $configParameters = $this->amconnectorHelper->getConfigParameters($storeId);
            $response = $this->common->getAcumaticaResponse($configParameters, $XMLGetRequest, $url, $action);
            $data = $response->Body->GetListResponse->GetListResult;
            return $data;

        } catch (SoapFault $e) {
            echo $e->getMessage();
        }

    }
}
