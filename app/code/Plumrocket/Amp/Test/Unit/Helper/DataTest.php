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

use Plumrocket\Amp\Helper\Data;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var null|Data
     */
    private $dataHelper;

    /**
     * Create Video Helper for tests
     */
    protected function setUp()
    {
        $this->dataHelper = $this->getMockBuilder(Data::class)
             ->disableOriginalConstructor()
             ->setMethodsExcept(['removeRequestParam'])
             ->getMock();
    }

    /**
     * @dataProvider urlProvider()
     *
     * @param $url
     * @param $paramKey
     * @param $caseSensitive
     * @param $expect
     */
    public function testRemoveRequestParam($url, $paramKey, $caseSensitive, $expect)
    {
        $this->assertEquals($expect, $this->dataHelper->removeRequestParam($url, $paramKey, $caseSensitive));
    }

    /**
     * @return \Generator
     */
    public function urlProvider() : \Generator
    {
        yield [
            'url' => 'https://ex.com/watch?v=vr0qNXmkUJ8',
            'paramKey' => 'v',
            'caseSensitive' => false,
            'expect' => 'https://ex.com/watch',

        ];

        yield [
            'url' => 'https://ex.com/watch?v=vr0qNXmkUJ8&p=1',
            'paramKey' => 'p',
            'caseSensitive' => false,
            'expect' => 'https://ex.com/watch?v=vr0qNXmkUJ8',
        ];

        yield [
            'url' => 'https://ex.com/watch',
            'paramKey' => 'p',
            'caseSensitive' => false,
            'expect' => 'https://ex.com/watch',
        ];

        yield [
            'url' => 'https://ex.com/watch?pa=1&pa=2',
            'paramKey' => 'pa',
            'caseSensitive' => false,
            'expect' => 'https://ex.com/watch',
        ];

        yield [
            'url' => 'https://ex.com/watch?pa=1&pa=2',
            'paramKey' => 'PA',
            'caseSensitive' => false,
            'expect' => 'https://ex.com/watch',
        ];

        yield [
            'url' => 'https://ex.com/watch?pa=1&pat=2',
            'paramKey' => 'PA',
            'caseSensitive' => false,
            'expect' => 'https://ex.com/watch?pat=2',
        ];

        yield [
            'url' => 'https://ex.com/watch?p=1&amp=2',
            'paramKey' => 'p',
            'caseSensitive' => false,
            'expect' => 'https://ex.com/watch?amp=2',
        ];

        yield [
            'url' => 'https://ex.com/watch?p=1&amp=2#p=1',
            'paramKey' => 'p',
            'caseSensitive' => false,
            'expect' => 'https://ex.com/watch?amp=2#p=1',
        ];

        yield [
            'url' => 'https://ex.com/watch#p=1',
            'paramKey' => 'p',
            'caseSensitive' => false,
            'expect' => 'https://ex.com/watch#p=1',
        ];
    }
}
