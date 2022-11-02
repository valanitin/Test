<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_SeoCore
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SeoCore\Plugin\Model\Category;

/**
 * Class DataProvider
 * @package Bss\SeoCore\Plugin\Model\Category
 */
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * DataProvider constructor.
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param \Magento\Catalog\Model\Category\DataProvider $subject
     * @param array $result
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterPrepareMeta(\Magento\Catalog\Model\Category\DataProvider $subject, $result)
    {
        $meta = array_replace_recursive($result, $this->_prepareFieldsMeta(
            $this->_getFieldsMap(),
            $subject->getAttributesMeta($this->eavConfig->getEntityType('catalog_category'))
        ));
        return $meta;
    }

    /**
     * @param array $fieldsMap
     * @param array $fieldsMeta
     * @return array
     */
    public function _prepareFieldsMeta($fieldsMap, $fieldsMeta)
    {
        $result = [];
        $fieldSet = 'search_engine_optimization';
        if (!isset($fieldsMap[$fieldSet])) {
            return $result;
        }
        foreach ($fieldsMap[$fieldSet] as $field) {
            if (isset($fieldsMeta[$field]) && $field == 'seo_h1_title') {
                $fieldsMeta[$field]['sortOrder'] = 610;
                $result[$fieldSet]['children'][$field]['arguments']['data']['config'] = $fieldsMeta[$field];
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function _getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['search_engine_optimization'][] = 'seo_h1_title';
        return $fields;
    }
}
