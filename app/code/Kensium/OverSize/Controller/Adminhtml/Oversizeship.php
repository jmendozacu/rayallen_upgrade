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
namespace Kensium\OverSize\Controller\Adminhtml;

abstract class Oversizeship extends \Magento\Backend\App\Action
{
    /**
     * Over Size Ship Factory
     * 
     * @var \Kensium\OverSize\Model\OversizeshipFactory
     */
    protected $oversizeshipFactory;

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
     * @param \Kensium\OverSize\Model\OversizeshipFactory $oversizeshipFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Kensium\OverSize\Model\OversizeshipFactory $oversizeshipFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->oversizeshipFactory   = $oversizeshipFactory;
        $this->coreRegistry          = $coreRegistry;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Init Over Size Ship
     *
     * @return \Kensium\OverSize\Model\Oversizeship
     */
    protected function initOversizeship()
    {
        $oversizeshipId  = (int) $this->getRequest()->getParam('oversizeship_id');
        /** @var \Kensium\OverSize\Model\Oversizeship $oversizeship */
        $oversizeship    = $this->oversizeshipFactory->create();
        if ($oversizeshipId) {
            $oversizeship->load($oversizeshipId);
        }
        $this->coreRegistry->register('kensium_oversize_oversizeship', $oversizeship);
        return $oversizeship;
    }
}
