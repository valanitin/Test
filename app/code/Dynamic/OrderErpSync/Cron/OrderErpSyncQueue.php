<?php

namespace Dynamic\OrderErpSync\Cron;

use Zend\Mail\Transport\Smtp;

class OrderErpSyncQueue
{
    /**
     * CurlFactory
     *
     * @var \Magento\Framework\HTTP\Client\CurlFactory
     */
    protected $curlFactory;

    /**
     * Order
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * DirectoryList
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * Constructor
     *
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     * @param \Magento\Sales\Model\Order $order
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) { 
        $this->curlFactory = $curlFactory;
        $this->order = $order;
        $this->directoryList = $directoryList;
    }

	public function execute()
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/OrderErpApi.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("cron run successfully...");
		$this->generate();
		return $this;
	}

	public function generate() {

		$orderData = $this->order->getCollection()->addFieldToFilter("order_erp_sync_flag", ["eq", 0]);

		if(count($orderData) > 0 && !empty($orderData)) {

			$orderFailedFilePath = $this->directoryList->getPath('log');
    		$orderFailedFileName = $orderFailedFilePath.'/'.date("Y-m-d").'failedOrders.csv';

			foreach ($orderData as $order) {

				$billingAddress = $order->getBillingAddress();
			    $shippingAddress = $order->getShippingAddress();

			    $orderRealId = $order->getIncrementId();
			    $websiteName = 'WWW.SOLOLUXURY.COM';

			    $websiteid = $order->getStore()->getWebsiteId();
			    $orderBaseCurrencyCode = $order->getBaseCurrencyCode();
			    $baseDiscountAmount = $order->getBaseDiscountAmount();
			    $baseGrandTotal = $order->getBaseGrandTotal();
			    $baseDiscountTaxCompensationAmount = $order->getBaseDiscountTaxCompensationAmount();
			    $baseShippingAmount = $order->getBaseShippingAmount();
			    $baseShippingDiscountAmount = $order->getBaseShippingDiscountAmount();
			    $baseShippingDiscountTaxCompensationAmnt = $order->getBaseShippingDiscountTaxCompensationAmnt();
			    $baseShippingInclTax = $order->getBaseShippingInclTax();
			    $baseShippingTaxAmount = $order->getBaseShippingTaxAmount();
			    $baseSubtotal = $order->getBaseSubtotal();
			    $baseSubtotalInclTax = $order->getBaseSubtotalInclTax();
			    $baseTaxAmount = $order->getBaseTaxAmount();
			    $baseTotalDue = $order->getBaseTotalDue();
			    $baseToGlobalRate = $order->getBaseToGlobalRate();
			    $baseToOrderRate = $order->getBaseToOrderRate();
			    $billingAddressId = $order->getBillingAddressId();
			    $createdAt = $order->getCreatedAt();
			    $customerEmail = $order->getCustomerEmail();
			    $customerFirstname = $order->getCustomerFirstname();
			    $customerGroupId = $order->getCustomerGroupId();
			    $customerId = $order->getCustomerId();
			    $customerIsGuest = $order->getCustomerIsGuest();
			    $customerLastname = $order->getCustomerLastname();
			    $customerNoteNotify = $order->getCustomerNoteNotify();
			    $discountAmount = $order->getDiscountAmount();
			    $emailSent = $order->getEmailSent();
			    $entityId = $order->getEntityId();
			    $globalCurrencyCode = $order->getGlobalCurrencyCode();
			    $grandTotal = $order->getGrandTotal();
			    $discountTaxCompensationAmount = $order->getDiscountTaxCompensationAmount();
			    $incrementId = $order->getIncrementId();
			    $isVirtual = $order->getIsVirtual();
			    $orderCurrencyCode = $order->getOrderCurrencyCode();
			    $protectCode = $order->getProtectCode();
			    $quoteId = $order->getQuoteId();
			    $remoteIp = $order->getRemoteIp();
			    $shippingAmount = $order->getShippingAmount();
			    $shippingDescription = $order->getShippingDescription();
			    $shippingDiscountAmount = $order->getShippingDiscountAmount();
			    $shippingDiscountTaxCompensationAmount = $order->getShippingDiscountTaxCompensationAmount();
			    $shippingInclTax = $order->getShippingInclTax();
			    $shippingTaxAmount = $order->getShippingTaxAmount();
			    $state = $order->getState();
			    $status = $order->getStatus();
			    $storeCurrencyCode = $order->getStoreCurrencyCode();
			    $storeId = $order->getStoreId();
			    $storeName = $order->getStoreName();
			    $storeToBaseRate = $order->getStoreToBaseRate();
			    $storeToOrderRate = $order->getStoreToOrderRate();
			    $subtotal = $order->getSubtotal();
			    $subtotalInclTax = $order->getSubtotalInclTax();
			    $taxAmount = $order->getTaxAmount();
			    $totalDue = $order->getTotalDue();
			    $totalItemCount = $order->getTotalItemCount();
			    $totalQtyOrdered = $order->getTotalQtyOrdered();
			    $updatedAt = $order->getUpdatedAt();
			    $weight = $order->getWeight();
			    $xForwardedFor = $order->getXForwardedFor();

			    $amountRefunded = $order->getAmountRefunded();
			    $baseAmountRefunded = $order->getBaseAmountRefunded();
			    $baseDiscountAmount = $order->getBaseDiscountAmount();
			    $baseDiscountInvoiced = $order->getBaseDiscountInvoiced();
			    $baseDiscountTaxCompensation = $order->getBaseDiscountTaxCompensation();
			    $baseOriginalPrice = $order->getBaseOriginalPrice();
			    $basePrice = $order->getBasePrice();
			    $basePriceInclTax = $order->getBasePriceInclTax();
			    $baseRowInvoiced = $order->getBaseRowInvoiced();
			    $baseRowTotal = $order->getBaseRowTotal();
			    $baseRowTotalIncl = $order->getBaseRowTotalIncl();
			    $baseTaxAmount = $order->getBaseTaxAmount();
			    $baseTaxInvoiced = $order->getBaseTaxInvoiced();
			    $createdAt = $order->getCreatedAt();
			    $discountAmount = $order->getDiscountAmount();
			    $discountInvoiced = $order->getDiscountInvoiced();
			    $discountPercent = $order->getDiscountPercent();
			    $freeShipping = $order->getFreeShipping();
			    $discountTaxCompensationAmount = $order->getDiscountTaxCompensationAmount();
			    $isQtyDecimal = $order->getIsQtyDecimal();
			    $isVirtual = $order->getIsVirtual();
			    $itemId = $order->getItemId();
			    $name = $order->getName();
			    $noDiscount = $order->getNoDiscount();
			    $orderId = $order->getOrderId();
			    $originalPrice = $order->getOriginalPrice();
			    $price = $order->getPrice();
			    $priceInclTax = $order->getPriceInclTax();
			    $productId = $order->getProductId();
			    $productType = $order->getProductType();
			    $qtyCanceled = $order->getQtyCanceled();
			    $qtyInvoiced = $order->getQtyInvoiced();
			    $qtyOrdered = $order->getQtyOrdered();
			    $qtyRefunded = $order->getQtyRefunded();
			    $qtyShipped = $order->getQtyShipped();
			    $quoteItemId = $order->getQuoteItemId();
			    $rowInvoiced = $order->getRowInvoiced();
			    $rowTotal = $order->getRowTotal();
			    $rowTotalInclTax = $order->getRowTotalInclTax();
			    $rowWeight = $order->getRowWeight();
			    $sku = $order->getSku();
			    $storeId = $order->getStoreId();
			    $taxAmount = $order->getTaxAmount();
			    $taxInvoiced = $order->getTaxInvoiced();
			    $taxPercent = $order->getTaxPercent();
			    $updatedAt = $order->getUpdatedAt();
			    $weight = $order->getWeight();

			    $allItems = $order->getAllItems();

			    //billing information
			    $addressTypeBilling = $billingAddress->getAddressType();
			    $cityBilling = $billingAddress->getCity();
			    $countryIdBilling = $billingAddress->getCountryId();
			    $customerIdBilling = $billingAddress->getCustomerId();
			    $emailBilling = $billingAddress->getEmail();
			    $entityIdBilling = $billingAddress->getEntityId();
			    $firstnameBilling = $billingAddress->getFirstname();
			    $lastnameBilling = $billingAddress->getLastname();
			    $parentIdBilling = $billingAddress->getParentId();
			    $postcodeBilling = $billingAddress->getPostcode();
			    $streetBilling = $billingAddress->getStreet();
			    $telephoneBilling = $billingAddress->getTelephone();
			    //end

			    //shipping information
			    if(empty($shippingAddress)) {
			    	$addressTypeShipping = $billingAddress->getAddressType();
				    $cityShipping = $billingAddress->getCity();
				    $countryIdShipping = $billingAddress->getCountryId();
				    $customerIdShipping = $billingAddress->getCustomerId();
				    $emailShipping = $billingAddress->getEmail();
				    $entityIdShipping = $billingAddress->getEntityId();
				    $firstnameShipping = $billingAddress->getFirstname();
				    $lastnameShipping = $billingAddress->getLastname();
				    $parentIdShipping = $billingAddress->getParentId();
				    $postcodeShipping = $billingAddress->getPostcode();
				    $streetShipping = $billingAddress->getStreet();
				    $telephoneShipping = $billingAddress->getTelephone();
			    } else {
			    	$addressTypeShipping = $shippingAddress->getAddressType();
				    $cityShipping = $shippingAddress->getCity();
				    $countryIdShipping = $shippingAddress->getCountryId();
				    $customerIdShipping = $shippingAddress->getCustomerId();
				    $emailShipping = $shippingAddress->getEmail();
				    $entityIdShipping = $shippingAddress->getEntityId();
				    $firstnameShipping = $shippingAddress->getFirstname();
				    $lastnameShipping = $shippingAddress->getLastname();
				    $parentIdShipping = $shippingAddress->getParentId();
				    $postcodeShipping = $shippingAddress->getPostcode();
				    $streetShipping = $shippingAddress->getStreet();
				    $telephoneShipping = $shippingAddress->getTelephone();
			    }

			    //payment information
			    $payment = $order->getPayment();
			    $paymentMethod = $payment->getMethodInstance();
			    $paymentMethodTitle = $paymentMethod->getTitle();
			    $paymentMethodCode = $paymentMethod->getCode();
			    $amountOrdered = $payment->getAmountOrdered();
			    $baseAmountOrdered = $payment->getBaseAmountOrdered();
			    $cclast4 = $payment->getCcLast4();

			    $items = [];
			    $itemArray = [];
			    $tableVal = '';

			    $failedOrderList = [];

			    foreach($allItems as $item){
					$itemArray['amount_refunded'] = $item->getAmountRefunded();
					$itemArray['base_amount_refunded'] = $item->getBaseAmountRefunded();
					$itemArray['base_discount_amount'] = $item->getBaseDiscountAmount();
					$itemArray['base_discount_invoiced'] = $item->getBaseDiscountInvoiced();
					$itemArray['base_discount_tax_compensation_amount'] = $item->getBaseDiscountTaxCompensationAmount();
					$itemArray['base_original_price'] = $item->getBaseOriginalPrice();
					$itemArray['base_price'] = $item->getBasePrice();
					$itemArray['base_price_incl_tax'] = $item->getBasePriceInclTax();
					$itemArray['base_row_invoiced'] = $item->getBaseRowInvoiced();
					$itemArray['base_row_total'] = $item->getBaseRowTotal();
					$itemArray['base_row_total_incl_tax'] = $item->getBaseRowTotalInclTax();
					$itemArray['base_tax_amount'] = $item->getBaseTaxAmount();
					$itemArray['base_tax_invoiced'] = $item->getBaseTaxInvoiced();
					$itemArray['created_at'] = $item->getCreatedAt();
					$itemArray['discount_amount'] = $item->getDiscountAmount();
					$itemArray['discount_invoiced'] = $item->getDiscountInvoiced();
					$itemArray['discount_percent'] = $item->getDiscountPercent();
					$itemArray['free_shipping'] = $item->getFreeShipping();
					$itemArray['discount_tax_compensation_amount'] = $item->getDiscountTaxCompensationAmount();
					$itemArray['is_qty_decimal'] = $item->getIsQtyDecimal();
					$itemArray['is_virtual'] = $item->getIsVirtual();
					$itemArray['item_id'] = $item->getItemId();
					$itemArray['name'] = $item->getName();
					$itemArray['no_discount'] = $item->getNoDiscount();
					$itemArray['order_id'] = $item->getOrderId();
					$itemArray['original_price'] = $item->getOriginalPrice();
					$itemArray['price'] = $item->getPrice();
					$itemArray['price_incl_tax'] = $item->getPriceInclTax();
					$itemArray['product_id'] = $item->getProductId();
					$itemArray['product_type'] = $item->getProductType();
					$itemArray['qty_canceled'] = $item->getQtyCanceled();
					$itemArray['qty_invoiced'] = $item->getQtyInvoiced();
					$itemArray['qty_ordered'] = $item->getQtyOrdered();
					$itemArray['qty_refunded'] = $item->getQtyRefunded();
					$itemArray['qty_shipped'] = $item->getQtyShipped();
					$itemArray['quote_item_id'] = $item->getQuoteItemId();
					$itemArray['row_invoiced'] = $item->getRowInvoiced();
					$itemArray['row_total'] = $item->getRowTotal();
					$itemArray['row_total_incl_tax'] = $item->getRowTotalInclTax();
					$itemArray['row_weight'] = $item->getRowWeight();
					$itemArray['sku'] = $item->getSku();
					$itemArray['store_id'] = $item->getStoreId();
					$itemArray['tax_amount'] = $item->getTaxAmount();
					$itemArray['tax_invoiced'] = $item->getTaxInvoiced();
					$itemArray['tax_percent'] = $item->getTaxPercent();
					$itemArray['updated_at'] = $item->getUpdatedAt();
					$itemArray['weight'] = $item->getWeight();

					$items[] = $itemArray;
					$productSku = $item->getSku();
					$productName = $item->getName();
					$prodQtyOrdered = $item->getQtyOrdered();
					$tableVal .= '<tr>
						<td>
						  	<table border="0" cellspacing="0" cellpadding="0" class="container-social" align="left" width="100%" style="text-align:left;">
						  	<tr>
						  	<td width="20"></td>
						  	<td width="560">
						    	<p style="font-size:14px;color:#000;font-family:helvetica;text-align:left;font-weight:bold;">Product SKU: '.$productSku.'</p>
						    	<p style="font-size:14px;color:#000;font-family:helvetica;text-align:left;font-weight:bold;">Product Name: '.$productName.'</p>
						    <p style="font-size:14px;color:#000;font-family:helvetica;text-align:left;font-weight:bold;">Product Quantity Ordered: '.$prodQtyOrdered.'</p>
						  </td>
						  <td width="20"></td>

						  </tr>
						  </table>
						</td>
					</tr>';
			    }

			    $billingAddressArray = [
					'address_type' => $addressTypeBilling,
					'city' => $cityBilling,
					'country_id' => $countryIdBilling,
					'customer_id' => $customerIdBilling,
					'email' => $emailBilling,
					'entity_id' => $entityIdBilling,
					'firstname' => $firstnameBilling,
					'lastname' => $lastnameBilling,
					'parent_id' => $parentIdBilling,
					'postcode' => $postcodeBilling,
					'street' => $streetBilling,
					'telephone' => $telephoneBilling
			    ];

			    $shippingAddressArray = [
					'address_type' => $addressTypeShipping,
					'city' => $cityShipping,
					'country_id' => $countryIdShipping,
					'customer_id' => $customerIdShipping,
					'email' => $emailShipping,
					'entity_id' => $entityIdShipping,
					'firstname' => $firstnameShipping,
					'lastname' => $lastnameShipping,
					'parent_id' => $parentIdShipping,
					'postcode' => $postcodeShipping,
					'street' => $streetShipping,
					'telephone' => $telephoneShipping

			    ];

			    $paymentArray = [
			      	'payment_title' => $paymentMethodTitle,
			      	'amount_ordered' => $amountOrdered,
			      	'base_amount_ordered' => $baseAmountOrdered,
			      	'base_shipping_amount' => $baseShippingAmount,
			      	'cc_last4' => $cclast4,
			      	'entity_id' => $entityId,
			      	'method' => $paymentMethodCode,
			      	'shipping_amount' => $shippingAmount
			    ];

			    //The JSON data.
			    $newjsonData = [
				    'website' => $websiteName,
				    'base_currency_code' => $orderBaseCurrencyCode,
				    'base_discount_amount' => $baseDiscountAmount,
				    'base_grand_total' => $baseGrandTotal,
				    'base_discount_tax_compensation_amount' => $baseDiscountTaxCompensationAmount,
				    'base_shipping_amount' => $baseShippingAmount,
				    'base_shipping_discount_amount' => $baseShippingDiscountAmount,
				    'base_shipping_discount_tax_compensation_amnt' => $baseShippingDiscountTaxCompensationAmnt,
				    'base_shipping_incl_tax' => $baseShippingInclTax,
				    'base_shipping_tax_amount' => $baseShippingTaxAmount,
				    'base_subtotal' => $baseSubtotal,
				    'base_subtotal_incl_tax' => $baseSubtotalInclTax,
				    'base_tax_amount' => $baseTaxAmount,
				    'base_total_due' => $baseTotalDue,
				    'base_to_global_rate' => $baseToGlobalRate,
				    'base_to_order_rate' => $baseToOrderRate,
				    'billing_address_id' => $billingAddressId,
				    'created_at' => $createdAt,
				    'customer_email' => $customerEmail,
				    'customer_firstname' => $customerFirstname,
				    'customer_group_id' => $customerGroupId,
				    'customer_id' => $customerId,
				    'customer_is_guest' => $customerIsGuest,
				    'customer_lastname' => $customerLastname,
				    'customer_note_notify' => $customerNoteNotify,
				    'discount_amount' => $discountAmount,
				    'email_sent' => $emailSent,
				    'entity_id' => $entityId,
				    'global_currency_code' => $globalCurrencyCode,
				    'grand_total' => $grandTotal,
				    'discount_tax_compensation_amount' => $discountTaxCompensationAmount,
				    'increment_id' => $incrementId,
				    'is_virtual' => $isVirtual,
				    'order_currency_code' => $orderCurrencyCode,
				    'protect_code' => $protectCode,
				    'quote_id' => $quoteId,
				    'remote_ip' => $remoteIp,
				    'shipping_amount' => $shippingAmount,
				    'shipping_description' => $shippingDescription,
				    'shipping_discount_amount' => $shippingDiscountAmount,
				    'shipping_discount_tax_compensation_amount' => $shippingDiscountTaxCompensationAmount,
				    'shipping_incl_tax' => $shippingInclTax,
				    'shipping_tax_amount' => $shippingTaxAmount,
				    'state' => $state,
				    'status' => $status,
				    'store_currency_code' => $storeCurrencyCode,
				    'store_id' => $storeId,
				    'store_name' => $storeName,
				    'store_to_base_rate' => $storeToBaseRate,
				    'store_to_order_rate' => $storeToOrderRate,
				    'subtotal' => $subtotal,
				    'subtotal_incl_tax' => $subtotalInclTax,
				    'tax_amount' => $taxAmount,
				    'total_due' => $totalDue,
				    'total_item_count' => $totalItemCount,
				    'total_qty_ordered' => $totalQtyOrdered,
				    'updated_at' => $updatedAt,
				    'weight' => $weight,
				    'x_forwarded_for' => $xForwardedFor,
				    'items' => $items,
				    'billing_address' => $billingAddressArray,
				    'shipping_address' => $shippingAddressArray,
				    'payment' => $paymentArray
			    ];

			    $emailTop = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			    <html xmlns="http://www.w3.org/1999/xhtml">
			    <head>
			    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
			    <meta name="format-detection" content="telephone=no">
			    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
			    <title>Sololuxury</title>

			    <style type="text/css">
			    tr td a div,tr td a{
			    display:list-item !important;
			    list-style:none;
			    }
			    p{
			    margin: 0;
			    }
			    </style>
			    </head>
			    <body style="height: 100%; background-color: #fefdfb; width: 100%;">
			    <div style="background-color:#fefdfb;margin:0;-webkit-font-smoothing:antialiased;-ms-text-size-adjust:none;width:100%;padding:0;">
			    <table border="0" cellspacing="0" cellpadding="0" style="width:600px;background-color:#fefdfb;border:solid 1px #ebebeb;" align="center" class="container" id="main">
			    <tr>
			    <td height="20"></td>
			    </tr>

			    <tr>
			      <td>
			        <table border="0" cellspacing="0" cellpadding="0" class="container-social" align="left" width="100%" style="text-align:left;">
			        <tr>
			        <td width="20"></td>
			        <td width="560">
			          <p>Hello team,</p>
			          <p>&nbsp;</p>
			          <p>Kindly note that order with order no #'.$orderRealId.' and below item details was not pushed to ERP from Magento.</p>
			          <p>&nbsp;</p>
			        </td>
			        <td width="20"></td>

			        </tr>
			        </table>
			      </td>
			    </tr>';
			    $emailBottom = '<tr>
			      <td>
			        <table border="0" cellspacing="0" cellpadding="0" class="container-social" align="left" width="100%" style="text-align:left;">
			        <tr>
			        <td width="20"></td>
			        <td width="560">
			          <p>&nbsp;</p>
			          <p>Thank you,</p>
			          <p>Sololuxury.</p>
			          <p>&nbsp;</p>
			        </td>
			        <td width="20"></td>

			        </tr>
			        </table>
			      </td>
			    </tr>
			    </table>
			    </div>
			    </body>
			    </html>';

			    //push order to erp
			    $url = 'https://erp.theluxuryunlimited.com/api/magento/order-create';
			    $curl = $this->curlFactory->create();
				$curl->setOption(CURLOPT_RETURNTRANSFER, 1);
				$curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$curl->post($url, json_encode($newjsonData));

			    //Execute the request
			    try {
			      	$result   = $curl->getBody();
			      	$response = json_decode($result,true);
			      	$status = $response['status'];
			      	$message = $response['message'];
			      	$response['incrementId'] =  $orderRealId;

			      	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/OrderErpApi.log');
					$logger = new \Zend\Log\Logger();
					$logger->addWriter($writer);
					$logger->info($response);

					if($status == 'true') {
			      		$order->setOrderErpSyncFlag(1);
			      		$order->save();
			      	}

			     	if($status != 'true'){
			     		$order->setOrderErpSyncFlag(2);
			      		$order->save();
						$config = array('auth' => 'login',
						'port' => 587,
						"ssl" => "tls",
						'username' => "buying@amourint.com",
						'password' => "Kgdg81#1");
						$transport = new \Zend_Mail_Transport_Smtp('mail.amourint.com', $config);
						$emailSubject = 'Order push to ERP failed from | Sololuxury';
						$subject = $emailSubject;
						$smartDubaiBody = $emailTop.$tableVal.$emailBottom;
						$to = [$customerEmail];
						$customerName = $customerFirstname;
						$zendemail = new \Zend_Mail();
						$zendemail->setSubject($subject);
						$zendemail->setBodyHtml($smartDubaiBody);
						$zendemail->setFrom('sololuxurydubai@gmail.com','Sololuxury');
						$zendemail->addTo($to, $customerName);
						try {
						    $zendemail->send($transport);
						    $failedOrderList = [[$orderRealId]];
				            $file = fopen($orderFailedFileName,"a");
				            foreach ($failedOrderList as $line) {
				              fputcsv($file, $line);
				            }
				            fclose($file);
						} catch (Exception $ex) {
							$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/OrderErpApi.log');
							$logger = new \Zend\Log\Logger();
							$logger->addWriter($writer);
							$logger->info($ex->getMessage().' == '.$incrementId);
						}
			      	}
			      //end
			    } catch(Exception $e) {
			      	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/OrderErpApi.log');
					$logger = new \Zend\Log\Logger();
					$logger->addWriter($writer);
					$logger->info($e->getMessage().' == '.$incrementId);
			    }
			}
		}
		return true;
    }
}
