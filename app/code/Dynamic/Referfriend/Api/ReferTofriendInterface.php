<?php
namespace Dynamic\Referfriend\Api;

/**
 * ReferTofriendInterface interface
 *
 */
interface ReferTofriendInterface
{
    /**
    * @return \Dynamic\Referfriend\Api\ReferTofriendInterface[]
    */
    public function getMessage();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
}
