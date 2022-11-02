<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Model\ResourceModel\Redirect;

use Amasty\SeoToolKit\Api\Data\RedirectInterface;
use Magento\Store\Model\Store;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init(\Amasty\SeoToolKit\Model\Redirect::class, \Amasty\SeoToolKit\Model\ResourceModel\Redirect::class);
        $this->_setIdFieldName(RedirectInterface::REDIRECT_ID);
    }

    /**
     * @param array $orders
     */
    public function setOrders(array $orders)
    {
        $this->_orders = $orders;

        return $this;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter(int $storeId)
    {
        $storeIds = [$storeId, Store::DEFAULT_STORE_ID];
        $this->getSelect()->joinLeft(
            ['stores' => $this->getTable(RedirectInterface::STORE_TABLE_NAME)],
            'main_table.redirect_id = stores.redirect_id',
            ['store_id']
        )
            ->where('store_id IN (?)', $storeIds)
            ->group('main_table.redirect_id');

        return $this;
    }
}
