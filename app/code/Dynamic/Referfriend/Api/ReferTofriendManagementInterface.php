<?php
namespace Dynamic\Referfriend\Api;

/**
 * Interface ReferTofriendManagementInterface
 *
 * @package Dynamic\Referfriend\Api
 */
interface ReferTofriendManagementInterface
{
    /**
     * Refer-friend form.
     *
     * @param mixed $referForm
     * 
     * @return \Dynamic\Referfriend\Api\ReferTofriendInterface
     */
    public function submitForm($referForm);
}