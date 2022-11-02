<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Test\Unit\Model\Utils;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Plumrocket\Base\Model\Utils\GetRelativePathFromUrl;

/**
 * @since 2.3.1
 */
class GetRelativePathFromUrlTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Plumrocket\Base\Model\Utils\GetRelativePathFromUrl
     */
    private $getRelativePathFromUrl;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeMock;

    protected function setUp(): void
    {
        $this->storeMock = $this->createMock(Store::class);

        $storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
                                 ->disableOriginalConstructor()
                                 ->getMockForAbstractClass();

        $storeManagerMock
            ->method('getStore')
            ->willReturn($this->storeMock);

        $objectManager = new ObjectManager($this);
        $this->getRelativePathFromUrl = $objectManager->getObject(
            GetRelativePathFromUrl::class,
            [
                'storeManager' => $storeManagerMock,
            ]
        );
    }

    public function testEmptyUrl(): void
    {
        $this->storeMock
            ->method('getBaseUrl')
            ->willReturn('https://example.shop.com');

        $this->assertSame('/', $this->getRelativePathFromUrl->execute(''));
    }

    public function testBaseUrl(): void
    {
        $this->storeMock
            ->method('getBaseUrl')
            ->willReturn('https://example.shop.com');

        $this->assertSame('/', $this->getRelativePathFromUrl->execute('https://example.shop.com'));
    }

    /**
     * @dataProvider urlsProvider
     *
     * @param string $result
     * @param string $baseUrl
     * @param string $url
     * @param bool   $removeGetParams
     * @param bool   $removeFragment
     */
    public function testNotHaveConsent(
        string $result,
        string $baseUrl,
        string $url,
        bool $removeGetParams,
        bool $removeFragment
    ): void {
        $this->storeMock
            ->method('getBaseUrl')
            ->willReturn($baseUrl);

        $this->assertSame($result, $this->getRelativePathFromUrl->execute($url, $removeGetParams, $removeFragment));
    }

    /**
     * @return \Generator
     */
    public function urlsProvider(): \Generator
    {
        yield [
            'result'          => '/test/',
            'baseUrl'         => 'https://example.shop.com',
            'url'             => 'https://example.shop.com/test/',
            'removeGetParams' => true,
            'removeFragment'  => true,
        ];
        yield [
            'result'          => '/test/',
            'baseUrl'         => 'https://example.shop.com',
            'url'             => '/test/',
            'removeGetParams' => true,
            'removeFragment'  => true,
        ];
        yield [
            'result'          => '/test/',
            'baseUrl'         => 'https://example.shop.com',
            'url'             => '/test',
            'removeGetParams' => true,
            'removeFragment'  => true,
        ];
        yield [
            'result'          => '/test/',
            'baseUrl'         => 'https://example.shop.com',
            'url'             => '/test?a=1',
            'removeGetParams' => true,
            'removeFragment'  => true,
        ];
        yield [
            'result'          => '/test/',
            'baseUrl'         => 'https://example.shop.com',
            'url'             => '/test?a',
            'removeGetParams' => true,
            'removeFragment'  => true,
        ];
        yield [
            'result'          => '/test/',
            'baseUrl'         => 'https://example.shop.com',
            'url'             => '/test#anchor',
            'removeGetParams' => true,
            'removeFragment'  => true,
        ];
        yield [
            'result'          => '/test/',
            'baseUrl'         => 'https://example.shop.com',
            'url'             => '/test/?a=1#anchor',
            'removeGetParams' => true,
            'removeFragment'  => true,
        ];
        yield [
            'result'          => '/test/?a=1',
            'baseUrl'         => 'https://example.shop.com',
            'url'             => '/test/?a=1#anchor',
            'removeGetParams' => false,
            'removeFragment'  => true,
        ];
        yield [
            'result'          => '/test/#anchor',
            'baseUrl'         => 'https://example.shop.com',
            'url'             => '/test/?a=1#anchor',
            'removeGetParams' => true,
            'removeFragment'  => false,
        ];
        yield [
            'result'          => '/test/?a=1#anchor',
            'baseUrl'         => 'https://example.shop.com',
            'url'             => '/test/?a=1#anchor',
            'removeGetParams' => false,
            'removeFragment'  => false,
        ];
    }
}
