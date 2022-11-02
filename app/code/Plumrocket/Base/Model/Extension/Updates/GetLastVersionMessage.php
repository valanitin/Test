<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Updates;

use Plumrocket\Base\Model\Extension\GetModuleName;
use Plumrocket\Base\Model\External\Urls;

/**
 * @since 2.5.0
 * @deprecated since 2.8.0 - no more used
 */
class GetLastVersionMessage
{
    /**
     * @var \Plumrocket\Base\Model\Extension\GetModuleName
     */
    private $getModuleName;

    /**
     * @param \Plumrocket\Base\Model\Extension\GetModuleName $getModuleName
     */
    public function __construct(GetModuleName $getModuleName)
    {
        $this->getModuleName = $getModuleName;
    }

    public function execute(string $moduleName): array
    {
        $moduleName = $this->getModuleName->execute($moduleName);

        $xmlPath = 'https://' . Urls::VERSIONS_URL;
        $message = '';
        $version = '';

        try {
            $context = stream_context_create(
                [
                    'http' => [
                        'timeout'       => 2,
                        'ignore_errors' => true,
                    ],
                ]
            );
            $string = file_get_contents($xmlPath, false, $context);

            if ($string && $moduleName) {
                $xml = simplexml_load_string($string);
                if ($xml && isset($xml->Magento2->{$moduleName})) {
                    $extData = $xml->Magento2->{$moduleName} ?? null;

                    if ($extData !== null && isset($extData->message, $extData->version)) {
                        $message = (string)$extData->message;
                        $version = (string)$extData->version;
                    }
                }
            }
        } catch (\Exception $e) {}

        return ['message' => $message, 'newv' => $version];
    }
}
