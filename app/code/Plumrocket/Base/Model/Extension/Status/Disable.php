<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Status;

use Magento\Config\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Model\Extension\Status\Disable\Logger;

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
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Plumrocket\Base\Model\Extension\Status\Disable\Logger
     */
    private $disableExtensionLogger;

    /**
     * @param \Magento\Config\Model\Config                           $config
     * @param \Magento\Framework\App\ResourceConnection              $resourceConnection
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface  $getExtensionInformation
     * @param \Magento\Framework\ObjectManagerInterface              $objectManager
     * @param \Plumrocket\Base\Model\Extension\Status\Disable\Logger $disableExtensionLogger
     */
    public function __construct(
        Config $config,
        ResourceConnection $resourceConnection,
        GetExtensionInformationInterface $getExtensionInformation,
        ObjectManagerInterface $objectManager,
        Logger $disableExtensionLogger
    ) {
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
        $this->getExtensionInformation = $getExtensionInformation;
        $this->objectManager = $objectManager;
        $this->disableExtensionLogger = $disableExtensionLogger;
    }

    /**
     * @param string $moduleName
     * @param string $description
     * @return bool
     */
    public function execute(string $moduleName, string $description = ''): bool
    {
        // TODO: remove usage of 'disableExtension' after finished integration with all extensions
        $helper = $this->getHelper($moduleName);
        if ($helper && method_exists($helper, 'disableExtension')) {
            $helper->disableExtension();
            $this->disableExtensionLogger->log($moduleName, $description);
            return true;
        }

        $configPath = $this->getExtensionInformation->execute($moduleName)->getIsEnabledFieldConfigPath();
        if (! $configPath) {
            return false;
        }

        $connection = $this->resourceConnection->getConnection('core_write');
        $connection->delete(
            $this->resourceConnection->getTableName('core_config_data'),
            [
                $connection->quoteInto('path = ?', $configPath)
            ]
        );

        $this->disableExtensionLogger->log($moduleName, $description);

        try {
            $this->config->setDataByPath($configPath, 0);
            $this->config->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Receive data helper
     *
     * @param string $moduleName
     * @return \Magento\Framework\App\Helper\AbstractHelper|false
     */
    private function getHelper(string $moduleName)
    {
        $type = "Plumrocket\\{$moduleName}\Helper\Data";
        try {
            return $this->objectManager->get($type);
        } catch (\ReflectionException $reflectionException) {
            return false;
        }
    }
}
