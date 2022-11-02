<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extensions;

use Plumrocket\Base\Api\GetModuleVersionInterface;
use Plumrocket\Base\Model\Extensions\Updates\Load as LoadUpdates;

/**
 * @since 2.3.0
 */
class GetUpdates
{
    /**
     * @var \Plumrocket\Base\Api\GetModuleVersionInterface
     */
    private $getModuleVersion;

    /**
     * @var LoadUpdates
     */
    private $loadUpdates;

    /**
     * GetUpdates constructor.
     *
     * @param \Plumrocket\Base\Api\GetModuleVersionInterface $getModuleVersion
     * @param \Plumrocket\Base\Model\Extensions\Updates\Load $loadUpdates
     */
    public function __construct(
        GetModuleVersionInterface $getModuleVersion,
        LoadUpdates $loadUpdates
    ) {
        $this->getModuleVersion = $getModuleVersion;
        $this->loadUpdates = $loadUpdates;
    }

    /**
     * @param array $extensions
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException if we cannot fetch file with updates
     */
    public function execute(array $extensions): array
    {
        $result = [];

        $updates = $this->loadUpdates->execute();

        foreach ($extensions as $extensionName) {
            if (isset($updates[$extensionName])) {
                $extensionUpdates = $this->fixBrokenStructure($updates[$extensionName]);

                $result[$extensionName] = $this->extractNewReleases(
                    $this->getModuleVersion->execute("Plumrocket_{$extensionName}"),
                    $extensionUpdates
                );
            } else {
                $result[$extensionName] = [];
            }
        }

        return $result;
    }

    /**
     * @param string $currentVersion
     * @param array  $updatesInfo
     * @return array
     */
    private function extractNewReleases(string $currentVersion, array $updatesInfo): array
    {
        return array_filter(
            $updatesInfo['changelogs'],
            static function ($changelog) use ($currentVersion) {
                return version_compare($currentVersion, $changelog['version'], '<');
            }
        );
    }

    /**
     * When new version has only one changelog, xml can been broken during convert from array
     * We detect and fix it here
     *
     * @param array $updatesInfo
     * @return array
     */
    public function fixBrokenStructure(array $updatesInfo): array
    {
        if (! isset($updatesInfo['changelogs'])) {
            $updatesInfo['changelogs'] = [];
            return $updatesInfo;
        }

        if (isset($updatesInfo['changelogs']['version'])) {
            $updatesInfo['changelogs'] = [$updatesInfo['changelogs']];
        }

        $updatesInfo['changelogs'] = $this->fixChangelogsStructure($updatesInfo['changelogs']);

        return $updatesInfo;
    }

    /**
     * @param array $changelogs
     * @return array
     */
    private function fixChangelogsStructure(array $changelogs): array
    {
        return array_map(
            static function ($changelog) {
                if (isset($changelog['changes']['type'])) {
                    $changelog['changes'] = [$changelog['changes']];
                }
                return $changelog;
            },
            $changelogs
        );
    }
}
