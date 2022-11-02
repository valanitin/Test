<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Statistic\Usage;

use Magento\Config\Model\ConfigFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * @since 2.3.0
 */
class Status implements StatusInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Config\Model\ConfigFactory
     */
    private $configFactory;

    /**
     * @var string
     */
    private $xmlPath;

    /**
     * SystemConfigSaveAfter constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection          $resourceConnection
     * @param \Magento\Config\Model\ConfigFactory                $configFactory
     * @param string                                             $xmlPath
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResourceConnection $resourceConnection,
        ConfigFactory $configFactory,
        string $xmlPath
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resourceConnection = $resourceConnection;
        $this->configFactory = $configFactory;
        $this->xmlPath = $xmlPath . (strpos($xmlPath, '/') === false ? '/general/new_changes' : '');
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        return (bool) $this->scopeConfig->getValue($this->xmlPath);
    }

    /**
     * @return $this
     */
    public function switchToCollect(): StatusInterface
    {
        return $this->changeStatus(1);
    }

    /**
     * @return $this
     */
    public function switchToMiss(): StatusInterface
    {
        return $this->changeStatus(0);
    }

    /**
     * @param int $status
     * @return $this
     */
    private function changeStatus(int $status): self
    {
        /** @var \Magento\Config\Model\Config $config */
        $config = $this->configFactory->create();
        $connection = $this->resourceConnection->getConnection('core_write');

        $connection->delete(
            $this->resourceConnection->getTableName('core_config_data'),
            [
                $connection->quoteInto('path = ?', $this->xmlPath)
            ]
        );

        $config->setDataByPath($this->xmlPath, $status);

        try {
            $config->save();
        } catch (\Exception $e) {
            return $this;
        }

        return $this;
    }
}
