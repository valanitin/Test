<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Model\Config\Backend;

class ViewAllCanonical extends \Magento\Framework\App\Config\Value
{
    const CATALOG_FRONTEND_LIST_ALLOW_ALL = 'catalog/frontend/list_allow_all';

    /**
     * @return \Magento\Framework\App\Config\Value
     */
    public function beforeSave()
    {
        if ($this->isValueChanged() && $this->isInvalidData()) {
            $this->getData('messageManager')->addWarningMessage(
                __('Use Canonical to ‘View All‘ for Paginated Pages setting can’t be saved in ‘Yes’ state until '
                    . 'the option to display all products per category is disabled under '
                    . 'Stores-Catalog-Storefront-Allow All Products per Page.')
            );

            $this->setValue('0');
        }

        return parent::beforeSave();
    }

    private function isInvalidData(): bool
    {
        return $this->getValue() && !$this->_config->isSetFlag(self::CATALOG_FRONTEND_LIST_ALLOW_ALL);
    }
}
