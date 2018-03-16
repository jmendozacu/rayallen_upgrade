<?php
/*
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\DataFeedManager\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        
        // $context->getVersion() = version du module actuelle
        // 10.0.0 = version en cours d'installation
        
        if (version_compare($context->getVersion(), '10.0.0') < 0) {
            $installer = $setup;
            $installer->startSetup();
            // do what you have to do
            
            $installer->endSetup();
        }
    }
}
