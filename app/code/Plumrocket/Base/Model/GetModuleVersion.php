<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Composer\ComposerInformation;
use Magento\Framework\Config\DataInterface;
use Plumrocket\Base\Api\GetModuleVersionInterface;
use Plumrocket\Base\Model\Extension\GetModuleName;

/**
 * Reads version from composer.json
 *
 * Has special logic for metapackage.
 *
 * @since 2.1.6
 */
class GetModuleVersion implements GetModuleVersionInterface
{
    public const CACHE_IDENTIFIER = 'PR_EXTENSION_VERSION';
    public const METAPACKAGE_CACHE_IDENTIFIER = 'PR_METAPACKAGE_VERSION';

    /**
     * @var \Magento\Framework\App\Utility\Files
     */
    private $files;

    /**
     * For example [ ModuleName => '2.1.3' ]
     *
     * @var string[]
     */
    private $versionsLocalCache = [];

    /**
     * For example [ ModuleName => '2.1.3' ]
     *
     * @var string[]
     */
    private $metapackageVersions = [];

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetModuleName
     */
    private $getModuleName;

    /**
     * @var \Magento\Framework\Config\DataInterface
     */
    private $extensionsConfig;

    /**
     * @var \Magento\Framework\Composer\ComposerInformation
     */
    private $composerInformation;

    /**
     * @param \Magento\Framework\App\Utility\Files            $files
     * @param \Magento\Framework\App\CacheInterface           $cache
     * @param \Plumrocket\Base\Model\Extension\GetModuleName  $getModuleName
     * @param \Magento\Framework\Config\DataInterface         $extensionsConfig
     * @param \Magento\Framework\Composer\ComposerInformation $composerInformation
     */
    public function __construct(
        Files $files,
        CacheInterface $cache,
        GetModuleName $getModuleName,
        DataInterface $extensionsConfig,
        ComposerInformation $composerInformation
    ) {
        $this->files = $files;
        $this->cache = $cache;
        $this->getModuleName = $getModuleName;
        $this->extensionsConfig = $extensionsConfig;
        $this->composerInformation = $composerInformation;
    }

    /**
     * Get module version from composer.json.
     *
     * @param string $moduleName
     * @return string
     */
    public function execute($moduleName)
    {
        $moduleName = $this->getModuleName->execute($moduleName);

        if ($this->shouldUseMetapackage($moduleName)) {
            return $this->getMetapackageVersion($moduleName);
        }
        return $this->getModuleVersion($moduleName);
    }

    /**
     * Check if module is part of metapackage.
     *
     * For metapackage module we should use version from metapackage, not from specific module.
     *
     * @param string $moduleName e.g. SocialLoginFree
     * @return bool
     */
    private function shouldUseMetapackage(string $moduleName): bool
    {
        return (bool) $this->extensionsConfig->get("$moduleName/metapackage/composer_name");
    }

    /**
     * Get version of module.
     *
     * @param string $moduleName e.g. SocialLoginFree
     * @return string
     */
    private function getModuleVersion(string $moduleName): string
    {
        if (! isset($this->versionsLocalCache[$moduleName]) || ! $this->versionsLocalCache[$moduleName]) {
            $moduleVersions = $this->getFromCache(self::CACHE_IDENTIFIER);

            if (! isset($moduleVersions[$moduleName])) {
                $this->versionsLocalCache[$moduleName] = '';
                $composerFilePaths = $this->files->getComposerFiles(ComponentRegistrar::MODULE);

                $version = $this->getModuleVersionFromAppCode($composerFilePaths, "Plumrocket/$moduleName");
                if ($version) {
                    $this->versionsLocalCache[$moduleName] = $version;
                } else {
                    $versions = $this->getModulesVersionFromVendorFolder($composerFilePaths);
                    $this->versionsLocalCache = array_merge($this->versionsLocalCache, $versions);
                }

                $moduleVersions = array_merge($moduleVersions, $this->versionsLocalCache);
                $this->saveToCache(self::CACHE_IDENTIFIER, $moduleVersions);
            }

            $this->versionsLocalCache = $moduleVersions;
        }

        return $this->versionsLocalCache[$moduleName];
    }

