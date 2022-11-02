<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Debug;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * @since 3.2.0
 */
class NetworkLogger implements NetworkLoggerInterface
{

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    private $customerSession;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Debug\Logger
     */
    private $logger;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @param \Magento\Framework\Session\SessionManagerInterface    $customerSession
     * @param \Plumrocket\SocialLoginFree\Model\Network\Debug\Logger $logger
     * @param \Magento\Framework\Serialize\SerializerInterface      $serializer
     */
    public function __construct(
        SessionManagerInterface $customerSession,
        Logger $logger,
        SerializerInterface $serializer
    ) {
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * Add response info for debug.
     *
     * @param string           $networkCode
     * @param string|array|int $message
     * @param array            $sensitiveData
     * @return void
     */
    public function add(string $networkCode, $message, array $sensitiveData = []): void
    {
        $message = $this->serialize($networkCode, $message);

        $sensitiveData = array_filter($sensitiveData);
        if ($sensitiveData) {
            $message = str_replace(array_values($sensitiveData), array_keys($sensitiveData), $message);
        }

        $logs = $this->customerSession->getPsloginLog() ?: [];
        $logs[] = $message;
        $this->customerSession->setPsloginLog($logs);
    }

    /**
     * Add exception to log.
     *
     * @param string     $networkCode
     * @param \Exception $exception
     */
    public function addException(string $networkCode, \Exception $exception): void
    {
        $this->add($networkCode, $exception->getMessage());
    }

    /**
     * Get error information.
     *
     * @return string
     */
    public function getDebugInfo(): string
    {
        $logs = $this->customerSession->getPsloginLog() ?: [];
        return implode(PHP_EOL, $logs);
    }

    /**
     * Record collected logs to file.
     */
    public function recordLogs(): void
    {
        $logs = $this->customerSession->getPsloginLog();
        if (! is_array($logs)) {
            return;
        }
        $this->logger->debug(implode(PHP_EOL, $logs));
    }

    /**
     * Clear log from session.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->customerSession->unsPsloginLog();
    }

    /**
     * Serialize network response.
     *
     * @param string $networkCode
     * @param mixed  $response
     * @return string
     */
    public function serialize(string $networkCode, $response): string
    {
        $log = [];
        if (is_string($response)) {
            try {
                $response = $this->serializer->unserialize($response);
            } catch (\InvalidArgumentException $e) {
                // ignore
            }
        }

        if (is_array($response) || is_object($response)) {
            $log[] = print_r($response, true);
        } elseif (strpos(trim((string) $response), ' ') === false) {
            parse_str((string) $response, $result);
            $log[] = print_r($result, true);
        } else {
            $log[] = $response;
        }

        return implode(PHP_EOL, $log);
    }
}
