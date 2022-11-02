<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Product\Command;

use Magento\Config\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Plumrocket\Base\Api\GetExtensionInformationInterface;

/**
 * @since 2.3.7
 */
class Disable
{
    /**
     * @var \Magento\Config\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @param \Magento\Config\Model\Config                          $config
     * @param \Magento\Framework\App\ResourceConnection             $resourceConnection
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface $getExtensionInformation
     */
    public function __construct(
        Config $config,
        ResourceConnection $resourceConnection,
        GetExtensionInformationInterface $getExtensionInformation
    ) {
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
        $this->getExtensionInformation = $getExtensionInformation;
    }

    public function execute(string $moduleName): bool
    {
        $configPath = $this->getExtensionInformation->execute($moduleName)->getIsEnabledFieldConfigPath();
        if (! $configPath) {
            return false;
        }

        $resource = $this->resourceConnection;
        $connection = $resource->getConnection('core_write');
        $connection->delete(
            $resource->getTableName('core_config_data'),
            [
                $connection->quoteInto('path = ?', $configPath)
            ]
        );
        $this->config->setDataByPath($configPath, 0);

        try {
            $this->config->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
