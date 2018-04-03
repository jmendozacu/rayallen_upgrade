<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Kensium\Amconnector\Helper\Time;
use Psr\Log\LoggerInterface as Logger;
use Kensium\Amconnector\Model\TimeFactory;
use Kensium\Amconnector\Block\Adminhtml\System\Config\Fields;

class ServerUrl extends Fields
{

    /**
     * @var Time
     */
    public $timeHelper;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var Collection
     */
    public $timeCollection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $syncResourceModel;

    /**
     * @param Time $timeHelper
     * @param Logger $logger
     * @param TimeFactory $timeFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Kensium\Amconnector\Helper\Sync $sync
     * @param \Magento\Store\Model\Website $websiteRepository
     */
    public function __construct(
        Time $timeHelper,
        Logger $logger,
        TimeFactory $timeFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Kensium\Amconnector\Helper\Sync $sync,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Magento\Store\Model\Website $websiteRepository
    )
    {
        $this->timeHelper = $timeHelper;
        $this->logger = $logger;
        $this->timeFactory = $timeFactory;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->request = $request;
        $this->sync = $sync;
        $this->websiteRepository = $websiteRepository;
        $this->syncResourceModel = $syncResourceModel;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $storeId = $this->request->getParam('store');
        if($storeId == 0 || $storeId == NULL){
            $scope = 'default';
        }else{
            $scope = 'stores';
        }
        $serverUrl = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/serverUrl',$scope,$storeId);
        if($serverUrl == '' && $storeId==1){
            $serverUrl = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/serverUrl');
        }
        $html = "
        <form name='serverForm'>
            <input value='$serverUrl' class='required-entry input-text admin__control-text' type='text' id='amconnectorcommon_amconnectoracucon_serverUrl' name='groups[amconnectoracucon][fields][serverUrl][value]' onchange='javascript:checkUrl(); return false;'>
        ";
        $html .= "<span id='statusImage' ></span><span style='color: red' id='statusMessage' class='statusMsg'></span><span style='color: green' id='statussuccessMsg' class='statussuccessMsg'></span>";
        $html .= "</form> ";
        return $html;
    }
}