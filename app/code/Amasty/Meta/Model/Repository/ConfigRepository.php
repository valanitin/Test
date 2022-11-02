<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Model\Repository;

use Amasty\Meta\Api\Data\ConfigInterface;
use Amasty\Meta\Api\ConfigRepositoryInterface;
use Amasty\Meta\Model\ConfigFactory;
use Amasty\Meta\Model\ResourceModel\Config as ConfigResource;
use Amasty\Meta\Model\ResourceModel\Config\CollectionFactory;
use Amasty\Meta\Model\ResourceModel\Config\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigRepository implements ConfigRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * @var ConfigResource
     */
    private $configResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $configs;

    /**
     * @var CollectionFactory
     */
    private $configCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        ConfigFactory $configFactory,
        ConfigResource $configResource,
        CollectionFactory $configCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->configFactory = $configFactory;
        $this->configResource = $configResource;
        $this->configCollectionFactory = $configCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(ConfigInterface $config)
    {
        try {
            if ($config->getId()) {
                $config = $this->getById($config->getId())->addData($config->getData());
            }
            $this->configResource->save($config);
            unset($this->configs[$config->getId()]);
        } catch (\Exception $e) {
            if ($config->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save config with ID %1. Error: %2',
                        [$config->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new config. Error: %1', $e->getMessage()));
        }

        return $config;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->configs[$id])) {
            /** @var \Amasty\Meta\Model\Config $config */
            $config = $this->configFactory->create();
            $this->configResource->load($config, $id);
            if (!$config->getId()) {
                throw new NoSuchEntityException(__('Config with specified ID "%1" not found.', $id));
            }
            $this->configs[$id] = $config;
        }

        return $this->configs[$id];
    }

    /**
     * @inheritdoc
     */
    public function delete(ConfigInterface $config)
    {
        try {
            $this->configResource->delete($config);
            unset($this->configs[$config->getId()]);
        } catch (\Exception $e) {
            if ($config->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove config with ID %1. Error: %2',
                        [$config->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove config. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $configModel = $this->getById($id);
        $this->delete($configModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\Meta\Model\ResourceModel\Config\Collection $configCollection */
        $configCollection = $this->configCollectionFactory->create();
        
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $configCollection);
        }
        
        $searchResults->setTotalCount($configCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $configCollection);
        }
        
        $configCollection->setCurPage($searchCriteria->getCurrentPage());
        $configCollection->setPageSize($searchCriteria->getPageSize());
        
        $configs = [];
        /** @var ConfigInterface $config */
        foreach ($configCollection->getItems() as $config) {
            $configs[] = $this->getById($config->getId());
        }
        
        $searchResults->setItems($configs);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $configCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $configCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $configCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection $configCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $configCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $configCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
