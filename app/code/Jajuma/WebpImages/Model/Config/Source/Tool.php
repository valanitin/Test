<?php

namespace Jajuma\WebpImages\Model\Config\Source;

class Tool implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'cwebp', 'label' => __('Cwebp')],
            ['value' => 'convert', 'label' => __('Imagemagick')],
            ['value' => 'gd', 'label' => __('GD')]
        ];
    }
}