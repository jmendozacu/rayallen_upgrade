<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Ui\Component;

class ColumnFactory extends \Magento\Catalog\Ui\Component\ColumnFactory
{
    /**
     * @var \Amasty\Orderattr\Block\Data\Form\Element\BooleanFactory
     */
    private $booleanFactory;

    /**
     * @var \Amasty\Orderattr\Helper\Config
     */
    private $config;

    public function __construct(
        \Magento\Framework\View\Element\UiComponentFactory $componentFactory,
        \Amasty\Orderattr\Block\Data\Form\Element\BooleanFactory  $booleanFactory,
        \Amasty\Orderattr\Helper\Config\Proxy $config
    ) {
        parent::__construct($componentFactory);
        $this->booleanFactory = $booleanFactory;
        $this->config = $config;
    }

    /**
     * @var array
     */
    protected $jsComponentMap = [
        'text' => 'Magento_Ui/js/grid/columns/column',
        'select' => 'Magento_Ui/js/grid/columns/select',
        'date' => 'Magento_Ui/js/grid/columns/date',
        'datetime' => 'Magento_Ui/js/grid/columns/date',
    ];

    /**
     * @var array
     */
    protected $dataTypeMap = [
        'default' => 'text',
        'text' => 'text',
        'boolean' => 'select',
        'select' => 'select',
        'multiselect' => 'select',
        'radios' => 'select',
        'checkboxes' => 'select',
        'date' => 'date',
        'datetime' => 'date',
    ];

    /**
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param array $config
     * @return \Magento\Ui\Component\Listing\Columns\ColumnInterface
     */
    public function create($attribute, $context, array $config = [])
    {
        $columnName = $attribute->getAttributeCode();
        $config = array_merge([
            'label' => __($attribute->getDefaultFrontendLabel()),
            'dataType' => $this->getDataType($attribute),
            'add_field' => true,
            'visible' => $attribute->getIsVisibleInGrid(),
            'filter' => ($attribute->getIsFilterableInGrid())
                ? $this->getFilterType($attribute->getFrontendInput())
                : null,
        ], $config);

        switch ($attribute->getFrontendInput()) {
            case 'date':
                $config['dateFormat'] = str_replace('y', 'Y', $this->config->getCheckoutDateFormat());
                break;
            case 'datetime':
                $config['dateFormat'] = str_replace('y', 'Y', $this->config->getCheckoutDateTimeFormat());
                break;
        }

        if ($attribute->usesSource()) {
            $config['options'] = $attribute->getSource()->getAllOptions();
        }
        if ($attribute->getFrontendInput() == 'boolean') {
            $config['options'] = $this->booleanFactory->create()->getValues();
        }
        
        $config['component'] = $this->getJsComponent($config['dataType']);
        
        $arguments = [
            'data' => [
                'config' => $config,
            ],
            'context' => $context,
        ];
        
        return $this->componentFactory->create($columnName, 'column', $arguments);
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     * @return string
     */
    protected function getFilterType($frontendInput)
    {
        $filtersMap = ['date' => 'dateRange', 'datetime' => 'dateRange'];
        $result = array_replace_recursive($this->dataTypeMap, $filtersMap);
        return isset($result[$frontendInput]) ? $result[$frontendInput] : $result['default'];
    }
}
