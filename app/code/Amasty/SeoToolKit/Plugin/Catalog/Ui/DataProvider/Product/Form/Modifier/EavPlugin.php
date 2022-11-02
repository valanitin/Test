<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Amasty\SeoToolKit\Model\RegistryConstants;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav as NativeModifier;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product;

class EavPlugin
{
    const ATTRIBUTES_FOR_ADD = [
        RegistryConstants::AMTOOLKIT_ROBOTS,
        RegistryConstants::AMTOOLKIT_CANONICAL,
    ];

    const SEARCH_ENGINE_OPTIMIZATION = 'search-engine-optimization';

    const MIGRATION_META_INFORMATION = 'migration-meta-information';

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var array|null
     */
    private $attributes = null;

    public function __construct(
        LocatorInterface $locator,
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->locator = $locator;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function afterModifyMeta(NativeModifier $modifier, array $meta): array
    {
        foreach ($this->getAttributes() as $attribute) {
            $codeWithPrefix = NativeModifier::CONTAINER_PREFIX . $attribute->getAttributeCode();

            if (isset($meta[self::SEARCH_ENGINE_OPTIMIZATION])) {
                $meta[self::SEARCH_ENGINE_OPTIMIZATION]['children'][$codeWithPrefix] =
                    $this->createAttributeContainer($attribute, $modifier, self::SEARCH_ENGINE_OPTIMIZATION);
            } elseif (isset($meta[self::MIGRATION_META_INFORMATION])) {
                $meta[self::MIGRATION_META_INFORMATION]['children'][$codeWithPrefix] =
                    $this->createAttributeContainer($attribute, $modifier, self::MIGRATION_META_INFORMATION);
            }
        }

        return $meta;
    }

    private function createAttributeContainer(
        ProductAttributeInterface $attribute,
        NativeModifier $modifier,
        $groupCode
    ): array {
        $attributeContainer = $modifier->setupAttributeContainerMeta($attribute);
        $attributeContainer = $modifier->addContainerChildren(
            $attributeContainer,
            $attribute,
            $groupCode,
            $attribute->getSortOrder()
        );

        return $attributeContainer;
    }

    private function getAttributes(): array
    {
        if ($this->attributes === null) {
            try {
                $this->searchCriteriaBuilder->addFilter('attribute_code', self::ATTRIBUTES_FOR_ADD, 'in');
                $this->attributes = $this->attributeRepository
                    ->getList(Product::ENTITY, $this->searchCriteriaBuilder->create())
                    ->getItems();
            } catch (NoSuchEntityException $entityException) {
                $this->attributes = [];
            }
        }

        return $this->attributes;
    }

    public function afterModifyData(NativeModifier $modifier, array $data): array
    {
        foreach ($this->getAttributes() as $attribute) {
            $productId = $this->locator->getProduct()->getId();

            $data[$productId][NativeModifier::DATA_SOURCE_DEFAULT][$attribute->getAttributeCode()] =
                $modifier->setupAttributeData($attribute);
        }

        return $data;
    }
}
