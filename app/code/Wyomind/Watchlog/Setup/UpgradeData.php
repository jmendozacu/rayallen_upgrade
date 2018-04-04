<?php

/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\Watchlog\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade data for SImple Google Shopping
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        
        // $context->getVersion() = version du module actuelle
        // 10.0.0 = version en cours d'installation
        
        if (version_compare($context->getVersion(), '2.0.0') < 0) {
            $installer = $setup;
            $installer->startSetup();
            // do what you have to do
            
            $installer->endSetup();
        }
    }
}
