<?php
namespace Firas\DonationProduct\Api;

interface DonationListInterface
{
    /**
     * GET for Post api
     * @return \Firas\DonationProduct\Api\DonationListInterface
     */
    public function getList();

    /**
     * Save Donations
     * @param int $cartId
     * @param int $donationId
     * @param float $donationAmount
     * @return \Firas\DonationProduct\Api\Data\DonationsInterface
     */
    public function setDonation($cartId, $donationId, $donationAmount);
}
