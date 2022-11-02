<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as HelperContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Plumrocket\Base\Api\ExtensionStatusInterface;

/**
 * @since 1.0.0
 * @deprecated since 2.5.0 - we move logic from this class to others
 */
class Base extends AbstractHelper
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Each module must override this value
     * @var string
     */
    protected $_configSectionId;

    /**
     * Initialize helper
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param HelperContext                             $context
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        HelperContext $context
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * @param string $customerKey
     * @return string
     */
    protected function getTrueCustomerKey($customerKey)
    {
        $trueKey = '';

        if ($customerKey === '532416486b540ea2a1e50c4070b671611b44f52718') {
            $data = explode('_', $this->_getModuleName());
            $modName =  $data[1] ?? '';
            $trueKey = $this->getConfig($modName . '/module/data');
        }

        return $trueKey ?: $customerKey;
    }

    /**
     * Receive config section id
     *
     * @deprecated since 2.3.0 - use \Plumrocket\Base\Api\GetExtensionInformationInterface
     * @return string
     */
    public function getConfigSectionId()
    {
        return $this->_configSectionId;
    }

    /**
     * Receive magento config value
     * @deprecated since 2.3.0
     * @see \Plumrocket\Base\Helper\AbstractConfig::getConfig
     *
     * @param string      $path
     * @param string|int  $store
     * @param string|null $scope
     * @return mixed
     */
    public function getConfig($path, $store = null, $scope = null)
    {
        if ($scope === null) {
            $scope = ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->getValue($path, $scope, $store);
    }

    /**
     * @deprecated since 2.3.0
     * @see \Plumrocket\Base\Api\ExtensionStatusInterface::isEnabled
     *
     * @param  string $moduleName
     * @return bool
     */
    public function moduleExists($moduleName)
    {
        return $this->getModuleStatus($moduleName) ?: false;
    }

    /**
     * Receive status of Plumrocket module
     * @deprecated since 2.3.9
     * @see \Plumrocket\Base\Api\ExtensionStatusInterface
     *
     * @param string $moduleName e.g. SocialLoginFree
     * @return int
     * @api
     * @since 2.3.0
     */
    public function getModuleStatus(string $moduleName)
    {
        $hasModule = $this->_moduleManager->isEnabled("Plumrocket_$moduleName");
        if (! $hasModule) {
            return ExtensionStatusInterface::NOT_INSTALLED;
        }

        try {
            return $this->getConfigHelper($moduleName)->isModuleEnabled()
                ? ExtensionStatusInterface::ENABLED
                : ExtensionStatusInterface::DISABLED;
        } catch (NoSuchEntityException $e) {
            try {
                $dataHelper = $this->getModuleHelper($moduleName);
                if (method_exists($dataHelper, 'moduleEnabled')) {
                    return $dataHelper->moduleEnabled()
                        ? ExtensionStatusInterface::ENABLED
                        : ExtensionStatusInterface::DISABLED;
                }

                return ExtensionStatusInterface::ENABLED;
            } catch (NoSuchEntityException $e) {
                return ExtensionStatusInterface::ENABLED;
            }
        }
    }

    /**
     * Receive helper
     *
     * @param string $moduleName
     * @return \Magento\Framework\App\Helper\AbstractHelper
     * @deprecated since 2.7.0
     */
    public function getModuleHelper($moduleName)
    {
        $type = "Plumrocket\\{$moduleName}\Helper\Data";
        try {
            $dataHelper = $this->_objectManager->get($type);
        } catch (\ReflectionException $reflectionException) {
            return $this->_objectManager->get(DataFallback::class);
        }

        return $dataHelper;
    }

    /**
     * @param string $moduleName e.g. SizeChart
     * @return \Plumrocket\Base\Helper\AbstractConfig
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated since 2.7.0
     */
    public function getConfigHelper(string $moduleName)
    {
        $type = "Plumrocket\\{$moduleName}\Helper\Config";
        try {
            $configHelper = $this->_objectManager->get($type);
        } catch (\ReflectionException $reflectionException) {
            throw new NoSuchEntityException(__('Cannot create %1', $type));
        }

        if ($configHelper && $configHelper instanceof AbstractConfig) {
            return $configHelper;
        }

        throw new NoSuchEntityException(__('Cannot create %1', $type));
    }
}
