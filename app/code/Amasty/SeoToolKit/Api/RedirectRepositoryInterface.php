<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Api;

interface RedirectRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\SeoToolKit\Api\Data\RedirectInterface $redirect
     *
     * @return \Amasty\SeoToolKit\Api\Data\RedirectInterface
     */
    public function save(\Amasty\SeoToolKit\Api\Data\RedirectInterface $redirect);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\SeoToolKit\Api\Data\RedirectInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\SeoToolKit\Api\Data\RedirectInterface $redirect
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\SeoToolKit\Api\Data\RedirectInterface $redirect);

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
