<?php

declare(strict_types=1);

namespace LuxuryUnlimited\WishListBuyNow\Helper;

/**
 * Class WishlistData
 */
class WishlistData extends \Magento\Wishlist\Helper\Data
{
    /**
     * Retrieve params for removing item from wishlist
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item $item
     * @param bool $addReferer
     * @return string
     */
    public function getAddToCartParams($item, $addReferer = false): string
    {
        $params = $this->_getCartUrlParameters($item);
        $params[\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED] = '';

        if ($addReferer) {
            $params = $this->addRefererToParams($params);
        }

        return $this->_postDataHelper->getPostData(
            $this->_getUrlStore($item)->getUrl('buynow/index/cart'),
            $params
        );
    }

    /**
     * Retrieve config value
     *
     * @param $config
     * @return string
     */
    public function getConfig($config): string
    {
        return $this->scopeConfig->getValue($config, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
