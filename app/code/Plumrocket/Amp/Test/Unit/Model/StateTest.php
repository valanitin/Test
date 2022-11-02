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

namespace Plumrocket\Amp\Test\Unit\Model;

use Plumrocket\Amp\Model\State;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Plumrocket\Amp\ViewModel\GlobalState;

class StateTest extends TestCase
{
    /**
     * @var State
     */
    private $state;

    protected function setUp() //@codingStandardsIgnoreLine
    {
        $this->state = (new ObjectManager($this))->getObject(State::class);
    }

    /**
     * @dataProvider getSetterStates()
     *
     * @param $testCase
     * @param $path
     * @param $value
     * @param $stateName
     * @param $expect
     */
    public function testCreateAmpJsSetter($testCase, $path, $value, $stateName, $expect)
    {
        $this->assertSame($expect, $this->state->createAmpJsSetter($path, $value, $stateName), $testCase);
    }

    /**
     * @return \Generator
     */
    public function getSetterStates() : \Generator
    {
        yield [
            'Test Case 1',
            ['form', 'product'],
            1,
            false,
            '{form: {product: 1}}'
        ];
        yield [
            'Test Case 2',
            ['form', 'product', 'id', 'value'],
            100,
            'test',
            '{test: {form: {product: {id: {value: 100}}}}}'
        ];
        yield [
            'Test Case 3',
            ['form', 'product', 'id', 'value'],
            '\'100\'',
            GlobalState::STATE_NAME,
            '{' . GlobalState::STATE_NAME . ': {form: {product: {id: {value: \'100\'}}}}}'
        ];
        yield [
            'Test Case 3',
            ['store', 'target'],
            'fr',
            GlobalState::STATE_NAME,
            '{' . GlobalState::STATE_NAME . ': {store: {target: \'fr\'}}}'
        ];
    }

    /**
     * @dataProvider getConditionStates()
     *
     * @param $testCase
     * @param $path
     * @param $value
     * @param $stateName
     * @param $expect
     */
    public function testCreateAmpJsCondition($testCase, $path, $value, $stateName, $expect)
    {
        $this->assertSame($expect, $this->state->createAmpJsCondition($path, $value, $stateName), $testCase);
    }

    /**
     * @return \Generator
     */
    public function getConditionStates() : \Generator
    {
        yield [
            'Test Case 1',
            ['form', 'product'],
            1,
            false,
            'form.product == 1'
        ];
        yield [
            'Test Case 2',
            ['form', 'product', 'id', 'value'],
            100,
            'test',
            'test.form.product.id.value == 100'
        ];
        yield [
            'Test Case 3',
            ['form', 'product', 'id', 'value'],
            '100',
            GlobalState::STATE_NAME,
            GlobalState::STATE_NAME . '.form.product.id.value == \'100\''
        ];
    }

    /**
     * @dataProvider getGetterStates()
     *
     * @param $testCase
     * @param $path
     * @param $stateName
     * @param $expect
     */
    public function testCreateAmpJsGetter($testCase, $path, $stateName, $expect)
    {
        $this->assertSame($expect, $this->state->createAmpJsGetter($path, $stateName), $testCase);
    }

    /**
     * @return \Generator
     */
    public function getGetterStates() : \Generator
    {
        yield [
            'Test Case 1',
            ['form', 'product'],
            false,
            'form.product'
        ];
        yield [
            'Test Case 2',
            ['form', 'product', 'id', 'value'],
            'test',
            'test.form.product.id.value'
        ];
        yield [
            'Test Case 3',
            ['form', 'product', 'id', 'value'],
            GlobalState::STATE_NAME,
            GlobalState::STATE_NAME . '.form.product.id.value'
        ];
    }
}
