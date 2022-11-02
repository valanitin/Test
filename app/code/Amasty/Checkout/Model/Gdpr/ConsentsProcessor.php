<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */

declare(strict_types=1);

namespace Amasty\Checkout\Model\Gdpr;

use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

class ConsentsProcessor
{
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ManagerInterface $eventManager,
        LoggerInterface $logger
    ) {
        $this->eventManager = $eventManager;
        $this->logger = $logger;
    }

    /**
     * @param OrderInterface $order
     * @param array $consentsData
     */
    public function process(OrderInterface $order, array $consentsData): void
    {
        $storeId = (int)$order->getStoreId();
        $customerId = (int)$order->getCustomerId();
        $email = (string)$order->getCustomerEmail();
        $consentsData = $this->groupConsentsData($consentsData);
    }

    /**
     * @param array $consentsData
     * @return array
     */
    private function groupConsentsData(array $consentsData): array
    {
        $grouped = [];

        foreach ($consentsData as $consentCode => $consent) {
            $from = $consent['from'];
            $checked = $consent['checked'] ?? $consent;
            $grouped[$from][$consentCode] = $checked;
        }

        return $grouped;
    }
}
