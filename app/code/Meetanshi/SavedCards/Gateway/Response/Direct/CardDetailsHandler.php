<?php

namespace Meetanshi\SavedCards\Gateway\Response\Direct;

use Magento\Customer\Model\Session;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\Config;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;
use Meetanshi\SavedCards\Helper\Data;

/**
 * Class CardDetailsHandler
 * @package Meetanshi\SavedCards\Gateway\Response\Direct
 */
class CardDetailsHandler implements HandlerInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var TimezoneInterface
     */
    private $date;

    /**
     * CardDetailsHandler constructor.
     * @param Config $config
     * @param Session $customerSession
     * @param Data $helper
     * @param EncryptorInterface $encryptor
     * @param TimezoneInterface $date
     */
    public function __construct(Config $config, Session $customerSession, Data $helper, EncryptorInterface $encryptor, TimezoneInterface $date)
    {
        $this->config = $config;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->encryptor = $encryptor;
        $this->date = $date;
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);

        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        $billingAddress = $order->getBillingAddress();
        ContextHelper::assertOrderPayment($payment);

        $cardCvv = $response['cc_cvv'];
        $cardNumber = $response['cc_number'];
        $ccHolderName = $response['card_holder_name'];

        $ccTypes = $this->config->getCcTypes();
        $payment->setAdditionalInformation('card_holder_name', $ccHolderName);
        $payment->setAdditionalInformation(
            'cc_type',
            $ccTypes[$payment->getAdditionalInformation(OrderPaymentInterface::CC_TYPE)]
        );

        $maskCcNumber = 'XXXX-' . $response['last_four'];

        $payment->setAdditionalInformation('cc_last_4', $maskCcNumber);

        $payment->setAdditionalInformation(
            'card_expiry_date',
            sprintf(
                '%s/%s',
                $payment->getAdditionalInformation(OrderPaymentInterface::CC_EXP_MONTH),
                $payment->getAdditionalInformation(OrderPaymentInterface::CC_EXP_YEAR)
            )
        );
        $payment->setAdditionalInformation('card_number', $cardNumber);
        $payment->setAdditionalInformation('cc_cvv', $cardCvv);

        $payment->unsAdditionalInformation(OrderPaymentInterface::CC_NUMBER_ENC);
        $payment->unsAdditionalInformation('cc_cid_enc');
        $payment->unsAdditionalInformation('cc_exp_month');
        $payment->unsAdditionalInformation('cc_exp_year');
        $payment->unsAdditionalInformation('method_title');
    }
}
