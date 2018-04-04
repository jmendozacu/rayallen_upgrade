<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Model;

use Amasty\Customform\Api\Data\AnswerInterface;

class Answer extends \Magento\Framework\Model\AbstractModel implements AnswerInterface
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Customform\Model\ResourceModel\Answer');
        $this->setIdFieldName('answer_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getAnswerId()
    {
        return $this->_getData(AnswerInterface::ANSWER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAnswerId($answerId)
    {
        $this->setData(AnswerInterface::ANSWER_ID, $answerId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return $this->_getData(AnswerInterface::FORM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setFormId($formId)
    {
        $this->setData(AnswerInterface::FORM_ID, $formId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->_getData(AnswerInterface::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        $this->setData(AnswerInterface::STORE_ID, $storeId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->_getData(AnswerInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(AnswerInterface::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIp()
    {
        return $this->_getData(AnswerInterface::IP);
    }

    /**
     * {@inheritdoc}
     */
    public function setIp($ip)
    {
        $this->setData(AnswerInterface::IP, $ip);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseJson()
    {
        return $this->_getData(AnswerInterface::RESPONSE_JSON);
    }

    /**
     * {@inheritdoc}
     */
    public function setResponseJson($json)
    {
        $this->setData(AnswerInterface::RESPONSE_JSON, $json);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->_getData(AnswerInterface::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        $this->setData(AnswerInterface::CUSTOMER_ID, $customerId);

        return $this;
    }
}
