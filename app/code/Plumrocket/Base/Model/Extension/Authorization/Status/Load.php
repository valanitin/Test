<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization\Status;

use Exception;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\LocalizedException;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Helper\Config;
use Plumrocket\Base\Model\Extension\Authorization\Key;
use Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey;
use Plumrocket\Base\Model\External\Connector;
use Plumrocket\Base\Model\External\Urls;
use Plumrocket\Base\Model\Utils\GetEnabledStoresUrls;
use Plumrocket\Base\Model\Extension\GetInstallationType;

/**
 * Get status from store
 *
 * @since 2.5.0
 */
class Load
{
    /**
     * @var \Plumrocket\Base\Model\Utils\GetEnabledStoresUrls
     */
    private $getEnabledStoresUrls;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @var \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey
     */
    private $getTrueCustomerKey;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Plumrocket\Base\Model\External\Connector
     */
    private $externalConnector;

    /**
     * @var \Plumrocket\Base\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Key
     */
    private $authorizationKey;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Status\Calculate
     */
    private $calculateStatus;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetInstallationType
     */
    private $getInstallType;

    /**
     * @param \Plumrocket\Base\Model\Utils\GetEnabledStoresUrls               $getEnabledStoresUrls
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface           $getExtensionInformation
     * @param \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey    $getTrueCustomerKey
     * @param \Magento\Framework\App\ProductMetadataInterface                 $productMetadata
     * @param \Plumrocket\Base\Model\External\Connector                       $externalConnector
     * @param \Plumrocket\Base\Helper\Config                                  $config
     * @param \Plumrocket\Base\Model\Extension\Authorization\Key              $authorizationKey
     * @param \Plumrocket\Base\Model\Extension\Authorization\Status\Calculate $calculateStatus
     */
    public function __construct(
        GetEnabledStoresUrls $getEnabledStoresUrls,
        GetExtensionInformationInterface $getExtensionInformation,
        GetTrueCustomerKey $getTrueCustomerKey,
        ProductMetadataInterface $productMetadata,
        Connector $externalConnector,
        Config $config,
        Key $authorizationKey,
        Calculate $calculateStatus,
        GetInstallationType $getInstallType
    ) {
        $this->getEnabledStoresUrls = $getEnabledStoresUrls;
        $this->getExtensionInformation = $getExtensionInformation;
        $this->getTrueCustomerKey = $getTrueCustomerKey;
        $this->productMetadata = $productMetadata;
        $this->externalConnector = $externalConnector;
        $this->config = $config;
        $this->authorizationKey = $authorizationKey;
        $this->calculateStatus = $calculateStatus;
        $this->getInstallType = $getInstallType;
    }

    /**
     * @param string $moduleName
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $moduleName): int
    {
        $extensionInformation = $this->getExtensionInformation->execute($moduleName);
        try {
            $params = [
                'edition'         => $this->productMetadata->getEdition(),
                'session'         => $this->authorizationKey->get($moduleName),
                'base_urls'       => $this->getEnabledStoresUrls->execute(),
                'name'            => $extensionInformation->getModuleName(),
                'name_version'    => $extensionInformation->getInstalledVersion(),
                'customer'        => $this->getTrueCustomerKey->execute($moduleName),
                'title'           => $extensionInformation->getTitle(),
                'platform'        => 'm2',
                'magento_version' => $this->productMetadata->getVersion(),
                'installation'    => $this->getInstallType->execute($moduleName),
            ];

            $xml = $this->externalConnector->connect('https://' . Urls::PINGBACK_URL . '/extension/', $params);
            if (empty($xml['status'])) {
                throw new LocalizedException(__('Status is missing.'), null, 1);
            }
            $status = (int) $xml['status'];
        } catch (Exception $e) {
            if ($this->config->isDebugMode()) {
                throw new LocalizedException(__($e->getMessage()), null, 1);
            }
            $status = $this->calculateStatus->execute($moduleName);
        }

        return $status;
    }
}
