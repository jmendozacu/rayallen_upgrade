<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Api\Form;

/**
 * Interface ElementRepositoryInterface
 * @api
 */
interface ElementRepositoryInterface
{
    /**
     * @param \Amasty\Customform\Api\Data\Form\ElementInterface $element
     * @return \Amasty\Customform\Api\Data\Form\ElementInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Amasty\Customform\Api\Data\Form\ElementInterface $element);

    /**
     * @param int $elementId
     * @return \Amasty\Customform\Api\Data\Form\ElementInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($elementId);

    /**
     * @param int $formId
     * @return \Amasty\Customform\Model\ResourceModel\Form\Element\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCollectionIdsByFormId($formId);

    /**
     * @param \Amasty\Customform\Api\Data\Form\ElementInterface $element
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Customform\Api\Data\Form\ElementInterface $element);

    /**
     * @param int $elementId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($elementId);

    /**
     * @param $formId
     * @param array $excludedElementsIds
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function truncateByFormId($formId, $excludedElementsIds = []);
}
