<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Hreflang;

interface DataProviderInterface
{
    /**
     * @param int $currentStoreId
     * @param array|null $entityIds
     * @return array
     */
    public function get($currentStoreId, array $entityIds = null);
}
