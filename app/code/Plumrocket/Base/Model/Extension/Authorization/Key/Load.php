<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization\Key;

use Exception;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\LocalizedException;
use Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Helper\Config;
use Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey;
use Plumrocket\Base\Model\External\Connector;
use Plumrocket\Base\Model\External\Urls;
use Plumrocket\Base\Model\Utils\GetEnabledStoresUrls;

/**
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
     * @param \Plumrocket\Base\Model\Utils\GetEnabledStoresUrls            $getEnabledStoresUrls
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface        $getExtensionInformation
     * @param \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey $getTrueCustomerKey
     * @param \Magento\Framework\App\ProductMetadataInterface              $productMetadata
     * @param \Plumrocket\Base\Model\External\Connector                    $externalConnector
     * @param \Plumrocket\Base\Helper\Config                               $config
     */
    public function __construct(
        GetEnabledStoresUrls $getEnabledStoresUrls,
        GetExtensionInformationInterface $getExtensionInformation,
        GetTrueCustomerKey $getTrueCustomerKey,
        ProductMetadataInterface $productMetadata,
        Connector $externalConnector,
        Config $config
    ) {
        $this->getEnabledStoresUrls = $getEnabledStoresUrls;
        $this->getExtensionInformation = $getExtensionInformation;
        $this->getTrueCustomerKey = $getTrueCustomerKey;
        $this->productMetadata = $productMetadata;
        $this->externalConnector = $externalConnector;
        $this->config = $config;
    }

    /**
     * @param \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface $extension
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(ExtensionAuthorizationInterface $extension)
    {
        $extensionInformation = $this->getExtensionInformation->execute($extension->getModuleName());
        $key = '';
        $expiration = 0;
        try {
            $data = [
                'edition'      => $this->productMetadata->getEdition(),
                'base_urls'    => $this->getEnabledStoresUrls->execute(),
                'name'         => $extensionInformation->getModuleName(),
                'name_version' => $extensionInformation->getInstalledVersion(),
                'customer'     => $this->getTrueCustomerKey->execute($extension->getModuleName()),
                'title'        => $extensionInformation->getTitle(),
                'platform'     => 'm2',
            ];

            $xml = $this->externalConnector->connect('https://' . Urls::PINGBACK_URL . '/session/', $data);
            $cacheTime = (int) ($res['cache_time'] ?? 0);
            if ($cacheTime > 0) {
                $expiration = $cacheTime;
            }
            $key = $xml['data'] ?? '';
        } catch (Exception $e) {
            if ($this->config->isDebugMode()) {
                throw new LocalizedException(__($e->getMessage()), null, 1);
            }
        }

        return [$key, $expiration];
    }
}
