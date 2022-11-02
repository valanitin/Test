<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zealousweb\AppleLogin\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class RedirectionType implements \Magento\Framework\Option\ArrayInterface
{
    const REDIRECT_HOMEPAGE = 1;
    const REDIRECT_CURRENTPGE = 2;
    const REDIRECT_MYACCOUNT = 3;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => self::REDIRECT_HOMEPAGE, 'label' => __('Home Page')], ['value' => self::REDIRECT_CURRENTPGE, 'label' => __('Current Page')], ['value' => self::REDIRECT_MYACCOUNT, 'label' => __('My Account')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::REDIRECT_HOMEPAGE => __('Home Page'), self::REDIRECT_CURRENTPGE => __('Current Page'), self::REDIRECT_MYACCOUNT => __('My Account')];
    }
}
