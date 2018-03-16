<?php

namespace Kensium\Affliate\Controller\Save;

use Magento\Customer\Model\Customer;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;

class Save extends \Magento\Framework\App\Action\Action
{
     /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $addressFactory;


   /**
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory    $customerFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory
    ) {
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;
        $this->addressFactory  = $addressFactory;

        parent::__construct($context);
    }
    public function execute()
    {
   	   /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
         // Get Website ID
        $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();

        // Instantiate object (this is the most important part)
        $customer   = $this->customerFactory->create();

        try {
        $customer->setWebsiteId($websiteId);

        // Preparing data for new customer
        $customer->setEmail($this->getRequest()->getPost('email'));      
        $customer->setFirstname($this->getRequest()->getPost('firstname'));
        $customer->setLastname($this->getRequest()->getPost('lastname'));
        $customer->setPassword($this->getRequest()->getPost('password'));

        // Save data
        $customer->save();

        $addressesData =  array (
                    'firstname' => $this->getRequest()->getPost('firstname'),
                    'lastname' => $this->getRequest()->getPost('lastname'),
                    'street' => $this->getRequest()->getPost('address').' '.$this->getRequest()->getPost('address1'),
                    'city' => $this->getRequest()->getPost('city'),
                    'country_id' => $this->getRequest()->getPost('country_id'),
                    'region' => $this->getRequest()->getPost('region_id'),
                    'region_id' => $this->getRequest()->getPost('region_id'),
                    'postcode' => $this->getRequest()->getPost('postcode'),
                    'telephone' => $this->getRequest()->getPost('telephone'),                  
                    'is_default_billing' => 1,
                    'is_default_shipping' => 1
                );
	$address = $this->addressFactory->create()->addData($addressesData);
	$customer->addAddress($address)
		    ->setId($customer->getId())->save();
        $message = __('Thank you for registering with %1.', $this->storeManager->getStore()->getFrontendName());
        $this->messageManager->addSuccess($message);
        //$customer->sendNewAccountEmail();
        }catch (StateException $e) {
            $url = $this->urlModel->getUrl('customer/account/forgotpassword');
            // @codingStandardsIgnoreStart
            $message = __(
                'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.',
                $url
            );
            // @codingStandardsIgnoreEnd
            $this->messageManager->addError($message);
        } catch (InputException $e) {
            $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addError($this->escaper->escapeHtml($error->getMessage()));
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t save the customer.'));
        }
       $this->_redirect('affiliate/affliate/affliate');
    }
}
