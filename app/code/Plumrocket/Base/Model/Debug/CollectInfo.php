<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Debug;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Util;
use Magento\Framework\View\Asset\ConfigInterface;
use Magento\Framework\View\Design\Theme\ThemeList;
use Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey;

/**
 * @since 2.3.0
 */
class CollectInfo
{
    /**
     * @var array
     */
    private $notConflictingVendors = [
        'Magento',
        'Amazon',
        'Klarna',
        'MSP',
        'Temando',
        'Vertex',
        'Dotdigitalgroup',
        'Yotpo',
    ];

    /**
     * @var array
     */
    private $notConflictingExtensions = [];

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * @var \Magento\Framework\View\Design\Theme\ThemeList
     */
    private $themeList;

    /**
     * @var \Magento\Framework\Util
     */
    private $util;

    /**
     * @var \Magento\Framework\View\Asset\ConfigInterface
     */
    private $assetConfig;

    /**
     * @var \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey
     */
    private $getTrueCustomerKey;

    /**
     * @param \Magento\Framework\App\State                                 $appState
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                  $dateTime
     * @param \Magento\Framework\App\ProductMetadataInterface              $productMetadata
     * @param \Magento\Framework\Module\ModuleListInterface                $moduleList
     * @param \Magento\Framework\Encryption\EncryptorInterface             $encryptor
     * @param \Magento\Framework\View\Design\Theme\ThemeList               $themeList
     * @param \Magento\Framework\Util                                      $util
     * @param \Magento\Framework\View\Asset\ConfigInterface                $assetConfig
     * @param \Plumrocket\Base\Model\Extension\Customer\GetTrueCustomerKey $getTrueCustomerKey
     */
    public function __construct(
        State $appState,
        DateTime $dateTime,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        EncryptorInterface $encryptor,
        ThemeList $themeList,
        Util $util,
        ConfigInterface $assetConfig,
        GetTrueCustomerKey $getTrueCustomerKey
    ) {
        $this->appState = $appState;
        $this->dateTime = $dateTime;
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
        $this->encryptor = $encryptor;
        $this->themeList = $themeList;
        $this->util = $util;
        $this->assetConfig = $assetConfig;
        $this->getTrueCustomerKey = $getTrueCustomerKey;
    }

    /**
     * @return array
     */
    public function execute() : array
    {
        return [
            'core' => $this->getCoreMagentoInfo(),
            'phpVersion' => $this->util->getTrimmedPhpVersion(),
            'additional' => [
                'time' => $this->dateTime->date(),
                'cssMinification' => $this->assetConfig->isMergeCssFiles(),
                'jsMinification' => $this->assetConfig->isMergeJsFiles(),
                'jsBundling' => $this->assetConfig->isBundlingJsFiles(),
                'minifyHtml' => $this->assetConfig->isMinifyHtml(),
                'potential_conflicts' => $this->getPotentialConflicts(),
                'signature' => $this->encryptor->hash($this->getTrueCustomerKey->execute('Base')),
            ],
        ];
    }

    /**
     * @return array
     */
    private function getCoreMagentoInfo() : array
    {
        return [
            'mode' => $this->appState->getMode(),
            'version' => $this->productMetadata->getVersion(),
            'name' => $this->productMetadata->getName(),
            'edition' => $this->productMetadata->getEdition(),
        ];
    }

    /**
     * @return array
     */
    private function getPotentialConflicts() : array
    {
        return [
            'modules' => $this->getThirdPartyExtensions(),
            'themes' => $this->getThirdPartyThemes(),
        ];
    }

    /**
     * @return array
     */
    private function getThirdPartyExtensions() : array
    {
        $notConflictingVendors = $this->notConflictingVendors;
        $notConflictingExtensions = $this->notConflictingExtensions;

        return array_values(
            array_filter(
                $this->moduleList->getNames(),
                static function ($moduleFullName) use ($notConflictingVendors, $notConflictingExtensions) {
                    $vendor = explode('_', $moduleFullName)[0];
                    if (in_array($vendor, $notConflictingVendors, true)) {
                        return false;
                    }

                    if (in_array($moduleFullName, $notConflictingExtensions, true)) {
                        return false;
                    }

                    return true;
                }
            )
        );
    }

    /**
     * @return array
     */
    private function getThirdPartyThemes() : array
    {
        return array_filter(
            $this->themeList->getColumnValues('code'),
            static function ($theme) {
                return strpos($theme, 'Magento/') !== 0;
            }
        );
    }
}
