<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Model\Form\Element;

use Amasty\Customform\Api\Data;
use Amasty\Customform\Model\Form\Element\OptionFactory;
use Amasty\Customform\Model\ResourceModel\Form\Element\Option\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class OptionRepository implements \Amasty\Customform\Api\Form\Element\OptionRepositoryInterface
{
    /**
     * @var array
     */
    protected $option = [];

    /**
     * @var \Amasty\Customform\Model\ResourceModel\Form\Element\Option
     */
    private $optionResource;

    /**
     * @var OptionFactory
     */
    private $optionFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Amasty\Customform\Model\ResourceModel\Form\Element\Option $optionResource,
        CollectionFactory $collectionFactory,
        OptionFactory $optionFactory
    ) {
        $this->optionResource = $optionResource;
        $this->optionFactory = $optionFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Data\Form\Element\OptionInterface $option)
    {
        if ($option->getOptionId()) {
            $option = $this->get($option->getOptionId())->addData($option->getData());
        }

        try {
            $this->optionResource->save($option);
            unset($this->option[$option->getOptionId()]);
        } catch (\Exception $e) {
            if ($option->getOptionId()) {
                throw new CouldNotSaveException(
                    __('Unable to save option with ID %1. Error: %2', [$option->getOptionId(), $e->getMessage()])
                );
            }
            throw new CouldNotSaveException(__('Unable to save new option. Error: %1', $e->getMessage()));
        }
        
        return $option;
    }

    /**
     * {@inheritdoc}
     */
    public function get($optionId)
    {
        if (!isset($this->option[$optionId])) {
            /** @var \Amasty\Customform\Model\Form\Element\Option $option */
            $option = $this->optionFactory->create();
            $this->optionResource->load($option, $optionId);
            if (!$option->getOptionId()) {
                return $option;
            }
            $this->option[$optionId] = $option;
        }
        return $this->option[$optionId];
    }

    public function getByElementAndValue($elementId, $value)
    {
        if (!isset($this->option[$elementId . '-' .$value])) {
            $collection = $this->collectionFactory->create()
                ->addFieldToFilter('element_id', $elementId)
                ->addFieldToFilter('value', $value);
            /** @var \Amasty\Customform\Model\Form\Element\Option $option */
            $option = $collection->getFirstItem();
            if (!$option || !$option->getOptionId()) {
                $option = $this->optionFactory->create();
            }
            $this->option[$elementId . '-' .$value] = $option;
        }
        return $this->option[$elementId . '-' .$value];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Data\Form\Element\OptionInterface $option)
    {
        try {
            $this->optionResource->delete($option);
            unset($this->option[$option->getOptionId()]);
        } catch (\Exception $e) {
            if ($option->getOptionId()) {
                throw new CouldNotDeleteException(
                    __('Unable to remove option with ID %1. Error: %2', [$option->getOptionId(), $e->getMessage()])
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove option. Error: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($optionId)
    {
        $model = $this->get($optionId);
        $this->delete($model);
        return true;
    }
}
