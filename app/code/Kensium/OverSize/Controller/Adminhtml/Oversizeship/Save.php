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

class Save extends \Kensium\OverSize\Controller\Adminhtml\Oversizeship
{
    /**
     * Backend session
     * 
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * constructor
     * 
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Kensium\OverSize\Model\OversizeshipFactory $oversizeshipFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Kensium\OverSize\Model\OversizeshipFactory $oversizeshipFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->backendSession = $backendSession;
        parent::__construct($oversizeshipFactory, $registry, $resultRedirectFactory, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('oversizeship');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $oversizeship = $this->initOversizeship();
            $oversizeship->setData($data);
            $this->_eventManager->dispatch(
                'kensium_oversize_oversizeship_prepare_save',
                [
                    'oversizeship' => $oversizeship,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $oversizeship->save();
                $this->messageManager->addSuccess(__('The Over Size Ship has been saved.'));
                $this->backendSession->setKensiumOverSizeOversizeshipData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'kensium_oversize/*/edit',
                        [
                            'oversizeship_id' => $oversizeship->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('kensium_oversize/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Over Size Ship.'));
            }
            $this->_getSession()->setKensiumOverSizeOversizeshipData($data);
            $resultRedirect->setPath(
                'kensium_oversize/*/edit',
                [
                    'oversizeship_id' => $oversizeship->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('kensium_oversize/*/');
        return $resultRedirect;
    }
}
