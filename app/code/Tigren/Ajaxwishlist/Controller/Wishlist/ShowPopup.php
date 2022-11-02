<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Ajaxwishlist\Controller\Wishlist;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Tigren\Ajaxsuite\Helper\Data;

/**
 * Class ShowPopup
 *
 * @package Tigren\Ajaxwishlist\Controller\Wishlist
 */
class ShowPopup extends Action
{
    /**
     * @var \Tigren\Ajaxwishlist\Helper\Data
     */
    protected $_ajaxWishlistHelper;
    /**
     * @var Data
     */
    protected $_ajaxSuiteHelper;
    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;
    /**
     * @var Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * ShowPopup constructor.
     *
     * @param Context $context
     * @param \Tigren\Ajaxwishlist\Helper\Data $ajaxWishlistHelper
     * @param Data $ajaxSuiteHelper
     * @param ProductRepositoryInterface $productRepository
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        \Tigren\Ajaxwishlist\Helper\Data $ajaxWishlistHelper,
        Data $ajaxSuiteHelper,
        ProductRepositoryInterface $productRepository,
        Registry $registry
    )
    {
        parent::__construct($context);
        $this->_ajaxWishlistHelper = $ajaxWishlistHelper;
        $this->_productRepository = $productRepository;
        $this->_coreRegistry = $registry;
        $this->_ajaxSuiteHelper = $ajaxSuiteHelper;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws LocalizedException
     */
    public function execute()
    {
        $result = [];
        $params = $this->_request->getParams();
        $isLoggedIn = $this->_ajaxSuiteHelper->getLoggedCustomer();

        if ($isLoggedIn == true) {
            try {
                $product = $this->_initProduct();
                if ($product->getTypeId() != "simple"
                    && $product->getTypeId() != "mageworx_giftcards"
                    && $product->getTypeId() != "downloadable"
                    && $product->getTypeId() != "virtual") {
                    $this->_coreRegistry->register('product', $product);
                    $this->_coreRegistry->register('current_product', $product);
                    $htmlPopup = $this->_ajaxWishlistHelper->getOptionsPopupHtml($product);
                    $result['success'] = true;
                    $result['html_popup'] = $htmlPopup;
                } else {
                    $this->_forward('add', 'index', 'wishlist', $params);
                    return;
                }

            } catch (Exception $e) {
                $this->messageManager->addException($e, __('You can\'t login right now.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $result['success'] = false;
            }
        } else {
            $product = $this->_initProduct();
            $this->_coreRegistry->register('product', $product);
            $this->_coreRegistry->register('current_product', $product);

            $htmlPopup = $this->_ajaxWishlistHelper->getErrorHtml($product);
            $result['success'] = false;
            $result['html_popup'] = $htmlPopup;
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }

    /**
     * @return bool|ProductInterface
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            try {
                $product = $this->_productRepository->getById($productId, false, $storeId);
                return $product;
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
}
