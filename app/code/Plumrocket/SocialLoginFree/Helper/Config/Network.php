<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Helper\Config;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Config\DataInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Plumrocket\Base\Model\Utils\Config;

/**
 * @since 3.2.0
 */
class Network extends AbstractHelper
{

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * @var \Magento\Framework\Config\DataInterface
     */
    private $networkXmlConfig;

    /**
     * @var \Plumrocket\Base\Model\Utils\Config
     */
    private $configUtils;

    /**
     * @param \Magento\Framework\App\Helper\Context            $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Config\DataInterface          $networkXmlConfig
     * @param \Plumrocket\Base\Model\Utils\Config              $configUtils
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        DataInterface $networkXmlConfig,
        Config $configUtils
    ) {
        parent::__construct($context);
        $this->encryptor = $encryptor;
        $this->networkXmlConfig = $networkXmlConfig;
        $this->configUtils = $configUtils;
    }

    /**
     * Get network config.
     *
     * @param string $networkCode
     * @param string $field
     * @param null   $scopeCode
     * @param null   $scope
     * @return mixed
     */
    public function getNetworkConfig(string $networkCode, string $field, $scopeCode = null, $scope = null)
    {
        return $this->configUtils->getConfig("psloginfree/$networkCode/$field", $scopeCode, $scope);
    }

    /**
     * Check if integration is enabled.
     *
     * @param string $networkCode
     * @param null   $scopeCode
     * @param null   $scope
     * @return bool
     */
    public function isEnabled(string $networkCode, $scopeCode = null, $scope = null): bool
    {
        return (bool) $this->getNetworkConfig($networkCode, 'enable', $scopeCode, $scope);
    }

    /**
     * Get application id.
     *
     * @param string $networkCode
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getApplicationId(string $networkCode, $scopeCode = null, $scope = null): string
    {
        return trim(
            (string) $this->getNetworkConfig($networkCode, 'application_id', $scopeCode, $scope)
        );
    }

    /**
     * Retrieve encoded secret key of application
     *
     * @param string $networkCode
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getApplicationSecretKey(string $networkCode, $scopeCode = null, $scope = null): string
    {
        return trim(
            $this->encryptor->decrypt(
                $this->getNetworkConfig($networkCode, 'secret', $scopeCode, $scope)
            )
        );
    }

    /**
     * Get connector class name.
     *
     * @param string $networkCode
     * @return string
     */
    public function getConnectorClass(string $networkCode): string
    {
        return (string) $this->networkXmlConfig->get("$networkCode/connector/class");
    }

    /**
     * Get network protocol.
     *
     * @param string $networkCode
     * @return string
     */
    public function getProtocol(string $networkCode): string
    {
        return (string) $this->networkXmlConfig->get("$networkCode/protocol");
    }

    /**
     * Get modal width.
     *
     * @param string $networkCode
     * @return int
     */
    public function getModalWidth(string $networkCode): int
    {
        return (int) $this->networkXmlConfig->get("$networkCode/modal/width");
    }

    /**
     * Get modal height.
     *
     * @param string $networkCode
     * @return int
     */
    public function getModalHeight(string $networkCode): int
    {
        return (int) $this->networkXmlConfig->get("$networkCode/modal/height");
    }

    /**
     * Get modal Url resolver.
     *
     * @param string $networkCode
     * @return string
     */
    public function getModalUrlResolver(string $networkCode): string
    {
        return (string) $this->networkXmlConfig->get("$networkCode/modal/url/resolver");
    }

    /**
     * Get modal url path.
     *
     * @param string $networkCode
     * @return string
     */
    public function getModalUrlPath(string $networkCode): string
    {
        return (string) $this->networkXmlConfig->get("$networkCode/modal/url/path");
    }

    /**
     * Get modal params.
     *
     * @param string $networkCode
     * @return array
     */
    public function getModalUrlParams(string $networkCode): array
    {
        return $this->networkXmlConfig->get("$networkCode/modal/url/params") ?? [];
    }
}
