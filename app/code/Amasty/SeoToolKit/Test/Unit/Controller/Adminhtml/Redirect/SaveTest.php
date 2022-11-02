<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Test\Unit\Controller\Adminhtml\Redirect;

use Amasty\SeoToolKit\Model\Redirect;
use Amasty\SeoToolKit\Test\Unit\Traits;
use Amasty\SeoToolKit\Controller\Adminhtml\Redirect\Save;

/**
 * Class SaveTest
 *
 * @see Save
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class SaveTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Save::isWrongAsteriskCount
     * @dataProvider isWrongAsteriskCountDataProvider
     */
    public function testIsWrongAsteriskCount(string $requestPath, string $targetPath, bool $result)
    {
        $controller = $this->getObjectManager()->getObject(Save::class);
        $redirect = $this->getObjectManager()->getObject(Redirect::class);

        $redirect->setRequestPath($requestPath);
        $redirect->setTargetPath($targetPath);

        $this->assertEquals($result, $this->invokeMethod($controller, 'isWrongAsteriskCount', [$redirect]));
    }

    /**
     * Data provider for isWrongAsteriskCount test
     * @return array
     */
    public function isWrongAsteriskCountDataProvider()
    {
        return [
            ['/aaa/', '/bbb/', false],
            ['/aaa/*/bbb/', '/ccc/*/ddd/', false],
            ['*/aaa/', '/bbb/', false],
            ['*/aaa/*', '*/ccc/*', true],
            ['/aaa/', '*/bbb/', true],
            ['/aaa/*/*/bbb', '/ccc/*/*/ddd', true],
        ];
    }
}
