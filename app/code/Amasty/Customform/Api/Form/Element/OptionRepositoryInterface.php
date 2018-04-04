<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Api\Form\Element;

/**
 * Interface OptionRepositoryInterface
 * @api
 */
interface OptionRepositoryInterface
{
    /**
     * @param \Amasty\Customform\Api\Data\Form\Element\OptionInterface $option
     * @return \Amasty\Customform\Api\Data\Form\Element\OptionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Amasty\Customform\Api\Data\Form\Element\OptionInterface $option);

    /**
     * @param int $optionId
     * @return \Amasty\Customform\Api\Data\Form\Element\OptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($optionId);

    /**
     * @param \Amasty\Customform\Api\Data\Form\Element\OptionInterface $option
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Customform\Api\Data\Form\Element\OptionInterface $option);

    /**
     * @param int $optionId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($optionId);
}
