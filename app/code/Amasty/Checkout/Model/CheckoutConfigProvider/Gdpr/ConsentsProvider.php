<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */

declare(strict_types=1);

namespace Amasty\Checkout\Model\CheckoutConfigProvider\Gdpr;

use Amasty\Checkout\Model\ModuleEnable;
use Magento\Framework\ObjectManagerInterface;

class ConsentsProvider
{
    /**
     * @var ModuleEnable
     */
    private $moduleEnable;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $consentsConfig;

    public function __construct(
        ModuleEnable $moduleEnable,
        ObjectManagerInterface $objectManager
    ) {
        $this->moduleEnable = $moduleEnable;
        $this->objectManager = $objectManager;
    }

    /**
     * @return array
     */
    public function getConsentsConfig(): array
    {
        if (!$this->moduleEnable->isGdprEnable()) {
            return [];
        }

        if ($this->consentsConfig === null) {
            $consentsConfig = [];
            $this->consentsConfig = $consentsConfig;
        }

        return $this->consentsConfig;
    }
}
