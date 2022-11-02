<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Plumrocket\Amp\Controller\Review\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Review\Model\Review;

class Post extends \Magento\Review\Controller\Product\Post implements
    \Plumrocket\Amp\Model\MagentoTwoTwo\CsrfAwareActionInterface
{
    use \Plumrocket\Amp\Controller\ValidateForCsrfTrait;

    /**
     * @deprecated since 2.8.1
     */
    const SUCCESS_MESSAGE = 'You submitted your review for moderation.';

    /**
     * @deprecated since 2.8.1
     */
    const ERROR_MESSAGE = 'We can\'t post your review right now.';

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @deprecated since 2.8.1 - use $ampResultFactory instead
     *
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Plumrocket\Amp\Model\Result\AmpJsonFactory
     */
    private $ampResultFactory;

    /**
     * Post constructor.
     *
     * @param \Magento\Framework\App\Action\Context            $context
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Psr\Log\LoggerInterface                         $logger
     * @param \Magento\Catalog\Api\ProductRepositoryInterface  $productRepository
     * @param \Magento\Review\Model\ReviewFactory              $reviewFactory
     * @param \Magento\Review\Model\RatingFactory              $ratingFactory
     * @param \Magento\Catalog\Model\Design                    $catalogDesign
     * @param \Magento\Framework\Session\Generic               $reviewSession
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator   $formKeyValidator
     * @param \Plumrocket\Amp\Helper\Data                      $dataHelper
     * @param \Plumrocket\Amp\Model\Result\AmpJsonFactory      $ampResultFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Framework\Session\Generic $reviewSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Plumrocket\Amp\Helper\Data $dataHelper,
        \Plumrocket\Amp\Model\Result\AmpJsonFactory $ampResultFactory
    ) {
        $this->_dataHelper = $dataHelper;

        parent::__construct(
            $context,
            $coreRegistry,
            $customerSession,
            $categoryRepository,
            $logger,
            $productRepository,
            $reviewFactory,
            $ratingFactory,
            $catalogDesign,
            $reviewSession,
            $storeManager,
            $formKeyValidator
        );
        $this->ampResultFactory = $ampResultFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (! $this->_dataHelper->moduleEnabled()) {
            return $resultRedirect;
        }

        $resultJson = $this->ampResultFactory->create();
        $globalMessages = '';

        $data = $this->reviewSession->getFormData(true);
        if ($data) {
            $rating = [];
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data = $this->getRequest()->getPostValue();
            $rating = $this->getRequest()->getParam('ratings', []);
        }
        if (($product = $this->initProduct()) && !empty($data)) {
            /** @var \Magento\Review\Model\Review $review */
            $review = $this->reviewFactory->create()->setData($data);
            $review->unsetData('review_id');

            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $review->setEntityId($review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($product->getId())
                        ->setStatusId(Review::STATUS_PENDING)
                        ->setCustomerId($this->customerSession->getCustomerId())
                        ->setStoreId($this->storeManager->getStore()->getId())
                        ->setStores([$this->storeManager->getStore()->getId()])
                        ->save();

                    foreach ($rating as $ratingId => $optionId) {
                        $this->ratingFactory->create()
                            ->setRatingId($ratingId)
                            ->setReviewId($review->getId())
                            ->setCustomerId($this->customerSession->getCustomerId())
                            ->addOptionVote($optionId, $product->getId());
                    }

                    $review->aggregate();
                    return $resultJson->setData([
                        'result' => 'success',
                        'message' => __('You submitted your review for moderation.')
                    ]);
                } catch (\Exception $e) {
                    $this->reviewSession->setFormData($data);
                    return $resultJson->setData([
                        'result' => 'error',
                        'message' => __('We can\'t post your review right now.')
                    ]);
                }
            } else {
                $this->reviewSession->setFormData($data);
                if (is_array($validate)) {
                    $globalMessages .= implode('. ', $validate);
                } else {
                    $globalMessages .= __('We can\'t post your review right now.');
                }
            }
        }

        return $resultJson->setData([
            'result' => 'error',
            'message' => __($globalMessages)
        ]);
    }
}
