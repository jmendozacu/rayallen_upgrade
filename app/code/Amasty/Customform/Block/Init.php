<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Block;

use Magento\Backend\Block\Widget\Grid\Column\Filter\Store;
use Magento\Framework\Exception\NoSuchEntityException;

class Init extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'init.phtml';

    /**
     * @var \Amasty\Customform\Model\Form
     */
    protected $currentForm;

    /**
     * @var \Amasty\Customform\Helper\Data
     */
    private $helper;

    /**
     * @var \Amasty\Customform\Model\FormRepository
     */
    private $formRepository;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Customform\Helper\Data $helper,
        \Amasty\Customform\Model\FormRepository $formRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->request = $request;
        $this->formRepository = $formRepository;
        $this->jsonEncoder = $jsonEncoder;
        $this->sessionFactory = $sessionFactory;

        parent::__construct($context, $data);
    }

    public function _construct()
    {
        $id = $this->getFormId();
        if ($id) {
            try {
                $this->currentForm = $this->formRepository->get($id);
            } catch (NoSuchEntityException $e) {
                $this->currentForm = false;
            }
        }
        parent::_construct();
    }

    public function getHelper()
    {
        return $this->helper;
    }

    public function toHtml()
    {
        if ($this->validate()) {
            return parent::toHtml();
        }

        return '';
    }

    protected function validate()
    {
        if (!$this->currentForm || !$this->currentForm->isEnabled()) {
            return false;
        }
        /* check for store ids*/
        $stores = $this->currentForm->getStoreId();
        $stores = explode(',', $stores);
        $currentStoreId = $this->_storeManager->getStore()->getId();
        if (!in_array(Store::ALL_STORE_VIEWS, $stores) && !in_array($currentStoreId, $stores)) {
            return false;
        }

        /* check for customer groups*/
        $availableGroups = $this->currentForm->getCustomerGroup();
        $availableGroups = explode(',', $availableGroups);
        $currentGroup = $this->sessionFactory->create()->getCustomerGroupId();
        if (!in_array($currentGroup, $availableGroups)) {
            return false;
        }

        return true;
    }

    /**
     * @return \Amasty\Customform\Model\Form
     */
    public function getCurrentForm()
    {
        return $this->currentForm;
    }

    public function getButtonTitle()
    {
        $title = $this->currentForm->getSubmitButton();
        if (!$title) {
            $title = __('Submit');
        }

        return $title;
    }

    public function getFormDataJson()
    {
        $result = [
            'dataType' => 'json',
            'formData' => $this->getCurrentForm()->getFormJson()
        ];

        return $this->jsonEncoder->encode($result);
    }

    public function getFormAction()
    {
        return $this->helper->getSubmitUrl();
    }
}
