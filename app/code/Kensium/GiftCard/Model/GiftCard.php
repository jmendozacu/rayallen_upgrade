<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\GiftCard\Model;

use Magento\GiftCardAccount\Model\Pool;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class GiftCard
{

    /**
     * @var \Magento\GiftCardAccount\Model\Pool
     */
    private $pool;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;


    /**
     * @param Pool $pool
     * @param ManagerInterface $messageManager
     */
    public function __construct(Pool $pool,ManagerInterface $messageManager) {
        $this->pool = $pool;
        $this->messageManager = $messageManager;
    }

    public function generateCodePool(){
        $usage = $this->pool->getPoolUsageInfo();
        $unUsedCodes = $usage->getFree();
        if($unUsedCodes < 10){
            try {
                $this->pool->generatePool();
                $this->messageManager->addSuccess(__('New code pool was generated.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We were unable to generate a new code pool'));
            }
        }else{
            $this->messageManager->addSuccess(__('still more than 50 free codes available'));
        }
    }
}

