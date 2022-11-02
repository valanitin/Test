<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Provider;

class Types implements \Plumrocket\SocialLoginFree\Api\TypesProviderInterface
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\Config\Provider
     */
    private $configProvider;

    /**
     * Types constructor.
     *
     * @param \Plumrocket\SocialLoginFree\Model\Config\Provider $configProvider
     */
    public function __construct(\Plumrocket\SocialLoginFree\Model\Config\Provider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param null $scope
     * @param null $scopeCode
     * @return array
     */
    public function getEnabledList($scopeCode = null, $scope = null)
    {
        return array_keys(array_filter($this->configProvider->getNetworkStatusInfo($scope, $scopeCode)));
    }

    /**
     * @param null $scope
     * @param null $scopeCode
     * @return array
     */
    public function getDisabledList($scopeCode = null, $scope = null)
    {
        return array_keys(
            array_filter(
                $this->configProvider->getNetworkStatusInfo($scope, $scopeCode),
                static function ($status) {
                    return !$status;
                }
            )
        );
    }

    /**
     * @param null $scope
     * @param null $scopeCode
     * @return array
     */
    public function getAll($scopeCode = null, $scope = null)
    {
        return array_keys($this->configProvider->getNetworkStatusInfo($scope, $scopeCode));
    }
}
