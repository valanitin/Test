<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Amp\Model\Base;

/**
 * @since 2.9.15
 */
class Information extends \Plumrocket\Base\Model\Extensions\Information
{
    const IS_SERVICE = false;
    const NAME = 'Accelerated Mobile Pages (AMP)';
    const WIKI = 'http://wiki.plumrocket.com/Accelerated_Mobile_Pages_Magento_2_Extension_v2.x';
    const CONFIG_SECTION = 'pramp';
    const MODULE_NAME = 'Amp';
}
