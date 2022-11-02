<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Plumrocket\Base\Helper\AbstractConfig;
use Plumrocket\SocialLoginFree\Model\Facebook;
use Plumrocket\SocialLoginFree\Model\Information;

class Config extends AbstractConfig
{
    const SECTION_ID = Information::CONFIG_SECTION;

    const GROUP_DEVELOPER = 'developer';

    const XML_PATH_BUTTON_SORT = 'psloginfree/general/sortable';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Helper\Context            $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor
    ) {
        parent::__construct($context);
        $this->encryptor = $encryptor;
    }

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function isModuleEnabled($store = null, $scope = null): bool
    {
        return (bool) $this->getConfigByGroup(self::GROUP_GENERAL, 'enable');
    }

    /**
     * @return bool
     */
    public function isEnabledSubscription(): bool
    {
        return (bool) $this->getConfigByGroup(self::GROUP_GENERAL, 'enable_subscription');
    }

    /**
     * @param null $store
     * @param null $scope
     * @return array
     */
    public function getSortableParams($store = null, $scope = null): array
    {
        $value = $this->getConfig(self::XML_PATH_BUTTON_SORT, $store, $scope);
        if ($value) {
            parse_str($value, $sortParams);
            return $sortParams;
        }

        return [];
    }

    /**
     * @return bool
     */
    public function isIgnoreValidation(): bool
    {
        return (bool) $this->getConfigByGroup(self::GROUP_GENERAL, 'validate_ignore');
    }

    /**
     * @return bool
     */
    public function createFakeData(): bool
    {
        return (bool) $this->getConfigByGroup(self::GROUP_GENERAL, 'validate_ignore');
    }

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return bool
     */
    public function isPhotoEnabled($scopeCode = null, $scope = null): bool
    {
        return (bool) $this->getConfigByGroup(self::GROUP_GENERAL, 'enable_photo', $scopeCode, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return array
     */
    public function getEnabledPositions($store = null, $scope = null): array
    {
        return $this->prepareMultiselectValue(
            $this->getConfigByGroup(self::GROUP_GENERAL, 'enable_for', $store, $scope),
            true
        );
    }

    /**
     * @param string $position
     * @param null   $store
     * @param null   $scope
     * @return bool
     */
    public function isModulePositionEnabled(string $position, $store = null, $scope = null): bool
    {
        return $this->isModuleEnabled() && in_array($position, $this->getEnabledPositions($store, $scope), true);
    }

    /**
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return bool
     */
    public function isEnabledNetwork(string $type, $scopeCode = null, $scope = null): bool
    {
        return (bool) $this->getConfigByGroup($type, 'enable', $scopeCode, $scope);
    }

    /**
     * Retrieve Application ID
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkApplicationId(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) trim($this->getConfigByGroup($type, 'application_id', $scopeCode, $scope));
    }

    /**
     * Retrieve encoded secret key of application
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkApplicationSecretKey(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) trim(
            $this->encryptor->decrypt(
                $this->getConfigByGroup($type, 'secret', $scopeCode, $scope)
            )
        );
    }

    /**
     * Retrieve name of icon
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkSmallIconButton(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup($type, 'icon_btn', $scopeCode, $scope);
    }

    /**
     * Retrieve name of icon
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkLoginIconButton(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup($type, 'login_btn', $scopeCode, $scope);
    }

    /**
     * Retrieve name of icon
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkRegisterIconButton(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup($type, 'register_btn', $scopeCode, $scope);
    }

    /**
     * Retrieve text from button
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkLoginButtonText(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup($type, 'login_btn_text', $scopeCode, $scope);
    }

    /**
     * Retrieve text from button
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkRegisterButtonText(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup($type, 'register_btn_text', $scopeCode, $scope);
    }

    /**
     * @deprecated since 3.1.0 - now we are always getting birthday
     *
     * @param null $scopeCode
     * @param null $scope
     * @return string
     */
    public function isEnabledFacebookBirthday($scopeCode = null, $scope = null): string
    {
        return (bool) $this->getConfigByGroup(Facebook::PROVIDER, 'enable_birthday', $scopeCode, $scope);
    }

    /**
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return (bool) $this->getConfigByGroup(self::GROUP_DEVELOPER, 'enable');
    }

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return string
     */
    public function getRedirectForLogin($scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup(self::GROUP_GENERAL, 'redirect_for_login', $scopeCode, $scope);
    }

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return string
     */
    public function getRedirectForLoginLink($scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup(self::GROUP_GENERAL, 'redirect_for_login_link', $scopeCode, $scope);
    }

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return string
     */
    public function getRedirectForRegister($scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup(self::GROUP_GENERAL, 'redirect_for_register', $scopeCode, $scope);
    }

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return string
     */
    public function getRedirectForRegisterLink($scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup(self::GROUP_GENERAL, 'redirect_for_register_link', $scopeCode, $scope);
    }
}
