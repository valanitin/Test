<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\ResourceModel\Method;

/**
 * Class Price
 */
class Price extends AbstractMethod
{
    /**
     * {@inheritdoc}
     */
    public function apply($collection, $direction)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAlias()
    {
        return 'price';
    }

    /**
     * @inheritdoc
     */
    public function getIndexedValues($storeId)
    {
        return [];
    }
}
