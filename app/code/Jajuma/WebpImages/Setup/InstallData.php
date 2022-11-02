<?php

namespace Jajuma\WebpImages\Setup;

use Magento\Framework\App\Config\Storage\WriterInterface;
use \Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    protected $configWriter;

    protected $dir;

    public function __construct(WriterInterface $configWriter, DirectoryList $dir)
    {
        $this->configWriter = $configWriter;
        $this->dir = $dir;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->configWriter->save('webp/advance_mode/path_to_cwebp', $this->dir->getPath('app') . '/code/Jajuma/WebpImages/bin/cwebp');
    }
}