<?php
namespace Dynamic\Orderreturn\Api;

/**
 * Interface UpdatereturnManagementInterface
 *
 * @package Dynamic\Orderreturn\Api
 */
interface UpdatereturnManagementInterface
{
    /**
     * Status-return form.
     *
     * @param mixed $statusReturn
     * 
     * @return \Dynamic\Orderreturn\Api\OrderreturnInterface
     */
    public function statusReturnForm($statusReturn);
}