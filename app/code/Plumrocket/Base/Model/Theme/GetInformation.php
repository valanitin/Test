<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Theme;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @since 2.3.5
 */
class GetInformation
{

    public const THEME_CODE_DEFAULT = 'Magento/blank';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ThemeProviderInterface */
    private $themeProvider;

    /** @var StoreManagerInterface */
    private $storeManager;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider
     * @param \Magento\Store\Model\StoreManagerInterface                  $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ThemeProviderInterface $themeProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->themeProvider = $themeProvider;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getVendor(): string
    {
        return mb_strstr(mb_strtolower($this->getCode()), '/', true);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return str_replace('/', '-', mb_strtolower($this->getCode()));
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        $themeId = $this->scopeConfig->getValue(
            DesignInterface::XML_PATH_THEME_ID,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        $themeData = $this->themeProvider->getThemeById($themeId)->getData();
        return $themeData['code'] ?? self::THEME_CODE_DEFAULT;
    }
}
