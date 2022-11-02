<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Statistic\Usage;

use Plumrocket\Base\Api\ConfigUtilsInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Api\UsageStatisticCollectorInterface;
use Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey;

/**
 * Class for integrate and merge other collectors data
 *
 * @since 2.3.0
 */
class CompositeCollector implements UsageStatisticCollectorInterface
{

    /**
     * @var \Plumrocket\Base\Api\ConfigUtilsInterface
     */
    private $config;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @var \Plumrocket\Base\Api\ModuleUsageStatisticCollectorInterface[]
     */
    private $moduleCollectors;

    /**
     * @var \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey
     */
    private $getTrueCustomerKey;

    /**
     * @param \Plumrocket\Base\Api\ConfigUtilsInterface                    $config
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface        $getExtensionInformation
     * @param \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey $getTrueCustomerKey
     * @param array                                                        $moduleCollectors
     */
    public function __construct(
        ConfigUtilsInterface $config,
        GetExtensionInformationInterface $getExtensionInformation,
        GetTrueCustomerKey $getTrueCustomerKey,
        array $moduleCollectors = []
    ) {
        $this->config = $config;
        $this->getExtensionInformation = $getExtensionInformation;
        $this->moduleCollectors = $moduleCollectors;
        $this->getTrueCustomerKey = $getTrueCustomerKey;
    }

    /**
     * @inheritDoc
     */
    public function collect(): array
    {
        $modulesData = [];
        foreach ($this->moduleCollectors as $moduleName => $moduleCollector) {
            if (! $moduleCollector->hasNewData()) {
                continue;
            }

            $extensionInformation = $this->getExtensionInformation->execute($moduleName);
            $serialKey = (string) $this->config->getStoreConfig(
                "{$extensionInformation->getConfigSection()}/general/serial"
            );

            $modulesData[$moduleName] = [
                'license_key' => $serialKey,
                'customer_hash'=> $this->getTrueCustomerKey->execute($moduleName),
                'statistic' => $moduleCollector->collect()
            ];
        }

        return $modulesData;
    }
}
