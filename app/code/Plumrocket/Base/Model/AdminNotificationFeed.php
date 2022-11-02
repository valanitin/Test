<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model;

use Magento\AdminNotification\Model\Feed;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\UrlInterface;
use Plumrocket\Base\Api\ExtensionInformationListInterface;
use Plumrocket\Base\Helper\Config;
use Plumrocket\Base\Model\External\Urls;

/**
 * Plumrocket Base admin notification feed model
 */
class AdminNotificationFeed extends Feed
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $backendAuthSession;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Plumrocket\Base\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\Base\Api\ExtensionInformationListInterface
     */
    private $extensionInformationList;

    /**
     * @var \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey
     */
    private $getTrueCustomerKey;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Key
     */
    private $authorizationKey;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Backend\App\ConfigInterface                         $backendConfig
     * @param \Magento\AdminNotification\Model\InboxFactory                $inboxFactory
     * @param \Magento\Backend\Model\Auth\Session                          $backendAuthSession
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory                  $curlFactory
     * @param \Magento\Framework\App\DeploymentConfig                      $deploymentConfig
     * @param \Magento\Framework\App\ProductMetadataInterface              $productMetadata
     * @param \Magento\Framework\UrlInterface                              $urlBuilder
     * @param \Plumrocket\Base\Helper\Config                               $config
     * @param \Plumrocket\Base\Api\ExtensionInformationListInterface       $extensionInformationList
     * @param \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey $getTrueCustomerKey
     * @param \Plumrocket\Base\Model\Extension\Authorization\Key           $authorizationKey
     * @param \Magento\Framework\Module\ModuleListInterface                $moduleList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\ConfigInterface $backendConfig,
        \Magento\AdminNotification\Model\InboxFactory $inboxFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        UrlInterface $urlBuilder,
        Config $config,
        ExtensionInformationListInterface $extensionInformationList,
        \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey $getTrueCustomerKey,
        \Plumrocket\Base\Model\Extension\Authorization\Key $authorizationKey,
        ModuleListInterface $moduleList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $backendConfig,
            $inboxFactory,
            $curlFactory,
            $deploymentConfig,
            $productMetadata,
            $urlBuilder,
            $resource,
            $resourceCollection,
            $data
        );
        $this->backendAuthSession  = $backendAuthSession;
        $this->productMetadata = $productMetadata;
        $this->config = $config;
        $this->extensionInformationList = $extensionInformationList;
        $this->getTrueCustomerKey = $getTrueCustomerKey;
        $this->authorizationKey = $authorizationKey;
        $this->moduleList = $moduleList;
    }

    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl(): string
    {
        if ($this->_feedUrl === null) {
            $this->_feedUrl = 'https://' . Urls::NOTIFICATIONS_URL . '/';
        }

        $domain = parse_url($this->urlBuilder->getBaseUrl(), PHP_URL_HOST) ?: '';

        $url = $this->_feedUrl . 'domain/' . urlencode($domain);

        $modulesParams = [];
        foreach ($this->extensionInformationList->getList()->getItems() as $extensionInformation) {
            $modulesParams[] = implode(',', [
                $extensionInformation->getModuleName(),
                $extensionInformation->getInstalledVersion(),
                $this->getNotificationKey($extensionInformation->getModuleName())
            ]);
        }

        if (count($modulesParams)) {
            $url .= '/modules/' . base64_encode(implode(';', $modulesParams));
        }

        if ($this->config->isEnabledNotifications() && $this->config->getEnabledNotificationLists()) {
            $url .= '/lists/' . implode('|', $this->config->getEnabledNotificationLists());
        } else {
            $url .= '/lists/none';
        }

        if ($this->config->isEnabledStatistic()) {
            $ed = $this->productMetadata->getEdition();
            $url .= '/platform/' . (($ed === 'Comm'.'unity') ? 'm2ce' : 'm2ee');
            $url .= '/edition/' . $ed;
            $url .= '/magento_version/' . $this->productMetadata->getVersion();
        }

        return $url;
    }

    /**
     * Check feed for modification
     *
     * @return $this
     */
    public function checkUpdate()
    {
        if (! $this->moduleList->has('Magento_AdminNotification')) {
            return $this;
        }

        $session = $this->backendAuthSession;
        $time = time();
        $frequency = $this->getFrequency();
        if (($frequency + $session->getMfBaseNoticeLastUpdate() > $time)
            || ($frequency + $this->getLastUpdate() > $time)
        ) {
            return $this;
        }

        $session->setPANLastUpdate($time);
        parent::checkUpdate();
        return $this;
    }

    /**
     * Retrieve update frequency
     *
     * @return int
     */
    public function getFrequency(): int
    {
        return 86400;
    }

    /**
     * Retrieve last update time
     *
     * @return int
     */
    public function getLastUpdate(): int
    {
        return (int) $this->_cacheManager->load('plumrocket_admin_notifications_lastcheck');
    }

    /**
     * Set last update time (now)
     *
     * @return $this
     */
    public function setLastUpdate()
    {
        $this->_cacheManager->save(time(), 'plumrocket_admin_notifications_lastcheck');
        return $this;
    }

    /**
     * Receive key
     *
     * @param string $moduleName
     * @return string
     */
    public function getNotificationKey($moduleName): string
    {
        return implode(',', [
            $this->getTrueCustomerKey->execute($moduleName),
            $this->authorizationKey->get($moduleName)
        ]);
    }
}
