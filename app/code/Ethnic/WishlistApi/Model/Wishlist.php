<?php
/**
 * @copyright: Copyright © 2017 Ethnic GmbH. All rights reserved.
 * @see LICENSE.txt
 */

namespace Ethnic\WishlistApi\Model;

use Ethnic\WishlistApi\Api\WishlistInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Magento\Wishlist\Model\ResourceModel\Wishlist as ResourceWishlist;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection;
use Magento\Wishlist\Model\ItemFactory;

/**
 * Class Wishlist
 * @package Ethnic\WishlistApi\Model
 */
class Wishlist extends \Magento\Wishlist\Model\Wishlist implements WishlistInterface
{
    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;
    
    /**
     * @var ProductRepository
     */
    protected $productRepo;

    /**
     * Wishlist constructor.
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param Data $wishlistData
     * @param ResourceWishlist $resource
     * @param Collection $resourceCollection
     * @param StoreManagerInterface $storeManager
     * @param DateTime\DateTime $date
     * @param ItemFactory $wishlistItemFactory
     * @param CollectionFactory $wishlistCollectionFactory
     * @param ProductFactory $productFactory
     * @param Random $mathRandom
     * @param DateTime $dateTime
     * @param ProductRepository $productRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param ProductRepository $productRepo
     * @param bool $useCurrentWebsite
     * @param array $data
     * @param Json|null $serializer
     * @param StockRegistryInterface|null $stockRegistry
     * @param ScopeConfigInterface|null $scopeConfig
     * @param StockConfigurationInterface|null $stockConfiguration
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Catalog\Helper\Product $catalogProduct,
        Data $wishlistData,
        ResourceWishlist $resource,
        Collection $resourceCollection,
        StoreManagerInterface $storeManager,
        DateTime\DateTime $date,
        ItemFactory $wishlistItemFactory,
        CollectionFactory $wishlistCollectionFactory,
        ProductFactory $productFactory,
        Random $mathRandom,
        DateTime $dateTime,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        ProductRepository $productRepo,
        $useCurrentWebsite = true,
        array $data = [],
        Json $serializer = null,
        StockRegistryInterface $stockRegistry = null,
        ScopeConfigInterface $scopeConfig = null,
        ?StockConfigurationInterface $stockConfiguration = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $catalogProduct,
            $wishlistData,
            $resource,
            $resourceCollection,
            $storeManager,
            $date,
            $wishlistItemFactory,
            $wishlistCollectionFactory,
            $productFactory,
            $mathRandom,
            $dateTime,
            $productRepository,
            $useCurrentWebsite,
            $data,
            $serializer
        );
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->productRepo = $productRepo;
    }

    /**
     * @inheritdoc
     */
    public function getItems()
    {
        $itemCollection = $this->getItemCollection()->getItems();
        $searchCriteriaBuilder =  $this->searchCriteriaBuilderFactory->create();
        foreach($itemCollection as $key => $item){
            $searchCriteria = $searchCriteriaBuilder
            ->addFilter('entity_id', $item->getProduct()->getId(), 'eq')
            ->create();
            $list = $this->productRepo->getList($searchCriteria)->getItems();
            $extensionAttributes = $item->getProduct()->getExtensionAttributes();
            foreach ($list as $items) {
                $extensionAttributes->setConvertedRegularPrice($items->getExtensionAttributes()->getConvertedRegularPrice());
                $extensionAttributes->setConvertedRegularOldPrice($items->getExtensionAttributes()->getConvertedRegularOldPrice());
                $extensionAttributes->setStockItems($items->getExtensionAttributes()->getStockItem());
                $extensionAttributes->setConfigurableProductOptions($items->getExtensionAttributes()->getConfigurableProductOptions());
            }
        }
        
        return $itemCollection;
    }
}
