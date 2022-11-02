<?php
namespace Dynamic\Notifyme\Api;

/**
 * NotifystockInterface interface
 *
 */
interface NotifystockInterface
{
    /**
    * @return \Dynamic\Notifyme\Api\NotifystockInterface[]
    */
    public function getMessage();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
}
