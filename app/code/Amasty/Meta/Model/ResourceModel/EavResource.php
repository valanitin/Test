<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Model\ResourceModel;

class EavResource extends \Magento\Eav\Model\ResourceModel\Config
{
    public function getProductTypeId(): int
    {
        $select = $this->getConnection()->select()->from($this->getTable('eav_entity_type'))
            ->where("entity_type_code = 'catalog_product'");

        return (int)$this->getConnection()->fetchOne($select);
    }

    public function getUrlPathId(int $productTypeId): int
    {
        $select = $this->getConnection()->select()->from($this->getTable('eav_attribute'))
            ->where('entity_type_id = ? AND (attribute_code = "url_path")', $productTypeId);

        return (int)$this->getConnection()->fetchOne($select);
    }

    public function getUrlKeyId(int $productTypeId): int
    {
        $select = $this->getConnection()->select()->from($this->getTable('eav_attribute'))
            ->where('entity_type_id = ? AND (attribute_code = "url_key")', $productTypeId);

        return (int)$this->getConnection()->fetchOne($select);
    }
}
