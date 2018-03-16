<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Help\Helper;
 
/**
 * Custom Module Email helper
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /* Here section and group refer to name of section and group where you create this field in configuration*/
 
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
 
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
 
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
 
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
     
    /**
     * @var string
    */
    protected $temp_id;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder; 
    }
 
    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
 
    /**
     * Return store 
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }


    /**
     * @param int $storeId
     * @param $templateParams
     * @param $sender
     * @param $recieverInfo
     */
    public function sendEmail($storeId =1,$templateParams,$sender,$recieverInfo)
    {

        $transport = $this->_transportBuilder->setTemplateIdentifier('help_email_template_id')
            ->setTemplateOptions(['area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 'store' => $this->getStore()])
            ->setTemplateVars($templateParams)
            ->setFrom($sender)
            ->addTo($recieverInfo)
            ->getTransport();
        $transport->sendMessage();
    }



}
