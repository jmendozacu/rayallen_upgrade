<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AcumaticaBaseData extends \Kensium\Amconnector\Controller\Adminhtml\Log\AbstractLog
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $time =  date('Y-m-d');
        $fileName = 'acumaticaBaseData';
        $data['logUrl'] = $this->createBaiscLogFile($fileName);
        $data['logPath'] = BP."/var/log/".$fileName."/".$time."/".$fileName.".log";
        $data['ajaxLogUrl'] = $this->getUrl('amconnector/log/ajaxLog');
        $data['syncStop']= '';
        $data['path'] = '';
        $data['pubStaticUrl'] = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC);
        $data['stopSyncUrl']= '';
        $resultPage = $this->resultPageFactory->create();
        $blockInstance = $resultPage->getLayout()->createBlock(
            '\Magento\Framework\View\Element\Template',
            'amconnectorlogview',
            array()

        )->setTemplate('Kensium_Amconnector::amconnector/logtail.phtml')->assign('data', $data);
        echo $blockInstance->toHtml();
    }
}
