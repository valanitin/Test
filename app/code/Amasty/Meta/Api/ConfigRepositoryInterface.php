<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Api;

/**
 * @api
 */
interface ConfigRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Meta\Api\Data\ConfigInterface $config
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function save(\Amasty\Meta\Api\Data\ConfigInterface $config);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\Meta\Api\Data\ConfigInterface $config
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Meta\Api\Data\ConfigInterface $config);

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
