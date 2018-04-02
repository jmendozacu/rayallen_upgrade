<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Block\Adminhtml\Sitemap\Edit;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;

class Form extends GenericForm
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $store;

    protected $formValues = [];

    /**
     * Form constructor.
     * @param Store $store
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Store $store,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->store               =  $store;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    public function _construct()
    {
        parent::_construct();
        $this->setId('sitemap_form');
    }

    protected function _initFormValues()
    {
        $model = $this->_getModel();

        if ($model && $model->getId()) {
            $this->_formValues = [
                'sitemap_id' => $model->getId(),
                'sitemap_filename' => $model->getSitemapFilename(),
                'sitemap_path' => $model->getSitemapPath(),
                'store_id' => $model->getStoreId()
            ];
        } else {
            $this->_formValues = [
                'sitemap_id' => '',
                'sitemap_filename' => '',
                'sitemap_path' => '',
                'store_id' => $this->_storeManager->getStore(true)->getId()
            ];
        }

        return $this;
    }

    /**
     * Prepare the form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $this->_initFormValues();

        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ],
            ]
        );

        $form->setHtmlIdPrefix('sitemap_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Sitemap')]);

        $fieldset->addField(
            'sitemap_id',
            'hidden',
            [
                'name' => 'sitemap_id',
                'value' => $this->_formValues['sitemap_id']
            ]
        );

        $fieldset->addField(
            'sitemap_filename',
            'text',
            [
                'label' => __('Filename'),
                'title' => __('Filename'),
                'name' => 'sitemap_filename',
                'required' => true,
                'value' => $this->_formValues['sitemap_filename'],
                'note' => 'Example: sitemap.xml'
            ]
        );


        $fieldset->addField(
            'sitemap_path',
            'text',
            [
                'label' => __('Path'),
                'title' => __('Path Card Code'),
                'name' => 'sitemap_path',
                'required' => true,
                'value' => $this->_formValues['sitemap_path'],
                'note' => 'Example: "/sitemap/" or "/" for base path (path must be writeable)'
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'select',
                [
                    'name'     => 'store_id',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $this->store->getStoreValuesForForm(false, false)
                ]
            );
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                    'name' => 'store_id',
                    'value' => $this->_formValues['store_id']
                ]
            );
        }

        $form->setValues($this->_formValues);
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get Sitemap model instance
     *
     * @return \MageWorx\XmlSitemap\Model\Sitemap
     */
    protected function _getModel()
    {
        return $this->_coreRegistry->registry('mageworx_xmlsitemap_sitemap');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}

