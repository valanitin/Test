<?php
namespace Dynamic\Mytickets\Api;

/**
 * Interface MyticketsManagementInterface
 *
 * @package Dynamic\Mytickets\Api
 */
interface MyticketsManagementInterface
{
    /**
     * Mytickets form.
     *
     * @param mixed $ticketForm
     * 
     * @return \Dynamic\Mytickets\Api\MyticketsInterface
     */
    public function submitForm($ticketForm);
}