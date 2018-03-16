<?php
/**
 * Kensium_OverSize extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_OverSize
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\OverSize\Controller\Adminhtml\Oversizeship;

class Edit extends \Kensium\OverSize\Controller\Adminhtml\Oversizeship
{
    /**
     * Backend session
     * 
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * Page factory
     * 
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Result JSON factory
     * 
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * constructor
     * 
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Kensium\OverSize\Model\OversizeshipFactory $oversizeshipFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Kensium\OverSize\Model\OversizeshipFactory $oversizeshipFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->backendSession    = $backendSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($oversizeshipFactory, $registry, $resultRedirectFactory, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Kensium_OverSize::oversizeship');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('oversizeship_id');
        /** @var \Kensium\OverSize\Model\Oversizeship $oversizeship */
        $oversizeship = $this->initOversizeship();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Kensium_OverSize::oversizeship');
        $resultPage->getConfig()->getTitle()->set(__('Oversized Item'));
        if ($id) {
            $oversizeship->load($id);
            if (!$oversizeship->getId()) {
                $this->messageManager->addError(__('This Over Size Ship no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'kensium_oversize/*/edit',
                    [
                        'oversizeship_id' => $oversizeship->getId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }
        $title = $oversizeship->getId() ? $oversizeship->getSku() : __('New Oversized Item');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $data = $this->backendSession->getData('kensium_oversize_oversizeship_data', true);
        if (!empty($data)) {
            $oversizeship->setData($data);
        }
        return $resultPage;
    }
}
