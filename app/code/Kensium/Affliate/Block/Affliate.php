<?php
namespace Kensium\Affliate\Block;

class Affliate extends \Magento\Framework\View\Element\Template
{

    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
   
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $_coreRegistry
     * @param Vendor $vendor
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $_coreRegistry,        
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    )
    {
        $this->_coreRegistry = $_coreRegistry;
        $this->_filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }    
    public function getAction()
    {
        return $this->getUrl('*/save/save');
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
