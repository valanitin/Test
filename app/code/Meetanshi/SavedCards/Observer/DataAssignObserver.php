<?php

namespace Meetanshi\SavedCards\Observer;

use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Framework\Event\Observer;

/**
 * Class DataAssignObserver
 * @package Meetanshi\SavedCards\Observer
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     *
     */
    const CC_NUMBER = 'cc_number';
    /**
     *
     */
    const CC_CID = 'cc_cid';
    /**
     *
     */
    const CC_CID_ENC = 'cc_cid_enc';
    /**
     *
     */
    const CC_HOLDER_NAME = 'card_holder_name';

    /**
     * @var array
     */
    protected $additionalInformation = [
        self::CC_NUMBER,
        self::CC_CID,
        self::CC_CID_ENC,
        self::CC_HOLDER_NAME,
        OrderPaymentInterface::CC_TYPE,
        OrderPaymentInterface::CC_EXP_MONTH,
        OrderPaymentInterface::CC_EXP_YEAR,
    ];

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additional = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additional)) {
            return;
        }

        $payment = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformation as $additionalInformation) {
            $value = isset($additional[$additionalInformation])
                ? $additional[$additionalInformation]
                : null;

            if ($value === null) {
                continue;
            }

            if ($additionalInformation == self::CC_NUMBER) {
                $payment->setAdditionalInformation(
                    OrderPaymentInterface::CC_NUMBER_ENC,
                    $payment->encrypt($value)
                );

                continue;
            } elseif ($additionalInformation == self::CC_CID) {
                $payment->setAdditionalInformation(
                    self::CC_CID_ENC,
                    $payment->encrypt($value)
                );

                continue;
            }

            $payment->setAdditionalInformation(
                $additionalInformation,
                $value
            );

            $payment->setData(
                $additionalInformation,
                $value
            );
        }
    }
}
