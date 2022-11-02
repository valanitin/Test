<?php
namespace Dynamic\ContactformApi\Api;

/**
 * ContactusInterface interface
 *
 */
interface ContactusInterface
{
    /**
    * @return \Dynamic\ContactformApi\Api\ContactusInterface[]
    */
    public function getMessage();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
}
