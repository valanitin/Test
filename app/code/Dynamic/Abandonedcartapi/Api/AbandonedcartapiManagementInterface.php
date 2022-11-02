<?php
namespace Dynamic\Abandonedcartapi\Api;

/**
 * Interface AbandonedcartapiManagementInterface
 *
 * @package Dynamic\Abandonedcartapi\Api
 */
interface AbandonedcartapiManagementInterface
{
    /**
     * Abandonedcartapi form.
     * 
     * @param mixed $cartInfo
     * @return \Dynamic\Abandonedcartapi\Api\AbandonedcartapiInterface
     */
    public function submitForm($cartInfo);
}