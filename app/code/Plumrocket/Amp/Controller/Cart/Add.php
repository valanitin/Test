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

namespace Plumrocket\Amp\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;

/**
 * Class Add
 *
 * TODO: remove after left support magento 2.2
 *
 * @deprecated since 2.6.0 - use \Plumrocket\Amp\Controller\Api\Cart\Add instead
 *
 * @package Plumrocket\Amp\Controller\Cart
 */
class Add extends \Magento\Checkout\Controller\Cart\Add
{
    const EVENT_GET_NAME = 'addedtocart';

    /**
     * Product that was added to cart
     * @var  \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * Has added to cart
     * @var bool
     */
    protected $_addedToCart;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $helperJson;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $helperCart;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $resolver;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

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
     * @param \Magento\Framework\Json\Helper\Data                $helperJson
     * @param \Magento\Checkout\Helper\Cart                      $helperCart
     * @param \Magento\Framework\Locale\ResolverInterface        $resolver
     * @param \Magento\Framework\Escaper                         $escaper
     * @param \Psr\Log\LoggerInterface                           $logger
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
        \Magento\Framework\Json\Helper\Data $helperJson,
        \Magento\Checkout\Helper\Cart $helperCart,
        \Magento\Framework\Locale\ResolverInterface $resolver,
        \Magento\Framework\Escaper $escaper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->dataHelper   = $dataHelper;
        $this->scopeConfig  = $scopeConfig;
        $this->helperJson   = $helperJson;
        $this->helperCart   = $helperCart;
        $this->resolver     = $resolver;
        $this->escaper      = $escaper;
        $this->logger       = $logger;

        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository
        );
    }

    /**
     * Override parent method
     * Set back redirect url to response
     *
     * @param null|string $backUrl
     *
     * @return \Magento\Framework\Controller\Result\Redirect|void
     */
    protected function goBack($backUrl = null, $product = null)
    {
        /**
         * Processing ajax requests
         */
        if ($this->getRequest()->isAjax()) {
            $result = [];

            if ($backUrl || $backUrl = $this->getBackUrl()) {
                $result['backUrl'] = $this->dataHelper->getCanonicalUrl(
                    $backUrl,
                    [\Plumrocket\Amp\Helper\Data::AMP_ONLY_OPTIONS_KEYWORD => 1, '_secure'=>true]
                );
            } else {
                if ($product && !$product->getIsSalable()) {
                    $result['product'] = [
                        'statusText' => __('Out of stock')
                    ];
                }
            }

            $this->getResponse()->representJson(
                $this->helperJson->jsonEncode($result)
            );
            return;
        }

        /**
         * If parameter enabled - After Adding a Product Redirect to Shopping Cart
         */
        if ($this->_addedToCart
            && $this->scopeConfig->getValue('checkout/cart/redirect_to_cart', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ) {

            $cartUrl = $this->helperCart->getCartUrl();
            $productUrl = $this->_redirect->getRefererUrl();

            if ($product && $product->getId()) {
                $productUrl = $this->dataHelper->getOnlyOptionsUrl($product);
            }

            $bodyHtml = '<html><head></head><body>
                 <script type="text/javascript">
                     try {
                         if (!parent.window.location.href) {window.location.href = "' . $productUrl . '";}
                         else {parent.window.location.href = "' . $cartUrl . '";}
                     } catch (e) {
                         window.location.href = "' . $productUrl . '";
                     }
                 </script>
             </body></html>';

            $this->getResponse()->setBody($bodyHtml)->sendResponse();
            return;
        }

        /**
         * Generate default redirect result
         * Used t parameter for resolving issue with FPC
         */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($backUrl || $backUrl = $this->getBackUrl($this->_redirect->getRefererUrl())) {
            $params = [];
            if (strpos($backUrl, '?') !== false) {
                $urlParts = explode('?', $backUrl);
                $backUrl = $urlParts[0];
                mb_parse_str($urlParts[1], $params);
            }

            $params['t'] = function_exists('microtime') ? microtime() : time();
            if ($this->_addedToCart) {
                $params['added_product'] = $this->getRequest()->getParam('product');
                $params['event'] = self::EVENT_GET_NAME;
            } else {
                unset($params['added_product'], $params['event']);
            }

            $backUrl .= ( (strpos($backUrl, '?') !== false) ? '&' : '?') .
                http_build_query($params);
            $resultRedirect->setUrl($backUrl);
        }

        return $resultRedirect;
    }

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /**
         * Checking request data
         */
        if (!$this->dataHelper->moduleEnabled()) {
            return $this->resultRedirectFactory->create()->setPath('noRoute');
        }

        $this->dataHelper->sanitizeHttpHeaders();
        $this->getResponse()->setHeader('Cache-Control', 'max-age=0, private, no-cache, no-store');

        $params = $this->getRequest()->getParams();

        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->resolver->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();

            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                return $this->goBack();
            }

            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();

            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );

            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $product->getName()
                    );
                    $this->messageManager->addSuccessMessage($message);
                }

                $this->_addedToCart = true;
                $backUrl = !empty($params['backUrl']) ? $params['backUrl'] : null;

                return $this->goBack($backUrl, $product);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice(
                    $this->escaper->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError(
                        $this->escaper->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);

            if (!$url) {
                $cartUrl = $this->helperCart->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }

            return $this->goBack($url);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            $this->logger->critical($e);
            return $this->goBack();
        }
    }
}