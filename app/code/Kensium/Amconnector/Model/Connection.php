<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface as PsrLogger;
use Magento\Framework\Webapi\Soap;
use Symfony\Component\Config\Definition\Exception\Exception;
use Kensium\Amconnector\Helper\Data;
use Kensium\Amconnector\Helper\Client;
use Kensium\Amconnector\Helper\Url;
use Magento\Framework\Message\ManagerInterface;

class Connection
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var PsrLogger
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $dataHelper;

    protected $clientHelper;

    /**
     * @var Sync
     */
    protected $urlHelper;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param PsrLogger $logger
     * @param Data $dataHelper
     * @param Url $urlHelper
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        PsrLogger $logger,
        Data $dataHelper,
        Client $clientHelper,
        Url $urlHelper,
        ManagerInterface $messageManager
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->dataHelper = $dataHelper;
        $this->clientHelper = $clientHelper;
        $this->urlHelper = $urlHelper;
        $this->messageManager = $messageManager;
    }

    /**
     * @param $params
     * @param $serverUrl
     * @return array|int|DataObject
     */
    public function checkConnection($params, $serverUrl)
    {
        // Default response
        $response = new DataObject([
            'is_valid' => false,
            'request_date' => '',
            'request_identifier' => '',
            'request_success' => false,
            'request_message' => __('Error during Test Connection.')
        ]);
        try {
            if (!empty($params)) {
                $serverUrl = $this->urlHelper->getBasicConfigUrl($serverUrl);
                $response = $this->clientHelper->login($params, $serverUrl);
                $this->logger->critical(new LocalizedException(__('Acumatica connection is successfull!')));
                $this->messageManager->addSuccessMessage( __('Acumatica connection is successfull!') );
                return $response;
            }
        } catch (Exception $e) {
            $this->logger->critical(new LocalizedException(__('Test connection failed. Please try again.')));
            $this->messageManager->addErrorMessage( __('Test connection failed. Please try again.') );
            return $response;
        }
    }


}
