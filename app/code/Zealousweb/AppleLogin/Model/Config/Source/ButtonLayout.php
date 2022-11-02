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
class ButtonLayout implements \Magento\Framework\Option\ArrayInterface
{
    const BUTTON_LAYOUT_RIGHT_SIDE = "right";
    const BUTTON_LAYOUT_CENTER = "center";
    const BUTTON_LAYOUT_LEFT_SIDE = "left";

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => self::BUTTON_LAYOUT_RIGHT_SIDE, 'label' => __('Right Side')], ['value' => self::BUTTON_LAYOUT_CENTER, 'label' => __('Center')], ['value' => self::BUTTON_LAYOUT_LEFT_SIDE, 'label' => __('Left Side')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::BUTTON_LAYOUT_RIGHT_SIDE => __('Right Side'), self::BUTTON_LAYOUT_CENTER => __('Center'), self::BUTTON_LAYOUT_LEFT_SIDE => __('Left Side')];
    }
}
