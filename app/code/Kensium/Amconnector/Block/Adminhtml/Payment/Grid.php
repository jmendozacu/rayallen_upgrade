<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Payment;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $dataCollectionFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Kensium\Amconnector\Model\PaymentFactory
     */
    protected $paymentFactory;

    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $paymentConfig;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Payment
     */
    protected $resourceModelPayment;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Data\CollectionFactory $dataCollectionFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Kensium\Amconnector\Model\PaymentFactory $paymentFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Data\CollectionFactory $dataCollectionFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Kensium\Amconnector\Model\PaymentFactory $paymentFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Payment\Model\Config $paymentConfig,
        \Kensium\Amconnector\Model\ResourceModel\Payment $resourceModelPayment,
        $data = array()
    )
    {
        $this->session = $context->getBackendSession();
        $this->paymentFactory = $paymentFactory;
        $this->moduleManager = $moduleManager;
        $this->backendHelper = $backendHelper;
        $this->dataCollectionFactory = $dataCollectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->paymentConfig = $paymentConfig;
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->resourceModelPayment = $resourceModelPayment;
        parent::__construct($context, $backendHelper, $data = array());
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('paymentsMapGrid');
        $this->setDefaultSort('payments');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('payments');
        $this->session->setData('gridData', '');
        $storeId = $this->getRequest()->getParam('store');
        if ($storeId == 0 || $storeId == NULL) {
            $storeId = 1;
        }
        $payments = $this->paymentConfig->getActiveMethods();
        $paymentMethods = array();
        $collection = $this->dataCollectionFactory->create();
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->scopeConfigInterface->getValue('payment/' . $paymentCode . '/title');
            $shipMethods['payment'] = $paymentTitle;
            $rowObj = $this->dataObjectFactory->create();
            $rowObj->setData($paymentMethods);
            $collection->addItem($rowObj);
        }
        $gridArray = array();
        foreach ($collection as $col) {
            $attrCode = $col->getStatus();
            $gridArray[$attrCode] = array('acumatica_attr_code' => 'please select', 'store_id' => $storeId);
        }
        $this->session->setData('gridData', $gridArray);
        $this->session->setData('storeId', $storeId);

    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $payments = $this->paymentConfig->getActiveMethods();

        $shipMethods = array();
        $collection = $this->dataCollectionFactory->create();
        foreach ($payments as $paymentCode => $paymentModel) {
            //echo '<pre>';print_r($paymentCode);
            $paymentTitle = $this->scopeConfigInterface->getValue('payment/' . $paymentCode . '/title');
            $paymentMethods['payments'] = $paymentTitle;
            $rowObj = $this->dataObjectFactory->create();
            $rowObj->setData($paymentMethods);
            $collection->addItem($rowObj);
            if ($paymentCode == 'authnetcim') {
                $authPayments = $this->paymentFactory->create()->authorizenetPayments($paymentCode);
                foreach ($authPayments as $authPayment) {
                    $shipMethods['payments'] = $authPayment;
                    $rowObj = $this->dataObjectFactory->create();
                    $rowObj->setData($shipMethods);
                    $collection->addItem($rowObj);
                }

            }
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'payments',
            [
                'header' => __('Magento Payment Methods'),
                'type' => 'varchar',
                'index' => 'payments',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'acumatica_attr_code',
            [
                'header' => __('Acumatica Payment Methods'),
                'renderer' => '\Kensium\Amconnector\Block\Adminhtml\Renderer\AcumaticaPayment',
                'class' => 'xxx',
                'filter' => false
            ]
        );
        $this->addColumn(
            'cash_account',
            [
                'header' => __('Cash Account'),
                'renderer' => '\Kensium\Amconnector\Block\Adminhtml\Renderer\AcumaticaCashAccount',
                'class' => 'xxx',
                'filter' => false
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $storeId = $this->getRequest()->getParam('store');
        if ($storeId == 0 || $storeId == NULL) {
                $storeId = 1;
        }
        $moduleName = 'amconnector';
        $controllerNamePleaseSelect = 'amconnector';
        $controllerNameCategory = 'category';
        $controllerNameProduct = 'product';
        $controllerNameCustomer = 'customermapping';
        $controllerNameOrder = 'order';
        $controllerNamePayment = 'payment';
        $controllerNameShip = 'ship';
        $adminUrlAmconnectorCategory = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameCategory . '/index/store/'.$storeId);
        $adminUrlAmconnectorProduct = $this->backendHelper->getUrl($moduleName. '/' . $controllerNameProduct . '/index/store/'.$storeId);
        $adminUrlAmconnectorCustomer = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameCustomer . '/index/store/'.$storeId);
        $adminUrlAmconnectorOrder = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameOrder . '/index/store/'.$storeId);
        $adminUrlAmconnectorPayment = $this->backendHelper->getUrl($moduleName . '/' . $controllerNamePayment . '/index/store/'.$storeId);
        $adminUrlAmconnectorPleaseSelect = $this->backendHelper->getUrl($moduleName . '/' . $controllerNamePleaseSelect . '/index/store/'.$storeId);
        $adminUrlAmconnectorShip = $this->backendHelper->getUrl($moduleName . '/' . $controllerNameShip . '/index/store/'.$storeId);


        $html = parent::getMainButtonsHtml();
        $addButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label' => 'Save',
                'onclick' => "setLocation('" . $this->backendHelper->getUrl('*/*/save') . "')",
                'class' => 'add'
            ))->toHtml();

        $paymentMappingCount = $this->resourceModelPayment->getAcumaticaAttrCount($storeId);

        $schemaButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label' => 'Update Schema',
                'onclick' => "updateSchema('" . $this->backendHelper->getUrl('*/*/saveSchema') . "','" . $paymentMappingCount . "')",
                'class' => 'add'
            ))->toHtml();

        $addSelect = '
                    <form name="productselect">
                        <select name="menu" class="admin__control-select" onChange="top.location.href=this.options[this.selectedIndex].value;" value="GO">
                            <option value=' . "$adminUrlAmconnectorPleaseSelect" . '>Please Select</option>
                            <option value=' . "$adminUrlAmconnectorCategory" . '>Category</option>
                            <option value=' . "$adminUrlAmconnectorProduct" . '>Product</option>
                            <option  value=' . "$adminUrlAmconnectorCustomer" . '>Customer</option>
			    			<option value=' . "$adminUrlAmconnectorShip" . ' >Shipping Methods</option>
	                    	<option selected="selected" value=' . "$adminUrlAmconnectorPayment" . ' >Payment Methods</option>
                            <option value=' . "$adminUrlAmconnectorOrder" . ' >Order Status</option>
                        </select>
                    </form>';
        if (isset($recommendedSettings))
            return $addSelect . $addButton . $html . $schemaButton . $recommendedSettings;
        else
            return $addSelect . $addButton . $html . $schemaButton;
    }
    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {

        return '';
    }
}

