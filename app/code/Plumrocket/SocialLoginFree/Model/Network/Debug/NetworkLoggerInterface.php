<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Network\Debug;

/**
 * @since 3.2.0
 */
interface NetworkLoggerInterface
{

    /**
     * Add response info for debug.
     *
     * @param string           $networkCode
     * @param string|array|int $message
     * @param array            $sensitiveData
     * @return void
     */
    public function add(string $networkCode, $message, array $sensitiveData = []): void;

    /**
     * Add exception to log.
     *
     * @param string     $networkCode
     * @param \Exception $exception
     */
    public function addException(string $networkCode, \Exception $exception): void;

    /**
     * Get errors.
     *
     * @return string
     */
    public function getDebugInfo(): string;

    /**
     * Record collected logs to file.
     *
     * @return void
     */
    public function recordLogs(): void;

    /**
     * Clear log from session.
     *
     * @return void
     */
    public function clear(): void;
}
