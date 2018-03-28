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
namespace Kensium\Contact\Block\Adminhtml\Contact\Edit\Tab;

class Contact extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * State options
     * 
     * @var \Kensium\Contact\Model\Contact\Source\State
     */
    protected $stateOptions;

    /**
     * Please Do NOT Add Me To The Ray Allen Mailing List options
     * 
     * @var \Kensium\Contact\Model\Contact\Source\OptOut
     */
    protected $optOutOptions;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * constructor
     * 
     * @param \Kensium\Contact\Model\Contact\Source\State $stateOptions
     * @param \Kensium\Contact\Model\Contact\Source\OptOut $optOutOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Kensium\Contact\Model\Contact\Source\State $stateOptions,
        \Kensium\Contact\Model\Contact\Source\OptOut $optOutOptions,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $storeManager,
        array $data = []
    )
    {
        $this->stateOptions  = $stateOptions;
        $this->optOutOptions = $optOutOptions;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Kensium\Contact\Model\Contact $contact */
        $contact = $this->_coreRegistry->registry('kensium_contact_contact');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('contact_');
        $form->setFieldNameSuffix('contact');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Contact Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        if ($contact->getId()) {
            $fieldset->addField(
                'contact_id',
                'hidden',
                ['name' => 'contact_id']
            );
        }
        $fieldset->addField(
            'fname',
            'text',
            [
                'name'  => 'fname',
                'label' => __('First Name'),
                'title' => __('First Name'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'lname',
            'text',
            [
                'name'  => 'lname',
                'label' => __('Last Name'),
                'title' => __('Last Name'),
                'required' => true,
            ]
        );

        $options = [];
        foreach ($this->_storeManager->getWebsites() as $website) {
            $options[] = ['label' => $website->getName(), 'value' => $website->getId()];
        }

        $fieldset->addField(
            'store_id',
            'select',
            [
                'name'  => 'store_id',
                'label' => __('Websites'),
                'title' => __('Websites'),
                'required' => true,
                'values' => $options
            ]
        );

        $fieldset->addField(
            'address',
            'text',
            [
                'name'  => 'address',
                'label' => __('Address'),
                'title' => __('Address'),
            ]
        );
        $fieldset->addField(
            'city',
            'text',
            [
                'name'  => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'state',
            'select',
            [
                'name'  => 'state',
                'label' => __('State'),
                'title' => __('State'),
                'values' => array_merge(['' => ''], $this->stateOptions->toOptionArray()),
            ]
        );
        $fieldset->addField(
            'zip',
            'text',
            [
                'name'  => 'zip',
                'label' => __('Postal Code'),
                'title' => __('Postal Code'),
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name'  => 'email',
                'label' => __('Email Address'),
                'title' => __('Email Address'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'phone',
            'text',
            [
                'name'  => 'phone',
                'label' => __('Phone'),
                'title' => __('Phone'),
            ]
        );
        $fieldset->addField(
            'fax',
            'text',
            [
                'name'  => 'fax',
                'label' => __('Fax'),
                'title' => __('Fax'),
            ]
        );

        $fieldset->addField(
            'company',
            'text',
            [
                'name'  => 'company',
                'label' => __('Company'),
                'title' => __('Company'),
            ]
        );
        $fieldset->addField(
            'position',
            'text',
            [
                'name'  => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
            ]
        );
        $fieldset->addField(
            'comments',
            'textarea',
            [
                'name'  => 'comments',
                'label' => __('Comments'),
                'title' => __('Comments'),
            ]
        );
        $fieldset->addField(
            'opt_out',
            'multiselect',
            [
                'name'  => 'opt_out',
                'label' => __('Please Do NOT Add Me To The Ray Allen Mailing List'),
                'title' => __('Please Do NOT Add Me To The Ray Allen Mailing List'),
                'values' => $this->optOutOptions->toOptionArray(),
            ]
        );

        $contactData = $this->_session->getData('kensium_contact_contact_data', true);
        if ($contactData) {
            $contact->addData($contactData);
        } else {
            if (!$contact->getId()) {
                $contact->addData($contact->getDefaultValues());
            }
        }
        $form->addValues($contact->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Contact');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
