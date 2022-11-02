<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Model\Repository;

use Amasty\SeoToolKit\Api\Data\RedirectInterface;
use Amasty\SeoToolKit\Api\RedirectRepositoryInterface;
use Amasty\SeoToolKit\Model\RedirectFactory;
use Amasty\SeoToolKit\Model\ResourceModel\Redirect as RedirectResource;
use Amasty\SeoToolKit\Model\ResourceModel\Redirect\CollectionFactory;
use Amasty\SeoToolKit\Model\ResourceModel\Redirect\Collection;
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
class RedirectRepository implements RedirectRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var RedirectResource
     */
    private $redirectResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $redirects;

    /**
     * @var CollectionFactory
     */
    private $redirectCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        RedirectFactory $redirectFactory,
        RedirectResource $redirectResource,
        CollectionFactory $redirectCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->redirectFactory = $redirectFactory;
        $this->redirectResource = $redirectResource;
        $this->redirectCollectionFactory = $redirectCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(RedirectInterface $redirect)
    {
        try {
            if ($redirect->getId()) {
                $redirect = $this->getById($redirect->getId())->addData($redirect->getData());
            }
            $this->redirectResource->save($redirect);
            unset($this->redirects[$redirect->getId()]);
        } catch (\Exception $e) {
            if ($redirect->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save redirect with ID %1. Error: %2',
                        [$redirect->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new redirect. Error: %1', $e->getMessage()));
        }

        return $redirect;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->redirects[$id])) {
            /** @var \Amasty\SeoToolKit\Model\Redirect $redirect */
            $redirect = $this->redirectFactory->create();
            $this->redirectResource->load($redirect, $id);
            if (!$redirect->getId()) {
                throw new NoSuchEntityException(__('Redirect with specified ID "%1" not found.', $id));
            }
            $this->redirects[$id] = $redirect;
        }

        return $this->redirects[$id];
    }

    /**
     * @inheritdoc
     */
    public function delete(RedirectInterface $redirect)
    {
        try {
            $this->redirectResource->delete($redirect);
            unset($this->redirects[$redirect->getId()]);
        } catch (\Exception $e) {
            if ($redirect->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove redirect with ID %1. Error: %2',
                        [$redirect->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove redirect. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $redirectModel = $this->getById($id);
        $this->delete($redirectModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\SeoToolKit\Model\ResourceModel\Redirect\Collection $redirectCollection */
        $redirectCollection = $this->redirectCollectionFactory->create();
        
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $redirectCollection);
        }
        
        $searchResults->setTotalCount($redirectCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $redirectCollection);
        }
        
        $redirectCollection->setCurPage($searchCriteria->getCurrentPage());
        $redirectCollection->setPageSize($searchCriteria->getPageSize());
        
        $redirects = [];
        /** @var RedirectInterface $redirect */
        foreach ($redirectCollection->getItems() as $redirect) {
            $redirects[] = $this->getById($redirect->getId());
        }
        
        $searchResults->setItems($redirects);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $redirectCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $redirectCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $redirectCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection $redirectCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $redirectCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $redirectCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
