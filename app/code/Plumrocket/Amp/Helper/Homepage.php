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

namespace Plumrocket\Amp\Helper;

class Homepage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var \Plumrocket\Amp\Model\Image\Compressor
     */
    private $imageCompressor;

    /**
     * @param \Magento\Framework\App\Helper\Context                           $context
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Plumrocket\Amp\Model\Image\Compressor                          $imageCompressor
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Plumrocket\Amp\Model\Image\Compressor $imageCompressor
    ) {
        $this->_storeManager = $storeManager;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context);
        $this->imageCompressor = $imageCompressor;
    }

    /**
     * Retrieve collection of categories
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getTopLevelCategories()
    {
        $rootCategoryId = $this->_storeManager->getStore()->getRootCategoryId();

        return $this->_categoryCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->setOrder('position', 'ASC')
            ->addAttributeToFilter('level', 2)
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('include_in_menu', 1)
            ->addFieldToFilter('path', array('like' => "%/{$rootCategoryId}/%"))
            ->addIsActiveFilter();
    }

    /**
     * Retrieve base url for media resources
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param int                             $containerWidth
     * @param int                             $containerHeight
     * @param string                          $default
     * @return mixed
     */
    public function getResizedImage($category, $containerWidth, $containerHeight, $default)
    {
        $ampHomepageImage = $this->imageCompressor->getHomePageImage(
            $category->getData('amp_homepage_image'),
            $containerWidth,
            $containerHeight
        );

        if (! $ampHomepageImage) {
            return $default;
        }

        return $ampHomepageImage['url'];
    }
}
