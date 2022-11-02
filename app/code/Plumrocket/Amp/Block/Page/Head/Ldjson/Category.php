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
 * @package     Plumrocket_Amp 2.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Page\Head\Ldjson;

use Magento\Store\Model\ScopeInterface;

class Category
    extends \Magento\Framework\View\Element\Template
    implements \Plumrocket\Amp\Block\Page\Head\LdjsonInterface
{
    const DEFAULT_CATEGORY_NAME = 'Magento Category Name';
    const DEFAULT_CATEGORY_DESCRIPTION = 'Magento Category Description';

    const LOGO_IMAGE_WIDTH = 272;
    const LOGO_IMAGE_HEIGHT = 90;

    const DEFAULT_THUMB_WIDTH = 696;
    const DEFAULT_THUMB_HEIGHT = 696;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_helper;

    /**
     * Category constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Plumrocket\Amp\Helper\Data                      $helper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Plumrocket\Amp\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_helper = $helper;
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        // Temporally disable article structure data for category page, because google add img size validator
        return false;

        $currentCategory = $this->_coreRegistry->registry('current_category');
        return $currentCategory && $currentCategory->getId();
    }

    /**
     * Retrieve string by JSON format according to http://schema.org requirements
     * @return string
     */
    public function getJson()
    {
        $siteName = $this->_scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE);
        if (!$siteName) {
            $siteName = 'Magento Store';
        }

        $logoBlock = $this->getLayout()->getBlock('amp.logo');
        $logo = $logoBlock ? $logoBlock->getLogoSrc() : '';

        $currentCategory = $this->_coreRegistry->registry('current_category');

        $categoryName = $currentCategory->getName() ? $currentCategory->getName() : self::DEFAULT_CATEGORY_NAME;
        $categoryDescription = $this->pageConfig->getDescription() ? mb_substr($this->pageConfig->getDescription(), 0, 250, 'UTF-8') : self::DEFAULT_CATEGORY_DESCRIPTION;
        $categoryCreatedAt = $currentCategory->getCreatedAt() ? $currentCategory->getCreatedAt() : '';
        $categoryUpdatedAt = $currentCategory->getUpdatedAt() ? $currentCategory->getUpdatedAt() : '';

        if ($this->pageConfig->getTitle()->get()) {
            $pageContentHeading = $this->pageConfig->getTitle()->get();
        } else {
            $pageContentHeading = $categoryName;
        }

        $categoryThumb = (string)$currentCategory->getImageUrl();
        if($categoryThumb) {
            $dataImageObject = array(
                '@type' => 'ImageObject',
                'url' => $categoryThumb,
                'width' => self::DEFAULT_THUMB_WIDTH,
                'height' => self::DEFAULT_THUMB_HEIGHT,
            );
        } else {
            $dataImageObject = array(
                '@type' => 'ImageObject',
                'url' => $logo,
                'width' => 696,
                'height' => self::LOGO_IMAGE_HEIGHT,
            );
        }

        /**
         * Set scheme JSON data
         */
        $json = array(
            "@context" => "http://schema.org",
            "@type" => "Article",
            "author" => $siteName,
            "image" => $dataImageObject,
            "name" => $categoryName,
            "description" => $categoryDescription,
            "datePublished" => $categoryCreatedAt,
            "dateModified" => $categoryUpdatedAt,
            "headline" => mb_substr($pageContentHeading, 0, 110, 'UTF-8'),
            "publisher" => array(
                '@type' => 'Organization',
                'name' => $siteName,
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $logo,
                ),
            ),
            "mainEntityOfPage" => array(
                "@type" => "WebPage",
                "@id" => $this->getUrl(),
            ),
        );

        return str_replace('\/', '/', json_encode($json));
    }
}