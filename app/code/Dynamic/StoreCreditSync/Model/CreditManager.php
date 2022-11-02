<?php

namespace Dynamic\StoreCreditSync\Model;

class CreditManager implements \Dynamic\StoreCreditSync\Api\CreditManagerInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Transaction
     */
    private $transaction;

    /**
     * @param \Swarming\StoreCredit\Model\Transaction $transaction
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Transaction $transaction
    ) {
        $this->transaction = $transaction;
    }

    /**
     * @param int $customerId
     * @return float
     */
    public function getBalance($customerId)
    {
        $data = [];

        if($customerId) {

            $transactions = $this->transaction->getCollection()->addFieldToFilter("customer_id", ["eq" => $customerId]);

            if(!empty($transactions) && count($transactions) > 0) {
                foreach ($transactions as $transaction) {
                    $data[] = [
                        "transaction_id" => $transaction->getTransactionId(),
                        "customer_id" => $transaction->getCustomerId(),
                        "amount" => $transaction->getAmount(),
                        "balance" => $transaction->getBalance(),
                        "used" => $transaction->getUsed(),
                        "order_id" => $transaction->getOrderId(),
                        "invoice_id" => $transaction->getInvoiceId(),
                        "creditmemo_id" => $transaction->getCreditmemoId(),
                        "suppress_notification" => $transaction->getSuppressNotification(),
                        "at_time" => $transaction->getAtTime(),
                        "summary" => $transaction->getSummary(),
                        "type" => $transaction->getType(),
                    ];
                }
            } else {
                $data = [['status' => 'No Data', 'message' => __('There are no transaction data in this website.') ]];
            }
        } else {
            $data = [['status' => 'No Data', 'message' => __('Enter the Customer Id and try again.') ]];
        }

       return $data;
    }
}
