<?php

namespace LuxuryUnlimited\CustomCategories\Plugin;

use Magento\Catalog\Api\Data\CategoryTreeInterface;
use Magento\Catalog\Model\CategoryManagement as Subject;
use Magento\Catalog\Model\CategoryFactory;
use Exception;

class CategoryManagement
{
    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(CategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Plugin to Modify the Category Tree Structure
     *
     * @param Subject               $subject
     * @param CategoryTreeInterface $result
     * @param int|null              $rootCategoryId
     * @param int|null              $depth
     *
     * @return CategoryTreeInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetTree(Subject $subject, CategoryTreeInterface $result, $rootCategoryId = null, $depth = null)
    {
        try {
            return $this->addFakeCategories($result);
        } catch (Exception $exception) {
            return $result;
        }
    }

    /**
     *  Adding the View All Categories for the level 2
     *
     * @param mixed $category
     *
     * @return mixed
     */
    public function addFakeCategories($category)
    {
        if ($category->getChildrenData()) {
            foreach ($category->getChildrenData() as $childrenCat) {
                $this->addFakeCategories($childrenCat);
            }
            if ($category->getLevel() == 2) {
                $newCategory = $this->categoryFactory->create();
                $newCategory->setId($category->getId())
                            ->setParentId($category->getParentId())
                            ->setName('All ' . $category->getName())
                            ->setIsActive(1)
                            ->setPosition($category->getPosition())
                            ->setLevel($category->getLevel())
                            ->setProductCount($category->getProductCount())
                            ->setChildrenData([]);
                $newChildrenData = $category->getChildrenData();
                array_unshift($newChildrenData, $newCategory);
                $category->setChildrenData($newChildrenData);

            }

            return $category;
        }

        return $category;
    }
}
