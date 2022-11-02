<?php
/**
 * Copyright ©  MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Info\Model;

use Magento\AdminNotification\Model\Feed;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Escaper;
use Magento\AdminNotification\Model\InboxFactory;

class AbstractFeed extends Feed
{
    /**
     * @var string
     */
    const CACHE_IDENTIFIER = '';

    /**
     * Update frequency in days
     *
     * @var int
     */
    const FREQUENCY = 1;

    /**
     * @var Escaper|null
     */
    protected $magentoEscaper;

    /**
     * AbstractFeed constructor.
     *
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\ConfigInterface $backendConfig
     * @param InboxFactory $inboxFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param Escaper|null $escaper
     */
    public function __construct(
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\ConfigInterface $backendConfig,
        InboxFactory $inboxFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        Escaper $escaper = null
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
        $this->magentoEscaper = $escaper ?? ObjectManager::getInstance()->get(
                Escaper::class
            );
    }

    /**
     * Retrieve Update Frequency
     *
     * @return int
     */
    public function getFrequency()
    {
        return self::FREQUENCY * 3600;
    }

    /**
     * Retrieve feed Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return $this->_cacheManager->load(static::CACHE_IDENTIFIER);
    }

    /**
     * Set feed last update time (now)
     *
     * @return $this
     */
    public function setLastUpdate()
    {
        $this->_cacheManager->save(time(), static::CACHE_IDENTIFIER);

        return $this;
    }

    /**
     * @return $this
     */
    public function checkUpdate()
    {
        if ($this->getFrequency() + $this->getLastUpdate() > time()) {
            return $this;
        }

        $feedData = [];
        $feedXml  = $this->getFeedData();

        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {
                $data = $this->prepareFeedItemData($item);

                if ($data) {
                    $feedData[] = $data;
                }
            }

            if ($feedData) {
                $this->_inboxFactory->create()->parse(array_reverse($feedData));
            }
        }
        $this->setLastUpdate();

        return $this;
    }

    /**
     * @return false|int
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    protected function getMagentoInstallDate()
    {
        return strtotime($this->_deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_INSTALL_DATE));
    }

    /**
     * @param $item
     * @return array
     */
    protected function prepareFeedItemData($item)
    {
        $data                = [];
        $installDate         = $this->getMagentoInstallDate();
        $itemPublicationDate = strtotime((string)$item->pubDate);

        //Use current time for empty date case
        if (!$itemPublicationDate) {
            $itemPublicationDate = time();
        }

        if ($installDate <= $itemPublicationDate) {
            $data = [
                'severity'    => (int)$item->severity,
                'date_added'  => date('Y-m-d H:i:s', $itemPublicationDate),
                'title'       => $this->magentoEscaper->escapeHtml((string)$item->title),
                'description' => $this->magentoEscaper->escapeHtml((string)$item->description),
                'url' => $this->magentoEscaper->escapeHtml((string)$item->link),
            ];
        }

        return $data;
    }
}
