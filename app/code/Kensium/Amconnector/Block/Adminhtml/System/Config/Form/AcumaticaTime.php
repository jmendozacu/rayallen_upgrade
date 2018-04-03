<?php
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Kensium\Amconnector\Helper\Time;
use Psr\Log\LoggerInterface as Logger;
use Kensium\Amconnector\Model\TimeFactory;
use Kensium\Amconnector\Block\Adminhtml\System\Config\Fields;

class AcumaticaTime extends Fields
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
     * @param Time $timeHelper
     * @param Logger $logger
     */
    public function __construct(
        Time $timeHelper,
        Logger $logger,
        TimeFactory $timeFactory,
        \Magento\Framework\App\Request\Http $request,
        \Kensium\Amconnector\Helper\Sync $sync,
        \Magento\Store\Model\Website $websiteRepository
    )
    {
        $this->timeHelper = $timeHelper;
        $this->logger = $logger;
        $this->timeFactory = $timeFactory;
        $this->request = $request;
        $this->sync = $sync;
        $this->websiteRepository = $websiteRepository;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function _getElementHtml(AbstractElement $element)
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
            $acumaticaTime = $timeData['accumatica_time'];
            $acumaticaTimeZone = $timeData['accumatica_timezone'];
            $time = $acumaticaTime.' '.$acumaticaTimeZone;
        }
        return $time;
    }
}
