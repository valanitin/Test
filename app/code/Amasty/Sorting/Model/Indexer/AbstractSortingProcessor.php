<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Indexer;

use Magento\Framework\Indexer\AbstractProcessor;

abstract class AbstractSortingProcessor extends AbstractProcessor
{
    public function markIndexerAsInvalid()
    {
        if ($this->isIndexerScheduled()) {
            parent::markIndexerAsInvalid();
        }
    }
}
