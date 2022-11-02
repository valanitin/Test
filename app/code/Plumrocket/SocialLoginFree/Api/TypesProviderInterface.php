<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Api;

interface TypesProviderInterface
{
    /**
     * @param null $scopeCode
     * @param null $scope
     * @return mixed
     */
    public function getEnabledList($scopeCode = null, $scope = null);

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return mixed
     */
    public function getDisabledList($scopeCode = null, $scope = null);

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return mixed
     */
    public function getAll($scopeCode = null, $scope = null);
}
