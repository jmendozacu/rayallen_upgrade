<?php

namespace Kensium\Quote\Controller\Save;

 use Magento\Framework\App\Config\ScopeConfigInterface;
 use Magento\Store\Model\ScopeInterface;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_helper;
     protected $request;
     protected $scopeConfig;
     protected $QuoteFactory;
     protected $message;
     protected $storeManager;
     
    /**
     * @var CaptchaStringResolver
     */
    protected $captchaStringResolver;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Kensium\Quote\Model\QuoteFactory $QuoteFactory,
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver,
        \Magento\Framework\Mail\Message $message,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
         ScopeConfigInterface $scopeConfig
        
    ) {
        $this->_helper = $helper;
        $this->QuoteFactory = $QuoteFactory;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->message = $message;
         $this->scopeConfig = $scopeConfig;
         $this->storeManager = $storeManager;
        
        
        parent::__construct($context);
    }
    public function execute()
    {
  
  
   $data = $this->getRequest()->getPost();//print_r($data);exit;  
   $storename = $this->storeManager->getStore()->getData('name');
  
   
   /*    $data2=serialize($data['itemdata']);
       $data['productdata'] = $data2;*/
       $data = $data->toArray();
       //echo "<pre>";print_r($data);
        $formId = 'contact_us';
        $captcha = $this->_helper->getCaptcha($formId);
     //  $data2 = trim(($data));

        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller ='' ;//$observer->getControllerAction();
        if (!$captcha->isCorrect($this->captchaStringResolver->resolve($this->getRequest(), $formId))) {
            $this->messageManager->addError(__('Incorrect CAPTCHA.'));
            $this->_redirect('request_quote');
            return;
        }
     foreach($data as $key=>$value){

     if(!is_array($value))
     $data2[$key]=trim($value);

     }
    
     if(!empty($data['fname']) && !empty($data['lname']) && !empty($data['address']) && !empty($data['city']) && !empty($data['state']) && !empty($data['zip']) && !empty($data['phone']) && !empty($data['email']))
     {
		 
	   $admin = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
    // echo "<pre>"; print_r($data);
     $html = "";
     $html .= "First Name: ".$data['fname']."<br>";
     $html .= "Last Name: ".$data['lname']."<br>";
     if($data['bname']!=""){
        $html .= "Business/Organization Name : ".$data['bname']."<br>";
     }
     
     $html .= "Address: ".$data['address']."<br>";
     $html .= "City: ".$data['city']."<br>";
     $html .= "State/Province : ".$data['state']."<br>";
     $html .= "ZIP: ".$data['zip']."<br>";
     $html .= "Country: ".$data['country']."<br>";
     $html .= "Phone Number: ".$data['phone']."<br>";
     if($data['fax']!=""){
        $html .= "Fax: ".$data['fax']."<br>";
     }     
     $html .= "Email address: ".$data['email']."<br>";
     $html .= '<table border="0" width="80%">';
     if($data['qty1'] != "" || $data['item1'] != "" || $data['description1'] != "" ){
        $html .= "<tr><td>Quantity1: ".$data['qty1']."</td><td>Item1: ".$data['item1']."</td><td>Description1: ".$data['description1']."</td></tr>";
     }
     if($data['qty2'] != "" || $data['item2'] != "" || $data['description2'] != "" ){
        $html .= "<tr><td>Quantity2: ".$data['qty2']."</td><td>Item2: ".$data['item2']."</td><td>Description2: ".$data['description2']."</td></tr>";
     }
     if($data['qty3'] != "" || $data['item3'] != "" || $data['description3'] != "" ){
        $html .= "<tr><td>Quantity3: ".$data['qty3']."</td><td>Item3: ".$data['item3']."</td><td>Description3: ".$data['description3']."</td></tr>";
     }
     if($data['qty4'] != "" || $data['item4'] != "" || $data['description4'] != "" ){
        $html .= "<tr><td>Quantity4: ".$data['qty4']."</td><td>Item4: ".$data['item4']."</td><td>Description4: ".$data['description4']."</td></tr>";
     }
     if($data['qty5'] != "" || $data['item5'] != "" || $data['description5'] != "" ){
        $html .= "<tr><td>Quantity5: ".$data['qty5']."</td><td>Item5: ".$data['item5']."</td><td>Description5: ".$data['description5']."</td></tr>";
     }
     if($data['qty6'] != "" || $data['item6'] != "" || $data['description6'] != "" ){
        $html .= "<tr><td>Quantity6: ".$data['qty6']."</td><td>Item6: ".$data['item6']."</td><td>Description6: ".$data['description6']."</td></tr>";
     }
     if($data['qty7'] != "" || $data['item7'] != "" || $data['description7'] != "" ){
        $html .= "<tr><td>Quantity7: ".$data['qty7']."</td><td>Item7: ".$data['item7']."</td><td>Description7: ".$data['description7']."</td></tr>";
     }
     if($data['qty8'] != "" || $data['item8'] != "" || $data['description8'] != "" ){
        $html .= "<tr><td>Quantity8: ".$data['qty8']."</td><td>Item8: ".$data['item8']."</td><td>Description8: ".$data['description8']."</td></tr>";
     }
     if($data['qty9'] != "" || $data['item9'] != "" || $data['description9'] != "" ){
        $html .= "<tr><td>Quantity9: ".$data['qty9']."</td><td>Item9: ".$data['item9']."</td><td>Description9: ".$data['description9']."</td></tr>";
     }
     if($data['qty10'] != "" || $data['item10'] != "" || $data['description10'] != "" ){
        $html .= "<tr><td>Quantity10: ".$data['qty10']."</td><td>Item10: ".$data['item10']."</td><td>Description10: ".$data['description10']."</td></tr>";
     }
     $html .= '</table>';
     
	   $mail = $this->message;
   // $mail->setToName(‘ Name that the email will be sent to’);
      //$mail->addCc(array("akashc@kensium.com","sathishs@kensium.com"));
      $mail->addCc(array("rchapman@rayallen.com","krobinson@rayallen.com","sathishs@kensium.com"));
      $mail->addTo($admin);
      $mail->setBodyHTML($html);
	  $mail->setSubject($storename.'-Quote Submission from - '.$data['fname']);
	  //$mail->setFrom($data['email'],$data['fname']);
	  $mail->setFrom("sales@rayallen.com");
	  //$mail->setType('multipart/mixed');// YOu can use Html or text as Mail format
	   $mail->send(); 
       $quote = $this->QuoteFactory->create();
       if($quote->setData($data2)->save()){
       $this->messageManager->addSuccess("Quote Submitted Successfully");
       
       }
      } 
       
      else{
      $this->messageManager->addError("Quote was not submitted, please fill all mandatory fields");
      }
	$this->_redirect('request_quote');
    }
}
