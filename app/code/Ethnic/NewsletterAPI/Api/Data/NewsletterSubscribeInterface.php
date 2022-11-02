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
namespace Ethnic\NewsletterAPI\Api\Data;

/**
 * NewsletterSubscribeInterface
 */
interface NewsletterSubscribeInterface
{
    /**
     * Get success
     *
     * @return boolean
     */
    public function getSuccess();

    /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage();
}
