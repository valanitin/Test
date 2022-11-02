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

class Cms extends \Magento\Framework\View\Element\Template implements \Plumrocket\Amp\Block\Page\Head\LdjsonInterface
{
    const DEFAULT_PAGE_TITLE = "Magento Cms Page";
    const DEFAULT_PAGE_CONTENT_HEADING = "Page Content Heading";
    const DEFAULT_PAGE_DESCRIPTION = "Default Description";

    const LOGO_IMAGE_WIDTH = 272;
    const LOGO_IMAGE_HEIGHT = 90;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_cmsPage;

    /**
     * Cms constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Cms\Model\Page                          $cmsPage
     * @param \Magento\Framework\App\DeploymentConfig          $deploymentConfig
     * @param \Magento\Framework\Stdlib\DateTime\DateTime      $date
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Page $cmsPage,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_cmsPage = $cmsPage;
        $this->_deploymentConfig = $deploymentConfig;
        $this->_date = $date;
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        return $this->_cmsPage && $this->_cmsPage->getId();
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

        $cmsPage = $this->_cmsPage;

        $logoBlock = $this->getLayout()->getBlock('amp.logo');
        $logo = $logoBlock ? $logoBlock->getLogoSrc() : '';

        if ($cmsPage->getTitle()) {
            $pageTitle = $cmsPage->getTitle();
        } elseif ($this->pageConfig->getTitle()->get()) {
            $pageTitle = $this->pageConfig->getTitle()->get();
        } else {
            $pageTitle = self::DEFAULT_PAGE_TITLE;
        }
        $pageCreatedAt = $cmsPage->getCreationTime() ? $cmsPage->getCreationTime() : '';
        if (!$pageCreatedAt) {
            $date = $this->_deploymentConfig->get('install/date');
            $pageCreatedAt = date('c', strtotime($date));
        }
        $pageUpdatedAt = $cmsPage->getUpdateTime() ? $cmsPage->getUpdateTime() : '';
        if (!$pageUpdatedAt) {
            $date = $this->_date->gmtDate('Y-m') . '-01';
            $pageUpdatedAt = date('c', strtotime($date));
        }
        $pageDescription = $this->pageConfig->getDescription() ? mb_substr($this->pageConfig->getDescription(), 0, 250, 'UTF-8') : self::DEFAULT_PAGE_DESCRIPTION;

        if ($cmsPage->getContentHeading()) {
            $pageContentHeading = $cmsPage->getContentHeading();
        } elseif($this->pageConfig->getTitle()->get()) {
            $pageContentHeading = $this->pageConfig->getTitle()->get();
        } else {
            $pageContentHeading = self::DEFAULT_PAGE_CONTENT_HEADING;
        }

        $json = [
            "@context" => "http://schema.org",
            "@type" => "Article",
            "author" => $siteName,
            "image" => [
                '@type' => 'ImageObject',
                'url' => $logo,
                'width' => 696,
                'height' => self::LOGO_IMAGE_HEIGHT,
            ],
            "name" => $pageTitle,
            "description" => $pageDescription,
            "datePublished" => $pageCreatedAt,
            "dateModified" => $pageUpdatedAt,
            "headline" => mb_substr($pageContentHeading, 0, 110, 'UTF-8'),
            "publisher" => [
                '@type' => 'Organization',
                'name' => $siteName,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $logo,
                ],
            ],
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id" => $this->getUrl(),
            ],
        ];

        return str_replace('\/', '/', json_encode($json));
    }
}
