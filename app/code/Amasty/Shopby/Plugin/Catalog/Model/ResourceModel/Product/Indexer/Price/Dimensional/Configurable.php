<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Catalog\Model\ResourceModel\Product\Indexer\Price\Dimensional;

class Configurable extends Simple
{
    /**
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterExecuteByDimensions($subject, $result)
    {
        $this->addSpecialPriceToConfigurable();

        return $result;
    }

    /**
     * @return void
     */
    private function addSpecialPriceToConfigurable()
    {
        if (!$this->entityIds) {
            return;
        }

        $connection = $this->resource->getConnection();
        $select = $connection->select()->from(
            ['main_table' => $this->getDataTable()]
        );

        if ($this->productIdLink == 'row_id') {
            $select->joinInner(
                ['product_entity' => $this->resource->getTableName('catalog_product_entity')],
                'product_entity.entity_id=main_table.entity_id',
                []
            );
            $entityIdLink = 'product_entity.row_id';
        } else {
            $entityIdLink = 'main_table.entity_id';
        }

        $select->joinInner(
            ['simple_link' => $this->resource->getTableName('catalog_product_super_link')],
            'simple_link.product_id=' . $entityIdLink,
            ['parent_id' => 'simple_link.parent_id']
        );

        $select->where('simple_link.parent_id IN (?)', $this->entityIds);
        $select->where('main_table.price > main_table.final_price and main_table.final_price > 0');

        $select->group(['simple_link.parent_id', 'main_table.customer_group_id', 'main_table.website_id']);

        $insertData = $connection->fetchAll($select);
        if (!empty($insertData)) {
            foreach ($insertData as &$row) {
                if (isset($row['parent_id'])) {
                    $row['entity_id'] = $row['parent_id'];
                    unset($row['parent_id']);
                }
            }

            $connection->insertOnDuplicate(
                $this->getIdxTable(),
                $insertData,
                ['price', 'final_price']
            );
        }
    }
}
