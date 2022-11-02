<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Elasticsearch\Adapter;

use Magento\Framework\Indexer\IndexerRegistry;
use Amasty\Sorting\Model\ResourceModel\Method\AbstractMethod;
use Amasty\Sorting\Helper\Data;

/**
 * Class IndexedDataMapper
 */
abstract class IndexedDataMapper implements DataMapperInterface
{
    const DEFAULT_VALUE = 0;

    /**
     * @var AbstractMethod
     */
    protected $resourceMethod;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    public function __construct(
        IndexerRegistry $indexerRegistry,
        AbstractMethod $resourceMethod,
        Data $helper
    ) {
        $this->resourceMethod = $resourceMethod;
        $this->helper = $helper;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @return string
     */
    abstract public function getIndexerCode();

    /**
     * @param $storeId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function loadValuesArray($storeId)
    {
        if (!isset($this->values[$storeId])) {
            $this->values[$storeId] = $this->forceLoad($storeId);
        }
    }

    /**
     * @param $storeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function forceLoad($storeId)
    {
        try {
            $indexer = $this->indexerRegistry->get($this->getIndexerCode());
            $indexer->reindexAll();
        } catch (\InvalidArgumentException $e) {
            ;//No action required
        }

        return $this->resourceMethod->getIndexedValues($storeId);
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        return !$this->helper->isMethodDisabled($this->resourceMethod->getMethodCode());
    }

    /**
     * @param int $entityId
     * @param array $entityIndexData
     * @param int $storeId
     * @param array $context
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function map($entityId, array $entityIndexData, $storeId, $context = [])
    {
        $this->loadValuesArray($storeId);
        $value = isset($this->values[$storeId][$entityId]) ? $this->values[$storeId][$entityId] : self::DEFAULT_VALUE;

        return [static::FIELD_NAME => $value];
    }
}
