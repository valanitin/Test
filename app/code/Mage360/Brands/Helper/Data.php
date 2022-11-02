<?php
/**
 * Mage360
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mage360.com license that is
 * available through the world-wide-web at this URL:
 * https://www.Mage360.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mage360
 * @package     Mage360_Brands
 * @copyright   Copyright (c) Mage360 (https://www.Mage360.com/)
 * @license     https://www.Mage360.com/LICENSE.txt
 */

namespace Mage360\Brands\Helper;

/**
 * Class Data
 * @package Mage360\Brands\Helper
 */
class Data
{
    const FILTER_TYPE_SLIDER = 'slider';
    const FILTER_TYPE_LIST   = 'list';

    /** @var \Mage360\Brands\Model\Layer\Filter */
    protected $filterModel;

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnabled($storeId = null)
    {
        return $this->getConfigGeneral('enable', $storeId) && $this->isModuleOutputEnabled();
    }

    /**
     * @param $filters
     * @return mixed
     */
    public function getLayerConfiguration($filters)
    {
        $filterParams = $this->_getRequest()->getParams();
        foreach ($filterParams as $key => $param) {
            $filterParams[$key] = htmlspecialchars($param);
        }

        $config = new \Magento\Framework\DataObject([
            'active'             => array_keys($filterParams),
            'params'             => $filterParams,
            'isCustomerLoggedIn' => $this->objectManager->create('Magento\Customer\Model\Session')->isLoggedIn(),
            'isAjax'             => $this->ajaxEnabled()
        ]);

        $this->getFilterModel()->getLayerConfiguration($filters, $config);

        return self::jsonEncode($config->getData());
    }

    /**
     * @return \Mage360\Brands\Model\Layer\Filter
     */
    public function getFilterModel()
    {
        if (!$this->filterModel) {
            $this->filterModel = $this->objectManager->create('Mage360\Brands\Model\Layer\Filter');
        }

        return $this->filterModel;
    }

    /**
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }
}
