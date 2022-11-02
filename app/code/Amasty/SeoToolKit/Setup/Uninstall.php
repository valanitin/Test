<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Setup;

use Amasty\SeoToolKit\Api\Data\RedirectInterface;
use Amasty\SeoToolKit\Setup\Uninstall\DeleteEavAttributes;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    const TABLES_TO_DROP = [
        RedirectInterface::TABLE_NAME,
        RedirectInterface::STORE_TABLE_NAME,
    ];

    /**
     * @var DeleteEavAttributes
     */
    private $deleteEavAttributes;

    public function __construct(
        DeleteEavAttributes $deleteEavAttributes
    ) {
        $this->deleteEavAttributes = $deleteEavAttributes;
    }

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        foreach (self::TABLES_TO_DROP as $table) {
            $connection->dropTable($installer->getTable($table));
        }

        $this->deleteEavAttributes->execute();
    }
}
