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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Amp\Model\Plugin\Magento\Csp\Model\Collector\CspWhitelistXml;

use Magento\Csp\Model\Collector\CspWhitelistXml\Data;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @since 2.9.15
 */
class DataPlugin {

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Add url with or without 'www' to CSP whitelist
     *
     * @param Data $data
     * @param $result
     * @param $path
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGet(Data $data, $result, $path)
    {
        switch ($path) {
            case null:
                $result['frame-src']['hosts'][] = $this->getProductOptionIframeHost();
                break;
            case (strpos($path, 'frame-src/hosts') !== false):
                $result[] = $this->getProductOptionIframeHost();
                break;
            case (strpos($path, 'frame-src') !== false):
                $result['hosts'][] = $this->getProductOptionIframeHost();
                break;
        }

        return $result;
    }

    /**
     * Retrieve url with or without 'www'
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductOptionIframeHost(): string
    {
        $url = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
        if (strpos($url, 'www')) {
            $urlResult =  str_replace('www.', '', $url);
        } else {
            $urlResult =  str_replace('://', '://www.', $url);
        }

        return $urlResult;
    }
}
