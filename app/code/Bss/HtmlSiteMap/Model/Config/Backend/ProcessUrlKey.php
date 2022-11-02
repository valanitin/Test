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
 * @package    BSS_HtmlSiteMap
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\HtmlSiteMap\Model\Config\Backend;


/**
 * Class ProcessUrlKey
 * @package Bss\HtmlSiteMap\Model\Config\Backend
 */
class ProcessUrlKey extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Bss\HtmlSiteMap\Helper\Data
     */
    private $dataHelper;

    /**
     * ProcessUrlKey constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Bss\HtmlSiteMap\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Bss\HtmlSiteMap\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return \Magento\Framework\App\Config\Value
     */
    public function beforeSave()
    {
        /* @var array $value */
        $value = $this->getValue();
        if ($value) {
            $value = chop($value);
            $value = preg_replace("/\r\n|\r|\n/", ' ', $value);
            $value = preg_replace('/[\s]+/mu', ' ', $value);
            $value = trim($value);
            $value = rtrim($value, ",");
            $value = $this->dataHelper->createSlugByString($value);
        } else {
            $value = \Bss\HtmlSiteMap\Helper\Data::DEFAULT_URL_KEY;
        }
        $this->setValue($value);
        return parent::beforeSave();
    }
}
