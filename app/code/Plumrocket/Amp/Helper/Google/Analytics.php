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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Helper\Google;

use \Magento\GoogleAnalytics\Helper\Data as GoogleAnalyticsHelper;
use \Plumrocket\Amp\Helper\Data as AmpHelper;
use \Magento\Store\Model\ScopeInterface as StoreScopeInterface;

class Analytics extends \Magento\GoogleAnalytics\Helper\Data
{
    /**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE  = AmpHelper::SECTION_ID . '/analytics/active';
    const XML_PATH_ACCOUNT = AmpHelper::SECTION_ID . '/analytics/account';

    /**
     * @var array
     */
    private $mapping = [
        GoogleAnalyticsHelper::XML_PATH_ACTIVE  => self::XML_PATH_ACTIVE,
        GoogleAnalyticsHelper::XML_PATH_ACCOUNT => self::XML_PATH_ACCOUNT,
    ];

    /**
     * Filter condition for config collection
     *
     * @var array
     */
    private $collectionCondition = ['like' => 'google/analytics/%'];

    /**
     * @var \Magento\Config\Model\Config|null
     */
    private $magentoConfig = null;

    /**
     * @var \Magento\Config\Model\ConfigFactory|null
     */
    private $magentoConfigFactory = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|null
     */
    private $storeManager = null;

    /**
     * @var null|\Plumrocket\Amp\Model\ResourceModel\Config\Collection
     */
    private $configCollection = null;

    /**
     * @var null|\Plumrocket\Amp\Model\ResourceModel\Config\CollectionFactory
     */
    private $configCollectionFactory = null;

    /**
     * Analytics constructor.
     *
     * @param \Magento\Framework\App\Helper\Context                        $context
     * @param \Magento\Config\Model\ConfigFactory                          $configFactory
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Plumrocket\Amp\Model\ResourceModel\Config\CollectionFactory $configCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\ConfigFactory $configFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Plumrocket\Amp\Model\ResourceModel\Config\CollectionFactory $configCollectionFactory
    ) {
        $this->magentoConfigFactory    = $configFactory;
        $this->storeManager            = $storeManager;
        $this->configCollectionFactory = $configCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Mapping getter
     *
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * Mapping setter
     *
     * @param array $mapping
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param null $store
     * @return bool
     */
    public function isGoogleAnalyticsAvailable($store = null)
    {
        $store = $store ?: $this->storeManager->getStore()->getId();
        return $this->scopeConfig->getValue(self::XML_PATH_ACTIVE, StoreScopeInterface::SCOPE_STORES, $store)
            && $this->getAccountId($store);
    }

    /**
     * Get GA account id
     *
     * @param string $store
     * @return string
     */
    public function getAccountId($store = null)
    {
        $store = $store ?: $this->storeManager->getStore()->getId();
        return (string)$this->scopeConfig->getValue(self::XML_PATH_ACCOUNT, StoreScopeInterface::SCOPE_STORES, $store);
    }

    /**
     * Copies Google Analytics configuration from the default module config
     * Used one time on install or update
     *
     * @return $this
     * @throws \Exception
     */
    public function copyConfig()
    {
        $this->initResources();
        foreach ($this->configCollection->getItems() as $config) {
            if (!isset($this->mapping[$config->getPath()])) {
                continue;
            }
            $this->copyScopeConfig($config);
        }
        $this->resetConfigSaver();
        return $this;
    }

    /**
     * Collection creation and filters applied
     *
     * @return $this
     */
    private function initResources()
    {
        $this->magentoConfig = $this->magentoConfigFactory->create();
        $this->configCollection = $this->configCollectionFactory->create();
        $this->configCollection->addFieldToFilter('path', $this->collectionCondition);

        return $this;
    }

    /**
     * Copies Google Analytics configuration for specific scope
     *
     * @param \Plumrocket\Amp\Model\Config $config
     * @return $this
     * @throws \Exception
     */
    private function copyScopeConfig($config)
    {
        $this->reInitConfigSaver($config);
        $this->magentoConfig->setDataByPath($this->mapping[$config->getPath()], $config->getValue());
        $this->magentoConfig->save();
        return $this;
    }

    /**
     * Set configuration for scope
     *
     * @param \Plumrocket\Amp\Model\Config $config
     * @return $this
     */
    private function reInitConfigSaver($config)
    {
        $this->resetConfigSaver();

        if ($config->getScope() === StoreScopeInterface::SCOPE_WEBSITES) {
            $this->magentoConfig->setWebsite($config->getScopeId());
        } elseif ($config->getScope() === StoreScopeInterface::SCOPE_STORES) {
            $this->magentoConfig->setStore($config->getScopeId());
        }

        return $this;
    }

    /**
     * Set default configuration
     *
     * @return $this
     */
    private function resetConfigSaver()
    {
        $this->magentoConfig->setStore('');
        $this->magentoConfig->setWebsite('');
        $this->magentoConfig->setSection('');
        return $this;
    }
}
