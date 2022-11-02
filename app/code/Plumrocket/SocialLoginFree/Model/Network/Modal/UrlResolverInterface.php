<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Network\Modal;

interface UrlResolverInterface
{

    /**
     * Get modal window url.
     *
     * @return string
     * @throws \Plumrocket\SocialLoginFree\Model\Network\Exception\NetworkIsNotConfiguredException
     */
    public function getUrl(): string;
}
