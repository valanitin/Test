<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Api\Data;

interface ConfigInterface
{
    const CONFIG_ID = 'config_id';
    const CATEGORY_ID = 'category_id';
    const STORE_ID = 'store_id';
    const IS_CUSTOM = 'is_custom';
    const CUSTOM_URL = 'custom_url';
    const PRIORITY = 'priority';
    const CUSTOM_META_TITLE = 'custom_meta_title';
    const CUSTOM_META_KEYWORDS = 'custom_meta_keywords';
    const CUSTOM_META_DESCRIPTION = 'custom_meta_description';
    const CUSTOM_CANONICAL_URL = 'custom_canonical_url';
    const CUSTOM_ROBOTS = 'custom_robots';
    const CUSTOM_H1_TAG = 'custom_h1_tag';
    const CUSTOM_IN_PAGE_TEXT = 'custom_in_page_text';
    const CAT_META_TITLE = 'cat_meta_title';
    const CAT_META_DESCRIPTION = 'cat_meta_description';
    const CAT_META_KEYWORDS = 'cat_meta_keywords';
    const CAT_H1_TAG = 'cat_h1_tag';
    const CAT_DESCRIPTION = 'cat_description';
    const CAT_IMAGE_ALT = 'cat_image_alt';
    const CAT_IMAGE_TITLE = 'cat_image_title';
    const CAT_AFTER_PRODUCT_TEXT = 'cat_after_product_text';
    const PRODUCT_META_TITLE = 'product_meta_title';
    const PRODUCT_META_KEYWORDS = 'product_meta_keywords';
    const PRODUCT_META_DESCRIPTION = 'product_meta_description';
    const PRODUCT_H1_TAG = 'product_h1_tag';
    const PRODUCT_SHORT_DESCRIPTION = 'product_short_description';
    const PRODUCT_DESCRIPTION = 'product_description';
    const SUB_PRODUCT_META_TITLE = 'sub_product_meta_title';
    const SUB_PRODUCT_META_KEYWORDS = 'sub_product_meta_keywords';
    const SUB_PRODUCT_META_DESCRIPTION = 'sub_product_meta_description';
    const SUB_PRODUCT_H1_TAG = 'sub_product_h1_tag';
    const SUB_PRODUCT_SHORT_DESCRIPTION = 'sub_product_short_description';
    const SUB_PRODUCT_DESCRIPTION = 'sub_product_description';
    const IS_BRAND_CONFIG = 'is_brand_config';
    const BRAND_META_TITLE = 'brand_meta_title';
    const BRAND_META_DESCRIPTION = 'brand_meta_description';
    const BRAND_META_KEYWORDS = 'brand_meta_keywords';
    const BRAND_H1_TAG = 'brand_h1_tag';
    const BRAND_DESCRIPTION = 'brand_description';
    const BRAND_AFTER_PRODUCT_TEXT = 'brand_after_product_text';
    const TABLE_NAME = 'amasty_meta_config';

    /**
     * @return int
     */
    public function getConfigId();

    /**
     * @param int $configId
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setConfigId($configId);

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $categoryId
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCategoryId($categoryId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getIsCustom();

    /**
     * @param int $isCustom
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setIsCustom($isCustom);

    /**
     * @return string|null
     */
    public function getCustomUrl();

    /**
     * @param string|null $customUrl
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCustomUrl($customUrl);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setPriority($priority);

    /**
     * @return string|null
     */
    public function getCustomMetaTitle();

    /**
     * @param string|null $customMetaTitle
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCustomMetaTitle($customMetaTitle);

    /**
     * @return string|null
     */
    public function getCustomMetaKeywords();

    /**
     * @param string|null $customMetaKeywords
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCustomMetaKeywords($customMetaKeywords);

    /**
     * @return string|null
     */
    public function getCustomMetaDescription();

    /**
     * @param string|null $customMetaDescription
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCustomMetaDescription($customMetaDescription);

    /**
     * @return string|null
     */
    public function getCustomCanonicalUrl();

    /**
     * @param string|null $customCanonicalUrl
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCustomCanonicalUrl($customCanonicalUrl);

    /**
     * @return int
     */
    public function getCustomRobots();

    /**
     * @param int $customRobots
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCustomRobots($customRobots);

    /**
     * @return string|null
     */
    public function getCustomH1Tag();

    /**
     * @param string|null $customH1Tag
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCustomH1Tag($customH1Tag);

    /**
     * @return string|null
     */
    public function getCustomInPageText();

    /**
     * @param string|null $customInPageText
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCustomInPageText($customInPageText);

    /**
     * @return string|null
     */
    public function getCatMetaTitle();