    /**
     * Get version of metapackage
     *
     * @param string $moduleName
     * @return string
     */
    private function getMetapackageVersion(string $moduleName): string
    {
        if (! isset($this->metapackageVersions[$moduleName]) || ! $this->metapackageVersions[$moduleName]) {
            $moduleVersions = $this->getFromCache(self::METAPACKAGE_CACHE_IDENTIFIER);

            if (! isset($moduleVersions[$moduleName])) {
                $this->metapackageVersions[$moduleName] = '';
                $composerFilePaths = $this->files->getComposerFiles(ComponentRegistrar::MODULE);

                $version = $this->getMetapackageVersionFromAppCode($composerFilePaths, $moduleName);
                if ($version) {
                    $this->metapackageVersions[$moduleName] = $version;
                } else {
                    $this->metapackageVersions[$moduleName] = $this->getMetapackageVersionFromComposer($moduleName);
                }

                $moduleVersions = array_merge($moduleVersions, $this->metapackageVersions);
                $this->saveToCache(self::METAPACKAGE_CACHE_IDENTIFIER, $moduleVersions);
            }

            $this->metapackageVersions = $moduleVersions;
        }

        return $this->metapackageVersions[$moduleName];
    }

    /**
     * Get versions from app/code/Plumrocket
     *
     * @param array  $composerFilePaths
     * @param string $modulePathName
     * @return mixed|string
     */
    private function getModuleVersionFromAppCode(array $composerFilePaths, string $modulePathName)
    {
        foreach ($composerFilePaths as $path => $absolutePath) {
            if (false !== strpos($path, "code/$modulePathName/composer.json")) {
                return $this->extractDataFromComposerJson($path)['version'];
            }
        }

        return '';
    }

    /**
     * Get versions from vendor/plumrocket/
     *
     * @param array $composerFilePaths
     * @return string[]
     */
    private function getModulesVersionFromVendorFolder(array $composerFilePaths): array
    {
        $versions = [];
        foreach ($composerFilePaths as $path => $absolutePath) {
            if (false !== strpos($path, 'plumrocket')) {
                $data = $this->extractDataFromComposerJson($path);
                if ($data['version']) {
                    $versions[$data['name']] = $data['version'];
                }
            }
        }

        return $versions;
    }

    /**
     * Read and parse composer.json
     *
     * @param string $path
     * @return string[]
     */
    public function extractDataFromComposerJson(string $path): array
    {
        if (0 === strpos(trim($path, '/'), 'app')
            || 0 === strpos(trim($path, '/'), 'vendor')
        ) {
            $path = BP . DIRECTORY_SEPARATOR . trim($path, '/');
        }

        $result = ['version' => '', 'name' => ''];
        if (! file_exists($path)) {
            return $result;
        }
        $content = file_get_contents($path);

        if ($content) {
            $jsonContent = json_decode($content, true);
            if (isset($jsonContent['version']) && ! empty($jsonContent['version'])) {
                $result['version'] = $jsonContent['version'];
            }
            if (isset($jsonContent['autoload']['psr-4']) && ! empty($jsonContent['autoload']['psr-4'])) {
                $directoryPath = trim(array_keys($jsonContent['autoload']['psr-4'])[0], '\\');
                $result['name'] = explode('\\', $directoryPath)[1];
            }
        }

        return $result;
    }

    /**
     * Get metapackage version from root magento composer.json file.
     *
     * Installing metapackage via composer do not create folder with composer.json file.
     *
     * @param string $moduleName
     * @return string
     */
    private function getMetapackageVersionFromComposer(string $moduleName): string
    {
        $composerName = $this->extensionsConfig->get("$moduleName/metapackage/composer_name");
        $package = $this->composerInformation->getInstalledMagentoPackages()[$composerName] ?? [];
        return $package['version'] ?? '';
    }

    /**
     * Find metapackage in app/code/Plumrocket and get its version.
     *
     * @param array  $composerFilePaths
     * @param string $moduleName
     * @return string
     */
    private function getMetapackageVersionFromAppCode(array $composerFilePaths, string $moduleName): string
    {
        $directory = (string) $this->extensionsConfig->get("$moduleName/metapackage/directory");
        foreach ($composerFilePaths as $path => $absolutePath) {
            if (false !== strpos($path, "code/Plumrocket/$moduleName/composer.json")) {
                $path = str_replace("Plumrocket/$moduleName", "Plumrocket/$directory", $path);
                return $this->extractDataFromComposerJson($path)['version'];
            }
        }
        return '';
    }

    /**
     * Get versions from cache.
     *
     * @param string $key
     * @return array
     */
    private function getFromCache(string $key): array
    {
        $moduleVersionsJson = $this->cache->load($key);
        if ($moduleVersionsJson) {
            $moduleVersions = (array) json_decode($moduleVersionsJson, true);
        } else {
            $moduleVersions = [];
        }
        return $moduleVersions;
    }

    /**
     * Cache versions as it is heavy operation.
     *
     * @param string $key
     * @param array  $moduleVersions
     */
    private function saveToCache(string $key, array $moduleVersions): void
    {
        $this->cache->save(json_encode($moduleVersions), $key, [Config::CACHE_TAG]);
    }
}
