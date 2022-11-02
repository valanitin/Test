<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Plumrocket\Base\Api\GetExtensionInformationInterface;

/**
 * Base class for extension's Helper/Config
 *
 * @since 2.3.0
 * @deprecated since 2.6.0
 * @see \Plumrocket\Base\Api\ConfigUtilsInterface
 */
abstract class AbstractConfig extends ConfigUtils
{
    /**
     * @deprecated since 2.5.0 - it is better to use "XML_PATH_" constants, see our docs
     */
    const SECTION_ID = '';
    /**
     * @deprecated since 2.5.0 - it is better to use "XML_PATH_" constants, see our docs
     */
    const GROUP_GENERAL = 'general';

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    protected $getExtensionInformation;

    /**
     * @param \Magento\Framework\App\Helper\Context                      $context
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface|null $getExtensionInformation
     */
    public function __construct(
        Context $context,
        GetExtensionInformationInterface $getExtensionInformation = null
    ) {
        parent::__construct($context);
        $this->getExtensionInformation = $getExtensionInformation ?: ObjectManager::getInstance()->get(
            GetExtensionInformationInterface::class
        );
    }

    /**
     * All modules must return his status.
     *
     * There are four ways to do this:
     *   1. Rewrite method and write own logic (recommended)
     *   3. Rewrite method and return true (recommended for free modules)
     *   4. Use \Plumrocket\Base\Helper\AbstractConfig::isModuleEnabledInConfig method (not recommended)
     *
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function isModuleEnabled($store = null, $scope = null): bool
    {
        $configPath = $this->getExtensionInformation->execute($this->_getModuleName())->getIsEnabledFieldConfigPath();
        if (! $configPath) {
            throw new \LogicException('You must specify enabled field config path via DI or rewrite method');
        }

        return (bool) $this->getConfig($configPath, $store, $scope);
    }

    /**
     * Can be used for certain modules with config path like "SECTION_ID/general/enabled"
     * @deprecated since 2.3.7 - it is better to have path to enabled field in one place and it here
     * @see \Plumrocket\Base\Model\Extensions\Information::IS_ENABLED_FIELD_CONFIG_PATH
     *
     * @param null $store
     * @param null $scope
     * @return bool
     */
    protected function isModuleEnabledInConfig($store = null, $scope = null): bool
    {
        return (bool) $this->getConfigByGroup(static::GROUP_GENERAL, 'enabled', $store, $scope);
    }

    /**
     * Receive magento config value
     * @deprecated since 2.3.7 - it is better to use constants, see our docs
     *
     * @param  string          $group second part of the path, e.g. "general"
     * @param  string          $path third part of the path, e.g. "enabled"
     * @param  string|int|null $scopeCode
     * @param  string|null     $scope
     * @return mixed
     */
    public function getConfigByGroup($group, $path, $scopeCode = null, $scope = null)
    {
        return $this->getConfig(
            implode('/', [static::SECTION_ID, $group, $path]),
            $scopeCode,
            $scope
        );
    }

    /**
     * Receive magento config value
     * Used for deep paths, like "pr_base/statistic/usage/enabled"
     * @deprecated since 2.3.7 - it is better to use constants, see our docs
     *
     * @param  string          $group second part of the path
     * @param  string          $subGroup third part of the path
     * @param  string          $path fourth part of the path
     * @param  string|int|null $scopeCode
     * @param  string|null     $scope
     * @return mixed
     */
    public function getConfigBySubGroup($group, $subGroup, $path, $scopeCode = null, $scope = null)
    {
        return $this->getConfigByGroup($group . '/' . $subGroup, $path, $scopeCode, $scope);
    }
}
