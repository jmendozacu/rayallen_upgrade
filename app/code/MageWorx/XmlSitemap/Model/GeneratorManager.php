<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model;

use MageWorx\XmlSitemap\Helper\Data as Helper;
/**
 * {@inheritdoc}
 */
class GeneratorManager
{
    /**
     * @var array
     */
    protected $countByEntity;

    /**
     * @var Sitemap
     */
    protected $model;

    /**
     * @var
     */
    protected $helper;

    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var GeneratorFactory
     */
    protected $generatorFactory;

    /**
     * @var Writer
     */
    protected $xmlWriter;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * GeneratorManager constructor.
     * @param GeneratorFactory $generatorFactory
     * @param Helper $helper
     * @param WriterInterface $xmlWriter
     */
    public function __construct(
        GeneratorFactory $generatorFactory,
        Helper $helper,
        WriterInterface $xmlWriter
    ) {
        $this->generatorFactory    = $generatorFactory;
        $this->helper              = $helper;
        $this->xmlWriter           = $xmlWriter;
    }

    /**
     * @param Sitemap $model
     */
    protected function init(Sitemap $model)
    {
        $this->model               = $model;
        $this->storeId             = $model->getStoreId();
        $this->helper->init($this->storeId);

        $this->xmlWriter->init(
            $this->model->getFullPath(), $this->model->getSitemapPath(), $this->model->getSitemapFilename(),
            $this->model->getFullTempPath(), $this->model->getStoreBaseUrl()
        );
    }

    /**
     * @param Sitemap $sitemap
     */
    public function generateXml(Sitemap $sitemap)
    {
        $this->init($sitemap);
        $this->xmlWriter->startWriteXml();

        foreach ($this->generatorFactory->getAllGenerators() as $generatorCode => $model) {
            $this->generator = $model;

            $this->generator->generate($this->storeId, $this->xmlWriter);
            $this->countByEntity[$generatorCode] = [
                'title' => $this->generator->getName(),
                'value' => $this->generator->getCounter()
            ];
        }

        $this->countByEntity['images']['title'] = __('Images');
        $this->countByEntity['images']['value'] = $this->xmlWriter->getWritedImages();

        $this->xmlWriter->endWriteXml();
        return;
    }

    /**
     * @return array
     */
    public function getCountByEntity() {
        return $this->countByEntity;
    }
}