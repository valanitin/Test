<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @since 2.5.0
 */
interface ExtensionInformationSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get extension information list.
     *
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface[]
     */
    public function getItems() : array;

    /**
     * Set extension information list.
     *
     * @param \Plumrocket\Base\Api\Data\ExtensionInformationInterface[] $items
     * @return $this
     */
    public function setItems(array $items) : self;
}
