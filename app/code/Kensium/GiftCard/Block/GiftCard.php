<?php
namespace Kensium\GiftCard\Block;
use Magento\Framework\View\Element\Template;

class GiftCard extends \Magento\Framework\View\Element\Template
{
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

}