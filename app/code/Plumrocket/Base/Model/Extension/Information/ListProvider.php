<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Information;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Plumrocket\Base\Api\Data\ExtensionInformationSearchResultsInterfaceFactory;
use Plumrocket\Base\Api\ExtensionInformationListInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Model\Extension\GetListOfInstalled;

/**
 * @since 2.5.0
 */
class ListProvider implements ExtensionInformationListInterface
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Plumrocket\Base\Api\Data\ExtensionInformationSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetListOfInstalled
     */
    private $getListOfInstalledExtensions;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                                $searchCriteriaBuilder
     * @param \Plumrocket\Base\Api\Data\ExtensionInformationSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Plumrocket\Base\Model\Extension\GetListOfInstalled                         $getListOfInstalledExtensions
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface                       $getExtensionInformation
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ExtensionInformationSearchResultsInterfaceFactory $searchResultsFactory,
        GetListOfInstalled $getListOfInstalledExtensions,
        GetExtensionInformationInterface $getExtensionInformation
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->getListOfInstalledExtensions = $getListOfInstalledExtensions;
        $this->getExtensionInformation = $getExtensionInformation;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): SearchResultsInterface
    {
        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }

        $extensionsNames = $this->getListOfInstalledExtensions->execute();

        $informationList = [];
        foreach ($extensionsNames as $extensionsName) {
            $informationList[] = $this->getExtensionInformation->execute($extensionsName);
        }

        /** @var \Plumrocket\Base\Api\Data\ExtensionInformationSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setItems($informationList);
        $searchResults->setTotalCount(count($informationList));
        $searchResults->setSearchCriteria($searchCriteria);

        return $searchResults;
    }
}
