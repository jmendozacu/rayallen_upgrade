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
namespace Kensium\Catalogrequest\Block\Adminhtml\Catalogrequest;

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
     * Initialize Catalogrequest edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'catalogrequest_id';
        $this->_blockGroup = 'Kensium_Catalogrequest';
        $this->_controller = 'adminhtml_catalogrequest';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Catalogrequest'));
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
        $this->buttonList->update('delete', 'label', __('Delete Catalogrequest'));
    }
    /**
     * Retrieve text for header element depending on loaded Catalogrequest
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Kensium\Catalogrequest\Model\Catalogrequest $catalogrequest */
        $catalogrequest = $this->coreRegistry->registry('kensium_catalogrequest_catalogrequest');
        if ($catalogrequest->getId()) {
            return __("Edit Catalogrequest '%1'", $this->escapeHtml($catalogrequest->getFname()));
        }
        return __('New Catalogrequest');
    }
}
