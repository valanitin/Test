<?php
namespace Dynamic\Orderreturn\Api;

/**
 * OrderreturnInterface interface
 *
 */
interface OrderreturnInterface
{
    /**
    * @return \Dynamic\Orderreturn\Api\OrderreturnInterface[]
    */
    public function getMessage();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
}
