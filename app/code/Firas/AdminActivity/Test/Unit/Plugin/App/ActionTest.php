<?php
/**
 * Firas
 *
 * Do not edit or add to this file if you wish to upgrade to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please contact us https://firas.co.uk/contacts.
 *
 * @category   Firas
 * @package    Firas_AdminActivity
 * @copyright  Copyright (C) 2018 Kiwi Commerce Ltd (https://firas.co.uk/)
 * @license    https://firas.co.uk/magento2-extension-license/
 */
namespace Firas\AdminActivity\Test\Unit\Plugin\App;

/**
 * Class ActionTest
 * @package Firas\AdminActivity\Test\Unit\Plugin\App
 */
class ActionTest extends \PHPUnit\Framework\TestCase
{

    public $processorMock;

    public $controllerMock;

    public $requestMock;

    /**
     * @requires PHP 7.0
     */
    public function setUp()
    {
        $this->processorMock = $this->getMockBuilder(\Firas\AdminActivity\Model\Processor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controllerMock = $this->getMockBuilder(\Magento\Framework\Interception\InterceptorInterface::class)
            ->setMethods(['getRequest','___callParent'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getActionName','getFullActionName','getModuleName'
            ])
            ->getMock();

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->actionTest = $objectManager->getObject(
            \Firas\AdminActivity\Plugin\App\Action::class,
            [
                'processor' => $this->processorMock
            ]
        );
    }

    /**
     * @requires PHP 7.0
     */
    public function testBeforeDispatch()
    {
        $this->controllerMock
            ->expects($this->exactly(3))
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->requestMock
            ->expects($this->once())
            ->method('getActionName')
            ->willReturn('action');

        $this->requestMock
            ->expects($this->once())
            ->method('getFullActionName')
            ->willReturn('fullaction');

        $this->requestMock
            ->expects($this->once())
            ->method('getModuleName')
            ->willReturn('module');

        $this->processorMock->expects($this->once())
            ->method('init')
            ->with('fullaction', 'action')
            ->willReturnSelf();

        $this->processorMock->expects($this->once())
            ->method('addPageVisitLog')
            ->with('module')
            ->willReturnSelf();

        $this->assertNull($this->actionTest->beforeDispatch($this->controllerMock));
    }
}
