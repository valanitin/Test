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
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Controller\Api\Compare;

use \Magento\Framework\Message\MessageInterface;

class Add extends \Magento\Catalog\Controller\Product\Compare\Add implements
    \Plumrocket\Amp\Model\MagentoTwoTwo\CsrfAwareActionInterface
{
    use \Plumrocket\Amp\Controller\ValidateForCsrfTrait;

    /**
     * @var \Plumrocket\Amp\Model\Result\AmpJsonFactory
     */
    private $ampResultFactory;

    /**
     * Add constructor.
     *
     * @param \Magento\Framework\App\Action\Context                                       $context
     * @param \Magento\Catalog\Model\Product\Compare\ItemFactory                          $compareItemFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Customer\Model\Session                                             $customerSession
     * @param \Magento\Customer\Model\Visitor                                             $customerVisitor
     * @param \Magento\Catalog\Model\Product\Compare\ListCompare                          $catalogProductCompareList
     * @param \Magento\Catalog\Model\Session                                              $catalogSession
     * @param \Magento\Store\Model\StoreManagerInterface                                  $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator                              $formKeyValidator
     * @param \Magento\Framework\View\Result\PageFactory                                  $resultPageFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                             $productRepository
     * @param \Plumrocket\Amp\Model\Validator                                             $validator
     * @param \Plumrocket\Amp\Model\Result\AmpJsonFactory                                 $ampResultFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Product\Compare\ItemFactory $compareItemFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Visitor $customerVisitor,
        \Magento\Catalog\Model\Product\Compare\ListCompare $catalogProductCompareList,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Plumrocket\Amp\Model\Validator $validator,
        \Plumrocket\Amp\Model\Result\AmpJsonFactory $ampResultFactory
    ) {
        parent::__construct(
            $context,
            $compareItemFactory,
            $itemCollectionFactory,
            $customerSession,
            $customerVisitor,
            $catalogProductCompareList,
            $catalogSession,
            $storeManager,
            $formKeyValidator,
            $resultPageFactory,
            $productRepository
        );
        $this->_formKeyValidator = $validator;
        $this->ampResultFactory = $ampResultFactory;
    }

    /**
     * Refactor response to json
     * Ignore form key validation
     *
     * @return \Plumrocket\Amp\Model\Result\AmpJson|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $successCount = $this->messageManager->getMessages()->getCountByType(MessageInterface::TYPE_SUCCESS);

        parent::execute();

        $result = $this->ampResultFactory->create();

        if ($successCount < $this->messageManager->getMessages()->getCountByType(MessageInterface::TYPE_SUCCESS)) {
            $items = $this->messageManager->getMessages(true)->getItemsByType(MessageInterface::TYPE_SUCCESS);
            $messageText = null;
            foreach ($items as $message) {
                if (null === $messageText) {
                    $messageText = $message->getText();
                    if (empty($messageText)) {
                        $messageData = $message->getData();
                        $productName = isset($messageData['product_name']) ? $messageData['product_name'] : '';
                        $messageText = $this->getSuccessMessage($productName);
                    }
                }
            }

            $result->addSuccessMessage($messageText);
        } else {
            $error = __('Something went wrong');
            $productId = (int)$this->getRequest()->getParam('product');
            if ($productId) {
                $storeId = $this->_storeManager->getStore()->getId();
                try {
                    $product = $this->productRepository->getById($productId, false, $storeId);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $product = null;
                }

                if (! $product) {
                    $error = __('We can\'t specify a product.');
                }
            } else {
                $error = __('We can\'t specify a product.');
            }

            $result->addErrorMessage($error->render());
        }

        return $result;
    }

    /**
     * Compatibility with Magento 2.2.7 and newest
     *
     * @param  string $productName
     * @return \Magento\Framework\Phrase
     */
    private function getSuccessMessage($productName)
    {
        return __('You added product %1 to the comparison list.', $productName);
    }
}
