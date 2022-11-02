<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Test\Unit\Helper;

use Plumrocket\Amp\Helper\Video;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class VideoTest extends TestCase
{
    /**
     * @var null|Video
     */
    private $videoHelper = null;

    /**
     * Create Video Helper for tests
     */
    protected function setUp()
    {
        $this->videoHelper = (new ObjectManager($this))->getObject(Video::class);
    }

    /**
     * Test parse data from video link
     *
     * @dataProvider videoUrlDataProvider
     *
     * @param string $url
     * @param bool   $allow
     * @param $result
     */
    public function testGetVideoData($url, $allow, $result)
    {
        $this->assertEquals($result, $this->videoHelper->getVideoData($url, $allow));
    }

    /**
     * @return array
     */
    public function videoUrlDataProvider()
    {
        return [
            [
                'url' => 'https://www.youtube.com/watch?v=vr0qNXmkUJ8',
                'allow' => false,
                'result' => [
                    'type'=> Video::VIDEO_TYPE_YOUTUBE,
                    'id' => "vr0qNXmkUJ8",
                    'autoplay'=> true
                ],
            ],
            [
                'url' => 'https://youtu.be/vr0qNXmkUJ8',
                'allow' => false,
                'result' => [
                    'type'=> Video::VIDEO_TYPE_YOUTUBE,
                    'id' => "vr0qNXmkUJ8",
                    'autoplay'=> true
                ],
            ],
            [
                'url' => 'https://vimeo.com/832895748',
                'allow' => true,
                'result' => [
                    'type'=> Video::VIDEO_TYPE_VIMEO,
                    'id' => "832895748",
                    'autoplay'=> false
                ],
            ],
            [
                'url' => 'http://magento2/pub/media/video.mp4',
                'allow' => true,
                'result' => [
                    'type'=> Video::VIDEO_TYPE_VIDEO,
                    'src' => "http://magento2/pub/media/video.mp4",
                    'autoplay'=> false
                ],
            ],
            [
                'url' => 'http://magento2/pub/media/video.mp4',
                'allow' => false,
                'result' => false,
            ],
        ];
    }
}
