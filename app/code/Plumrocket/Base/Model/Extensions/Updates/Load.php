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

namespace Plumrocket\Base\Model\Extensions\Updates;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Xml\Parser;
use Plumrocket\Base\Helper\Data;

/**
 * Retrieve information in format
 * [moduleName => data]
 *
 * @since 2.3.0
 */
class Load
{
    /**
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    private $httpsDriver;

    /**
     * @var array[]
     */
    private $updates;

    /**
     * @var \Magento\Framework\Xml\Parser
     */
    private $xmlParser;

    /**
     * Load constructor.
     *
     * @param \Magento\Framework\Filesystem\DriverInterface $httpsDriver
     * @param \Magento\Framework\Xml\Parser                 $xmlParser
     */
    public function __construct(DriverInterface $httpsDriver, Parser $xmlParser)
    {
        $this->httpsDriver = $httpsDriver;
        $this->xmlParser = $xmlParser;
    }

    /**
     * @return array|array[]|string
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if (null === $this->updates) {
            $this->updates = [];

            if ($fileContent = $this->readInternalFile()) {
                try {
                    $updates = $this->xmlParser->loadXML($fileContent)->xmlToArray();
                    if (isset($updates['modules'])) {
                        $this->updates = $updates['modules'];
                    }
                } catch (LocalizedException $e) {
                    $this->updates = [];
                }
            }
        }

        return $this->updates;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    private function readInternalFile() : string
    {
        try {
            $fileContent = "";
        } catch (FileSystemException $e) {
            throw new NotFoundException(__('Fail to fetch latest versions of Plumrocket extensions.'));
        }

        return $fileContent;
    }
}
