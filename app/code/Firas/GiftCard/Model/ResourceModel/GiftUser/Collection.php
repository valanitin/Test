<?php
/**
 * @Author      Firas Developers
 * @package     Firas_GiftCard
 * @copyright   Copyright (c) 2019 MAGETOP (https://www.firas.com)
 * @terms       https://www.firas.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Firas\GiftCard\Model\ResourceModel\GiftUser;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Firas Marketplace ResourceModel Seller collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'giftuserid';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Firas\GiftCard\Model\GiftUser',
            'Firas\GiftCard\Model\ResourceModel\GiftUser'
        );
        $this->_map['fields']['giftuserid'] = 'main_table.giftuserid';
        $this->_map['fields']['created_at'] = 'main_table.created_at';
    }

    
    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
}
