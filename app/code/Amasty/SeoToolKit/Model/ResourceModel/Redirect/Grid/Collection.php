<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Model\ResourceModel\Redirect\Grid;

use Amasty\SeoToolKit\Api\Data\RedirectInterface;
use Amasty\SeoToolKit\Model\Redirect;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    /**
     * @var string
     */
    protected $document = Redirect::class;

    /**
     * @return $this|Collection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['stores' => $this->getTable(RedirectInterface::STORE_TABLE_NAME)],
            'main_table.redirect_id = stores.redirect_id',
            ['GROUP_CONCAT(store_id SEPARATOR ",") as store_ids']
        )->group('main_table.redirect_id');

        return $this;
    }
}
