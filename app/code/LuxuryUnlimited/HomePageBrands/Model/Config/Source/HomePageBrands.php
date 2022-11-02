<?php

declare(strict_types=1);

namespace LuxuryUnlimited\HomePageBrands\Model\Config\Source;

use Magento\Eav\Model\Config;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * class HomePageBrands
 */
class HomePageBrands implements OptionSourceInterface
{
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @param Config $eavConfig
     */
    public function __construct(
        Config $eavConfig
    ){
        $this->eavConfig = $eavConfig;
    }

    /**
     * @return array[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->returnAllOptions() as $option) {
            $brandName = $option['label'];
            if(strlen($brandName) > 1){
                $result[] = ['value' => $option['value'], 'label' => __($brandName)];
            }
        }
        return $result;
    }

    /**
     * @return Config
     */
    public function getEavConfigManager() {
        return $this->eavConfig;
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBrands() {
        return $this->getEavConfigManager()->getAttribute('catalog_product', 'brands');
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function returnAllOptions(): array
    {
        return $this->getBrands()->getSource()->getAllOptions();
    }
}
