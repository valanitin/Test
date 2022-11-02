<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\ViewModel;

use Magento\Framework\Exception\NotFoundException;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Model\Extension\GetListOfInstalled;
use Plumrocket\Base\Model\Extension\Updates\Get as GetUpdates;

/**
 * @since 2.3.0
 */
class GetExtensionInformationWithUpdates
{
    /**
     * @var \Plumrocket\Base\Model\Extension\GetListOfInstalled
     */
    private $getPlumrocketInstalledExtensions;

    /**
     * @var \Plumrocket\Base\Model\Extension\Updates\Get
     */
    private $getUpdates;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @param \Plumrocket\Base\Model\Extension\GetListOfInstalled   $getPlumrocketInstalledExtensions
     * @param \Plumrocket\Base\Model\Extension\Updates\Get          $getUpdates
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface $getExtensionInformation
     */
    public function __construct(
        GetListOfInstalled $getPlumrocketInstalledExtensions,
        GetUpdates $getUpdates,
        GetExtensionInformationInterface $getExtensionInformation
    ) {
        $this->getPlumrocketInstalledExtensions = $getPlumrocketInstalledExtensions;
        $this->getUpdates = $getUpdates;
        $this->getExtensionInformation = $getExtensionInformation;
    }

    /**
     * Get extensions updates information.
     *
     * @return array
     */
    public function execute(): array
    {
        $extensions = $this->getPlumrocketInstalledExtensions->execute();
        try {
            $updates = $this->getUpdates->execute($extensions);
        } catch (NotFoundException $e) {
            $updates = false;
        }

        $result = [];
        foreach ($extensions as $extensionName) {
            $extensionInfo = $this->getExtensionInformation->execute($extensionName);
            if ($extensionInfo->isService()) {
                continue;
            }

            $extensionData = [
                'name' => $extensionInfo->getTitle() ?: $extensionName,
                'documentation' => $extensionInfo->getDocumentationLink(),
                'installedVersion' => $extensionInfo->getInstalledVersion(),
            ];

            if (false !== $updates) {
                $extensionData['successFetchUpdates'] = true;
                $extensionData['updates'] = $updates[$extensionName];
            } else {
                $extensionData['successFetchUpdates'] = false;
                $extensionData['updates'] = [];
            }

            $result[] = $extensionData;
        }

        usort(
            $result,
            static function ($extension1, $extension2) {
                $hasUpdates1 = empty($extension1['updates']);
                $hasUpdates2 = empty($extension2['updates']);
                if ($hasUpdates1 !== $hasUpdates2) {
                    return $hasUpdates1 <=> $hasUpdates2;
                }

                return $extension1['name'] <=> $extension2['name'];
            }
        );

        return $result;
    }
}
