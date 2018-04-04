<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Block\Adminhtml\Data\Edit;

use Amasty\Customform\Helper\Data;
use Magento\MediaStorage\Model\File\Uploader;

class Answer extends \Magento\Backend\Block\Template
{
    protected $currentResponse;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Amasty\Customform\Model\FormRepository
     */
    private $formRepository;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var \Amasty\Customform\Model\ResourceModel\Form\Element\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Amasty\Customform\Model\FormRepository $formRepository,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        Data $helper,
        \Amasty\Customform\Model\ResourceModel\Form\Element\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $context->getStoreManager();
        $this->formRepository = $formRepository;
        $this->jsonDecoder = $jsonDecoder;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    public function _construct()
    {
        $this->currentResponse = $this->coreRegistry->registry(
            \Amasty\Customform\Controller\Adminhtml\Answer::CURRENT_ANSWER_MODEL
        );
        parent::_construct();
    }

    /**
     * Add buttons on request view page
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'back_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation("' . $this->getUrl('*/*/index') . '")',
                'class' => 'back'
            ]
        );

        parent::_prepareLayout();

        return $this;
    }

    /**
     * Submit URL getter
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/send', ['request_id' => $this->getCurrentRequest()->getRequestId()]);
    }

    public function getInformationData()
    {
        /** @var \Amasty\Customform\Model\Answer $model */
        $model = $this->getCurrentResponse();

        try {
            $form = $this->formRepository->get($model->getFormId());
        } catch (\Exception $ex) {
            $form = null;
        }
        $formName = $form ? $form->getCode(): __('This Form #%1 was removed', $model->getFormId());

        $customerName = $this->helper->getCustomerName($model->getCustomerId(), true);
        $customerName = (array_key_exists('customer_link', $customerName) && $customerName['customer_link'])
            ? $customerName['customer_link'] : $customerName['customer_name'];
        $store = $this->storeManager->getStore($model->getStoreId())->getName();

        $result =  [
            ['label' => __('Form'), 'value' => $formName],
            ['label' => __('Submitted'), 'value' => $model->getCreatedAt()],
            ['label' => __('IP'), 'value' => $model->getIp()],
            ['label' => __('Customer'), 'value' => $customerName],
            ['label' => __('Store'), 'value' => $store]
        ];

        return $result;
    }

    public function getResponseData()
    {
        /** @var \Amasty\Customform\Model\Answer $model */
        $model = $this->getCurrentResponse();
        $result = [];
        $formData = $model->getResponseJson();
        if ($formData) {
            $fields = $this->jsonDecoder->decode($formData);

            foreach ($fields as $field) {
                $value = $this->escapeHtml($field['value']);
                if ($field['type'] == 'file') {
                    $value = Uploader::getCorrectFileName($value);
                    $url = $this->_storeManager->getStore()
                            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                            . Data::MEDIA_PATH . $value;
                    $value = '<a download href="' . $url .  '">' . __('Download: ') . $value . '</a>';
                }

                $result[] = [
                    'label' => $field['label'],
                    'value' => $value
                ];
            }
        }

        return $result;
    }

    /**
     * @return \Amasty\Customform\Model\Answer
     */
    public function getCurrentResponse()
    {
        return $this->currentResponse;
    }
}
