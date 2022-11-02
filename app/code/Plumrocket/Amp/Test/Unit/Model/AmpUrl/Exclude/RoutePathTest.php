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

namespace Plumrocket\Amp\Test\Unit\Model\AmpUrl\Exclude;

use Plumrocket\Amp\Model\AmpUrl\Exclude\RoutePath;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class RoutePathTest extends TestCase
{
    /**
     * @dataProvider getPathData()
     */
    public function testIsExcludedRoutePath($testCase, $path, $excludedPaths, $excludedRegex, $expect)
    {
        /** @var RoutePath $ampExcludedUrlModel */
        $ampExcludedUrlModel = (new ObjectManager($this))->getObject(
            RoutePath::class,
            [
                'excludedPaths' => $excludedPaths,
                'excludedRegex' => $excludedRegex
            ]
        );

        $this->assertSame($expect, $ampExcludedUrlModel->isExcluded($path), 'Test case: ' . $testCase);
    }

    public function getPathData() : \Generator
    {
        yield [
            'testCase' => '1',
            'path' => 'catalog/product/view',
            'excludedPaths' => [],
            'excludedRegex' => [],
            'expect' => false
        ];
        yield [
            'testCase' => '2',
            'path' => 'pramp/api_product/view',
            'excludedPaths' => [],
            'excludedRegex' => [],
            'expect' => true
        ];
        yield [
            'testCase' => '3',
            'path' => 'pramp/api',
            'excludedPaths' => [],
            'excludedRegex' => [],
            'expect' => false
        ];
        yield [
            'testCase' => '4',
            'path' => 'custom/pramp/api',
            'excludedPaths' => [],
            'excludedRegex' => [],
            'expect' => false
        ];
        yield [
            'testCase' => '5',
            'path' => 'custom/amp/action',
            'excludedPaths' => ['custom/amp/'],
            'excludedRegex' => [],
            'expect' => false
        ];
        yield [
            'testCase' => '6',
            'path' => 'custom/amp/action',
            'excludedPaths' => ['custom/amp/action'],
            'excludedRegex' => [],
            'expect' => true
        ];
        yield [
            'testCase' => '7',
            'path' => 'custom/amp/action',
            'excludedPaths' => [],
            'excludedRegex' => ['#^custom/amp/.*#'],
            'expect' => true
        ];
    }
}
