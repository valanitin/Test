<?php
namespace Dynamic\Abandonedcartapi\Api;

/**
 * AbandonedcartapiInterface interface
 *
 */
interface AbandonedcartapiInterface
{
    /**
    * @return \Dynamic\Abandonedcartapi\Api\AbandonedcartapiInterface[]
    */
    public function getMessage();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
}
