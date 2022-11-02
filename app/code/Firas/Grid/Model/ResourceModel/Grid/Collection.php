<?php

/**
 * Grid Grid Collection.
 *
 * @category  Firas
 * @package   Firas_Grid
 * @author    Firas
 * @copyright Copyright (c) 2010-2017 Firas Software Private Limited (https://firas.com)
 * @license   https://store.firas.com/license.html
 */
namespace Firas\Grid\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            'Firas\Grid\Model\Grid',
            'Firas\Grid\Model\ResourceModel\Grid'
        );
    }
}
