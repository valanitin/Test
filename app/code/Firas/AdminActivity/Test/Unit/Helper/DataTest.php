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
namespace Firas\AdminActivity\Test\Unit\Helper;

/**
 * Class DataTest
 * @package Firas\AdminActivity\Test\Unit\Helper
 */
class DataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @requires PHP 7.0
     */
    public function setUp()
    {
        $this->context = $this->getMockBuilder(\Magento\Framework\App\Helper\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeConfig = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
            ->getMockForAbstractClass();

        $this->scopeConfig->expects($this->any())
            ->method('isSetFlag')
            ->willReturn(true);

        $this->context->expects($this->any())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfig);

        $this->config = $this->getMockBuilder(\Firas\AdminActivity\Model\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper = new \Firas\AdminActivity\Helper\Data(
            $this->context,
            $this->config
        );
    }

    /**
     * @requires PHP 7.0
     */
    public function testIsEnable()
    {
        $this->assertSame(true, $this->helper->isEnable());
    }

    /**
     * @requires PHP 7.0
     */
    public function testIsLoginEnable()
    {
        $this->assertSame(true, $this->helper->isLoginEnable());
    }

    /**
     * @requires PHP 7.0
     */
    public function testIsWildCardModel()
    {
        $notwildcardmethod = \Firas\AdminActivity\Helper\Data::isWildCardModel(\Magento\Framework\App\Helper\Context::class);
        $this->assertSame(false, $notwildcardmethod);

        $notwildcardmethod = \Firas\AdminActivity\Helper\Data::isWildCardModel(\Magento\Framework\App\Config\Value\Interceptor::class);
        $this->assertSame(true, $notwildcardmethod);
    }
}