<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Controller\Adminhtml;

use Magento\Framework\View\Result\PageFactory;

/**
 *  controller
 */
abstract class Answer extends \Magento\Backend\App\Action
{
    const CURRENT_ANSWER_MODEL = 'amasty_customform_request_model';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Amasty\Customform\Model\AnswerRepository
     */
    protected $answerRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\Customform\Model\AnswerRepository $answerRepository,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->answerRepository = $answerRepository;
        $this->logger = $logger;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_Customform::data';

    /**
     * Initiate action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(self::ADMIN_RESOURCE)
            ->_addBreadcrumb(__('Amasty: Custom Forms'), __('Submitted Data'));

        return $this;
    }
}
