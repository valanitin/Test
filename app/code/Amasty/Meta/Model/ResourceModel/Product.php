<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Model\ResourceModel;

class Product extends \Magento\Catalog\Model\ResourceModel\Product
{
    const CATALOG_PRODUCT_ENTITY_VARCHAR = 'catalog_product_entity_varchar';

    public function getAttributeValue(int $attributeId, string $entityField, string $entityValue, int $storeId)
    {
        $condition = sprintf(
            'attribute_id = %s AND %s = %s AND store_id = %s',
            $attributeId,
            $entityField,
            $entityValue,
            $storeId
        );
        $select = $this->getConnection()->select()->from($this->getTable(self::CATALOG_PRODUCT_ENTITY_VARCHAR))
            ->where($condition);

        return $this->getConnection()->fetchRow($select);
    }

    public function updateAttributeValue(
        string $value,
        int $attributeId,
        string $entityField,
        string $entityValue,
        int $storeId
    ): Product {
        $this->getConnection()->update(
            $this->getTable(self::CATALOG_PRODUCT_ENTITY_VARCHAR),
            ['value' => $value],
            sprintf(
                'attribute_id = %s AND %s = %s AND store_id = %s',
                $attributeId,
                $entityField,
                $entityValue,
                $storeId
            )
        );

        return $this;
    }

    public function createAttributeValue(array $data): Product
    {
        $this->getConnection()->insert($this->getTable(self::CATALOG_PRODUCT_ENTITY_VARCHAR), $data);

        return $this;
    }
}
