<?php

/**
 * Copyright © 2022 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dynamic\Shippinginfoapi\Plugin;

class QuotePlugin
{

    /**
     * @var \Magento\Quote\Api\Data\TotalsItemExtensionFactory
     */
    protected $totalItemExtension;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $quoteItemFactory;

    /**
     * @param \Magento\Quote\Api\Data\TotalsItemExtensionFactory $totalItemExtension
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Quote\Api\Data\TotalsItemExtensionFactory $totalItemExtension,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepository,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
    ) {
        $this->totalItemExtension = $totalItemExtension;
        $this->productRepository  = $productRepository;
        $this->quoteItemFactory   = $quoteItemFactory;
    }

    /**
     * Add attribute values
     *
     * @param   \Magento\Quote\Api\CartRepositoryInterface $subject,
     * @param   $quote
     * @return  $quoteData
     */
    public function afterGet(
        \Magento\Quote\Api\CartTotalRepositoryInterface $subject, $quotetotals
    ) {
        $quoteData = $this->setAttributeValue($quotetotals);
        return $quoteData;
    }

    /**
     * set value of attributes
     *
     * @param   $product,
     * @return  $extensionAttributes
     */
    public function setAttributeValue($quoteTotals)
    {
        if (count($quoteTotals->getItems())) {
            foreach ($quoteTotals->getItems() as $item) {
                $extensionAttributes = $item->getExtensionAttributes();

                if ($extensionAttributes === null) {
                    $extensionAttributes = $this->totalItemExtension->create();
                }

                $quoteItem   = $this->quoteItemFactory->create()->load($item->getItemId());
                $productData = $this->productRepository->create()->getById($quoteItem->getProductId());
                $extensionAttributes->setImage($productData->getThumbnail());

                $item->setExtensionAttributes($extensionAttributes);
            }
        }

        return $quoteTotals;
    }

}
