<?php
namespace Kensium\GiftCard\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action {
    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;
    /**      * @param \Magento\Framework\App\Action\Context $context      */
    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory)     {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {

        $usage = $this->_objectManager->create('Magento\GiftCardAccount\Model\Pool')->getPoolUsageInfo();
        $unUsedCodes = $usage->getFree();
        echo $unUsedCodes;exit;
        if($unUsedCodes < 10){
            try {
                $this->_objectManager->create('Magento\GiftCardAccount\Model\Pool')->generatePool();
                $this->messageManager->addSuccess(__('New code pool was generated.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We were unable to generate a new code pool'));
            }
        }else{
            $this->messageManager->addSuccess(__('still more than 50 free codes available'));
        }
        //echo 'ssshi';exit;
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('GIFT CARD'));
        return $resultPage;
//        $this->_view->loadLayout();
//        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Gift Card Accounts'));
//        $this->_view->renderLayout();
    }
}