<?php

namespace Dynamic\Mytickets\Model;

/**
 * Mytickets Model
 *
 * @method \Dynamic\Mytickets\Model\Resource\Page _getResource()
 * @method \Dynamic\Mytickets\Model\Resource\Page getResource()
 */
class Mytickets extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Mytickets\Model\ResourceModel\Mytickets');
    }

}
