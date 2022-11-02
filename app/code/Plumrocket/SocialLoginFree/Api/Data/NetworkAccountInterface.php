<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Api\Data;

/**
 * @since 3.2.0
 */
interface NetworkAccountInterface
{

    /**
     * Get network code,
     *
     * @return string
     */
    public function getNetworkCode(): string;

    /**
     * Get network identifier.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Get photo url.
     *
     * @return string
     */
    public function getPhotoUrl(): string;

    /**
     * Retrieve only customer information that can be saved by magento model.
     *
     * @return string[]
     */
    public function getCustomerData(): array;
}
