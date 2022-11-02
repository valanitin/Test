<?php
/**
 * Copyright © Firas Mohammed(firasath90@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Firas\Price\Model\Calculator;

use Magento\Framework\Exception\LocalizedException;

/**
 * Round processor pool
 */
class RoundProcessorPool implements RoundProcessorPoolInterface
{
    /**
     * Round processors
     *
     * @var RoundProcessorInterface[]
     */
    private $processors;

    /**
     * Initialize pool
     *
     * @param RoundProcessorInterface[] $processors
     */
    public function __construct(
        array $processors = []
    ) {
        foreach ($processors as $processor) {
            if (!$processor instanceof RoundProcessorInterface) {
                throw new LocalizedException(
                    __('Round Processor must implement %1.', RoundProcessorInterface::class)
                );
            }
        }
        $this->processors = $processors;
    }

    /**
     * Retrieve the round processor
     *
     * @param string $roundType
     * @return RoundProcessorInterface
     */
    public function getProcessor($roundType)
    {
        if (!isset($this->processors[$roundType])) {
            throw new LocalizedException(
                __('There is no Round Processor registered for type %1.', $roundType)
            );
        }
        return $this->processors[$roundType];
    }
}