    /**
     * @param string|null $catMetaTitle
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCatMetaTitle($catMetaTitle);

    /**
     * @return string|null
     */
    public function getCatMetaDescription();

    /**
     * @param string|null $catMetaDescription
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCatMetaDescription($catMetaDescription);

    /**
     * @return string|null
     */
    public function getCatMetaKeywords();

    /**
     * @param string|null $catMetaKeywords
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCatMetaKeywords($catMetaKeywords);

    /**
     * @return string|null
     */
    public function getCatH1Tag();

    /**
     * @param string|null $catH1Tag
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCatH1Tag($catH1Tag);

    /**
     * @return string|null
     */
    public function getCatDescription();

    /**
     * @param string|null $catDescription
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCatDescription($catDescription);

    /**
     * @return string|null
     */
    public function getCatImageAlt();

    /**
     * @param string|null $catImageAlt
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCatImageAlt($catImageAlt);

    /**
     * @return string|null
     */
    public function getCatImageTitle();

    /**
     * @param string|null $catImageTitle
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCatImageTitle($catImageTitle);

    /**
     * @return string|null
     */
    public function getCatAfterProductText();

    /**
     * @param string|null $catAfterProductText
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setCatAfterProductText($catAfterProductText);

    /**
     * @return string|null
     */
    public function getProductMetaTitle();

    /**
     * @param string|null $productMetaTitle
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setProductMetaTitle($productMetaTitle);

    /**
     * @return string|null
     */
    public function getProductMetaKeywords();

    /**
     * @param string|null $productMetaKeywords
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setProductMetaKeywords($productMetaKeywords);

    /**
     * @return string|null
     */
    public function getProductMetaDescription();

    /**
     * @param string|null $productMetaDescription
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setProductMetaDescription($productMetaDescription);

    /**
     * @return string|null
     */
    public function getProductH1Tag();

    /**
     * @param string|null $productH1Tag
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setProductH1Tag($productH1Tag);

    /**
     * @return string|null
     */
    public function getProductShortDescription();

    /**
     * @param string|null $productShortDescription
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setProductShortDescription($productShortDescription);

    /**
     * @return string|null
     */
    public function getProductDescription();

    /**
     * @param string|null $productDescription
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setProductDescription($productDescription);

    /**
     * @return string|null
     */
    public function getSubProductMetaTitle();

    /**
     * @param string|null $subProductMetaTitle
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setSubProductMetaTitle($subProductMetaTitle);

    /**
     * @return string|null
     */
    public function getSubProductMetaKeywords();

    /**
     * @param string|null $subProductMetaKeywords
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setSubProductMetaKeywords($subProductMetaKeywords);

    /**
     * @return string|null
     */
    public function getSubProductMetaDescription();

    /**
     * @param string|null $subProductMetaDescription
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setSubProductMetaDescription($subProductMetaDescription);

    /**
     * @return string|null
     */
    public function getSubProductH1Tag();

    /**
     * @param string|null $subProductH1Tag
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setSubProductH1Tag($subProductH1Tag);

    /**
     * @return string|null
     */
    public function getSubProductShortDescription();

    /**
     * @param string|null $subProductShortDescription
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setSubProductShortDescription($subProductShortDescription);

    /**
     * @return string|null
     */
    public function getSubProductDescription();

    /**
     * @param string|null $subProductDescription
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setSubProductDescription($subProductDescription);

    /**
     * @return bool
     */
    public function getIsBrandConfig();

    /**
     * @param bool $brandMetaTitle
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setIsBrandConfig($isBrandConfig);

    /**
     * @return string|null
     */
    public function getBrandMetaTitle();

    /**
     * @param string|null $brandMetaTitle
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setBrandMetaTitle($brandMetaTitle);

    /**
     * @return string|null
     */
    public function getBrandMetaDescription();

    /**
     * @param string|null $brandMetaDescription
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setBrandMetaDescription($brandMetaDescription);

    /**
     * @return string|null
     */
    public function getBrandMetaKeywords();

    /**
     * @param string|null $brandMetaKeywords
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setBrandMetaKeywords($brandMetaKeywords);

    /**
     * @return string|null
     */
    public function getBrandH1Tag();

    /**
     * @param string|null $brandH1Tag
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setBrandH1Tag($brandH1Tag);

    /**
     * @return string|null
     */
    public function getBrandDescription();

    /**
     * @param string|null $brandDescription
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setBrandDescription($brandDescription);

    /**
     * @return string|null
     */
    public function getBrandAfterProductText();

    /**
     * @param string|null $brandAfterProductText
     *
     * @return \Amasty\Meta\Api\Data\ConfigInterface
     */
    public function setBrandAfterProductText($brandAfterProductText);
}
