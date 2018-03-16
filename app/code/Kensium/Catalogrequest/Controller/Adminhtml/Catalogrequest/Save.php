<?php
/**
 * Kensium_Catalogrequest extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Catalogrequest
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Catalogrequest\Controller\Adminhtml\Catalogrequest;

class Save extends \Kensium\Catalogrequest\Controller\Adminhtml\Catalogrequest
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
     * @param \Kensium\Catalogrequest\Model\CatalogrequestFactory $catalogrequestFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Kensium\Catalogrequest\Model\CatalogrequestFactory $catalogrequestFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->backendSession = $backendSession;
        parent::__construct($catalogrequestFactory, $registry, $resultRedirectFactory, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('catalogrequest');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $catalogrequest = $this->initCatalogrequest();
            $catalogrequest->setData($data);
            $this->_eventManager->dispatch(
                'kensium_catalogrequest_catalogrequest_prepare_save',
                [
                    'catalogrequest' => $catalogrequest,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $catalogrequest->save();
                $this->messageManager->addSuccess(__('The Catalogrequest has been saved.'));
                $this->backendSession->setKensiumCatalogrequestCatalogrequestData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'kensium_catalogrequest/*/edit',
                        [
                            'catalogrequest_id' => $catalogrequest->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('kensium_catalogrequest/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Catalogrequest.'));
            }
            $this->_getSession()->setKensiumCatalogrequestCatalogrequestData($data);
            $resultRedirect->setPath(
                'kensium_catalogrequest/*/edit',
                [
                    'catalogrequest_id' => $catalogrequest->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('kensium_catalogrequest/*/');
        return $resultRedirect;
    }
}
