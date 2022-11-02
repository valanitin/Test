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

namespace Plumrocket\Amp\Test\Unit\Model\Plugin\Framework\Controller;

use PHPUnit\Framework\TestCase;

// @codingStandardsIgnoreFile

class ResultInterfacePluginTest extends TestCase
{
    /**
     * @param $html
     * @return mixed
     */
    private function callPrivateMethodReplaceHtml($html)
    {
        $resultPlugin = $this->getMockBuilder(
            \Plumrocket\Amp\Model\Plugin\Framework\Controller\ResultInterfacePlugin::class
        )->disableOriginalConstructor()->getMock();

        $testMethod = new \ReflectionMethod(
            \Plumrocket\Amp\Model\Plugin\Framework\Controller\ResultInterfacePlugin::class,
            '_replaceHtml'
        );
        $testMethod->setAccessible(true);

        return $testMethod->invoke($resultPlugin, $html);
    }

    /**
     * Test parsing iframe
     *
     * @throws \ReflectionException
     * @dataProvider replaceIframeHtmlDataProvider
     */
    public function testReplaceIframeHtml($html, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->callPrivateMethodReplaceHtml($html));
    }

    /**
     * Test parsing img
     *
     * @throws \ReflectionException
     * @dataProvider replaceImageHtmlDataProvider
     */
    public function testReplaceImageHtml($html, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->callPrivateMethodReplaceHtml($html));
    }

    /**
     * Test parsing img
     *
     * @throws \ReflectionException
     * @dataProvider replaceYouTubeHtmlDataProvider
     */
    public function testReplaceYouTubeHtml($html, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->callPrivateMethodReplaceHtml($html));
    }

    /**
     * @return array
     */
    public function replaceIframeHtmlDataProvider()
    {
        $additionalAttributes = 'layout="responsive" sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"';
        $placeholder = '<div class="amp-iframe-placeholder" placeholder><span>Loading</span></div>';

        return [
            [
                'html' => '<iframe src="a"
                    width="b"
                    height="c"
                    ></iframe>',
                'expectedResult' => '<amp-iframe src="a" width="b" height="c" ' . $additionalAttributes . '>' . $placeholder . '</amp-iframe>',
            ],
            [
                'html' => '<iframe src="a" width="b" height="c"> </iframe>',
                'expectedResult' => '<amp-iframe src="a" width="b" height="c" ' . $additionalAttributes . '>' . $placeholder . '</amp-iframe>',
            ],
            [
                'html' => '<iframe src="a" width="b" height="c">
                </iframe>',
                'expectedResult' => '<amp-iframe src="a" width="b" height="c" ' . $additionalAttributes . '>' . $placeholder . '</amp-iframe>',
            ],
            [
                'html' => '<iframe  src="a"  width="b"  height="c" > </iframe>',
                'expectedResult' => '<amp-iframe src="a" width="b" height="c" ' . $additionalAttributes . '>' . $placeholder . '</amp-iframe>',
            ],
            [
                'html' => '<iframe  src="a"  width="b"  height="c" > content </iframe>',
                'expectedResult' => '<amp-iframe src="a" width="b" height="c" ' . $additionalAttributes . '>' . $placeholder . '</amp-iframe>',
            ],
            [
                'html' => '<iframe  src="a"  width="b"  height="c" ></iframe>',
                'expectedResult' => '<amp-iframe src="a" width="b" height="c" ' . $additionalAttributes . '>' . $placeholder . '</amp-iframe>',
            ],
            [
                'html' => '<iframe  width="b"  height="c" src="a"  > content </iframe>',
                'expectedResult' => '<amp-iframe width="b" height="c" src="a" ' . $additionalAttributes . '>' . $placeholder . '</amp-iframe>',
            ],
            [
                'html' => '<iframe  width="b"  height="c" src="a"  > content </iframe>',
                'expectedResult' => '<amp-iframe width="b" height="c" src="a" ' . $additionalAttributes . '>' . $placeholder . '</amp-iframe>',
            ],
            [
                'html' => '<iframe  width="b"  height="c" src="a"  > content </iframe>',
                'expectedResult' => '<amp-iframe width="b" height="c" src="a" ' . $additionalAttributes . '>' . $placeholder . '</amp-iframe>',
            ],
        ];
    }

    /**
     * @return array
     */
    public function replaceImageHtmlDataProvider()
    {
        return [
            [
                'html' => '<img src="a" width="10" height="50"/>',
                'expectedResult' => '<amp-img src="a" width="10" height="50"></amp-img>',
            ],
            [
                'html' => <<<HTML
<img
src="a"
width="10"
height="50"
/>
HTML
                ,
                'expectedResult' => <<<HTML
<amp-img src="a"
width="10"
height="50"
></amp-img>
HTML
                ,
            ],
            [
                'html' => '<img src="a" width="10"/>',
                'expectedResult' => '<amp-img height="100" src="a" width="10"></amp-img>',
            ],
            [
                'html' => '<img src="a" height="10"/>',
                'expectedResult' => '<amp-img width="290" src="a" height="10"></amp-img>',
            ],
            [
                'html' => '<img src="a" />',
                'expectedResult' => '<amp-img width="290" height="100" src="a" ></amp-img>',
            ],
        ];
    }

    /**
     * @return array
     */
    public function replaceYouTubeHtmlDataProvider()
    {
        return [
            [
                'html' => '<iframe width="640" height="360" src="//www.youtube.com/embed/fPYX00DEisM" frameborder="0" allowfullscreen=""></iframe>',
                'expectedResult' => '<amp-youtube data-videoid="fPYX00DEisM" layout="responsive" width="480" height="270"></amp-youtube>',
            ],
            [
                'html' => '<iframe
width="640"
height="360"
src="//www.youtube.com/embed/fPYX00DEisM"
frameborder="0"
allowfullscreen=""
></iframe
>',
                'expectedResult' => '<amp-youtube data-videoid="fPYX00DEisM" layout="responsive" width="480" height="270"></amp-youtube>',
            ],
        ];
    }
}
