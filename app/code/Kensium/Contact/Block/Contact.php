<?php
namespace Kensium\Contact\Block;

class Contact extends \Magento\Framework\View\Element\Template
{
 public function getAction()
 {
    return $this->getUrl('*/Save/Save');
 }
    /**
     * Get current store name.
     *
     * @return string
     */
    public function getCurrentStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }
    /**
     * Get current store id.
     *
     * @return string
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
?>
