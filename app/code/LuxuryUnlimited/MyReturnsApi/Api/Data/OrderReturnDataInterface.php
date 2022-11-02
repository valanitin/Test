<?php

namespace LuxuryUnlimited\MyReturnsApi\Api\Data;

interface OrderReturnDataInterface
{
    /**
     * Return the Error or Success message in the Response
     *
     * @return OrderReturnDataInterface[]
     */
    public function getMessage();

    /**
     *  Set the Error or Success message in the Response
     *
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message);

    /**
     * Return the Error status in the Response
     *
     * @return OrderReturnDataInterface[]
     */
    public function getError();

    /**
     * Set the error status in the response
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function setError($flag);
}
