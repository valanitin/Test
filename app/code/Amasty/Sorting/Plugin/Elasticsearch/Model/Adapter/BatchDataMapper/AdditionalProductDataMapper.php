<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Plugin\Elasticsearch\Model\Adapter\BatchDataMapper;

use Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapperInterface;
use Magento\Elasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper;

/**
 * Class AdditionalProductDataMapper
 */
class AdditionalProductDataMapper
{
    /**
     * @var DataMapperInterface[]
     */
    private $dataMappers = [];

    public function __construct(array $dataMappers = [])
    {
        $this->dataMappers = $dataMappers;
    }

    /**
     * Prepare index data for using in search engine metadata.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ProductDataMapper $subject
     * @param callable $proceed
     * @param array $documentData
     * @param $storeId
     * @param array $context
     * @return array
     */
    public function aroundMap(
        $subject,
        callable $proceed,
        array $documentData,
        $storeId,
        $context = []
    ) {
        $documentData = $proceed($documentData, $storeId, $context);
        foreach ($documentData as $productId => $document) {
            $context['document'] = $document;
            foreach ($this->dataMappers as $mapper) {
                if ($mapper instanceof DataMapperInterface && $mapper->isAllowed()) {
                    $document = array_merge($document, $mapper->map($productId, $document, $storeId, $context));
                }
            }
            $documentData[$productId] = $document;
        }

        return $documentData;
    }
}
