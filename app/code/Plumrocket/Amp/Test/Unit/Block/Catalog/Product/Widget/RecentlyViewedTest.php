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
use Plumrocket\Amp\Block\Catalog\Product\Widget\RecentlyViewed;

class RecentlyViewedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Plumrocket\Amp\Block\Catalog\Product\Widget\RecentlyViewed
     */
    private $block;

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $pricingHelper;

    protected function setUp()
    {
        $context = $this->createMock(\Magento\Catalog\Block\Product\Context::class);
        $productVisibility = $this->createMock(\Magento\Catalog\Model\Product\Visibility::class);
        $indexFactory = $this->createMock(\Magento\Reports\Model\Product\Index\Factory::class);

        $this->dataHelper = $this->createMock(\Plumrocket\Amp\Helper\Data::class);
        $this->pricingHelper = $this->createMock(\Magento\Framework\Pricing\Helper\Data::class);

        $this->block = (new ObjectManager($this))
            ->getObject(RecentlyViewed::class, [
                'context' => $context,
                'productVisibility' => $productVisibility,
                'indexFactory' => $indexFactory,
                'dataHelper' => $this->dataHelper,
                'pricingHelper' => $this->pricingHelper
            ]);
    }

    /**
     * @dataProvider providerMaxItems
     *
     * @param $passed
     * @param $expected
     */
    public function testGetMaxItems($passed, $expected)
    {
        $this->block->setProductsCount($passed);
        $this->assertEquals($expected, $this->block->getMaxItems());
    }

    /**
     * @return array
     */
    public function providerMaxItems()
    {
        return [
            [
                'passed' => 5,
                'expected' => 5,
            ],
            [
                'passed' => 3,
                'expected' => 3,
            ],
            [
                'passed' => 0,
                'expected' => RecentlyViewed::DEFAULT_PRODUCTS_COUNT,
            ],
            [
                'passed' => null,
                'expected' => RecentlyViewed::DEFAULT_PRODUCTS_COUNT,
            ],
        ];
    }
}
