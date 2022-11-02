<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Test\Unit\Model\Account\Data;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail;
use \Plumrocket\SocialLoginFree\Model\Account\Data\Prepare;

class PrepareTest extends TestCase
{
    /**
     * @var \Plumrocket\Base\Model\Utils\IsMatchUrl
     */
    private $isMatchUrl;

    protected function setUp(): void
    {
        $config = $this->createMock(\Plumrocket\SocialLoginFree\Helper\Config::class);
        $eveConfig = $this->createMock(\Magento\Eav\Model\Config::class);

        $objectManager = new ObjectManager($this);

        $fakeEmail = $objectManager->getObject(FakeEmail::class);

        $this->isMatchUrl = $objectManager->getObject(
            Prepare::class, [
                'config' => $config,
                'fakeEmail' => $fakeEmail,
                'eveConfig' => $eveConfig,
            ]
        );
    }

    /**
     * @dataProvider fakeEmailDataProvider
     *
     * @param array $dataIn
     * @param array $dataOut
     */
    public function testFakeEmail(array $dataIn, array $dataOut) {
        self::assertSame($dataOut, $this->isMatchUrl->names($dataIn));
    }

    /**
     * @dataProvider separatedEmailDataProvider
     *
     * @param array $dataIn
     * @param array $dataOut
     */
    public function testSeparatedEmail(array $dataIn, array $dataOut) {
        self::assertSame($dataOut, $this->isMatchUrl->names($dataIn));
    }

    /**
     * @dataProvider oneWordEmailDataProvider
     *
     * @param array $dataIn
     * @param array $dataOut
     */
    public function testOneWordEmail(array $dataIn, array $dataOut) {
        self::assertSame($dataOut, $this->isMatchUrl->names($dataIn));
    }

    /**
     * @dataProvider multyseparatorEmailDataProvider
     *
     * @param array $dataIn
     * @param array $dataOut
     */
    public function testMultyseparatorEmail(array $dataIn, array $dataOut) {
        self::assertSame($dataOut, $this->isMatchUrl->names($dataIn));
    }

    /**
     * @return \Generator
     */
    public function fakeEmailDataProvider()
    {
        yield [
            'dataIn' => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
            'dataOut' => [
                'firstname' => 'Customer',
                'lastname'  => 'Unknown',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => '',
                'lastname'  => 'Parker',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
            'dataOut' => [
                'firstname' => 'Customer',
                'lastname'  => 'Parker',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => 'John',
                'lastname'  => '',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Unknown',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => 'John',
                'lastname'  => 'Parker',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Parker',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
        ];
    }

    /**
     * @return \Generator
     */
    public function separatedEmailDataProvider()
    {
        yield [
            'dataIn' => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'jim.parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim',
                'lastname'  => 'Parker',
                'email'     => 'jim.parker@xyz.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => '',
                'lastname'  => 'Parker-Blake',
                'email'     => 'jim.parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim',
                'lastname'  => 'Parker-Blake',
                'email'     => 'jim.parker@xyz.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => 'John',
                'lastname'  => '',
                'email'     => 'jim-parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Parker',
                'email'     => 'jim-parker@xyz.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => 'John',
                'lastname'  => 'Parker-Blake',
                'email'     => 'jim-parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Parker-Blake',
                'email'     => 'jim-parker@xyz.com'
            ],
        ];
    }

    /**
     * @return \Generator
     */
    public function oneWordEmailDataProvider()
    {
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Customer',
                'lastname'  => 'Parker',
                'email'     => 'parker@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => 'Parker-Blake',
                'email'     => 'parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Customer',
                'lastname'  => 'Parker-Blake',
                'email'     => 'parker@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => 'John',
                'lastname'  => 'Parker-Blake',
                'email'     => 'parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Parker-Blake',
                'email'     => 'parker@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => 'John',
                'lastname'  => '',
                'email'     => 'qwerty@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Qwerty',
                'email'     => 'qwerty@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => 'John',
                'lastname'  => '',
                'email'     => 'qwerty123@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Qwerty123',
                'email'     => 'qwerty123@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => 'Parker-Blake',
                'email'     => 'qwerty123@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Customer',
                'lastname'  => 'Parker-Blake',
                'email'     => 'qwerty123@xyz.com'
            ],
        ];
    }

    /**
     * @return \Generator
     */
    public function multyseparatorEmailDataProvider()
    {
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'jim-test@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim',
                'lastname'  => 'Test',
                'email'     => 'jim-test@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'jim.parker-man@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim',
                'lastname'  => 'Parker-man',
                'email'     => 'jim.parker-man@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'jim-test.parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim-test',
                'lastname'  => 'Parker',
                'email'     => 'jim-test.parker@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'jim-test.parker.office@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim-test',
                'lastname'  => 'Parker',
                'email'     => 'jim-test.parker.office@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => 'Jim-test',
                'lastname'  => '',
                'email'     => 'jim-test.parker.office@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim-test',
                'lastname'  => 'Parker',
                'email'     => 'jim-test.parker.office@xyz.com'
            ],
        ];
    }
}
