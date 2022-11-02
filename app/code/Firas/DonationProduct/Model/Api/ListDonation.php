<?php

namespace Firas\DonationProduct\Model\Api;

use Firas\DonationProduct\Helper\Data as DonationHelper;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Form\FormKey;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;


/**
 * class ListDonation
 */
class ListDonation implements \Firas\DonationProduct\Api\DonationListInterface
{
    /**
     * @var DonationHelper
     */
    protected $donationHelper;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SortOrder
     */
    protected $sortOrder;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ImageFactory
     */
    protected $imageHelperFactory;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var
     */
    protected $quoteFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepositoryInterface;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Emulation
     */
    protected $appEmulation;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * ListProduct constructor.
     * @param DonationHelper $donationHelper
     * @param ProductRepository $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrder $sortOrder
     * @param ImageFactory $imageHelperFactory
     * @param Emulation $appEmulation
     * @param StoreManagerInterface $storeManager
     * @param Cart $cart
     * @param FormKey $formKey
     * @param CartRepositoryInterface $cartRepositoryInterface
     * @param Product $product
     */
    public function __construct(
        DonationHelper $donationHelper,
        ProductRepository $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrder $sortOrder,
        ImageFactory $imageHelperFactory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Cart $cart,
        FormKey $formKey,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        Product $product

    ) {
        $this->donationHelper = $donationHelper;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->appEmulation = $appEmulation;
        $this->storeManager = $storeManager;
        $this->formKey = $formKey;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->product = $product;
    }

    /**
     * @inheritdoc
     */
    public function getList()
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('type_id', 'donation', 'eq')->create();
            $products = $this->productRepository->getList($searchCriteria);
            $items = $products->getItems();
            $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
            $responseData = [];
            foreach ($items as $item) {
                $donationId = $item->getEntityId();
                $imageUrl = $this->imageHelperFactory->create()->init($item, 'product_base_image')->getUrl();
                $responseData[] = array('donationId' => $donationId, 'imageURL' => $imageUrl);
            }
            $response = array(['success' => true, 'items' => $responseData]);
        } catch (\Exception $e) {
            $response = array(['success' => false, 'message' => $e->getMessage()]);
        }
        $this->appEmulation->stopEnvironmentEmulation();

        return $response;
    }

    /**
     * @param $cartId
     * @param $donationId
     * @param $donationAmount
     * @param $storeId
     * @return array[]|false[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setDonation($cartId, $donationId, $donationAmount, $storeId = null)
    {
        $response = ['success' => false];
        $storeId = $this->storeManager->getStore()->getId();
        $storeCode = $this->storeManager->getStore()->getCode();
        $cartData = $this->cartRepositoryInterface->get($cartId);
        $formKey = $this->formKey->getFormKey();

        $params = ['amount' => $donationAmount, 'product' => $donationId, 'form_key' => $formKey];
        $product = $this->product->load($donationId);
        $request = new \Magento\Framework\DataObject();
        $request->setData($params);

        try {
            if ($product) {
                $cartData->addProduct($product, $request);
                $cartData->save();
                $quote = $this->cartRepositoryInterface->get($cartId);
                $quote->collectTotals()->save();
                $message = __(
                    'You added %1 to your shopping cart.',
                    $product->getName()
                );
                $response = array(['success' => true, 'message' => $message]);
            }
        } catch (\Exception $e) {
            $response = array(['success' => false, 'message' => $e->getMessage()]);
        }
        return $response;
    }
}