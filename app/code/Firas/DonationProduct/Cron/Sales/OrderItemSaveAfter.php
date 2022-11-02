<?php
/**
 * A Magento 2 module named Firas/DonationProduct
 * Copyright (C) 2017 Derrick Heesbeen
 *
 * This file is part of Firas/DonationProduct.
 *
 * Firas/DonationProduct is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Firas\DonationProduct\Cron\Sales;

use Firas\DonationProduct\Model\DonationsFactory;
use Firas\DonationProduct\Model\Product\Type\Donation;
use Firas\DonationProduct\Model\DonationsRepository;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderItemSaveAfter
 * @package Firas\DonationProduct\Observer\Sales
 */
class OrderItemSaveAfter
{
    /**
     * @var DonationsFactory
     */
    private $donationsModel;

    /**
     * @var DonationsRepository
     */
    private $donationsRepository;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    private  $orderRepository;

    /**
     * OrderPlaceAfter constructor.
     * @param DonationsFactory $donations
     * @param DonationsRepository $donationsRepository
     * @param Order $order
     * @internal param DonationsRepository $donations
     */
    public function __construct(
        DonationsFactory $donations,
        DonationsRepository $donationsRepository,
        OrderFactory $orderFactory,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->donationsModel = $donations;
        $this->donationsRepository = $donationsRepository;
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
    }
    /**
     * Execute observer
     *
     * @return void
     */
    public function execute() 
    {
        $donations = $this->donationsModel->create();

        $lastDonation = $donations->getCollection()->getLastItem();

        $lastDonationData = $lastDonation->getData();

        $lastDonationOrderId = $lastDonationData ? $lastDonationData['order_id'] : 1;

        $orders = $this->orderFactory->create();

        $orderCollection = $orders->getCollection();

        $limit = count($orderCollection->getData()) - $lastDonationOrderId;


        $orderCollection->getSelect()->limit((int)$limit,(int)$lastDonationOrderId);

        foreach($orderCollection as $index => $order){

            $orderId = $order->getId();
            

            foreach ($order->getAllItems() as $orderItem) {

                if ($orderItem->getProductType() != Donation::TYPE_CODE) {
                    continue;
                }
        
                /** @var \Firas\DonationProduct\Model\Donations $donation */
                $donation = $this->donationsModel->create()->load($orderItem->getItemId(), 'order_item_id');
                if ($donation->getId()) {
                    if ($orderItem->getQtyOrdered()==$orderItem->getQtyInvoiced()) {
                        $donation->setInvoiced(1);
                        $donation->save();
                    }
                    continue;
                }
        
                $orderId = $orderItem->getOrderId();
                $order = $this->order->load($orderId);
        
                $donation->setName($orderItem->getName());
                $donation->setSku($orderItem->getSku());
                $donation->setAmount($orderItem->getPrice());
                $donation->setOrderId($orderId);
                $donation->setOrderItemId($orderItem->getItemId());
                $donation->setOrderStatus($order->getStatus());
                $donation->setInvoiced('');
                $donation->setCreatedAt($orderItem->getCreatedAt());
                $donation->save();

            }

        }

        


    }

}
