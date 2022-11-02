<?php
namespace Local\ProductOutofstock\Model\ResourceModel\Outofstock;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Id Initialize
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init(
            \Local\ProductOutofstock\Model\Outofstock::class,
            \Local\ProductOutofstock\Model\ResourceModel\Outofstock::class
        );
    }
}
