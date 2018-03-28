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
namespace Kensium\Quote\Block\Adminhtml\Quote\Edit\Tab;


class Quote extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $storeManager,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry,$formFactory,$data);
    }
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Kensium\Quote\Model\Quote $quote */
        $quote = $this->_coreRegistry->registry('kensium_quote_quote');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('quote_');
        $form->setFieldNameSuffix('quote');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Quote Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        if ($quote->getId()) {
            $fieldset->addField(
                'quote_id',
                'hidden',
                ['name' => 'quote_id']
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
            'bname',
            'text',
            [
                'name'  => 'bname',
                'label' => __('Business/Organization Name'),
                'title' => __('Business/Organization Name'),
                'required' => false,
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
            'text',
            [
                'name'  => 'state',
                'label' => __('State/Province'),
                'title' => __('State/Province'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'zip',
            'text',
            [
                'name'  => 'zip',
                'label' => __('ZIP'),
                'title' => __('ZIP'),
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
                'required' => false,
            ]
        );
        $fieldset->addField(
            'phone',
            'text',
            [
                'name'  => 'phone',
                'label' => __('Phone'),
                'title' => __('Phone'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'fax',
            'text',
            [
                'name'  => 'fax',
                'label' => __('FAX'),
                'title' => __('FAX'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name'  => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
            ]
        );
        /* $fieldset->addField(
             'productdata',
             'text',
             [
                 'name'  => 'productdata',
                 'label' => __('Productdata'),
                 'title' => __('Productdata'),
                 'required' => false,
             ]
         );
         */
        $fieldset->addField(
            'qty1',
            'text',
            [
                'name'  => 'qty1',
                'label' => __('Qty1'),
                'title' => __('Qty1'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'item1',
            'text',
            [
                'name'  => 'item1',
                'label' => __('Item1'),
                'title' => __('Item1'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'description1',
            'text',
            [
                'name'  => 'description1',
                'label' => __('Description1'),
                'title' => __('Description1'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'qty2',
            'text',
            [
                'name'  => 'qty2',
                'label' => __('Qty2'),
                'title' => __('Qty2'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'item2',
            'text',
            [
                'name'  => 'item2',
                'label' => __('Item2'),
                'title' => __('Item2'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'description2',
            'text',
            [
                'name'  => 'description2',
                'label' => __('Description2'),
                'title' => __('Description2'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'qty3',
            'text',
            [
                'name'  => 'qty3',
                'label' => __('Qty3'),
                'title' => __('Qty3'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'item3',
            'text',
            [
                'name'  => 'item3',
                'label' => __('Item3'),
                'title' => __('Item3'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'description3',
            'text',
            [
                'name'  => 'description3',
                'label' => __('Description3'),
                'title' => __('Description3'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'qty4',
            'text',
            [
                'name'  => 'qty4',
                'label' => __('Qty4'),
                'title' => __('Qty4'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'item4',
            'text',
            [
                'name'  => 'item4',
                'label' => __('Item4'),
                'title' => __('Item4'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'description4',
            'text',
            [
                'name'  => 'description4',
                'label' => __('Description4'),
                'title' => __('Description4'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'qty5',
            'text',
            [
                'name'  => 'qty5',
                'label' => __('Qty5'),
                'title' => __('Qty5'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'item5',
            'text',
            [
                'name'  => 'item5',
                'label' => __('Item5'),
                'title' => __('Item5'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'description5',
            'text',
            [
                'name'  => 'description5',
                'label' => __('Description5'),
                'title' => __('Description5'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'qty6',
            'text',
            [
                'name'  => 'qty6',
                'label' => __('Qty6'),
                'title' => __('Qty6'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'item6',
            'text',
            [
                'name'  => 'item6',
                'label' => __('Item6'),
                'title' => __('Item6'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'description6',
            'text',
            [
                'name'  => 'description6',
                'label' => __('Description6'),
                'title' => __('Description6'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'qty7',
            'text',
            [
                'name'  => 'qty7',
                'label' => __('Qty7'),
                'title' => __('Qty7'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'item7',
            'text',
            [
                'name'  => 'item7',
                'label' => __('Item7'),
                'title' => __('Item7'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'description7',
            'text',
            [
                'name'  => 'description7',
                'label' => __('Description7'),
                'title' => __('Description7'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'qty8',
            'text',
            [
                'name'  => 'qty8',
                'label' => __('Qty8'),
                'title' => __('Qty8'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'item8',
            'text',
            [
                'name'  => 'item8',
                'label' => __('Item8'),
                'title' => __('Item8'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'description8',
            'text',
            [
                'name'  => 'description8',
                'label' => __('Description8'),
                'title' => __('Description8'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'qty9',
            'text',
            [
                'name'  => 'qty9',
                'label' => __('Qty9'),
                'title' => __('Qty9'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'item9',
            'text',
            [
                'name'  => 'item9',
                'label' => __('Item9'),
                'title' => __('Item9'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'description9',
            'text',
            [
                'name'  => 'description9',
                'label' => __('Description9'),
                'title' => __('Description9'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'qty10',
            'text',
            [
                'name'  => 'qty10',
                'label' => __('Qty10'),
                'title' => __('Qty10'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'item10',
            'text',
            [
                'name'  => 'item10',
                'label' => __('Item10'),
                'title' => __('Item10'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'description10',
            'text',
            [
                'name'  => 'description10',
                'label' => __('Description10'),
                'title' => __('Description10'),
                'required' => false,
            ]
        );

        $quoteData = $this->_session->getData('kensium_quote_quote_data', true);
        if ($quoteData) {
            $quote->addData($quoteData);
        } else {
            if (!$quote->getId()) {
                $quote->addData($quote->getDefaultValues());
            }
        }
        $form->addValues($quote->getData());
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
        return __('Quote');
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
