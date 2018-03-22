<?php
/***
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class CreditmemosTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    public function setUp()
    {
        $this->resource = 'Magento_SalesArchive::creditmemos';
        $this->uri = 'backend/sales/archive/creditmemos';
        parent::setUp();
    }
}
