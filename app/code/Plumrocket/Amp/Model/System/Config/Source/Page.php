<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\System\Config\Source;

class Page implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'cms_index_index' => __('Home Page'),
            'catalog_product_view' => __('Product Pages'),
            'catalog_category_view' => __('Category Pages'),
            'catalogsearch_result_index' => __('Catalog Search'),
            'cms_page_view' => __('CMS Pages'),
        ];
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->toArray() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label ];
        }

        return $result;
    }
}
