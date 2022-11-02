<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Test\Unit\Model\Redirect;

use Amasty\SeoToolKit\Model\Redirect;
use Amasty\SeoToolKit\Test\Unit\Traits;
use Amasty\SeoToolKit\Model\Redirect\RedirectGetter;
use Amasty\SeoToolKit\Model\ResourceModel\Redirect\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

/**
 * Class RedirectGetterTest
 *
 * @see RedirectGetter
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class RedirectGetterTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers RedirectGetter::getRedirect
     */
    public function testGetRedirect()
    {
        $collection = $this->createPartialMock(
            CollectionFactory::class,
            ['create', 'addFieldToFilter', 'addStoreFilter', 'setOrders']
        );
        $storeManager = $this->getMockBuilder(StoreManager::class)
            ->setMethods(['getStore', 'getId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $model = $this->getObjectManager()->getObject(
            RedirectGetter::class,
            [
                'collectionFactory' => $collection,
                'storeManager' => $storeManager,
            ]
        );
        $redirect1 = $this->getObjectManager()->getObject(Redirect::class);
        $redirect2 = $this->getObjectManager()->getObject(Redirect::class);

        $redirect1->setRequestPath('/aaa/bbb/ccc/');
        $redirect2->setRequestPath('/bbb/*');

        $collection->expects($this->any())->method('create')->willReturn($collection);
        $collection->expects($this->any())->method('addFieldToFilter')->willReturn($collection);
        $collection->expects($this->any())->method('addStoreFilter')->willReturn($collection);
        $collection->expects($this->any())->method('setOrders')->willReturn([$redirect1, $redirect2]);
        $storeManager->expects($this->any())->method('getStore')->willReturn($storeManager);
        $storeManager->expects($this->any())->method('getId')->willReturn(1);

        $this->assertNull($model->getRedirect('/aaa/ddd/ccc'));
        $this->assertEquals($redirect1, $model->getRedirect('/aaa/bbb/ccc'));
        $this->assertEquals($redirect2, $model->getRedirect('/bbb/sss/'));
    }
}
