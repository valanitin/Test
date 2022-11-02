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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 *
 * Listener script for AMP IFrame requests
 */

if (!empty($_GET['referrer'])) {
    $url = base64_decode($_GET['referrer']);
    $currentHost = preg_replace('/^www./', '', $_SERVER['HTTP_HOST']);

    if (mb_strpos($url, $currentHost) !== false) {
        header('Location: ' . $url);
        exit();
    }
}

header('Location:/');
exit();