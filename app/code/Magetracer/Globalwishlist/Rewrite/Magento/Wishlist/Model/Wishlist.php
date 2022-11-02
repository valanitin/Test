<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magetracer\Globalwishlist\Rewrite\Magento\Wishlist\Model;

class Wishlist extends \Magento\Wishlist\Model\Wishlist
{
	/**
     * Retrieve wishlist item collection
     *
     * @return \Magento\Wishlist\Model\ResourceModel\Item\Collection
     * @throws NoSuchEntityException
     */
    public function getItemCollection()
    {
        if ($this->_itemCollection === null) {
            $this->_itemCollection = $this->_wishlistCollectionFactory->create()->addWishlistFilter(
                $this
            )->setVisibilityFilter();
        }

        return $this->_itemCollection;
    }
}

