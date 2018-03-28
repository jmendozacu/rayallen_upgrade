<?php
namespace Kensium\Testimonial\Block;


class Testimonial extends \Magento\Framework\View\Element\Template
{

    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var Testimonial
     */
    protected $_testimonial;

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
        \Kensium\Testimonial\Model\Testimonial $testimonial,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    )
    {
        $this->_coreRegistry = $_coreRegistry;
        $this->_testimonial = $testimonial;
        $this->_filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    /*
     * It will return array of vendor names
     * @return array
     */
    public function getTestimonialsCollection()
    {
        $result = $this->_testimonial->getTestimonialsCollection();
        return $result;
    }

    /**
     * @param $content
     * @return string
     * @throws \Exception
     */
    public function converttoHtml($content)
    {
        $html = $this->_filterProvider->getBlockFilter()->filter($content);
        return $html;
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
