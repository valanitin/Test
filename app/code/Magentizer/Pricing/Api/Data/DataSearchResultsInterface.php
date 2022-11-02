<?php
/*
 * Magentizer_Pricing

 * @category   SussexDev
 * @package    Magentizer_Pricing
 * @copyright  Copyright (c) 2019 Scott Parsons
 * @license    https://github.com/ScottParsons/module-sampleuicomponent/blob/master/LICENSE.md
 * @version    1.1.2
 */
namespace Magentizer\Pricing\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface DataSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get data list.
     *
     * @return \Magentizer\Pricing\Api\Data\DataInterface[]
     */
    public function getItems();

    /**
     * Set data list.
     *
     * @param \Magentizer\Pricing\Api\Data\DataInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
