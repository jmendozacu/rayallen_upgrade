<?php
/**
 * Kensium_Contact extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Contact
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Contact\Block\Adminhtml\Contact;

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
     * Initialize Contact edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'contact_id';
        $this->_blockGroup = 'Kensium_Contact';
        $this->_controller = 'adminhtml_contact';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Contact'));
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
        $this->buttonList->update('delete', 'label', __('Delete Contact'));
    }
    /**
     * Retrieve text for header element depending on loaded Contact
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Kensium\Contact\Model\Contact $contact */
        $contact = $this->coreRegistry->registry('kensium_contact_contact');
        if ($contact->getId()) {
            return __("Edit Contact '%1'", $this->escapeHtml($contact->getFname()));
        }
        return __('New Contact');
    }
}
