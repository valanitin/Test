<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Account\Data;

use Magento\Framework\Math\Random;
use Plumrocket\SocialLoginFree\Helper\Config;

/**
 * Generate password to speed up account creation
 */
class PasswordGenerator
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    private $random;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * PasswordGenerator constructor.
     *
     * @param \Magento\Framework\Math\Random            $random
     * @param \Plumrocket\SocialLoginFree\Helper\Config $config
     */
    public function __construct(
        Random $random,
        Config $config
    ) {
        $this->random = $random;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function generatePassword(): string
    {
        $result = '';

        $passwordLength = $this->getPasswordLength();

        $characters = 1;
        $gradeLetter = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; //doesn't contain "I" and "O"
        $dataSet = [$gradeLetter, strtolower($gradeLetter), Random::CHARS_DIGITS, '@)<(=_)#!>'];

        $index = ceil($passwordLength / count($dataSet));

        while ($index--) {
            foreach ($dataSet as $data) {
                $result .= $this->random->getRandomString($characters, $data);
                if (strlen($result) >= $passwordLength) {
                    break 2;
                }
            }
        }

        return substr($result, 0, $passwordLength);
    }

    /**
     * @return int
     */
    public function getPasswordLength(): int
    {
        return (int) $this->config->getConfig('customer/password/minimum_password_length');
    }
}
