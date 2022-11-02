<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Plugin\View\Page;

use \Amasty\Meta\Helper\Data;

class Config
{
    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $data;

    public function __construct(
        Data $dataHelper
    ) {
        $this->data = $dataHelper;
    }

    /**
     * @param $pageConfig
     * @param $metaTitle
     * @return array
     */
    public function beforeSetMetaTitle(
        $pageConfig,
        $metaTitle
    ) {
        $replacedMetaTitle = $this->data->getReplaceData('meta_title');

        if ($replacedMetaTitle) {
            $metaTitle = $replacedMetaTitle;
        }

        return [$metaTitle];
    }

    /**
     * @param $pageConfig
     * @param $metaKeywords
     * @return bool|mixed|string
     */
    public function afterGetKeywords(
        $pageConfig,
        $metaKeywords
    ) {
        $replacedMetaKeywords = $this->data->getReplaceData('meta_keywords');

        if ($replacedMetaKeywords) {
            $metaKeywords = $replacedMetaKeywords;
        }

        return $metaKeywords;
    }

    /**
     * @param $pageConfig
     * @param $metaDescription
     * @return bool|mixed|string
     */
    public function afterGetDescription(
        $pageConfig,
        $metaDescription
    ) {
        $replacedMetaDesc = $this->data->getReplaceData('meta_description');

        if ($replacedMetaDesc) {
            $metaDescription = $replacedMetaDesc;
        }

        return $metaDescription;
    }

    /**
     * @param $pageConfig
     * @param $metaRobots
     * @return bool|mixed|string
     */
    public function afterGetRobots(
        $pageConfig,
        $metaRobots
    ) {
        $replacedMetaRobots = $this->data->getReplaceData('meta_robots');

        if ($replacedMetaRobots) {
            $metaRobots = $replacedMetaRobots;
        }

        return $metaRobots;
    }
}
