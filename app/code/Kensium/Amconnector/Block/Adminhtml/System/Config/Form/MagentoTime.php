<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Kensium\Amconnector\Helper\Time;
use Kensium\Amconnector\Model\TimeFactory;
use Symfony\Component\Config\Definition\Exception\Exception;
use Kensium\Amconnector\Block\Adminhtml\System\Config\Fields;

class MagentoTime extends Fields
{
    /**
     * @var Data
     */
    protected $timeHelper;

    /**
     * @var Collection
     */
    public $timeFactory;

    /**
     * @var
     */
    public $request;

    /**
     * @var
     */
    public $sync;

    /**
     * @var
     */
    public $websiteRepository;
    /**
     * @param Time $timeHelper
     * @param Collection $timeCollection
     */
    public function __construct(
        Time $timeHelper,
        \Magento\Framework\App\Request\Http $request,
        TimeFactory $timeFactory,
        \Kensium\Amconnector\Helper\Sync $sync,
        \Magento\Store\Model\Website $websiteRepository
    )
    {
        $this->timeHelper = $timeHelper;
        $this->timeFactory = $timeFactory;
        $this->request = $request;
        $this->sync = $sync;
        $this->websiteRepository = $websiteRepository;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $websiteCode = $this->request->getParam('website');
        $store = $this->request->getParam('store');

        if($websiteCode != ''){
            $website = $websiteCode;
            $storeCode = "";
        }else{
            $website = 'default';
            $storeCode = "";
        }
        if($store != ''){
            $website = "stores";
            $storeCode =  $store;
        }
        if($website == "default"){
            $scopeId = 0;
        }elseif($website == "stores"){
            $scopeId = $storeCode;
        }else{

            $scopeId = $this->websiteRepository->load($website)->getId();
        }
        $timeData = $this->timeFactory->create()->load($scopeId, "scope_id");
        if(count($timeData) == 0){
            $time = '<b class="text-in-red" style="color: #FF0000;">Not verified</b>';
        }else{
            $this->timeHelper->getVerifyTime($scopeId,$flag=1);
            $timeData = $this->timeFactory->create()->load($scopeId, "scope_id");
            $magentoTime = $timeData['magento_time'];
            $magentoTimeZone = $timeData['magento_timezone'];
            $time = $magentoTime.' '.$magentoTimeZone;
        }
        return $time;
    }
}