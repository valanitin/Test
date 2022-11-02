<?php
namespace Dynamic\Returnstatus\Api;

/**
 * ReturnstatusInterface interface
 *
 */
interface ReturnstatusInterface
{
    /**
    * @return \Dynamic\Returnstatus\Api\ReturnstatusInterface[]
    */
    public function getMessage();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
}
