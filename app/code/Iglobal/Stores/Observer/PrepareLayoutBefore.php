<?php

namespace Iglobal\Stores\Observer;

class PrepareLayoutBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Iglobal\Stores\Helper\Data
    */
    protected $_storesHelper;

    public function __construct(
        \Iglobal\Stores\Helper\Data $storesHelper
    ) {
        $this->_storesHelper = $storesHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_storesHelper->isEnabled()) {
            return $this;
        }

        /* @var $block Mage_Page_Block_Html_Head */
        $block = $observer->getEvent()->getBlock();

        if ("head" == $block->getNameInLayout()) {
            foreach ($this->_storesHelper->getFiles() as $file) {
                $block->addJs($this->_storesHelper->getJQueryPath($file));
            }
        }

        return $this;
    }
}
