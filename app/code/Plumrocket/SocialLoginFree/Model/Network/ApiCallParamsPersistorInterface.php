<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Network;

/**
 * Save information from network between different requests
 */
interface ApiCallParamsPersistorInterface
{
    /**
     * @param string $key
     * @param        $value
     * @return \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface
     */
    public function add(string $key, $value): ApiCallParamsPersistorInterface;

    /**
     * @param array $value
     * @return $this
     */
    public function set(array $value): ApiCallParamsPersistorInterface;

    /**
     * @param string|null $key
     * @return mixed
     */
    public function get(string $key = null);

    /**
     * @return $this
     */
    public function clear(): ApiCallParamsPersistorInterface;
}
