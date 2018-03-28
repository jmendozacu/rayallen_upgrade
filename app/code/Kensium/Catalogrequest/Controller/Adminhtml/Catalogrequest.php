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
namespace Kensium\Catalogrequest\Controller\Adminhtml;

abstract class Catalogrequest extends \Magento\Backend\App\Action
{
    /**
     * Catalogrequest Factory
     * 
     * @var \Kensium\Catalogrequest\Model\CatalogrequestFactory
     */
    protected $catalogrequestFactory;

    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     * 
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * constructor
     * 
     * @param \Kensium\Catalogrequest\Model\CatalogrequestFactory $catalogrequestFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Kensium\Catalogrequest\Model\CatalogrequestFactory $catalogrequestFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->catalogrequestFactory = $catalogrequestFactory;
        $this->coreRegistry          = $coreRegistry;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Init Catalogrequest
     *
     * @return \Kensium\Catalogrequest\Model\Catalogrequest
     */
    protected function initCatalogrequest()
    {
        $catalogrequestId  = (int) $this->getRequest()->getParam('catalogrequest_id');
        /** @var \Kensium\Catalogrequest\Model\Catalogrequest $catalogrequest */
        $catalogrequest    = $this->catalogrequestFactory->create();
        if ($catalogrequestId) {
            $catalogrequest->load($catalogrequestId);
        }
        $this->coreRegistry->register('kensium_catalogrequest_catalogrequest', $catalogrequest);
        return $catalogrequest;
    }
}
