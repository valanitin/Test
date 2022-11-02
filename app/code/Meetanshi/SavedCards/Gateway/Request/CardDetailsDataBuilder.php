<?php

namespace Meetanshi\SavedCards\Gateway\Request;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Meetanshi\SavedCards\Helper\Data as SavedCardsHelper;
use Meetanshi\SavedCards\Helper\Logger as SavedCardsLogger;
use Meetanshi\SavedCards\Observer\DataAssignObserver;

/**
 * Class CardDetailsDataBuilder
 *
 * @package Meetanshi\SavedCards\Gateway\Request
 */
class CardDetailsDataBuilder implements BuilderInterface
{
    /**
     *
     */
    const CARD_NUMBER = 'cc_number';
    /**
     *
     */
    const CC_HOLDER_NAME = 'card_holder_name';

    /**
     *
     */
    const EXP_MONTH = 'cc_exp_month';

    /**
     *
     */
    const EXP_YEAR = 'cc_exp_year';

    /**
     *
     */
    const CVV = 'cc_cvv';

    const LAST_FOUR = 'last_four';


    /**
     * @var SavedCardsHelper
     */
    private $savedcardsHelper;
    /**
     * @var SavedCardsLogger
     */
    private $savedcardsLogger;
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * CardDetailsDataBuilder constructor.
     *
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        SavedCardsHelper $savedcardsHelper,
        SavedCardsLogger $savedcardsLogger,
        EncryptorInterface $encryptor
    ) {
        $this->savedcardsHelper = $savedcardsHelper;
        $this->savedcardsLogger = $savedcardsLogger;
        $this->encryptor = $encryptor;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);

        $data = $payment->getAdditionalInformation();

        $month = $this->formatMonth($data[OrderPaymentInterface::CC_EXP_MONTH]);
        $year = substr($data[OrderPaymentInterface::CC_EXP_YEAR], 2, 3);
        $cardNumber = $data[OrderPaymentInterface::CC_NUMBER_ENC];
        $lastFour = substr($this->encryptor->decrypt($data[OrderPaymentInterface::CC_NUMBER_ENC]), -4);
        $cvn = $data[DataAssignObserver::CC_CID_ENC];

        return [
            self::CARD_NUMBER => $cardNumber,
            self::EXP_MONTH => $month,
            self::EXP_YEAR => $year,
            self::CC_HOLDER_NAME => $data[self::CC_HOLDER_NAME],
            self::CVV => $cvn,
            self::LAST_FOUR => $lastFour
        ];
    }

    /**
     * @param $month
     * @return string|null
     */
    private function formatMonth($month)
    {
        return !empty($month) ? sprintf('%02d', $month) : null;
    }
}
