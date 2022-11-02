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
class DisplayType implements \Magento\Framework\Option\ArrayInterface
{
    const DISPLAY_TYPE_BUTTON = 1;
    const DISPLAY_TYPE_ICON = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => self::DISPLAY_TYPE_BUTTON, 'label' => __('Button')], ['value' => self::DISPLAY_TYPE_ICON, 'label' => __('Icon')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::DISPLAY_TYPE_BUTTON => __('Button'), self::DISPLAY_TYPE_ICON => __('Icon')];
    }
}
