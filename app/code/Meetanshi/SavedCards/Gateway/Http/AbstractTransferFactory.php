<?php

namespace Meetanshi\SavedCards\Gateway\Http;

use Magento\Framework\Xml\Generator;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Meetanshi\SavedCards\Helper\Data as SavedCardsHelper;

/**
 * Class AbstractTransferFactory
 */
abstract class AbstractTransferFactory implements TransferFactoryInterface
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var TransferBuilder
     */
    protected $transferBuilder;

    /**
     * @var Generator
     */
    protected $generator;
    /**
     * @var SavedCardsHelper
     */
    protected $savedcardsHelper;
    /**
     * Transaction Type
     *
     * @var string
     */
    private $action;

    /**
     * AbstractTransferFactory constructor.
     *
     * @param ConfigInterface $config
     * @param TransferBuilder $transferBuilder
     * @param Generator $generator
     * @param null $action
     */
    public function __construct(
        ConfigInterface $config,
        TransferBuilder $transferBuilder,
        Generator $generator,
        SavedCardsHelper $savedcardsHelper,
        $action = null
    )
    {
        $this->config = $config;
        $this->transferBuilder = $transferBuilder;
        $this->generator = $generator;
        $this->action = $action;
        $this->savedcardsHelper = $savedcardsHelper;
    }
}
