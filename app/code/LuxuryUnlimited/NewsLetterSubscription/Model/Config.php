<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\NewsLetterSubscription\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{

    private const XML_PATH_SUB_CRON_ENABLED = 'luxuryunlimited_subscribers/api_config/enabled';
    private const XML_PATH_API_URL = 'luxuryunlimited_subscribers/api_config/api_url';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get Enabled
     *
     * @param string $store
     * @return bool
     */
    public function getEnabled($store = null): ?bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SUB_CRON_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get Api Url
     *
     * @param string $store
     * @return string
     */
    public function getApiUrl($store = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_URL,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
