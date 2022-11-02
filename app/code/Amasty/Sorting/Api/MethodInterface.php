<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Api;

/**
 * Interface IndexedMethodInterface
 * @api
 */
interface MethodInterface
{
    /**
     * Apply sorting method to collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param string $direction
     *
     * @return $this
     */
    public function apply($collection, $direction);

    /**
     * Returns Sorting method Code for using in code
     *
     * @return string
     */
    public function getMethodCode();

    /**
     * Returns Sorting method Name for using like Method label
     *
     * @return string
     */
    public function getMethodName();

    /**
     * Get method label for store
     *
     * @param null|int|\Magento\Store\Model\Store $store
     *
     * @return string
     */
    public function getMethodLabel($store = null);
}
