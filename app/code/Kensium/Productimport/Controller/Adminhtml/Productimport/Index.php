<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Productimport\Controller\Adminhtml\Productimport;

use Magento\Backend\App\Action;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Setup class for product attributes
     *
     * @var \Kensium\Productimport\Model\Attribute
     */
    protected $attributeSetup;

    /**
     * @param \Kensium\Productimport\Model\Attribute $attributeSetup
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Kensium\Productimport\Model\Category $categorySetup,
        \Kensium\Productimport\Model\Attribute $attributeSetup,
        \Kensium\Productimport\Model\Product $productSetup
    ) {
        parent::__construct($context);
        $this->categorySetup = $categorySetup;
        $this->attributeSetup = $attributeSetup;
        $this->productSetup = $productSetup;
    }

    /**
     * Productimports list
     *
     * @return void
     */
    public function execute()
    {
        $this->attributeSetup->install(['Kensium_Productimport::fixtures/attribute_file_sw_new.csv']);
    }
}
