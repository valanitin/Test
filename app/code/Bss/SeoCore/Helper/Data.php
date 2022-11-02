<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_SeoCore
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SeoCore\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Bss\SeoCore\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    public $postDataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->postDataHelper = $postDataHelper;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }

    /**
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

    /**
     * @return bool
     */
    public function isEnableH1Title()
    {
        return $this->scopeConfig->isSetFlag(
            'bss_seo_core/general/enable_h1_title',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getGoogleVerification()
    {
        return $this->scopeConfig->getValue(
            'bss_seo_core/general/google_verification',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getBingVerification()
    {
        return $this->scopeConfig->getValue(
            'bss_seo_core/general/bing_verification',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Rewrite function implode() fix all the version of php
     * @param string $separator
     * @param array $array
     * @return string
     */
    public function implode($separator, $array)
    {
        if (!is_array($separator) && $this->compareVersionPhp()) {
            return implode($separator, $array);
        }
        return implode($array, $separator);
    }

    /**
     * Compare Version Php
     */
    public function compareVersionPhp()
    {
        if (version_compare(PHP_VERSION, '7.4', '>=')) {
            return true;
        }
        return false;
    }
}
