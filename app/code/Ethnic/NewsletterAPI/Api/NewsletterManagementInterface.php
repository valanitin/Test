<?php
/**
 * Ethnic
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ethnic
 * @package     Ethnic_NewsletterAPI
 * @copyright   Copyright (c)  (https://Ethnic.net/)
 */
namespace Ethnic\NewsletterAPI\Api;

/**
 * Newsletter interface.
 * @api
 */
interface NewsletterManagementInterface
{
    /**
     * Subscribe an email.
     *
     * @param string $email
     * @return \Ethnic\NewsletterAPI\Api\Data\NewsletterSubscribeInterface
     */
    public function subscribe($email);

    /**
     * Subscribe an email.
     *
     * @param string $email
     * @return \Ethnic\NewsletterAPI\Api\Data\NewsletterSubscribeInterface
     */
    public function unsubscribeCustomer($email);
}
