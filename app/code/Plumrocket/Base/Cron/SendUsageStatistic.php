<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Cron;

use Plumrocket\Base\Api\UsageStatisticCollectorInterface;
use Plumrocket\Base\Helper\Config;
use Plumrocket\Base\Model\Statistic\Usage\Sender as UsageStatisticSender;

/**
 * Check if have some statistic to send and then send ot
 *
 * @since 2.3.0
 */
class SendUsageStatistic
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Plumrocket\Base\Api\UsageStatisticCollectorInterface
     */
    private $usageStatisticCollector;

    /**
     * @var UsageStatisticSender
     */
    private $usageStatisticSender;

    /**
     * @var \Plumrocket\Base\Model\Utils\GetEnabledStoresUrls
     */
    private $getEnabledStoresUrls;

    /**
     * @param \Plumrocket\Base\Helper\Config                        $config
     * @param \Plumrocket\Base\Api\UsageStatisticCollectorInterface $usageStatisticCollector
     * @param \Plumrocket\Base\Model\Statistic\Usage\Sender         $usageStatisticSender
     * @param \Plumrocket\Base\Model\Utils\GetEnabledStoresUrls     $getEnabledStoresUrls
     */
    public function __construct(
        Config $config,
        UsageStatisticCollectorInterface $usageStatisticCollector,
        UsageStatisticSender $usageStatisticSender,
        \Plumrocket\Base\Model\Utils\GetEnabledStoresUrls $getEnabledStoresUrls
    ) {
        $this->config = $config;
        $this->usageStatisticCollector = $usageStatisticCollector;
        $this->usageStatisticSender = $usageStatisticSender;
        $this->getEnabledStoresUrls = $getEnabledStoresUrls;
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        return false; // TODO: uncomment after create api

        if (! $this->config->isEnabledStatistic()) {
            return false;
        }

        if ($usageData = $this->usageStatisticCollector->collect()) {
            return $this->usageStatisticSender->send(
                [
                    'storesUrls' => $this->getEnabledStoresUrls->execute(),
                    'modules' => $usageData,
                ]
            );
        }

        return false;
    }
}
