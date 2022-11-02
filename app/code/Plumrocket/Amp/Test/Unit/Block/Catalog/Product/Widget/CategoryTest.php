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

namespace Plumrocket\Amp\Test\Unit\Block\Catalog\Product\Widget;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Plumrocket\Amp\Block\Catalog\Product\Widget\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /**
     * @var null | Category
     */
    private $block = null;

    protected function setUp()
    {
        $context = $this->createMock(\Magento\Catalog\Block\Product\Context::class);
        $productCollectionFactory = $this->createMock(
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::class
        );
        $productVisibility = $this->createMock(\Magento\Catalog\Model\Product\Visibility::class);

        $this->block = (new ObjectManager($this))
            ->getObject(Category::class, [
                'context' => $context,
                'productCollectionFactory' => $productCollectionFactory,
                'productVisibility' => $productVisibility,
            ]);
    }

    public function testGetType()
    {
        $this->assertEquals(Category::WIDGET_TYPE, $this->block->getType());
    }

    /**
     * @dataProvider getDataForGetCategoryId()
     * @param $data
     * @param $expect
     * @param bool $exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testGetCategoryId($data, $expect, $exception)
    {
        $this->block->setData($data);
        if ($exception) {
            $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        }
        $this->assertSame($expect, $this->block->getCategoryId());
    }

    public function getDataForGetCategoryId()
    {
        return [
          [
              'data' => ['category' => 'category/5'],
              'expect' => 5,
              'exception' => false,
          ],
          [
              'data' => ['category' => 'product/0'],
              'expect' => null,
              'exception' => true,
          ],
          [
              'data' => ['category' => 'product'],
              'expect' => null,
              'exception' => true,
          ],
          [
              'data' => ['category' => 'category/58'],
              'expect' => 58,
              'exception' => false,
          ],
          [
              'data' => ['category' => '5'],
              'expect' => 5,
              'exception' => false,
          ],
        ];
    }
}
