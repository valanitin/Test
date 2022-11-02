<?php

namespace Meetanshi\SavedCards\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Meetanshi\SavedCards\Helper\Data as SavedCardsHelper;
use Meetanshi\SavedCards\Helper\Logger as SavedCardsLogger;

/**
 * Class Curl
 * @package Meetanshi\SavedCards\Gateway\Http\Client
 */
class Curl implements ClientInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var SavedCardsHelper
     */
    private $helper;

    /**
     * @var SavedCardsLogger
     */
    private $savedcardsLogger;

    /**
     * Curl constructor.
     * @param Logger $logger
     * @param SavedCardsHelper $helper
     * @param SavedCardsLogger $savedcardsLogger
     */
    public function __construct(
        Logger $logger,
        SavedCardsHelper $helper,
        SavedCardsLogger $savedcardsLogger
    )
    {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->savedcardsLogger = $savedcardsLogger;
    }

    /**
     * @inheritdoc
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $log = [
            'request' => $transferObject->getBody()
        ];

        $this->savedcardsLogger->debug('Curl Request', $log);

        try {
            $data = $transferObject->getBody();
            $this->savedcardsLogger->debug('Body Data', (array)$data);
        } catch (\Exception $e) {
            throw new ClientException(__($e->getMessage()));
        } finally {
        }

        return (array)$data;
    }
}
