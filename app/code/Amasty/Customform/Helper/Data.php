<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */
namespace Amasty\Customform\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Model\UrlInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MEDIA_PATH = 'amasty/amcustomform/';

    const FILE_WAS_NOT_UPLOADED_CODE_ERROR = '666';

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $fileUploaderFactory;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param \Magento\Customer\Model\SessionFactory $sessionFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct($context);
        $this->layoutFactory = $layoutFactory;
        $this->remoteAddress = $context->getRemoteAddress();
        $this->sessionFactory = $sessionFactory;
        $this->jsonEncoder = $jsonEncoder;
        $this->customerRepository = $customerRepository;
        $this->jsonDecoder = $jsonDecoder;
        $this->filesystem = $filesystem;
        $this->ioFile = $ioFile;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->backendUrl = $backendUrl;
        $this->escaper = $escaper;
    }

    public function getModuleConfig($path)
    {
        return $this->scopeConfig->getValue('amasty_customform/' . $path);
    }

    public function escapeHtml($html)
    {
        return $this->escaper->escapeHtml($html);
    }

    public function renderForm($formId)
    {
        $layout = $this->layoutFactory->create();
        $html = $layout->createBlock(
            'Amasty\Customform\Block\Init',
            'amasty_customform_init',
            [
                'data' => [
                    'form_id' => $formId
                ]
            ]
        )->toHtml();

        return $html;
    }

    public function getSubmitUrl()
    {
        return $this->_getUrl('amasty_customform/form/submit');
    }

    public function getCurrentIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    public function getCurrentCustomerId()
    {
        $customerSession = $this->sessionFactory->create();

        return  $customerSession->getCustomerId();
    }

    public function encode($data)
    {
        return $this->jsonEncoder->encode($data);
    }

    public function decode($data)
    {
        return $this->jsonDecoder->decode($data);
    }

    /**
     * @param $customerId
     * @param bool $asLink
     * @return array|\Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomerName($customerId, $asLink = false)
    {
        $customerName =__('Guest');

        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (\Exception $ex) {
            $customer = null;
        }
        if ($customer) {
            $link = $this->backendUrl->getUrl('customer/index/edit', ['id' => $customer->getId()]);
            $linkString = sprintf(
                '<a href="%s">%s</a>',
                $link,
                $customer->getFirstname() . ' ' .  $customer->getLastname()
            );

            $customer = [
                'customer_link' =>
                    ($asLink ? $linkString : $link),
                'customer_name' => $customer->getFirstname() . ' ' .  $customer->getLastname()
            ];
        } else {
            $customer = [
                'customer_name' => $customerName,
                'customer_link' => ''
            ];
        }

        return $customer;
    }

    public function getAnswerViewUrl($id)
    {
        return  $this->backendUrl->getUrl(
            'amasty_customform/answer/edit',
            [
                'id' => $id,
                UrlInterface::SECRET_KEY_PARAM_NAME => $this->backendUrl->getSecretKey()
            ]
        );
    }

    /**
     * @param $name
     * @param $fileValidation
     * @return array
     * @throws LocalizedException
     */
    public function saveFileField($name, $fileValidation)
    {
        //upload images
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            self::MEDIA_PATH
        );

        $this->ioFile->checkAndCreateFolder($path);

        try {
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->fileUploaderFactory->create(['fileId' => $name]);
            if (array_key_exists('allowed_extension', $fileValidation)) {
                $uploader->setAllowedExtensions(explode(',', $fileValidation['allowed_extension']));
            }
            if (array_key_exists('max_file_size', $fileValidation)) {
                if ($uploader->getFileSize() > $fileValidation['max_file_size'] * 1024 * 1024) {
                    throw new LocalizedException(
                        __('Field exceeds the allowed file size(%1 mb).', $fileValidation['max_file_size'])
                    );
                }
            }

            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($path);
        } catch (\Exception $ex) {
            if (($ex->getCode() == self::FILE_WAS_NOT_UPLOADED_CODE_ERROR)
                && (!$fileValidation || !array_key_exists('required', $fileValidation))) {
                return $result['file'] = [];
            }

            throw new LocalizedException(__($ex->getMessage()));
        }

        return $result['file'];
    }
}
