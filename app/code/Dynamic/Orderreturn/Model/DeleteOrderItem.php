<?php

namespace Dynamic\Orderreturn\Model;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;

class DeleteOrderItem
{
    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItemRepository;
    
    protected $toOrderItem;
    protected $order;
    protected $quote;
    protected $quoteFactory;
    protected $_logger;

    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        ToOrderItem $toOrderItem,
        Order $order,
        //Quote $quote
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->toOrderItem = $toOrderItem;
        $this->order = $order;
        //$this->quote = $quote;
        $this->quoteFactory = $quoteFactory;
        $this->_logger = $logger;
    }

    /**
     * result
     *
     * @return bool
     */
    public function deleteOrderItem($orderId, $itemId)
    {
        if( !isset($itemId) || $itemId <= 0) return;
        
        
        if( !isset($orderId) || $orderId <= 0) return;
        
        
        try{
           // $this->orderItemRepository->deleteById($itemId);
            $this->updateOrder($orderId, $itemId);
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());            
        }
        return;
    }
    
  public function updateOrder($orderId ,  $itemId){
		$quoteToOrder = $this->toOrderItem; 		
		$order = $this->order->load($orderId);
		$quote =  $this->quoteFactory->create()->load($order->getQuoteId());		
		$items = $quote->getAllVisibleItems();				
		
        foreach ($items as $quoteItem) {
            $origOrderItem = $order->getItemByQuoteItemId($quoteItem->getId());
            $orderItemId = $origOrderItem->getItemId();
            //update quote item according your need 
            $item = $quote->getItemById($quoteItem->getId());
            if (!$item) {
				continue;
		     }
		     if($orderItemId == $itemId){				 
		         $item->setQty(0);
		         $item->setCustomPrice(0);
		         $item->setOriginalCustomPrice(0);
		         $item->getProduct()->setIsSuperMode(true);
		         $item->save();
		     }
        }
        $quote->collectTotals();
        $quote->save();         
        
       foreach ($items as $quoteItem) {
			$orderItem = $quoteToOrder->convert($quoteItem);
			$origOrderItemNew = $order->getItemByQuoteItemId($quoteItem->getId());
			if ($origOrderItemNew) {
				$origOrderItemNew->addData($orderItem->getData());
			} else {
				if ($quoteItem->getParentItem()) {
					$orderItem->setParentItem($order->getItemByQuoteItemId($orderItem->getParentItem()->getId()));
                  }
               $order->addItem($orderItem);
             }
          }
          $order->setSubtotal($quote->getSubtotal())
          ->setBaseSubtotal($quote->getBaseSubtotal())
          ->setGrandTotal($quote->getGrandTotal())
          ->setBaseGrandTotal($quote->getBaseGrandTotal());
          $quote->save();
         $order->save();
          
          # Update Order Totals          
          $order = $this->order->load($orderId);		
          if($order){
			  $orderSubTotal = 0;
			  $orderBaseTax = 0;
			  $orderDiscountAmount = 0;
			  foreach ($order->getAllVisibleItems() as $_item) {
				  $orderSubTotal += $_item->getBaseRowTotal();
				  $orderBaseTax += $_item->getBaseTaxAmount() + $_item->getBaseHiddenTaxAmount();
				  $orderDiscountAmount += $_item->getBaseDiscountAmount();
				  }
		      $grandTotal = ($orderSubTotal + $order->getShippingAmount() + $orderBaseTax) - $orderDiscountAmount;    		  
			  $order->setSubtotal($orderSubTotal)
			     ->setBaseSubtotal($orderSubTotal)
			     ->setDiscountAmount($orderDiscountAmount)
			     ->setBaseDiscountAmount($orderDiscountAmount)
			     ->setTaxAmount($orderBaseTax)
			     ->setBaseTaxAmount($orderBaseTax)
			     ->setGrandTotal($grandTotal)
			     ->setBaseGrandTotal($grandTotal)->save();
			     $order->save();			  
		  }                  
          return;        
	}    
    
}
