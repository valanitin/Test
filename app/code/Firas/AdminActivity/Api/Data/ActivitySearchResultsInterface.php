<?php
/**
 * Firas
 *
 * Do not edit or add to this file if you wish to upgrade to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please contact us https://firas.co.uk/contacts.
 *
 * @category   Firas
 * @package    Firas_AdminActivity
 * @copyright  Copyright (C) 2018 Kiwi Commerce Ltd (https://firas.co.uk/)
 * @license    https://firas.co.uk/magento2-extension-license/
 */
namespace Firas\AdminActivity\Api\Data;

/**
 * Interface LogSearchResultsInterface
 * @package Firas\EnhancedSMTP\Api\Data
 */
interface ActivitySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get admin activity list.
     * @api
     * @return \Firas\AdminActivity\Model\Activity[]
     */
    public function getItems();

    /**
     * Set admin activity list.
     * @api
     * @param \Firas\AdminActivity\Model\Activity[] $items
     * @return $this
     */
    public function setItems(array $items);
}
