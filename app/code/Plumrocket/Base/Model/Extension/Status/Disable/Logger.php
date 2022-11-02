<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Status\Disable;

use Plumrocket\Base\Model\External\LastRequests;
use Psr\Log\LoggerInterface;

/**
 * Logger for the extension disabling reason.
 *
 * If one of the plumrocket extensions disabled, we need have the opportunity to get know who have done it.
 * So we add logger that track all disabling due to bad licence, store problem, etc.
 *
 * @since 2.5.7
 */
class Logger
{
    /**
     * @var \Plumrocket\Base\Model\External\LastRequests
     */
    private $lastRequests;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Plumrocket\Base\Model\External\LastRequests $lastRequests
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        LastRequests $lastRequests,
        LoggerInterface $logger
    ) {
        $this->lastRequests = $lastRequests;
        $this->logger = $logger;
    }

    public function log(string $moduleName, string $description = '')
    {
        $logMessage = "$moduleName was disabled, for details see the request data and the backtrace below";
        $logMessage .= $description ? "\nDescription: $description" : '';
        $logMessage .= "\nConnector Requests Data: " . json_encode($this->lastRequests->getList());
        $logMessage .= "\n" . (new \Exception)->getTraceAsString();
        /** Use exception.log as module disabling is critical */
        $this->logger->critical(new \Exception($logMessage));

        // TODO: add admin notification
    }
}
