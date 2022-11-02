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
use Amasty\SeoToolKit\Model\Redirect\TargetPathResolver;

/**
 * Class TargetPathResolverTest
 *
 * @see TargetPathResolver
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class TargetPathResolverTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers TargetPathResolver::getTargetPath
     * @dataProvider getTargetPathDataProvider
     */
    public function testGetTargetPath(string $requestPath, string $targetPath, string $path, string $result)
    {
        $model = $this->getObjectManager()->getObject(TargetPathResolver::class);
        $redirect = $this->getObjectManager()->getObject(Redirect::class);

        $redirect->setRequestPath($requestPath);
        $redirect->setTargetPath($targetPath);

        $this->assertEquals($result, $model->getTargetPath($redirect, $path));
    }

    /**
     * Data provider for getTargetPath test
     * @return array
     */
    public function getTargetPathDataProvider()
    {
        return [
            ['/aaa/', '/bbb/', '/aaa/', '/bbb/'],
            ['aaa/*', '/bbb/', '/aaa/', '/bbb/'],
            ['/aaa/*', '/ccc/*', '/aaa/vvv/', '/ccc/vvv/'],
            ['*/aaa/', '*/ddd/', '/bbb/aaa/', '/bbb/ddd/'],
            ['/aaa/*/bbb/', '/ccc/*/ddd/', '/aaa/nnn/bbb/', '/ccc/nnn/ddd/'],
        ];
    }
}
