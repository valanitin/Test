<?php
/**
 * Copyright ©  MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Info\Model;

class OffersFeed extends AbstractFeed
{
    /**
     * @var string
     */
    const CACHE_IDENTIFIER = 'mageworx_offers_notifications_lastcheck';

    /**
     * Feed url
     * @var string
     */
    protected $_feedUrl = \MageWorx\Info\Helper\Data::MAGEWORX_SITE . '/infoprovider/index/offers';
}
