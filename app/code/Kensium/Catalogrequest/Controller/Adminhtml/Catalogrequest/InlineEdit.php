<?php
/**
 * Kensium_Catalogrequest extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Catalogrequest
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Catalogrequest\Controller\Adminhtml\Catalogrequest;

abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * JSON Factory
     * 
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * Catalogrequest Factory
     * 
     * @var \Kensium\Catalogrequest\Model\CatalogrequestFactory
     */
    protected $catalogrequestFactory;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Kensium\Catalogrequest\Model\CatalogrequestFactory $catalogrequestFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Kensium\Catalogrequest\Model\CatalogrequestFactory $catalogrequestFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->jsonFactory           = $jsonFactory;
        $this->catalogrequestFactory = $catalogrequestFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($postItems) as $catalogrequestId) {
            /** @var \Kensium\Catalogrequest\Model\Catalogrequest $catalogrequest */
            $catalogrequest = $this->catalogrequestFactory->create()->load($catalogrequestId);
            try {
                $catalogrequestData = $postItems[$catalogrequestId];//todo: handle dates
                $catalogrequest->addData($catalogrequestData);
                $catalogrequest->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithCatalogrequestId($catalogrequest, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithCatalogrequestId($catalogrequest, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithCatalogrequestId(
                    $catalogrequest,
                    __('Something went wrong while saving the Catalogrequest.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Catalogrequest id to error message
     *
     * @param \Kensium\Catalogrequest\Model\Catalogrequest $catalogrequest
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithCatalogrequestId(\Kensium\Catalogrequest\Model\Catalogrequest $catalogrequest, $errorText)
    {
        return '[Catalogrequest ID: ' . $catalogrequest->getId() . '] ' . $errorText;
    }
}
