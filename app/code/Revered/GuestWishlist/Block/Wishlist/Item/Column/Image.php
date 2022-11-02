<?php
/**
 * Revered Technologies Pvt. Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 *
 ********************************************************************
 * @category   Revered
 * @package    Revered_GuestWishlist
 * @copyright  Copyright (c) Revered Technologies Pvt. Ltd. (http://www.reveredtech.com)
 * @license    http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 */
declare(strict_types=1);

namespace Revered\GuestWishlist\Block\Wishlist\Item\Column;
use Magento\Framework\App\ObjectManager;
/**
 * Class Image
 * @package Revered\GuestWishlist\Block\Wishlist\Item\Column
 */
class Image extends \Revered\GuestWishlist\Block\Wishlist\Item\Column
{
    /**
     * Identify the product from which thumbnail should be taken.
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductForThumbnail(\Revered\GuestWishlist\Model\Item $item) : \Magento\Catalog\Model\Product
    {
        $itemResolver  = ObjectManager::getInstance()->get('\Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface');
        return $itemResolver->getFinalProduct($item);
    }
}
