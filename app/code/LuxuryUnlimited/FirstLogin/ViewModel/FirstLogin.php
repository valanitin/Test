<?php

declare(strict_types=1);

namespace LuxuryUnlimited\FirstLogin\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * class FirstLogin
 */
class FirstLogin implements ArgumentInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWebsiteId()
    {
        return $this->storeManager->getStore()->getWebsiteId();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWebsiteName(): string
    {
        return $this->storeManager->getWebsite()->getName();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoresList(): array
    {
        return $this->storeManager->getWebsite($this->getWebsiteId())->getStores();
    }

    /**
     * @param $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreUrl($storeId): string
    {
        return $this->storeManager->getStore($storeId)->getBaseUrl();
    }
}
