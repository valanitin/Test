<?php
namespace Dynamic\ContactformApi\Api;

/**
 * Interface ContactusManagementInterface
 *
 * @package Dynamic\ContactformApi\Api
 */
interface ContactusManagementInterface
{
    /**
     * Contact us form.
     *
     * @param mixed $contactForm
     *
     * @return \Dynamic\ContactformApi\Api\ContactusInterface
     */
    public function submitForm($contactForm);
}