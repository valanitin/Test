<?php

declare(strict_types = 1);

namespace Firas\Cronbrands\Abandonedcart;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Quote\Model\ResourceModel\Quote\Collection as QuoteCollection;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Exception;

Class Abandonedcart
{

	public const SOLOLUXURY_WEBSITE_CODE = 'gb';
	public const SOLOLUXURY_WEBSITE_NAME = 'WWW.SOLOLUXURY.COM';
	public const API_URL = "https://erp.theluxuryunlimited.com/api/customer/add_cart_data";

	/**
	 * @var StoreManagerInterface
	 */
	private $storeManager;

	/**
	 * @var QuoteCollection
	 */
	private $quoteCollectionFactory;

	/**
	 * @var WebsiteRepositoryInterface
	 */
	private $websiteRepository;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var TimezoneInterface
	 */
	private $timezone;

	public function __construct(
		CollectionFactory $quoteCollectionFactory,
		StoreManagerInterface $storeManager,
		WebsiteRepositoryInterface $websiteRepository,
		LoggerInterface $logger,
		TimezoneInterface $timezone,
		DateTime $dateTime
	) {
		$this->quoteCollectionFactory = $quoteCollectionFactory;
		$this->storeManager = $storeManager;
		$this->websiteRepository = $websiteRepository;
		$this->logger = $logger;
		$this->dateTime = $dateTime;
		$this->timezone = $timezone;
	}

  	public function execute()
	{
		$itemArray = [];
		$websiteId = $this->getWebsiteId();
		if ($websiteId === null) {
			$this->logger->info(sprintf("Website %s not found.", self::SOLOLUXURY_WEBSITE_CODE));
			return ;
		}
		$storeIds = $this->getStoreIds($websiteId);
		if (empty($storeIds)) {
			$this->logger->info("Store did not found.");
			return ;
		}
		$currentDate = $this->dateTime->gmtDate();
		$lastOneHourDateTIme = $this->dateTime->date('Y-m-d H:i:s', strtotime('-1 hour'));
		$lastTwoHourDateTIme = $this->dateTime->date('Y-m-d H:i:s', strtotime('-2 hour'));

		$quoteCollection = $this->quoteCollectionFactory->create()
								->addFieldToSelect('*')
								->addFieldToFilter('is_active', 1)
								->addFieldToFilter('store_id', ['in' => $storeIds])
								->addFieldToFilter('created_at', ['from' => $lastTwoHourDateTIme, 'to' => $lastOneHourDateTIme]);

		try{
			foreach($quoteCollection as $quote) {
				$quoteId = $quote->getEntityId();
				$storeId = $quote->getStoreId();
				$storeData = $this->storeManager->getStore($storeId);
				$storeCode = (string)$storeData->getCode();
				if($storeCode != 'english' && $storeCode != 'default') {
						$storeCode = explode("-", $storeCode);
						$storeCode = $storeCode[1].'-'.strtoupper($storeCode[0]);
					}
				$customerEmail = $quote->getCustomerEmail();
				$customerName = $quote->getCustomerFirstname().' '.$quote->getCustomerLastname();
				if($customerName == ' ') {
					$customerName = $customerEmail;
				}
				$currencyCode = $quote->getQuoteCurrencyCode();
				$baseCurrencyCode = $quote->getBaseCurrencyCode();
				$grandTotal = number_format((float) $quote->getGrandTotal(), 2);
				$itemArray = $this->getQuoteItemsData($quote);
				$newjsonData = [
					'name' => $customerName,
					'lang_code' => $storeCode,
					'email' => $customerEmail,
					'website' => self::SOLOLUXURY_WEBSITE_NAME,
					'item_info' => $itemArray
				];
				$this->sendDataToERP($newjsonData);
			}
		} catch(Exception $e) {
			$this->logger->info($e);
		}
	}

	private function sendDataToERP(array $abandonCartData)
	{
		//Initiate cURL.
		$ch = curl_init(self::API_URL);
		//Encode the array into JSON.
		$jsonDataEncoded = json_encode($abandonCartData);
		//Tell cURL that we want to send a POST request.
		curl_setopt($ch, CURLOPT_POST, 1);
		//Attach our encoded JSON string to the POST fields.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
		//Set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//Execute the request
		try{
			$result = curl_exec($ch);
			// $result = 'success';
			$result = json_decode($result, true);
			$status = $result['status'];
			// $status = 'failed';
			$message = $result['message'];
			$err = curl_error($ch);
			curl_close($ch);
		$this->logger->info(sprintf("Abandoned cart executed to erp with status.== %s", $status)); 
		} catch(Exception $e) {
			$this->logger->info($e);
		}
		$this->logger->info(sprintf("Abandoned cart %s .",$newjsonData['email']));
	}

	private function getQuoteItemsData(Quote $quoteModel): array
	{
		$itemArray = [];
		$items = $quoteModel->getAllItems();
		foreach($items as $key => $item) {
			$itemArray[$key]['sku'] = $item->getSku();
			$itemArray[$key]['qty'] = $item->getQty();
		}
		return $itemArray;
	}

	private function getWebsiteId(): ?int
	{
		try {
            $website = $this->websiteRepository->get(self::SOLOLUXURY_WEBSITE_CODE);
            return (int) $website->getId();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
		return null;
	}

	private function getStoreIds(int $websiteId): ?array
	{
		try {
            return $this->storeManager->getStoreByWebsiteId($websiteId);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
		return null;
	}
}
