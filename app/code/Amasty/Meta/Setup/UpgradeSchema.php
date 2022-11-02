<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Amasty\Meta\Setup\UpgradeSchema as Operations;

class UpgradeSchema implements UpgradeSchemaInterface
{
    private $addBrandOptions;

    public function __construct(
        Operations\AddBrandOptions $addBrandOptions
    ) {
        $this->addBrandOptions = $addBrandOptions;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addBrandOptions->execute($setup);
        }

        $setup->endSetup();
    }
}
