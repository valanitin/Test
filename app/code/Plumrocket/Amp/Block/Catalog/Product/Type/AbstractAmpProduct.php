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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Catalog\Product\Type;

class AbstractAmpProduct extends \Magento\Catalog\Block\Product\View
{
    const MESSAGE_BLOCK_NAME = 'amp.product.form.message';

    /**
     * @var \Plumrocket\Amp\Helper\Form
     */
    private $ampFormHelper;

    /**
     * @var \Plumrocket\Amp\Model\State
     */
    private $stateModel;

    /**
     * AbstractAmpProduct constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context              $context
     * @param \Magento\Framework\Url\EncoderInterface             $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface            $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils               $string
     * @param \Magento\Catalog\Helper\Product                     $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface           $localeFormat
     * @param \Magento\Customer\Model\Session                     $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface     $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface   $priceCurrency
     * @param \Plumrocket\Amp\Helper\Form                         $ampFormHelper
     * @param \Plumrocket\Amp\Model\State                         $stateModel
     * @param array                                               $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Plumrocket\Amp\Helper\Form $ampFormHelper,
        \Plumrocket\Amp\Model\State $stateModel,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
        $this->ampFormHelper = $ampFormHelper;
        $this->stateModel = $stateModel;
    }

    /**
     * Retrieve add to cart url by product
     *
     * @param int $productId
     * @return string
     */
    public function getAmpAddToCartUrl($productId)
    {
        return $this->ampFormHelper->getAmpAddToCartUrl($productId);
    }

    /**
     * @return string
     */
    public function getFormMessageEvents()
    {
        $this->getAmpMessagesBlock()
             ->addSubmitSuccessAction(
                 'AMP.setState(' . $this->stateModel->createAmpJsSetter($this->getProductIdStatePath(), 0) . ')',
                 'hideLoader'
             )
             ->addSubmitErrorAction(
                 'AMP.setState(' . $this->stateModel->createAmpJsSetter($this->getProductIdStatePath(), 0) . ')',
                 'hideLoader'
             );

        return $this->getAmpMessagesBlock()->getFormMessageEvents();
    }

    /**
     * @return bool|\Plumrocket\Amp\Block\Page\Form\Product\Message
     */
    protected function getAmpMessagesBlock() // @codingStandardsIgnoreLine
    {
        return $this->getLayout()->getBlock(self::MESSAGE_BLOCK_NAME);
    }

    /**
     * @return array
     */
    public function getProductIdStatePath()
    {
        return $this->getAmpMessagesBlock()->getProductIdStatePath();
    }
}
