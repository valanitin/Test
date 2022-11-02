<?php
namespace Local\ProductOutofstock\Model\ResourceModel;

class Outofstock extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init("outofstock_detail", "id");
    }
}
