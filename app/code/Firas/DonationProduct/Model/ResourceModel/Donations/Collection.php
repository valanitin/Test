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

namespace Firas\DonationProduct\Model\ResourceModel\Donations;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Firas\DonationProduct\Model\ResourceModel\Donations
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Firas\DonationProduct\Model\Donations',
            'Firas\DonationProduct\Model\ResourceModel\Donations'
        );
    }
}
