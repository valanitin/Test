<?php
/**
 * @copyright: Copyright © 2017 Ethnic GmbH. All rights reserved.
 * @see LICENSE.txt
 */

namespace Ethnic\WishlistApi\Api;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface ItemInterface
 * @package Ethnic\WishlistApi\Api
 * @api
 */
interface ItemInterface
{

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct(): ProductInterface;
}
