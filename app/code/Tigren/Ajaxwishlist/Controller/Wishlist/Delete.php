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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Controller\AbstractIndex;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Wishlist;
use Tigren\Ajaxwishlist\Helper\Data as AjaxwishlistData;

/**
 * Class Delete
 *
 * @package Tigren\Ajaxwishlist\Controller\Wishlist
 */
class Delete extends Action
{
    /**
     * @var Wishlist
     */
    protected $_wishlist;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var AjaxwishlistData
     */
    protected $_ajaxWishlistHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Delete constructor.
     *
     * @param Context                             $context
     * @param ProductRepositoryInterface          $productRepository
     * @param AjaxwishlistData                    $ajaxWishlistHelper
     * @param Registry                            $registry
     * @param StoreManagerInterface               $storeManager
     * @param \Magento\Framework\Json\Helper\Data $jsonEncode
     * @param Wishlist                            $wishlist
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        AjaxwishlistData $ajaxWishlistHelper,
        Registry $registry,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Json\Helper\Data $jsonEncode,
        Wishlist $wishlist
    ) {
        $this->_wishlist = $wishlist;
        $this->_ajaxWishlistHelper = $ajaxWishlistHelper;
        $this->_jsonEncode = $jsonEncode;
        $this->_storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * @throws NotFoundException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $request = $this->getRequest();
        $product_id = $request->getParam('product', null);
        if (!$product_id) {
            throw new NotFoundException(__('Page not found'));
        }

        $customer_id = $request->getParam('customerId', null);
        $wishlist = $this->_wishlist->loadByCustomerId($customer_id);
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found'));
        }

        $items = $wishlist->getItemCollection();
        foreach ($items as $item) {
            if ($item->getProductId() == $product_id) {
                try {
                    $item->delete();
                    $wishlist->save();
                } catch (LocalizedException $e) {
                    $this->messageManager->addError(
                        __(
                            'We can\'t delete the item from Wish List right now because of an error: %1.',
                            $e->getMessage()
                        )
                    );
                } catch (Exception $e) {
                    $this->messageManager->addError(__('We can\'t delete the item from the Wish List right now.'));
                }
            }
        }
        if (!empty($this->getRequest()->getParam('isRemoveSubmit', null))) {
            $product = $this->_initProduct();
            $this->_coreRegistry->register('product', $product);
            $this->_coreRegistry->register('current_product', $product);
            $htmlPopup = $this->_ajaxWishlistHelper->getSuccessRemoveHtml();
            $result['success'] = true;
            $result['html_popup'] = $htmlPopup;
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
            );
        }
        $this->_objectManager->get(Data::class)->calculate();
    }

    /**
     * @return bool|ProductInterface
     * @throws NoSuchEntityException
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product', null);
        if ($productId) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);

                return $product;
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
}
