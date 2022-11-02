<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * @since 2.5.0
 */
interface ExtensionInformationListInterface
{
    /**
     * Get extension information
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): SearchResultsInterface;
}
