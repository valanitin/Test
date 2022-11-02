<?php
namespace Local\ProductOutofstock\Model;

class Outofstock extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init(\Local\ProductOutofstock\Model\ResourceModel\Outofstock::class);
    }
}
