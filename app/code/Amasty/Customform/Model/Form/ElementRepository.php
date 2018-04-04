<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Model\Form;

use Amasty\Customform\Api\Data;
use Amasty\Customform\Model\Form\ElementFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class ElementRepository implements \Amasty\Customform\Api\Form\ElementRepositoryInterface
{
    /**
     * @var array
     */
    protected $element = [];

    /**
     * @var \Amasty\Customform\Model\ResourceModel\Form\Element
     */
    private $elementResource;

    /**
     * @var ElementFactory
     */
    private $elementFactory;

    /**
     * @var \Amasty\Customform\Model\ResourceModel\Form\Element\CollectionFactory
     */
    private $elementCollectionFactory;

    /**
     * ElementRepository constructor.
     * @param \Amasty\Customform\Model\ResourceModel\Form\Element $elementResource
     * @param \Amasty\Customform\Model\Form\ElementFactory $elementFactory
     * @param \Amasty\Customform\Model\ResourceModel\Form\Element\CollectionFactory $elementCollectionFactory
     */
    public function __construct(
        \Amasty\Customform\Model\ResourceModel\Form\Element $elementResource,
        ElementFactory $elementFactory,
        \Amasty\Customform\Model\ResourceModel\Form\Element\CollectionFactory $elementCollectionFactory
    ) {
        $this->elementResource = $elementResource;
        $this->elementFactory = $elementFactory;
        $this->elementCollectionFactory = $elementCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Data\Form\ElementInterface $element)
    {
        if ($element->getElementId()) {
            $element = $this->get($element->getElementId())->addData($element->getData());
        }

        try {
            $this->elementResource->save($element);
            unset($this->element[$element->getElementId()]);
        } catch (\Exception $e) {
            if ($element->getElementId()) {
                throw new CouldNotSaveException(
                    __('Unable to save element with ID %1. Error: %2', [$element->getElementId(), $e->getMessage()])
                );
            }
            throw new CouldNotSaveException(__('Unable to save new element. Error: %1', $e->getMessage()));
        }
        
        return $element;
    }

    /**
     * {@inheritdoc}
     */
    public function get($elementId)
    {
        if (!isset($this->element[$elementId])) {
            /** @var \Amasty\Customform\Model\Form\Element $element */
            $element = $this->elementFactory->create();
            $this->elementResource->load($element, $elementId);
            if (!$element->getElementId()) {
                return $element;
            }
            $this->element[$elementId] = $element;
        }
        return $this->element[$elementId];
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionIdsByFormId($formId)
    {
        $elementCollection = $this->elementCollectionFactory->create();
        $elementCollection->addFieldToFilter(
            \Amasty\Customform\Api\Data\Form\ElementInterface::FORM_ID,
            ['in' => $formId]
        );

        if (!$elementCollection->getSize()) {
            return false;
        }

        return $elementCollection->getAllIds();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Data\Form\ElementInterface $element)
    {
        try {
            $this->elementResource->delete($element);
            unset($this->element[$element->getElementId()]);
        } catch (\Exception $e) {
            if ($element->getElementId()) {
                throw new CouldNotDeleteException(
                    __('Unable to remove element with ID %1. Error: %2', [$element->getElementId(), $e->getMessage()])
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove element. Error: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function truncateByFormId($formId, $excludedElementsIds = [])
    {
        if ($formId) {
            try {
                $itemsIds = $this->getCollectionIdsByFormId($formId);

                if ($itemsIds && (count($itemsIds) > 0)) {
                    foreach ($itemsIds as $itemId) {
                        if ($excludedElementsIds && in_array($itemId, $excludedElementsIds)) {
                            continue;
                        } else {
                            $this->deleteById($itemId);
                        }
                    }

                    return true;
                }
            } catch (\Exception $e) {
                throw new CouldNotDeleteException(
                    __('Unable to remove old elements. Error: %1', [$e->getMessage()])
                );
            }

        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($elementId)
    {
        $model = $this->get($elementId);
        $this->delete($model);
        return true;
    }
}
