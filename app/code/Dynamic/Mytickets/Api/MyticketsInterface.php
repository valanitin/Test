<?php
namespace Dynamic\Mytickets\Api;

/**
 * MyticketsInterface interface
 *
 */
interface MyticketsInterface
{
    /**
    * @return \Dynamic\Mytickets\Api\MyticketsInterface[]
    */
    public function getMessage();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
}
