<?php

namespace Meetanshi\SavedCards\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

/**
 * Class InitializeCommand
 * @package Meetanshi\SavedCards\Gateway\Command
 */
class InitializeCommand implements CommandInterface
{
    /**
     * @param array $command
     * @return \Magento\Payment\Gateway\Command\ResultInterface|void|null
     */
    public function execute(array $command)
    {
        $state = SubjectReader::readStateObject($command);
        $paymentDO = SubjectReader::readPayment($command);

        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);

        $payment->setAmountAuthorized($payment->getOrder()->getTotalDue());
        $payment->setBaseAmountAuthorized($payment->getOrder()->getBaseTotalDue());
        $payment->getOrder()->setCanSendNewEmailFlag(false);

        $state->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
        $state->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
        $state->setData('is_notified', false);
    }
}
