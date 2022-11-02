<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension;

use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Module\Dir;

/**
 * @since 2.8.4
 */
class GetInstallationType
{
    public const NON_COMPOSER_EXTENSION_PATH = 'app/code/Plumrocket';

    public const NON_COMPOSER_INSTALL_TYPE = 'non-composer';

    public const COMPOSER_INSTALL_TYPE = 'composer';

    /**
     * @var Reader
     */
    private $moduleDirReader;

    /**
     * @param Reader $moduleDirReader
     */
    public function __construct(Reader $moduleDirReader)
    {
        $this->moduleDirReader = $moduleDirReader;
    }

    /**
     * Detect installation type of the Plumrocket extensions
     *
     * @param string $moduleName
     * @return string
     */
    public function execute(string $moduleName): string
    {
        $moduleName = "Plumrocket_" . $moduleName;
        $etcDirPath = $this->moduleDirReader->getModuleDir(Dir::MODULE_ETC_DIR, $moduleName);

        return strpos($etcDirPath, self::NON_COMPOSER_EXTENSION_PATH) !== false
            ? self::NON_COMPOSER_INSTALL_TYPE : self::COMPOSER_INSTALL_TYPE;
    }
}
