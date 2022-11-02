<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConfigProvider
{
    const AMMETA_PRODUCT_URL_TEMPLATE = 'ammeta/product/url_template';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function getProductTemplate(?string $storeCode): string
    {
        $storeCode = $storeCode ?: $this->storeManager->getStore()->getCode();
        $urlTemplate = $this->scopeConfig->getValue(
            self::AMMETA_PRODUCT_URL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeCode
        );

        return trim((string)$urlTemplate);
    }
}
