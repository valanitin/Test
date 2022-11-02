<?php

declare(strict_types=1);

namespace LuxuryUnlimited\HomePageBrands\Model\Config\Source\HomePageBrands;

use LuxuryUnlimited\HomePageBrands\Model\Config\Source\HomePageBrands;
/**
 * Class Extended
 */
class Extended extends HomePageBrands
{
    /**
     * @param $boolean
     * @return array[]|\string[][]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray(): array
    {
        $allOption = [[
            'value' => 'brands',
            'label' => (string)(__('NONE'))
        ]];
        return array_merge($allOption, parent::toOptionArray());
    }
}
