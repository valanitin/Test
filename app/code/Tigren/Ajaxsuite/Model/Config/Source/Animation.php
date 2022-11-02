<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Ajaxsuite\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Animation
 *
 * @package Tigren\Ajaxsuite\Model\Config\Source
 */
class Animation implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'fade', 'label' => __('Fade In')],
            ['value' => 'slide_top', 'label' => __('Slide from Top')],
            ['value' => 'slide_bottom', 'label' => __('Slide from Bottom')],
            ['value' => 'slide_left', 'label' => __('Slide from Left')],
            ['value' => 'slide_right', 'label' => __('Slide from Right')]
        ];
    }
}
