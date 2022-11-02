<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Account\Data;

use Magento\Eav\Model\Config as EavConfig;
use Plumrocket\SocialLoginFree\Helper\Config;

class Prepare
{
    const GENDER_NOT_SPECIFIED = 3;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail
     */
    private $fakeEmail;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * Prepare constructor.
     *
     * @param \Plumrocket\SocialLoginFree\Helper\Config                $config
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail $fakeEmail
     * @param \Magento\Eav\Model\Config                                $eavConfig
     */
    public function __construct(
        Config $config,
        FakeEmail $fakeEmail,
        EavConfig $eavConfig
    ) {
        $this->config = $config;
        $this->fakeEmail = $fakeEmail;
        $this->eavConfig = $eavConfig;
    }

    public function email(array $data): array
    {
        if (empty($data['email']) && $this->config->createFakeData()) {
            $data['email'] = $this->fakeEmail->generate();
        }

        return $data;
    }

    /**
     * Try parse names form email (only if it is real) if network didn't send them
     *
     * @param array $data
     * @return array
     */
    public function names(array $data): array
    {
        if (! empty($data['firstname']) && ! empty($data['lastname'])) {
            return $data;
        }

        if (! $this->fakeEmail->detect($data['email'])) {
            $login = $this->getLogin($data['email']);
            $loginParts = $this->splitLogin($login);

            if ((count($loginParts) === 1) && empty($data['lastname'])) {
                $data['lastname'] = ucfirst($loginParts[0]);
            }

            if (count($loginParts) === 2) {
                $data = $this->setMissedNames($data, $loginParts);
            }

            if (count($loginParts) >=3) {
                $data = $this->setMissedNamesFromLongLogin($data, $login);
            }
        }

        return $this->generateMissedNames($data);
    }

    /**
     * @param string $login
     * @return array
     */
    private function splitLogin(string $login): array
    {
        return preg_split('#[.\-]+#u', $login, 3);
    }

    /**
     * @param string $email
     * @return string
     */
    private function getLogin(string $email): string
    {
       return trim(strstr($email, '@', true));
    }

    /**
     * @param $data
     * @param $nameFromEmail
     * @return array
     */
    private function setMissedNamesFromLongLogin ($data, $nameFromEmail): array
    {
        $name = preg_split('#[.]+#u', $nameFromEmail, 2);
        if (isset($name[1]) && strpos($name[1], '.') !== false) {
            $name[1] = strstr($name[1], '.', true);
        }
        if (count($name) === 1) {
            $name = preg_split('#[-]+#u', $nameFromEmail, 2);
        }

        if (empty($data['firstname'])) {
            $data['firstname'] = ucfirst($name[0]);
        }

        if (empty($data['lastname'])) {
            $data['lastname'] = ucfirst($name[1]);
        }

        return $data;
    }

    /**
     * @param $data
     * @param $loginParts
     * @return array
     */
    private function setMissedNames(array $data, array $loginParts): array
    {
        if (empty($data['firstname'])) {
            $data['firstname'] = ucfirst($loginParts[0]);
        }

        if (empty($data['lastname'])) {
            $data['lastname'] = ucfirst($loginParts[1]);
        }

        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    private function generateMissedNames (array $data): array
    {
        if (empty($data['firstname'])) {
            $data['firstname'] = 'Customer';
        }
        if (empty($data['lastname'])) {
            $data['lastname'] = 'Unknown';
        }

        return $data;
    }

    /**
     * Fix format of date and set default if needed
     *
     * @param array $data
     * @param array $dob
     * @return array
     */
    public function dateOfBirth(array $data, $dob = []): array
    {
        if (! empty($data['dob'])) {
            $data['dob'] = call_user_func_array([$this, 'prepareDob'], array_merge([$data['dob']], $dob));
        } else {
            $data['dob'] = $this->config->createFakeData() ? '1901-01-01' : null;
        }
        return $data;
    }

    /**
     * Convert network gender types/labels to magento ids
     *
     * @param array $data
     * @param array $networkGenderMapping
     * @return array
     */
    public function gender(array $data, array $networkGenderMapping): array
    {
        if (! empty($data['gender'])) {
            $genderAttribute = $this->eavConfig->getAttribute('customer', 'gender');
            if ($genderAttribute && $options = $genderAttribute->getSource()->getAllOptions(false)) {
                switch ($data['gender']) {
                    case $networkGenderMapping[0]:
                        $data['gender'] = $options[0]['value'];
                        break;
                    case $networkGenderMapping[1]:
                        $data['gender'] = $options[1]['value'];
                        break;
                    default:
                        $data['gender'] = $options[2]['value'];
                }
            } else {
                $data['gender'] = self::GENDER_NOT_SPECIFIED;
            }
        } else {
            $data['gender'] = self::GENDER_NOT_SPECIFIED;
        }
        return $data;
    }

    protected function prepareDob($date, $p1 = 'month', $p2 = 'day', $p3 = 'year', $separator = '/'): string
    {
        $date = explode($separator, $date);

        $result = [
            'year' => '0000',
            'month' => '00',
            'day' => '00'
        ];

        $result[$p1] = $date[0];
        if (isset($date[1])) {
            $result[$p2] = $date[1];
        }
        if (isset($date[2])) {
            $result[$p3] = $date[2];
        }

        return implode('-', array_values($result));
    }

    /**
     * @param array $data
     * @return array
     */
    public function taxVat(array $data): array
    {
        $data['taxvat'] = $this->config->createFakeData() ? '0' : null;
        return $data;
    }
}
