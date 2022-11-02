<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\ResourceModel\Method;

use Amasty\Sorting\Model\Source\Stock as StockSource;

/**
 * Class Instock
 * Method Using like additional sorting and not visible in the list of methods
 */
class Instock extends AbstractMethod
{
    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\StatusFactory
     */
    private $stockStatusResourceFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    private $stockHelper;

    /**
     * Used for MSI
     *
     * @var null|int
     */
    private $stockId = null;

    public function __construct(
        Context $context,
        \Magento\Framework\Escaper $escaper,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\StatusFactory $stockStatusResourceFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        $connectionName = null,
        $methodCode = '',
        $methodName = ''
    ) {
        parent::__construct($context, $escaper, $connectionName, $methodCode, $methodName);
        $this->stockStatusResourceFactory = $stockStatusResourceFactory;
        $this->moduleManager = $moduleManager;
        $this->stockHelper = $stockHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($collection, $direction = '')
    {
        if (!$this->isMethodActive($collection) || $this->isMethodAlreadyApplied($collection)) {
            return $this;
        }

        $fromTables = $collection->getSelect()->getPart('from');
        if (!isset($fromTables['stock_status_index'])) {
            $this->stockHelper->addIsInStockFilterToCollection($collection);
            $fromTables = $collection->getSelect()->getPart('from');
        }

        $catalogInventoryTable = $collection->getResource()->getTable('cataloginventory_stock_status');
        if ($this->isMsiEnabled() && $fromTables['stock_status_index']['tableName'] != $catalogInventoryTable) {
            $qtyColumn = 'quantity';
            $salableColumn = 'is_salable';
        } else {
            $qtyColumn = 'qty';
            $salableColumn = 'stock_status';
        }
        $qtyAlias = 'stock_status_index.' . $qtyColumn;

        if ($this->isMsiEnabled()) {
            if (!isset($fromTables['reservation'])) {
                $reservedTable = $collection->getConnection()->select()
                    ->from($this->getTable('inventory_reservation'), ['sku', 'quantity' => 'SUM(quantity)'])
                    ->where('stock_id = ?', $this->getStockId())
                    ->group('sku');
                $collection->getSelect()->joinLeft(
                    ['reservation' => $reservedTable],
                    'reservation.sku = e.sku',
                    ['reserved_qty' => 'quantity']
                );
            }
            $qtyAlias .= ' + IF(reserved_qty IS NOT NULL, reserved_qty, 0)';
        }

        /**
         * join in @see \Magento\CatalogInventory\Model\AddStockStatusToCollection
         * so we don't need to process join, only add sorting
         */
        if ($this->helper->getScopeValue('general/out_of_stock_qty')) {
            $ignoreTypes = [
                '\'grouped\'',
                '\'bundle\''
            ];
            $collection->getSelect()->order(
                /** in result something like this IF(qty > 0, 0, 1) . bundles and grouped not processing */
                $this->getConnection()->getCheckSql(
                    sprintf(
                        '%s > %s OR e.type_id in (%s)',
                        $qtyAlias,
                        $this->helper->getQtyOutStock(),
                        implode(',', $ignoreTypes)
                    ),
                    '0',
                    '1'
                )
            );
        } else {
            $collection->getSelect()->order(
                'stock_status_index.' . $salableColumn . ' ' . $collection::SORT_ORDER_DESC
            );
        }

        $orders = $collection->getSelect()->getPart(\Zend_Db_Select::ORDER);
        // move from the last to the the first position
        array_unshift($orders, array_pop($orders));
        $collection->getSelect()->setPart(\Zend_Db_Select::ORDER, $orders);

        $this->markApplied($collection);

        return $this;
    }

    /**
     * Is can apply method sorting
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return bool
     */
    private function isMethodActive($collection)
    {
        // is out of stock is not displayed, method don't need to be applied
        $isShowOutOfStock = $this->scopeConfig->getValue(
            \Magento\CatalogInventory\Model\Configuration::XML_PATH_SHOW_OUT_OF_STOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$isShowOutOfStock) {
            return false;
        }

        $show = $this->helper->getScopeValue('general/out_of_stock_last');

        if (!$show || ($show == StockSource::SHOW_LAST_FOR_CATALOG && $this->isSearchModule())) {
            return false;
        }

        return true;
    }

    /**
     * skip search results
     *
     * @return bool
     */
    private function isSearchModule()
    {
        return in_array(
            $this->request->getModuleName(),
            ['sqli_singlesearchresult', 'catalogsearch']
        );
    }

    /**
     * @inheritdoc
     */
    public function getIndexedValues($storeId)
    {
        return [];
    }

    /**
     * For MSI.
     *
     * @return int
     */
    private function getStockId()
    {
        if ($this->stockId === null) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('inventory_stock_sales_channel'), ['stock_id'])
                ->where('type = \'website\' AND code = ?', $this->storeManager->getWebsite()->getCode());

            $this->stockId = (int)$this->getConnection()->fetchOne($select);
        }

        return $this->stockId;
    }

    /**
     * @return bool
     */
    private function isMsiEnabled()
    {
        return $this->moduleManager->isEnabled('Magento_Inventory');
    }
}
