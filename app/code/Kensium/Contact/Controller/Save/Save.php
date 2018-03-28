<?php

namespace Kensium\Contact\Controller\Save;

 use Magento\Framework\App\Config\ScopeConfigInterface;
 use Magento\Store\Model\ScopeInterface;

class Save extends \Magento\Framework\App\Action\Action
{
     protected $contactFactory;
     /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_helper;
    protected $request;
    protected $scopeConfig;
    protected $message;
    protected $storeManager;

    /**
     * @var CaptchaStringResolver
     */
    protected $captchaStringResolver;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Kensium\Catalogrequest\Model\CatalogrequestFactory $catalogrequestFactory
     * @param \Magento\Captcha\Helper\Data $helper
     */

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Kensium\Contact\Model\ContactFactory $contactFactory,
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver,
        \Magento\Framework\Mail\Message $message,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
        
    ) {
		$this->_helper = $helper;
        $this->contactFactory = $contactFactory;
         $this->captchaStringResolver = $captchaStringResolver;
          $this->message = $message;
          $this->scopeConfig = $scopeConfig;
          $this->storeManager = $storeManager;
        parent::__construct($context);
    }
    public function execute()
    {
    
    $data = $this->getRequest()->getPost();
    $storename = $this->storeManager->getStore()->getData('name');
    
    $data = $data->toArray();
   // echo "<pre>";
   //print_r($data);exit;
      $formId = 'contact_us';
	$captcha = $this->_helper->getCaptcha($formId);




	/** @var \Magento\Framework\App\Action\Action $controller */
	$controller ='' ;//$observer->getControllerAction();
            if (!$captcha->isCorrect($this->captchaStringResolver->resolve($this->getRequest(), $formId))) {
                $this->messageManager->addError(__('Incorrect CAPTCHA.'));
                $this->_redirect('contactus');
                return;
            }
     foreach($data as $key=>$value){

     if(!is_array($value))
     $data2[$key]=trim($value);

     }  
       if(!empty($data['fname']) && !empty($data['lname']) && !empty($data['email']))
     {  
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $region = $objectManager->create('Magento\Directory\Model\ResourceModel\Region\Collection')
                        ->addFieldToFilter('code', ['eq' => $data['state']])
                        ->addFieldToFilter('country_id', ['eq' => 'US'])
                        ->getFirstItem(); 
                         
		 $admin = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
         $html = "";
         $html .= "There is a contact request from - ".$data['email']."<br><br>";
         $html .= "First Name: ".$data['fname']."<br>";
         $html .= "Last Name: ".$data['lname']."<br>";
         if($data['address']!=""){
            $html .= "Address: ".$data['address']."<br>";
         }         
         if($data['city']!=""){
            $html .= "City: ".$data['city']."<br>";
         }         
         $html .= "State: ".$region->getData('name')."<br>";
         if($data['zip']!=""){
            $html .= "Postal Code: ".$data['zip']."<br>";   
         }
         
         $html .= "Email address: ".$data['email']."<br>";
         if($data['phone']!=""){
            $html .= "Phone Number: ".$data['phone']."<br>";
         }
         
         if($data['fax']!=""){
            $html .= "Fax: ".$data['fax']."<br>";
         }
         
         if($data['website']!=""){
            $html .= "Website: ".$data['website']."<br>";
         }
         
         if($data['company']!=""){
            $html .= "Company: ".$data['company']."<br>";
         }
         
         if($data['position']!=""){
            $html .= "Position: ".$data['position']."<br>";
         }
         
         if($data['comments']!=""){
            $html .= "Comments: ".$data['comments']."<br>";
         }
         
         $mail = $this->message;
         // $mail->setToName(‘ Name that the email will be sent to’);
      	 $mail->addCc(array("rchapman@rayallen.com","krobinson@rayallen.com","sathishs@kensium.com"));
         $mail->addTo($admin);
         $mail->setBodyHTML($html);
         $mail->setSubject($storename.'-Contact Submission from - '.$data['fname']);
         $mail->setFrom($data['email'],$data['fname']);
         
         //$mail->setType('multipart/mixed');// YOu can use Html or text as Mail format
         $mail->send(); 

       $freecatalog = $this->contactFactory->create();
  
       if($freecatalog->setData($data2)->save()){
       $this->messageManager->addSuccess("Contactus form  Submitted Successfully");
       }
     }
      else{
      $this->messageManager->addError("Contactus form is not submitted");
      }
	   $this->_redirect('contactus');
    }
}
