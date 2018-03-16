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
namespace Kensium\Catalogrequest\Block\Adminhtml\Catalogrequest\Edit\Tab;

class Catalogrequest extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * State options
     * 
     * @var \Kensium\Catalogrequest\Model\Catalogrequest\Source\State
     */
    protected $stateOptions;

    /**
     * Please Do NOT Add Me To The Ray Allen Mailing List options
     * 
     * @var \Kensium\Catalogrequest\Model\Catalogrequest\Source\OptOut
     */
    protected $optOutOptions;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * constructor
     * 
     * @param \Kensium\Catalogrequest\Model\Catalogrequest\Source\State $stateOptions
     * @param \Kensium\Catalogrequest\Model\Catalogrequest\Source\OptOut $optOutOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Kensium\Catalogrequest\Model\Catalogrequest\Source\State $stateOptions,
        \Kensium\Catalogrequest\Model\Catalogrequest\Source\OptOut $optOutOptions,
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
        /** @var \Kensium\Catalogrequest\Model\Catalogrequest $catalogrequest */
        $catalogrequest = $this->_coreRegistry->registry('kensium_catalogrequest_catalogrequest');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('catalogrequest_');
        $form->setFieldNameSuffix('catalogrequest');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Catalogrequest Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        if ($catalogrequest->getId()) {
            $fieldset->addField(
                'catalogrequest_id',
                'hidden',
                ['name' => 'catalogrequest_id']
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
            'title',
            'text',
            [
                'name'  => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
            ]
        );
        $fieldset->addField(
            'businessname',
            'text',
            [
                'name'  => 'businessname',
                'label' => __('Company/Division/Dept'),
                'title' => __('Company/Division/Dept'),
            ]
        );
        $fieldset->addField(
            'address',
            'text',
            [
                'name'  => 'address',
                'label' => __('Address'),
                'title' => __('Address'),
                'required' => true,
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
                'required' => true,
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
                'required' => true,
            ]
        );
        $fieldset->addField(
            'country',
            'text',
            [
                'name'  => 'country',
                'label' => __('Country'),
                'title' => __('Country'),
                'required' => true,
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
                'label' => __('Phone Number'),
                'title' => __('Phone Number'),
            ]
        );
        $fieldset->addField(
            'hearing',
            'text',
            [
                'name'  => 'hearing',
                'label' => __('How Did You Hear About Our Store?'),
                'title' => __('How Did You Hear About Our Store?'),
            ]
        );
        $fieldset->addField(
            'opt_out',
            'select',
            [
                'name'  => 'opt_out',
                'label' => __('Please Do NOT Add Me To The Ray Allen Mailing List'),
                'title' => __('Please Do NOT Add Me To The Ray Allen Mailing List'),
                'required' => true,
                'values' => array_merge(['' => ''], $this->optOutOptions->toOptionArray()),
            ]
        );

        $catalogrequestData = $this->_session->getData('kensium_catalogrequest_catalogrequest_data', true);
        if ($catalogrequestData) {
            $catalogrequest->addData($catalogrequestData);
        } else {
            if (!$catalogrequest->getId()) {
                $catalogrequest->addData($catalogrequest->getDefaultValues());
            }
        }
        $form->addValues($catalogrequest->getData());
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
        return __('Catalogrequest');
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
