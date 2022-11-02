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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Cache\Allowed;

use Plumrocket\Amp\Model\AllowedAmpUrlCacheInterface;

class AmpUrl implements AllowedAmpUrlCacheInterface
{
    const CACHE_IDENTIFIER = 'pramp_url';
    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var array;
     */
    private $loadedData = [];

    /**
     * @var array
     */
    private $newData = [];

    /**
     * AmpUrl constructor.
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * Add new record for potential save
     *
     * @param string $requestPath
     * @return self
     */
    public function add(string $requestPath)
    {
        $this->newData[$requestPath] = true;
        return $this;
    }

    /**
     * Remove cached data by identifier 'pramp_url'
     *
     * @return self
     */
    public function clean()
    {
        $this->cache->remove(self::CACHE_IDENTIFIER);
        return $this;
    }

    /**
     * Save data
     *
     * @return self
     */
    public function save()
    {
        if (! empty(array_diff_key($this->newData, $this->getList()))) {
            $data = array_merge($this->getList(), $this->newData);

            $this->cache->save(
                $this->serializer->serialize($data),
                self::CACHE_IDENTIFIER,
                [\Magento\Framework\App\Config::CACHE_TAG]
            );
        }

        return $this;
    }

    /**
     * Load data
     *
     * @return array
     */
    public function getList(): array
    {
        if (empty($this->loadedData)) {
            $data = $this->cache->load(self::CACHE_IDENTIFIER);

            if (false !== $data) {
                $this->loadedData = $this->serializer->unserialize($data);
            }
        }

        return $this->loadedData;
    }
}
