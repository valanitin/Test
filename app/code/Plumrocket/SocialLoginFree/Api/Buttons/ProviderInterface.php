<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Api\Buttons;

interface ProviderInterface
{
    /**
     * @param bool $onlyEnabled
     * @param null $storeId
     * @param bool $forceReload
     * @return mixed
     */
    public function getButtons($onlyEnabled = true, $storeId = null, $forceReload = false);

    /**
     * @param bool $onlyEnabled
     * @param bool $splitByVisibility
     * @param null $storeId
     * @param bool $forceReload
     * @return mixed
     */
    public function getPreparedButtons(
        $onlyEnabled = true,
        $splitByVisibility = true,
        $storeId = null,
        $forceReload = false
    );
}
