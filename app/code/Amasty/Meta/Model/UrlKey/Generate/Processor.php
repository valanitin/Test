<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Model\UrlKey\Generate;

use Amasty\Meta\Helper\UrlKeyHandler;
use Amasty\Meta\Model\ConfigProvider;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Processor
{
    const BATCH_COUNT = 500;

    /**
     * @var StoreInterface[]|null
     */
    private $stores = null;

    /**
     * @var Collection[]|null
     */
    private $collectionsPool = null;

    /**
     * @var bool
     */
    private $createRedirect = false;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var null|int
     */
    private $stepsAmount = null;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var bool
     */
    private $excludeDisabledProducts = false;

    /**
     * @var UrlKeyHandler
     */
    private $urlGenerator;

    public function __construct(
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        UrlKeyHandler $urlGenerator
    ) {
        $this->storeManager = $storeManager;
        $this->urlGenerator = $urlGenerator;
        $this->configProvider = $configProvider;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param StoreInterface[] $stores
     */
    public function setStores(?array $stores): void
    {
        $this->stores = $stores;
    }

    public function process(?callable $stepCallback = null, OutputInterface $output): void
    {
        foreach ($this->getStores() as $store) {
            $urlKeyTemplate = $this->configProvider->getProductTemplate($store->getCode());
            $storeId = (int)$store->getId();

            if (empty($urlKeyTemplate)) {
                $output->writeln(PHP_EOL . __('The url key for store "%1" was not specified.', $store->getName()));
                continue;
            }

            /** @var ProductInterface $product **/
            foreach ($this->getProductsByStoreId($storeId) as $product) {
                if ($stepCallback !== null) {
                    $stepCallback($product, $storeId);
                }

                $this->urlGenerator->processProduct($product, $store, $this->createRedirect, $urlKeyTemplate);
            }
        }
    }

    /**
     * @param int $storeId
     * @return \Generator<ProductInterface>
     */
    private function getProductsByStoreId(int $storeId): \Generator
    {
        $collection = $this->getCollectionByStoreId($storeId);
        $lastPageNumber = $collection->getLastPageNumber();

        for ($pageNumber = 1; $pageNumber <= $lastPageNumber; ++$pageNumber) {
            $batchCollection = clone $collection;

            yield from $batchCollection->setCurPage($pageNumber);
        }
    }

    /**
     * @return StoreInterface[]
     */
    private function getStores(): array
    {
        if ($this->stores === null) {
            $this->stores = $this->storeManager->getStores(false, true);
        }

        return $this->stores;
    }

    public function createCollection(StoreInterface $store): Collection
    {
        /** @var Collection $collection **/
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->setStore($store);
        $collection->setPageSize(static::BATCH_COUNT);

        if ($this->excludeDisabledProducts) {
            $collection->addAttributeToFilter(
                ProductAttributeInterface::CODE_STATUS,
                Status::STATUS_ENABLED
            );
        }

        return $collection;
    }

    private function getCollectionByStoreId(int $storeId): Collection
    {
        return $this->getCollectionsPool()[$storeId];
    }

    /**
     * @return Collection[]
     */
    private function getCollectionsPool(): array
    {
        if ($this->collectionsPool === null) {
            $this->collectionsPool = [];

            foreach ($this->getStores() as $store) {
                $this->collectionsPool[$store->getId()] = $this->createCollection($store);
            }
        }

        return $this->collectionsPool;
    }

    public function getProcessStepsAmount(): int
    {
        if ($this->stepsAmount === null) {
            $this->stepsAmount = array_reduce($this->getStores(), function (int $accumulator, StoreInterface $store) {
                return $accumulator + $this->getCollectionByStoreId((int)$store->getId())->getSize();
            }, 0);
        }

        return $this->stepsAmount;
    }

    public function setCreateRedirect(bool $isCreateRedirect): void
    {
        $this->createRedirect = $isCreateRedirect;
    }

    public function excludeDisabledProducts(bool $isProcess): void
    {
        $this->excludeDisabledProducts = $isProcess;
    }
}
