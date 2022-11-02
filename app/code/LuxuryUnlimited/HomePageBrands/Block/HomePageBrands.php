<?php

declare(strict_types=1);

namespace LuxuryUnlimited\HomePageBrands\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * class HomePageBrands
 */
class HomePageBrands extends Template
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param Config $eavConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $eavConfig,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->eavConfig = $eavConfig;
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function returnSelectedBrands(): array
    {
        $brandsIds = explode(",", 'brands,' . $this->getSelectedBrandsIds());
        $allBrands = $this->returnAllOptions();
        $result = [];
        foreach ($allBrands as $option) {
            $brandId = $option['value'];
            $brandName = $option['label'];
            if ($brandId > 1 && array_search($brandId, $brandsIds)) {
                $result[] = ['value' => $brandId, 'label' => __($brandName)];
            }
        }
        return $result;
    }

    /**
     * @return Config
     */
    public function getEavConfigManager()
    {
        return $this->eavConfig;
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBrands()
    {
        return $this->getEavConfigManager()->getAttribute('catalog_product', 'brands');
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function returnAllOptions(): array
    {
        return $this->getBrands()->getSource()->getAllOptions();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseLinkUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('homepage_brands/general/enable', $storeScope);
    }

    /**
     * @return mixed
     */
    public function getSelectedBrandsIds()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('homepage_brands/general/select_brands', $storeScope);
    }

    /**
     * @return mixed
     */
    public function getShowAllBrandsButton()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('homepage_brands/general/show_button', $storeScope);
    }

    /**
     * @return mixed
     */
    public function getButtonText()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('homepage_brands/general/button_label', $storeScope);
    }
}
