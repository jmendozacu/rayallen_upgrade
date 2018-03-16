<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Help\Block\Adminhtml\Support\Edit\Tab;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Model\Auth\Session $session,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        $data = []
    ) {
        parent::__construct($context,$registry,$formFactory, []);
        $this->session = $session;
        $this->admin = $context->getBackendSession();
        $this->_formFactory = $formFactory;
        $this->_registry = $registry;
    }
    /**
     * Init form
     */
    protected function _prepareForm()
    {

        $userId = $this->session->getUser()->getId();
        $user = $this->admin;
        $url = $this->getUrl("*/*/supportEmails");
        $form = $this->_formFactory->create();
        $this->setForm($form);
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

        if ( $this->session->getSupportData() )
        {
            $support_data = $this->session->getSupportData();
            $this->session->setSupportData(null);
        } elseif ( $this->_registry->registry('support_data') ) {
            $support_data = $this->_registry->registry('support_data')->getData();
        }

        if(isset($support_data['file']) && $support_data['file'] )
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $urlLoader = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
            $baseUrl = $urlLoader->getStore()->getBaseUrl();
            $support_data['file'] = $baseUrl . 'support/'.$support_data['file'];
        }

        $form->setValues($user->getData());

       return parent::_prepareForm();


    }
}