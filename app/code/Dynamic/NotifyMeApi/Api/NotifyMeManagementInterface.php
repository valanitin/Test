<?php
namespace Dynamic\NotifyMeApi\Api;

/**
 * Interface NotifyMeManagementInterface
 *
 * @package Dynamic\NotifyMeApi\Api
 */
interface NotifyMeManagementInterface
{
    /**
     * NotifyMe form.
     * @param string $email
     * @param string $website
     * @param string $sku
     * @param string $size
     * @return \Dynamic\NotifyMeApi\Api\NotifyMeManagementInterface
     */
    public function submitForm($email, $website, $sku, $size = null);
}