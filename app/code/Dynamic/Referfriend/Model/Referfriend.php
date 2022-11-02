<?php

namespace Dynamic\Referfriend\Model;

/**
 * Referfriend Model
 *
 * @method \Dynamic\Referfriend\Model\Resource\Page _getResource()
 * @method \Dynamic\Referfriend\Model\Resource\Page getResource()
 */
class Referfriend extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Referfriend\Model\ResourceModel\Referfriend');
    }

}
