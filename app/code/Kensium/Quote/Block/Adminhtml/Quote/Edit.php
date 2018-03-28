<?php
/**
 * Kensium_Quote extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Quote
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Quote\Block\Adminhtml\Quote;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize Quote edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'quote_id';
        $this->_blockGroup = 'Kensium_Quote';
        $this->_controller = 'adminhtml_quote';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Quote'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Quote'));
    }
    /**
     * Retrieve text for header element depending on loaded Quote
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Kensium\Quote\Model\Quote $quote */
        $quote = $this->coreRegistry->registry('kensium_quote_quote');
        if ($quote->getId()) {
            return __("Edit Quote '%1'", $this->escapeHtml($quote->getFname()));
        }
        return __('New Quote');
    }
}
