<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Plugin\Catalog\Helper;

use Magento\Catalog\Model\Product as ModelProduct;

class Output
{
    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $data;

    public function __construct(
        \Amasty\Meta\Helper\Data $data
    ) {
        $this->data = $data;
    }

    /**
     * @param \Magento\Catalog\Helper\Output $subject
     * @param \Closure $proceed
     * @param ModelProduct $product
     * @param string|object $attributeHtml
     * @param string $attributeName
     * @return string
     */
    public function aroundProductAttribute(
        $subject,
        \Closure $proceed,
        $product,
        $attributeHtml,
        $attributeName
    ) {
        $attributeHtml = $attributeHtml === null ? $attributeHtml : (string)$attributeHtml;
        $attributeHtml = $this->getProductAttributeValue($attributeName, $attributeHtml);

        return $proceed($product, $attributeHtml, $attributeName);
    }

    private function getProductAttributeValue(string $attributeName, ?string $attributeHtml): ?string
    {
        $result = '';

        if (in_array($attributeName, ['short_description', 'description'])) {
            $result = $this->data->getReplaceData($attributeName);
        }

        return $result ?: $attributeHtml;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param ModelProduct $product
     * @param string $attributeHtml
     * @param string $attributeName
     * @return string
     */
    public function aroundCategoryAttribute(
        $subject,
        \Closure $proceed,
        $product,
        $attributeHtml,
        $attributeName
    ) {
        $attributeHtml = $this->getCategoryAttributeValue($attributeName, $attributeHtml);

        return $proceed($product, $attributeHtml, $attributeName);
    }

    private function getCategoryAttributeValue(string $attributeName, ?string $attributeHtml): ?string
    {
        $result = '';
        switch ($attributeName) {
            case 'short_description':
            case 'description':
                $result = $this->data->getReplaceData($attributeName);
                break;
            case 'image':
                $result = preg_replace(
                    '@(alt=["\'])[^"\']*(["\'])@s',
                    '${1}' . $this->data->getReplaceData('image_alt') . '${2}',
                    $attributeHtml
                );
                break;
        }

        return $result ?: $attributeHtml;
    }
}
