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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model;

interface AllowedAmpUrlCacheInterface
{
    /**
     * Add new record for potential save
     *
     * @param string $requestPath
     * @return self
     */
    public function add(string $requestPath);

    /**
     * Remove cached data by tag 'pramp_url'
     *
     * @return self
     */
    public function clean();

    /**
     * Save data
     *
     * @return self
     */
    public function save();

    /**
     * Load data
     *
     * @return array
     */
    public function getList() : array;
}
