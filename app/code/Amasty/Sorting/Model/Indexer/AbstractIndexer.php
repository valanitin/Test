<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Indexer;

use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Registry;

class AbstractIndexer implements IndexerActionInterface, MviewActionInterface
{
    /**
     * @var \Amasty\Sorting\Api\IndexedMethodInterface
     */
    private $indexBuilder;

    /**
     * @var \Amasty\Sorting\Helper\Data
     */
    private $helper;

    /**
     * @var CacheTypeListInterface
     */
    private $cache;

    /**
     * @var CacheContext
     */
    private $cacheContext;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        \Amasty\Sorting\Api\IndexedMethodInterface $indexBuilder,
        \Amasty\Sorting\Helper\Data $helper,
        CacheTypeListInterface $cache,
        CacheContext $cacheContext,
        ManagerInterface $eventManager,
        Registry $registry
    ) {
        $this->indexBuilder = $indexBuilder;
        $this->helper = $helper;
        $this->cache = $cache;
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
        $this->registry = $registry;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $this->executeList($ids);
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        // do full reindex if method is not disabled
        if (!$this->helper->isMethodDisabled($this->indexBuilder->getMethodCode())
            && !$this->registry->registry('reindex_' . $this->indexBuilder->getMethodCode())
        ) {
            $this->indexBuilder->reindex();
            $this->cacheContext->registerTags(
                ['sorted_by_' . $this->indexBuilder->getMethodCode()]
            );
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
            $this->registry->register('reindex_' . $this->indexBuilder->getMethodCode(), true);
        }
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function executeList(array $ids)
    {
        if (!$ids) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Could not rebuild index for empty products array')
            );
        }
        $this->doExecuteList($ids);
    }

    /**
     * Execute partial indexation by ID list. Template method
     *
     * @param int[] $ids
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function doExecuteList($ids)
    {
        $this->executeFull();
    }

    /**
     * Execute partial indexation by ID. Template method
     *
     * @param int $id
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function doExecuteRow($id)
    {
        $this->executeFull();
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function executeRow($id)
    {
        if (!$id) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t rebuild the index for an undefined product.')
            );
        }
        $this->doExecuteRow($id);
    }
}
