<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Helper;

class Form extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Retrieve add to cart url by product
     *
     * @param int $productId
     * @return string
     */
    public function getAmpAddToCartUrl($productId = 0) : string
    {
        $params = ['_secure'  => true];

        if ($productId) {
            $params['product'] = $productId;
        }

        $url = $this->_getUrl(Data::SECTION_ID . '/api_cart/add', $params);
        $url = str_replace('http:', '', $url);

        return $url;
    }
}
