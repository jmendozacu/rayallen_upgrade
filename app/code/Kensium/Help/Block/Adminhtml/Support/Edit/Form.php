<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Help\Block\Adminhtml\Support\Edit;

/**
 * Class Form
 * @package Kensium\Help\Block\Adminhtml\System\Account\Edit
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_localeLists;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        $data = []
    ) {
        $this->_userFactory = $userFactory;
        $this->_authSession = $authSession;
        $this->_localeLists = $localeLists;
        parent::__construct($context, $registry, $formFactory, []);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $userId = $this->_authSession->getUser()->getId();
        $user = $this->_userFactory->create()->load($userId);
        $user->unsetData('password');
        $url = $this->getUrl("*/*/supportEmails");
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset("support_form", array("legend"=>'<h4 class="" style="background-image: linear-gradient(hsla(207, 10%, 35%, 0.97), hsla(207, 5%, 25%, 0.92));color:#fff;line-height: 20px;padding:10px">Support Information</h4>'));
        $fieldset->addField("type", "select", array(
            'label'     => 'Support Type',
            'name'      => 'type',
            'required'  => true,
            'onchange'  => 'support(\''.$url.'\',this.value)',
            'values'    => array(
                array(
                    'value'     => '',
                    'label'     => 'Please Select',
                ),
                array(
                    'value'     => '1',
                    'label'     => 'Sales Support',
                ),

                array(
                    'value'     => '2',
                    'label'     => 'Customization Service',
                ),
                array(
                    'value'     => '3',
                    'label'     => 'Free Trial Request',
                ),
                array(
                    'value'     => '4',
                    'label'     => 'Feedback and Complaint',
                ),
                array(
                    'value'     => '5',
                    'label'     => 'Technical Support',
                ),
                array(
                    'value'     => '6',
                    'label'     => 'Urgent Issue',
                ),
                array(
                    'value'     => '7',
                    'label'     => 'Installation Service',
                ),
                array(
                    'value'     => '8',
                    'label'     => 'Request Upgrade',
                ),
                array(
                    'value'     => '9',
                    'label'     => 'Other',
                ),
            ),
        ));
        $fieldset->addField("show_email", "label", array(
            "label" => "Email",
            "id"    => "email",
            "class" => "showemail",
            'after_element_html' => '<span id="show-email"></span>',
        ));
        $fieldset->addField("firstname", "text", array(
            "label" => "Name",
            "class" => "required-entry",
            "required" => true,
            "name" => "firstname",
        ));


        $fieldset->addField("subject", "text", array(
            "label" => "Subject",
            "class" => "required-entry",
            "required" => true,
            "name" => "subject",
        ));

        $fieldset->addField("priority", "select", array(
            'label'     => 'Priority',
            'name'      => 'priority',
            'values'    => array(
                array(
                    'value'     => 'Normal',
                    'label'     => 'Normal',
                ),
                array(
                    'value'     => 'Normal',
                    'label'     => 'Medium',
                ),

                array(
                    'value'     => 'High',
                    'label'     => 'High',
                ),
                array(
                    'value'     => 'Urgent',
                    'label'     => 'Urgent',
                ),

            ),
        ));

        $fieldset->addField('message', 'textarea', array (
            'name' => 'message',
            'label' => 'Message',
            'title' => 'Message',
//                'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
//                'wysiwyg'   => true,
            'required' => true
        ));

        $fieldset->addField("email", "text", array(
            "label" => "Email",
            "name"  => "email",
            "id"    => "email",
            "index" => "email",
            "class" => "hiddensupportemail"
        ));

        $data = $user->getData();
        $form->setValues($data);
        $form->setAction($this->getUrl('*/*/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
