<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization;

use Exception;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Plumrocket\Base\Model\Extension\GetInstallationType;
use Plumrocket\Base\Api\ExtensionAuthorizationRepositoryInterface;
use Plumrocket\Base\Api\ExtensionInformationListInterface;
use Plumrocket\Base\Helper\Config;
use Plumrocket\Base\Model\Extension\Authorization\Status\Calculate;
use Plumrocket\Base\Model\Extension\Authorization\Status\Update;
use Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey;
use Plumrocket\Base\Model\Extension\Status\Disable;
use Plumrocket\Base\Model\Extension\Status\IsEnabledOnAnyStoreView;
use Plumrocket\Base\Model\External\Connector;
use Plumrocket\Base\Model\External\Urls;
use Plumrocket\Base\Model\Utils\GetEnabledStoresUrls;

/**
 * @since 2.5.0
 */
class Reindex
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $backendAuthSession;

    /**
     * @var \Plumrocket\Base\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Plumrocket\Base\Model\Utils\GetEnabledStoresUrls
     */
    private $getEnabledStoresUrls;

    /**
     * @var \Plumrocket\Base\Api\ExtensionInformationListInterface
     */
    private $extensionInformationList;

    /**
     * @var \Plumrocket\Base\Api\ExtensionAuthorizationRepositoryInterface
     */
    private $extensionAuthorizationRepository;

    /**
     * @var \Plumrocket\Base\Model\Extension\Status\IsEnabledOnAnyStoreView
     */
    private $isEnabledOnAnyStoreView;

    /**
     * @var \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey
     */
    private $getTrueCustomerKey;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Key
     */
    private $authorizationKey;

    /**
     * @var \Plumrocket\Base\Model\External\Connector
     */
    private $externalConnector;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Status\Calculate
     */
    private $calculateStatus;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Factory
     */
    private $extensionAuthorizationFactory;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Status\Update
     */
    private $updateStatus;

    /**
     * @var \Plumrocket\Base\Model\Extension\Status\Disable
     */
    private $disableExtension;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetInstallationType
     */
    private $getInstallType;

    /**
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Plumrocket\Base\Helper\Config $config
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Plumrocket\Base\Model\Utils\GetEnabledStoresUrls $getEnabledStoresUrls
     * @param \Plumrocket\Base\Api\ExtensionInformationListInterface $extensionInformationList
     * @param \Plumrocket\Base\Api\ExtensionAuthorizationRepositoryInterface $extensionAuthorizationRepository
     * @param \Plumrocket\Base\Model\Extension\Status\IsEnabledOnAnyStoreView $isEnabledOnAnyStoreView
     * @param \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey $getTrueCustomerKey
     * @param \Plumrocket\Base\Model\Extension\Authorization\Key $authorizationKey
     * @param \Plumrocket\Base\Model\External\Connector $externalConnector
     * @param \Plumrocket\Base\Model\Extension\Authorization\Status\Calculate $calculateStatus
     * @param \Plumrocket\Base\Model\Extension\Authorization\Factory $extensionAuthorizationFactory
     * @param \Plumrocket\Base\Model\Extension\Authorization\Status\Update $updateStatus
     * @param \Plumrocket\Base\Model\Extension\Status\Disable $disableExtension
     * @param \Plumrocket\Base\Model\Extension\GetInstallationType $getInstallType
     */
    public function __construct(
        Session $backendAuthSession,
        Config $config,
        CacheInterface $cache,
        ProductMetadataInterface $productMetadata,
        GetEnabledStoresUrls $getEnabledStoresUrls,
        ExtensionInformationListInterface $extensionInformationList,
        ExtensionAuthorizationRepositoryInterface $extensionAuthorizationRepository,
        IsEnabledOnAnyStoreView $isEnabledOnAnyStoreView,
        GetTrueCustomerKey $getTrueCustomerKey,
        Key $authorizationKey,
        Connector $externalConnector,
        Calculate $calculateStatus,
        Factory $extensionAuthorizationFactory,
        Update $updateStatus,
        Disable $disableExtension,
        GetInstallationType $getInstallType
    ) {
        $this->backendAuthSession = $backendAuthSession;
        $this->config = $config;
        $this->cache = $cache;
        $this->productMetadata = $productMetadata;
        $this->getEnabledStoresUrls = $getEnabledStoresUrls;
        $this->extensionInformationList = $extensionInformationList;
        $this->extensionAuthorizationRepository = $extensionAuthorizationRepository;
        $this->isEnabledOnAnyStoreView = $isEnabledOnAnyStoreView;
        $this->getTrueCustomerKey = $getTrueCustomerKey;
        $this->authorizationKey = $authorizationKey;
        $this->externalConnector = $externalConnector;
        $this->calculateStatus = $calculateStatus;
        $this->extensionAuthorizationFactory = $extensionAuthorizationFactory;
        $this->updateStatus = $updateStatus;
        $this->disableExtension = $disableExtension;
        $this->getInstallType = $getInstallType;
    }

    /**
     * Reindex installed extensions.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(): void
    {
        $time = time();
        $session = $this->backendAuthSession;

        $cacheKey = 'Plumrocket_base_reindex';

        if (! $session->isLoggedIn()
            || (86400 + $session->getPBProductReindex() > $time)
            || (86400 + ((int) $this->cache->load($cacheKey)) > $time)
        ) {
            if (! $this->config->isDebugMode()) {
                return;
            }
        }

        $data = [
            'edition' => $this->productMetadata->getEdition(),
            'products' => [],
            'base_urls' => $this->getEnabledStoresUrls->execute(),
            'platform' => 'm2',
            'magento_version' => $this->productMetadata->getVersion()
        ];
        $activeModules = [];
        foreach ($this->extensionInformationList->getList()->getItems() as $extensionInformation) {
            $moduleName = $extensionInformation->getModuleName();
            try {
                $isCached = $this->extensionAuthorizationRepository->get($moduleName)->isCached();
            } catch (NoSuchEntityException $e) {
                $isCached = false;
            }

            if ($isCached || ! $this->isEnabledOnAnyStoreView->execute($moduleName)) {
                continue;
            }

            $activeModules[$moduleName] = $moduleName;
            $data['products'][$moduleName] = [
                $moduleName,
                $extensionInformation->getInstalledVersion(),
                $this->getTrueCustomerKey->execute($moduleName) ?: 0,
                $this->authorizationKey->get($moduleName) ?: 0,
                $extensionInformation->getTitle(),
                $this->getInstallType->execute($moduleName),
            ];
        }

        if (count($activeModules)) {
            try {
                $xml = $this->externalConnector->connect('https://' . Urls::PINGBACK_URL . '/extensions/', $data);
                if (! isset($xml['statuses'])) {
                    throw new LocalizedException(__('Statuses are missing.'), null, 1);
                }
                $statuses = $xml['statuses'];
            } catch (Exception $e) {
                if ($this->config->isDebugMode()) {
                    throw new LocalizedException(__($e->getMessage()), null, 1);
                }
                $statuses = [];
                foreach ($activeModules as $moduleName) {
                    $statuses[$moduleName] = $this->calculateStatus->execute($moduleName);
                }
            }
            foreach ($activeModules as $moduleName) {
                try {
                    $authorization = $this->extensionAuthorizationRepository->get($moduleName);
                } catch (NoSuchEntityException $e) {
                    $authorization = $this->extensionAuthorizationFactory->create($moduleName);
                }

                $status = $statuses[$moduleName] ?? 301;
                $authorization = $this->updateStatus->execute($authorization, $status);
                if (! $authorization->isAuthorized()) {
                    $this->disableExtension->execute($authorization->getModuleName());
                }
            }
        }
        $this->cache->save($time, $cacheKey);
        $session->setPBProductReindex($time);
    }
}
