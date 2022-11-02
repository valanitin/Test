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
class Gender implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata
    ) {
        $this->customerMetadata = $customerMetadata;
    }
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $genders = [];
        $options = $this->toArray();
        foreach ($options as $key => $value) {
            $genders[] = [
                'value'=> $key,
                'label'=> $value
            ];
        }
        return $genders;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $genders = [];
        try {
            $attribute = $this->customerMetadata->getAttributeMetadata('gender');
            $options = $attribute->getOptions();
            foreach ($options as $option) {
                $genders[$option->getValue()] = $option->getLabel();
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return [];
        }
        return $genders;
    }
}
