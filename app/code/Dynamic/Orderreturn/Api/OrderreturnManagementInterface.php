<?php
namespace Dynamic\Orderreturn\Api;

/**
 * Interface OrderreturnManagementInterface
 *
 * @package Dynamic\Orderreturn\Api
 */
interface OrderreturnManagementInterface
{
    /**
     * Order-return form.
     *
     * @param mixed $returnForm
     * 
     * @return \Dynamic\Orderreturn\Api\OrderreturnInterface
     */
    public function submitForm($returnForm);
}