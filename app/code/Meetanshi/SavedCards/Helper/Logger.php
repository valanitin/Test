<?php

namespace Meetanshi\SavedCards\Helper;

use Psr\Log\LoggerInterface;

/**
 * Class Logger
 * @package Meetanshi\SavedCards\Helper
 */
class Logger
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Logger constructor.
     * @param LoggerInterface $logger
     * @param Data $helper
     */
    public function __construct(LoggerInterface $logger, Data $helper)
    {
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * @param $message
     * @param array $context
     */
    public function debug($message, array $context = [])
    {
        if ($this->helper->isLoggerEnabled()) {
            $message = "SavedCards Direct : " . $message;
            $this->logger->debug($message, $context);
        }
    }
}
