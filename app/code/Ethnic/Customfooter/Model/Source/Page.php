<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ethnic\Customfooter\Model\Source;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Option\ArrayInterface;
use Psr\Log\LoggerInterface;

/**
 * Catalog category landing page attribute source
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Page extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{


    public function __construct(
        PageRepositoryInterface $pageRepositoryInterface,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
            $optionArray = [];
        try {
            $pages = $this->getCmsPageCollection();
            if ($pages instanceof LocalizedException) {
                throw $pages;
            }
            $cnt = 0;
            foreach ($pages as $page) {
                $optionArray[$cnt]['value'] = $page->getId();
                $optionArray[$cnt++]['label'] = $page->getTitle();
            }
        } catch (LocalizedException $e) {
            ObjectManager::getInstance()->get(LoggerInterface::class)->info($e->getMessage());
        } catch (\Exception $e) {
            ObjectManager::getInstance()->get(LoggerInterface::class)->info($e->getMessage());
        }

        return $optionArray;
    }
        /**
     * @return \Exception|PageInterface[]|LocalizedException
     */
    public function getCmsPageCollection()
    {
        $searchCriteria = $searchCriteria = $this->searchCriteriaBuilder->create();
        try {
            $collection = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            return $e;
        }
        return $collection;
    }
}
