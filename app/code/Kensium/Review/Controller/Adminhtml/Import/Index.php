<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Review\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Kensium\Review\Model\Review
     */
    protected $review;

    /**
     * @param Action\Context $context
     * @param \Kensium\Review\Model\Review $review
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Kensium\Review\Model\Review $review
    ) {
        parent::__construct($context);
        $this->review = $review;

    }

    /**
     * review import
     *
     * @return void
     */
    public function execute()
    {
        $this->review->install(['Kensium_Review::fixtures/rayallen_productreviews.csv']);
        //$this->review->install(['Kensium_Review::fixtures/jido_productreviews.csv']);
        $this->messageManager->addSuccess("Reviews imported successfully");
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
