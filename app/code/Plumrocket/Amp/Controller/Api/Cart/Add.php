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

namespace Plumrocket\Amp\Controller\Api\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Store\Model\ScopeInterface;

class Add extends \Magento\Checkout\Controller\Cart\Add implements
    \Plumrocket\Amp\Model\MagentoTwoTwo\CsrfAwareActionInterface
{
    use \Plumrocket\Amp\Controller\ValidateForCsrfTrait;

    /**
     * Has added to cart
     *
     * @var bool
     */
    private $addedToCart;

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    private $helperCart;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $resolver;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Plumrocket\Amp\Helper\Cors
     */
    private $corsHelper;

    /**
     * @var \Zend_Filter_LocalizedToNormalized
     */
    private $filterLocalizedToNormalized;

    /**
     * @var \Plumrocket\Amp\Model\Result\AmpJsonFactory
     */
    private $ampResultFactory;

    /**
     * Add constructor.
     *
     * @param \Magento\Framework\App\Action\Context              $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session                    $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator     $formKeyValidator
     * @param CustomerCart                                       $cart
     * @param ProductRepositoryInterface                         $productRepository
     * @param \Plumrocket\Amp\Helper\Data                        $dataHelper
     * @param \Magento\Checkout\Helper\Cart                      $helperCart
     * @param \Magento\Framework\Locale\ResolverInterface        $resolver
     * @param \Magento\Framework\Escaper                         $escaper
     * @param \Psr\Log\LoggerInterface                           $logger
     * @param \Plumrocket\Amp\Helper\Cors                        $corsHelper
     * @param \Zend_Filter_LocalizedToNormalized                 $filterLocalizedToNormalized
     * @param \Plumrocket\Amp\Model\Result\AmpJsonFactory        $ampResultFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        \Plumrocket\Amp\Helper\Data $dataHelper,
        \Magento\Checkout\Helper\Cart $helperCart,
        \Magento\Framework\Locale\ResolverInterface $resolver,
        \Magento\Framework\Escaper $escaper,
        \Psr\Log\LoggerInterface $logger,
        \Plumrocket\Amp\Helper\Cors $corsHelper,
        \Zend_Filter_LocalizedToNormalized $filterLocalizedToNormalized,
        \Plumrocket\Amp\Model\Result\AmpJsonFactory $ampResultFactory
    ) {
        $this->dataHelper   = $dataHelper;
        $this->helperCart   = $helperCart;
        $this->resolver     = $resolver;
        $this->escaper      = $escaper;
        $this->logger       = $logger;
        $this->corsHelper   = $corsHelper;

        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository
        );
        $this->filterLocalizedToNormalized = $filterLocalizedToNormalized;
        $this->ampResultFactory = $ampResultFactory;
    }

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\AbstractResult
     */
    public function execute()
    {
        $resultAmpJson = $this->ampResultFactory->create();

        /**
         * Checking request data
         */
        if (! $this->dataHelper->moduleEnabled() || ! $this->corsHelper->verifyRequest()) {
            $resultAmpJson->setFormRedirect($this->_url->getUrl('noRoute'));
            return $resultAmpJson;
        }

        $params = $this->getRequest()->getParams();

        try {
            $params = $this->prepareParams($params);

            /**
             * Check product availability
             */
            if (! $product = $this->_initProduct()) {
                return $this->goAjaxBack($resultAmpJson);
            }

            $this->cart->addProduct($product, $params);
            $this->addRelatedProducts($this->getRequest()->getParam('related_product'));

            $this->cart->save();

            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );

            if (! $this->_checkoutSession->getNoCartRedirect(true)) {
                if (! $this->cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $product->getName()
                    );

                    if ($this->shouldRedirectToCart()) {
                        $this->messageManager->addSuccessMessage($message);
                    } else {
                        $resultAmpJson->addSuccessMessage($message);
                    }
                }

                $this->addedToCart = true;
                $backUrl = ! empty($params['backUrl']) ? $params['backUrl'] : null;

                return $this->goAjaxBack($resultAmpJson, $backUrl, $product);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $messages = array_unique(explode("\n", $e->getMessage()));
            foreach ($messages as $message) {
                $resultAmpJson->addErrorMessage(
                    $this->escaper->escapeHtml($message)
                );
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);

            return $this->goAjaxBack($resultAmpJson, $url);
        } catch (\Exception $e) {
            $resultAmpJson->addErrorMessage(__('We can\'t add this item to your shopping cart right now.'));
            $this->logger->critical($e);
            return $this->goAjaxBack($resultAmpJson);
        }

        return $this->goAjaxBack($resultAmpJson);
    }

    /**
     * @param \Plumrocket\Amp\Model\Result\AmpJson $resultAmpJson
     * @param null|string                          $backUrl
     * @param null|\Magento\Catalog\Model\Product  $product
     * @return \Plumrocket\Amp\Model\Result\AmpJson
     */
    private function goAjaxBack(\Plumrocket\Amp\Model\Result\AmpJson $resultAmpJson, $backUrl = null, $product = null)
    {
        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $resultAmpJson->setFormRedirect($backUrl);
        } elseif ($product && ! $product->getIsSalable()) {
            $resultAmpJson->addErrorMessage(__('Out of stock'));
        }

        if ($this->addedToCart && $this->shouldRedirectToCart()) {
            $cartUrl = $this->helperCart->getCartUrl();
            $resultAmpJson->setFormRedirect($cartUrl);
        }

        return $resultAmpJson;
    }

    /**
     * @param array $params
     * @return array
     */
    private function prepareParams(array $params)
    {
        if (isset($params['qty'])) {
            $this->filterLocalizedToNormalized->setOptions(['locale' => $this->resolver->getLocale()]);
            $params['qty'] = $this->filterLocalizedToNormalized->filter($params['qty']);
        }

        return $params;
    }

    /**
     * @param $related
     * @return $this
     */
    private function addRelatedProducts($related)
    {
        if (! empty($related)) {
            $this->cart->addProductsByIds(explode(',', $related));
        }

        return $this;
    }

    /**
     * Is redirect should be performed after the product was added to cart.
     *
     * @return bool
     */
    private function shouldRedirectToCart()
    {
        return $this->_scopeConfig->isSetFlag(
            'checkout/cart/redirect_to_cart',
            ScopeInterface::SCOPE_STORE
        );
    }
}
