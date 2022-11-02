<?php
namespace Dynamic\Orderreturn\Api;

/**
 * UpdatereturnInterface interface
 *
 */
interface UpdatereturnInterface
{
    /**
    * @return \Dynamic\Orderreturn\Api\UpdatereturnInterface[]
    */
    public function getMessage();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
}
