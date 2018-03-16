<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Orderstatus\Controller\Save;

class Save extends \Magento\Framework\App\Action\Action
{
     protected $orderstatusFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Kensium\Orderstatus\Model\OrderstatusFactory $orderstatusFactory
        
    ) {
        $this->orderstatusFactory = $orderstatusFactory;
        parent::__construct($context);
    }
    public function execute()
    {
    
    $data = $this->getRequest()->getPost();
    
    $data = $data->toArray();
       $orderstatus = $this->orderstatusFactory->create();
  
       if($orderstatus->setData($data)->save()){
       $this->messageManager->addSuccess("Orderstatus Submitted Successfully");
       }
       
      else{
      $this->messageManager->addError("Orderstatus is not submitted");
      }
	   $this->_redirect('orderstatus/orderstatus/orderstatus');
    }
}
