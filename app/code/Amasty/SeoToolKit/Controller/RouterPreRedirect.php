<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Controller;

use Amasty\SeoToolKit\Api\Data\RedirectInterface;

class RouterPreRedirect extends RedirectRouterAbstract
{
    /**
     * @param RedirectInterface $redirect
     * @return bool
     */
    protected function isRedirectAllow(RedirectInterface $redirect): bool
    {
        return !$redirect->getUndefinedPageOnly();
    }
}
