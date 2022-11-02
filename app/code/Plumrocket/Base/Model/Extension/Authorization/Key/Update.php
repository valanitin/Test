<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization\Key;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface;
use Plumrocket\Base\Model\Extension\Authorization\Key;

/**
 * @since 2.5.0
 */
class Update
{
    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Key\Load
     */
    private $loadKey;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Status\Update
     */
    private $updateStatus;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Key
     */
    private $key;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    private $cacheTypeList;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    private $resourceModelConfig;

    /**
     * @var \Plumrocket\Base\Helper\Config
     */
    private $config;

    /**
     * @param \Plumrocket\Base\Model\Extension\Authorization\Key\Load      $loadKey
     * @param \Plumrocket\Base\Model\Extension\Authorization\Status\Update $updateStatus
     * @param \Plumrocket\Base\Model\Extension\Authorization\Key           $key
     * @param \Magento\Framework\App\Cache\TypeListInterface               $cacheTypeList
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Config\Model\ResourceModel\Config                   $resourceModelConfig
     * @param \Plumrocket\Base\Helper\Config                               $config\
     */
    public function __construct(
        Load $loadKey,
        \Plumrocket\Base\Model\Extension\Authorization\Status\Update $updateStatus,
        Key $key,
        TypeListInterface $cacheTypeList,
        ManagerInterface $eventManager,
        Config $resourceModelConfig,
        \Plumrocket\Base\Helper\Config $config
    ) {
        $this->loadKey = $loadKey;
        $this->updateStatus = $updateStatus;
        $this->key = $key;
        $this->cacheTypeList = $cacheTypeList;
        $this->eventManager = $eventManager;
        $this->resourceModelConfig = $resourceModelConfig;
        $this->config = $config;
    }

    /**
     * Load new key and update status
     *
     * @param \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface $extension
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface
     */
    public function execute(ExtensionAuthorizationInterface $extension): ExtensionAuthorizationInterface
    {
        try {
            list($key, $expirationDays) = $this->loadKey->execute($extension);
            $this->key->set($extension->getModuleName(), $key);

            $this->resourceModelConfig->saveConfig(
                $this->key->getPath($extension->getModuleName()),
                $key,
                'default',
                0
            );

            // clear the config cache
            $this->cacheTypeList->cleanType('config');
            $this->eventManager->dispatch('adminhtml_cache_refresh_type', ['type' => 'config']);

            return $this->updateStatus->execute($extension, null, $expirationDays);
        } catch (LocalizedException $localizedException) {
            if ($this->config->isDebugMode()) {
                throw $localizedException;
            }
        }

        return $extension;
    }
}
