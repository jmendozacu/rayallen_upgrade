<?php
/**
 * Kensium_OverSize extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_OverSize
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\OverSize\Controller\Adminhtml\Oversizeship;

abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * JSON Factory
     * 
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * Over Size Ship Factory
     * 
     * @var \Kensium\OverSize\Model\OversizeshipFactory
     */
    protected $oversizeshipFactory;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Kensium\OverSize\Model\OversizeshipFactory $oversizeshipFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Kensium\OverSize\Model\OversizeshipFactory $oversizeshipFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->jsonFactory         = $jsonFactory;
        $this->oversizeshipFactory = $oversizeshipFactory;
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
        foreach (array_keys($postItems) as $oversizeshipId) {
            /** @var \Kensium\OverSize\Model\Oversizeship $oversizeship */
            $oversizeship = $this->oversizeshipFactory->create()->load($oversizeshipId);
            try {
                $oversizeshipData = $postItems[$oversizeshipId];//todo: handle dates
                $oversizeship->addData($oversizeshipData);
                $oversizeship->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithOversizeshipId($oversizeship, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithOversizeshipId($oversizeship, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithOversizeshipId(
                    $oversizeship,
                    __('Something went wrong while saving the Over Size Ship.')
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
     * Add Over Size Ship id to error message
     *
     * @param \Kensium\OverSize\Model\Oversizeship $oversizeship
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithOversizeshipId(\Kensium\OverSize\Model\Oversizeship $oversizeship, $errorText)
    {
        return '[Over Size Ship ID: ' . $oversizeship->getId() . '] ' . $errorText;
    }
}
