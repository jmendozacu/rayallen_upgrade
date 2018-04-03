<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Attributemanager\Controller\Adminhtml\Category;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Kensium\Attributemanager\Controller\Adminhtml\Category
 */
class Edit extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var
     */
    protected $session;

    /**
     * @var
     */
    protected $coreRegistry;

    /**
     * @var
     */
    protected $_block;

    /**
     * @var \Magento\Eav\Model\EntityFactory
     */
    protected $entityFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param Context $context
     * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Eav\Model\EntityFactory $entityFactory
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Eav\Model\EntityFactory $entityFactory,
        \Magento\Eav\Model\Config $eavConfig,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->attributeFactory = $attributeFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->entityFactory = $entityFactory;
        $this->adminSession = $context->getSession();
        $this->setup = $setup;
        $this->eavConfig = $eavConfig;
        $this->_block = 'category';
        $this->_type =  'catalog_category';
    }

    /**
     * @return $this
     */
    public function _initAttributes()
    {

        $id = $this->_request->getParam('attribute_id');
        $model = $this->attributeFactory->create()->load($id);
        $this->setup->startSetup();
        if (0 !== $id) {
            $db = $this->setup->getConnection('core_write');
            $model->setData("sort_order", $db->fetchOne("select sort_order from " . $this->setup->getTable('eav_entity_attribute') . " where attribute_id=$id"));
        }
        $this->setup->endSetup();
        if ($model->getId() || $id == 0) {
            $data = $this->adminSession->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            return $model;
        }
    }
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        $attributeId = $this->getRequest()->getParam('id');
        $model = $this->_initAttributes();

        if (!$model->getId() && $attributeId) {
            $this->messageManager->addError(__('This attribute no longer exists.'));
            return;
        }

       $data = $this->adminSession->getFormData(true);
        if (!empty($data)) {
           $model->addData($data);
       }
        $this->_coreRegistry->register('attributemanager_data', $model);
        $this->_view->loadLayout();

        $page = $this->resultPageFactory->create();
        $this->_setActiveMenu('Kensium_Attributemanager::kensium_attributemanager');

        $this->_addBreadcrumb('Item Manager', 'Item Manager');
        $this->_addBreadcrumb('Item News', 'Item News');

        $page->getLayout()->getBlock('head');//->setCanLoadExtJs(true);

        $this
            ->_addContent($page->getLayout()->createBlock('Kensium\Attributemanager\Block\Adminhtml\Category\Edit'))
            ->_addLeft($page->getLayout()->createBlock('Kensium\Attributemanager\Block\Adminhtml\Category\Edit\Tabs'))
        ;

        $this->_view->renderLayout();
    } /*else {
            $this->messageManager->addError('Item does not exist');
        }*/
}
