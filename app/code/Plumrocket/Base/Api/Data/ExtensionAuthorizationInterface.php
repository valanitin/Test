<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Api\Data;

/**
 * @since 2.5.0
 */
interface ExtensionAuthorizationInterface
{
    public const SIGNATURE = 'signature';
    public const STATUS = 'status';
    public const DATE = 'date';

    /**
     * Get module name.
     *
     * @return string
     */
    public function getModuleName(): string;

    /**
     * Check if product is in stock
     *
     * @return bool
     */
    public function isAuthorized(): bool;

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus(): int;

    /**
     * Set status.
     *
     * @param int $status
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface
     */
    public function setStatus(int $status): ExtensionAuthorizationInterface;

    /**
     * Set signature.
     *
     * @param string $signature
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface
     */
    public function setSignature(string $signature): ExtensionAuthorizationInterface;

    /**
     * Get date.
     *
     * @return string
     */
    public function getDate(): string;

    /**
     * Set date.
     *
     * @param string $date
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface
     */
    public function setDate(string $date): ExtensionAuthorizationInterface;

    /**
     * Check if authorization is cached.
     *
     * @return bool
     */
    public function isCached(): bool;
}
