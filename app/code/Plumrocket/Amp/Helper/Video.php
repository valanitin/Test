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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Helper;

class Video extends \Magento\Framework\App\Helper\AbstractHelper
{
    const VIDEO_TYPE_YOUTUBE = 'youtube';
    const VIDEO_TYPE_VIMEO = 'vimeo';
    const VIDEO_TYPE_VIDEO = 'video';

    /**
     * List of patterns for detecting video ID
     * @var array
     */
    protected $_patternCollection = [
        self::VIDEO_TYPE_YOUTUBE => [
            '/youtube\.com\/watch\?v=([^\&\?\/]+)/',
            '/youtube\.com\/embed\/([^\&\?\/]+)/',
            '/youtube\.com\/v\/([^\&\?\/]+)/',
            '/youtu\.be\/([^\&\?\/]+)/',
        ],
        self::VIDEO_TYPE_VIMEO => [
            '/(?:https?:\/\/)?(?:www.)?(?:player.)?vimeo.com\/(?:[a-z]*\/)*([0-9]*)[?]?.*/',
        ],
        self::VIDEO_TYPE_VIDEO => [
            '/(?:https?:\/\/)?(?:www.)?(?:player.)?vimeo.com\/(?:[a-z]*\/)*([0-9]*)[?]?.*/',
        ],
    ];

    /**
     * Retrieve video data array by URL
     *
     * @param string $url
     * @param bool   $allowAdditionalTypes
     * @return array|bool
     */
    public function getVideoData($url, $allowAdditionalTypes = false)
    {
        if (!empty($url)) {
            foreach ($this->_patternCollection as $provider => $patterns) {
                foreach ($patterns as $pattern) {
                    $result = preg_match($pattern, $url, $matches);

                    if ($result && !empty($matches[1])) {
                        return [
                            'type' => $provider,
                            'id' => $matches[1],
                            'autoplay' => $provider === self::VIDEO_TYPE_YOUTUBE,
                        ];
                    }
                }
            }

            if ($allowAdditionalTypes) {
                return [
                    'type' => self::VIDEO_TYPE_VIDEO,
                    'src' => $url,
                    'autoplay' => false,
                ];
            }
        }

        return false;
    }
}
