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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Page\Head\Ldjson;

use Magento\Store\Model\ScopeInterface;

class Product extends \Magento\Catalog\Block\Product\AbstractProduct implements
    \Plumrocket\Amp\Block\Page\Head\LdjsonInterface
{
    /**
     * Default values
     */
    const DEFAULT_PRODUCT_NAME = 'Product name';
    const DEFAULT_PRODUCT_SHORT_DESCRIPTION = 'Product short description';
    const DEFAULT_PRODUCT_STATUS = 'http://schema.org/OutOfStock';
    const DEFAULT_PRODUCT_PRICE_CURRENCY = 'USD';
    const DEFAULT_PRODUCT_AGGREGATE_RATING_VALUE = 0;
    const DEFAULT_PRODUCT_AGGREGATE_RATING_REVIEW = 0;

    const PRODUCT_NAME_MAX_LEN = 99;
    const PRODUCT_SHORT_DESCRIPTION_MAX_LEN = 500;

    const PRODUCT_IMAGE_WIDTH = 250;
    const PRODUCT_IMAGE_HEIGHT = 250;

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $ratingFactory;

    /**
     * Product constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Plumrocket\Amp\Helper\Data            $helper
     * @param \Magento\Review\Model\RatingFactory    $ratingFactory
     * @param array                                  $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Plumrocket\Amp\Helper\Data $helper,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->ratingFactory = $ratingFactory;
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        $product = $this->getProduct();
        return $product && $product->getId();
    }

    /**
     * Retrieve string by JSON format according to http://schema.org requirements
     * @return string
     */
    public function getJson()
    {
        /**
         * Get product and store objects
         */
        $product = $this->getProduct();

        /**
         * Set product default values
         */
        $productName = self::DEFAULT_PRODUCT_NAME;
        $productShortDescription = self::DEFAULT_PRODUCT_SHORT_DESCRIPTION;
        $productStatus = self::DEFAULT_PRODUCT_STATUS;
        $productPriceCurrency = self::DEFAULT_PRODUCT_PRICE_CURRENCY;

        /**
         * Get product name
         */
        if ($product->getName()) {
            $productName = $this->escapeHtml(mb_substr($product->getName(), 0, self::PRODUCT_NAME_MAX_LEN, 'UTF-8'));
        }

        /**
         * Get product image
         */
        $productImage = $this->_imageHelper->init($product, 'product_page_image_small')
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepTransparency(true)
            ->keepFrame(false)
            ->resize(self::PRODUCT_IMAGE_WIDTH, self::PRODUCT_IMAGE_HEIGHT)
            ->getUrl();

        /**
         * Get product description
         */
        if (strlen($product->getShortDescription())) {
            $productShortDescription = $this->escapeHtml(
                mb_substr(
                    strip_tags($product->getShortDescription()),
                    0,
                    self::PRODUCT_SHORT_DESCRIPTION_MAX_LEN,
                    'UTF-8'
                )
            );
        }

        $json = [
            '@context'        => 'http://schema.org/',
            '@type'           => 'Product',
            'name'            => $productName,
            'image'           => $productImage,
            'description'     => $productShortDescription,
        ];

        /**
         * @var \Magento\Review\Model\Rating $rating
         */
        $rating = $this->ratingFactory->create();
        $rating->getEntitySummary($product->getId(), true);

        if (is_object($product->getRatingSummary())) {
            $reviewInformationObject = $product->getRatingSummary();
        } else { // since magento 2.3.3
            $reviewInformationObject = $product;
        }

        $ratingValue = $reviewInformationObject->getRatingSummary();
        $reviewCount = $reviewInformationObject->getReviewsCount();

        if ($ratingValue && $reviewCount) {
            $json += [
                'aggregateRating' => [
                    '@type'       => 'AggregateRating',
                    'bestRating'  => 100,
                    'ratingValue' => $ratingValue,
                    'reviewCount' => $reviewCount,
                ],
            ];
        }

        if ($product->getSpecialPrice()) {
            if ($product->isInStock()) {
                $productStatus = 'http://schema.org/InStock';
            }

            $siteName = $this->_scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE);
            if (!$siteName) {
                $siteName = 'Magento Store';
            }

            $json += [
                'offers' => [
                    '@type'           => 'Offer',
                    'priceCurrency'   => $this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
                    'price'           => number_format((float) $product->getSpecialPrice(), 2),
                    'priceValidUntil' => $product->getSpecialToDate(),
                    'url'             => $this->_helper->removeRequestParam($product->getProductUrl(), 'amp'),
                    'availability'    => $productStatus,
                    'seller'          => [
                        '@type' => 'Organization',
                        'name'  => $siteName,
                    ],
                ],
            ];
        }

        return str_replace('\/', '/', json_encode($json));
    }
}
