<?php
namespace Iglobal\Stores\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface{
  /**
   * @var \Iglobal\Stores\Model\SetupFactory
  */
  private $storeSetupFactory;

  public function __construct(
      \Iglobal\Stores\Model\SetupFactory $storeSetupFactory
    ){
      $this->storeSetupFactory = $storeSetupFactory;
  }

  public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context){
    $setup->startSetup();
    $storeSetupFactory = $this->storeSetupFactory->create(["setup" => $setup]);

    // Create iglobal attributes
    $storeSetupFactory->createAttribute('iGlobal Length','ig_length', 'text');
    $storeSetupFactory->createAttribute('iGlobal Width', 'ig_width', 'text');
    $storeSetupFactory->createAttribute('iGlobal Height', 'ig_height', 'text');
    $storeSetupFactory->createAttribute('iGlobal Weight', 'ig_weight', 'text');
        $weightUnits['value']['option_1'][0] = 'lbs';
        $weightUnits['value']['option_2'][0] = 'kg';
        $weightUnits['value']['option_3'][0] = 'oz';
        $weightUnits['value']['option_4'][0] = 'g';
    $storeSetupFactory->createAttribute('iGlobal Weight Units','ig_weight_units','select', $weightUnits );
        $dimUnits['value']['option_1'][0] = 'in';
        $dimUnits['value']['option_2'][0] = 'cm';
    $storeSetupFactory->createAttribute('iGlobal Dimension Units','ig_dimension_units','select', $dimUnits );
    $setup->endSetup();
  }

}
