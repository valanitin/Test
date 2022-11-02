<?php
namespace Dynamic\Returnstatus\Api;

/**
 * Interface OrderreturnManagementInterface
 *
 * @package Dynamic\Returnstatus\Api
 */
interface ReturnstatusManagementInterface
{
    /**
     * Order-return form.
     *
     * @param string $status
     * 
     * @return \Dynamic\Returnstatus\Api\ReturnstatusInterface
     */
    public function submitForm($status);

    /**
     * Order-return form.
     * 
     * @return array
     */
    public function getList();
}