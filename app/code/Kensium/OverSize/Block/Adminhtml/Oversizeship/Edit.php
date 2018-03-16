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
namespace Kensium\OverSize\Block\Adminhtml\Oversizeship;

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
     * Initialize Over Size Ship edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'oversizeship_id';
        $this->_blockGroup = 'Kensium_OverSize';
        $this->_controller = 'adminhtml_oversizeship';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Oversized Item'));
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
        $this->buttonList->update('delete', 'label', __('Delete Oversized Item'));
    }
    /**
     * Retrieve text for header element depending on loaded Over Size Ship
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Kensium\OverSize\Model\Oversizeship $oversizeship */
        $oversizeship = $this->coreRegistry->registry('kensium_oversize_oversizeship');
        if ($oversizeship->getId()) {
            return __("Edit Over Size Ship '%1'", $this->escapeHtml($oversizeship->getSku()));
        }
        return __('New Oversized Item');
    }
}
