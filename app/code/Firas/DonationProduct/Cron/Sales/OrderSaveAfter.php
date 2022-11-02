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

/**
 * Class OrderSaveAfter
 * @package Firas\DonationProduct\Observer\Sales
 */
class OrderSaveAfter
{
    /**
     * @var DonationsFactory
     */
    private $donationsModel;

    /**
     * @var DonationsRepository
     */
    private $donationsRepository;

    private $orderFactory;


    /**
     * OrderPlaceAfter constructor.
     * @param DonationsFactory $donations
     * @param DonationsRepository $donationsRepository
     * @internal param DonationsRepository $donations
     */
    public function __construct(
        DonationsFactory $donations,
        DonationsRepository $donationsRepository,
        OrderFactory $orderFactory

    ) {
        $this->donationsModel = $donations;
        $this->donationsRepository = $donationsRepository;
        $this->orderFactory = $orderFactory;

    }
    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute() {

        $donations = $this->donationsModel->create();

        $lastDonation = $donations->getCollection()->getLastItem();

        $lastDonationData = $lastDonation->getData();

        $lastDonationOrderId = $lastDonationData['order_id'];

        $orders = $this->orderFactory->create();

        $orderCollection = $orders->getCollection();

        $limit = count($orderCollection->getData()) - $lastDonationOrderId;
        $orderCollection->getSelect()->limit($limit,$lastDonationOrderId);

        foreach($orderCollection as $index => $order){
            $orderId = $order->getId();
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info(print_r($orderId,true));
            /** @var \Firas\DonationProduct\Model\Donations $donations */
            $donations = $this->donationsRepository->getDonationsByOrderId($orderId);

            foreach ($donations as $donationItem) {
                $this->updateDonationItemData($donationItem, $order->getStatus());
            }
        }


    }

    /**
     * @param $donationItem
     * @param $orderStatus
     */
    private function updateDonationItemData($donationItem, $orderStatus)
    {
        /** @var \Firas\DonationProduct\Model\Donations $donationItem */
        $donationItem->setOrderStatus($orderStatus);
        $donationItem->save();
    }
}
