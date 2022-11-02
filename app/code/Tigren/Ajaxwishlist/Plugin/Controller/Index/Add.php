<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Ajaxwishlist\Plugin\Controller\Index;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Tigren\Ajaxwishlist\Helper\Data as AjaxwishlistData;

/**
 * Class Add
 *
 * @package Tigren\Ajaxwishlist\Plugin\Controller\Index
 */
class Add
{
    /**
     * @var Registry|null
     */
    protected $_coreRegistry = null;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var Data
     */
    protected $_jsonEncode;
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;
    /**
     * @var AjaxwishlistData
     */
    protected $_ajaxWishlistHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Add constructor.
     *
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param Data $jsonEncode
     * @param RedirectFactory $redirectFactory
     * @param AjaxwishlistData $ajaxWishlistHelper
     */
    public function __construct(
        Registry $registry,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        Data $jsonEncode,
        RedirectFactory $redirectFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        AjaxwishlistData $ajaxWishlistHelper
    )
    {
        $this->resultRedirectFactory = $redirectFactory;
        $this->_jsonEncode = $jsonEncode;
        $this->_storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->_ajaxWishlistHelper = $ajaxWishlistHelper;
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
    }

    /**
     * @param  $subject
     * @param  $proceed
     * @return Redirect
     * @throws LocalizedException
     */
    public function aroundExecute($subject, $proceed)
    {
        $result = [];
        $params = $subject->getRequest()->getParams();

        $product = $this->_initProduct($subject);

        if (!empty($params['isWishlistSubmit'])) {
            $proceed();
            $this->_coreRegistry->register('product', $product);
            $this->_coreRegistry->register('current_product', $product);

            $htmlPopup = $this->_ajaxWishlistHelper->getSuccessHtml();
            $result['success'] = true;
            $result['html_popup'] = $htmlPopup;
            $result['check_add_class'] = true;
            $subject->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
            );
        } else {
            $proceed();
            return $this->resultRedirectFactory->create()->setPath('*');
        }
    }

    /**
     * @param  $subject
     * @return bool|ProductInterface
     * @throws NoSuchEntityException
     */
    protected function _initProduct($subject)
    {
        $productId = (int)$subject->getRequest()->getParam('product');
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
