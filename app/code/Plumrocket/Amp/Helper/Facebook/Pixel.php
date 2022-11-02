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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Helper\Facebook;

use Plumrocket\Amp\Helper\Data;

class Pixel extends \Plumrocket\Amp\Helper\Main
{
    /**
     * @param int|null $store
     * @return boolean
     */
    public function isFacebookPixelEnable($store = null) : bool
    {
        return (bool)$this->getConfig(Data::SECTION_ID . '/facebook_pixel/enabled', $store);
    }

    /**
     * @param int|null $store
     * @return string
     */
    public function getPixelId($store = null) : string
    {
        return (string)$this->getConfig(Data::SECTION_ID . '/facebook_pixel/pixel_id', $store);
    }
}
