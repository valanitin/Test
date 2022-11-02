<?php
/**
 * A Magento 2 module named Firas/DonationProduct
 * Copyright (C) 2017 Derrick Heesbeen
 *
 * This file is part of Firas/DonationProduct.
 *
 * Firas/DonationProduct is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Firas\DonationProduct\Api;

/**
 * Interface DonationsRepositoryInterface
 * @package Firas\DonationProduct\Api
 */
interface DonationsRepositoryInterface
{


    /**
     * Save Donations
     * @param \Firas\DonationProduct\Api\Data\DonationsInterface $donations
     * @return \Firas\DonationProduct\Api\Data\DonationsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Firas\DonationProduct\Api\Data\DonationsInterface $donations
    );

    /**
     * Retrieve Donations
     * @param string $donationsId
     * @return \Firas\DonationProduct\Api\Data\DonationsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($donationsId);

    /**
     * Retrieve Donations matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Firas\DonationProduct\Api\Data\DonationsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Donations
     * @param \Firas\DonationProduct\Api\Data\DonationsInterface $donations
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Firas\DonationProduct\Api\Data\DonationsInterface $donations
    );

    /**
     * Delete Donations by ID
     * @param string $donationsId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($donationsId);
}
