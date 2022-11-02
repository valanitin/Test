<?php

namespace Dynamic\Returnstatus\Model;

/**
 * Returnstatus Model
 *
 * @method \Dynamic\Returnstatus\Model\Resource\Page _getResource()
 * @method \Dynamic\Returnstatus\Model\Resource\Page getResource()
 */
class Returnstatus extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Returnstatus\Model\ResourceModel\Returnstatus');
    }

}
