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
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\AmpUrl\Exclude;

use Plumrocket\Amp\Helper\Data;

class RoutePath
{
    /**
     * @var array
     */
    private $excludedRegexPaths = [
        '#^' . Data::SECTION_ID . '/api_.*$#',
    ];

    /**
     * @var array
     */
    private $excludedPaths = [];

    /**
     * Url constructor.
     *
     * @param array $excludedPaths
     * @param array $excludedRegex
     */
    // @codingStandardsIgnoreLine
    public function __construct(
        array $excludedPaths = [],
        array $excludedRegex = []
    ) {
        $this->excludedPaths = $this->mergeExcluded($this->excludedPaths, $excludedPaths);
        $this->excludedRegexPaths = $this->mergeExcluded($this->excludedRegexPaths, $excludedRegex);
    }

    /**
     * @param array $baseExcluded
     * @param array $excludedPaths
     * @return array
     */
    protected function mergeExcluded(array $baseExcluded, array $excludedPaths)  // @codingStandardsIgnoreLine
    {
        foreach ($excludedPaths as $excludedPath) {
            if (! in_array($excludedPath, $baseExcluded, true)) {
                $baseExcluded[] = $excludedPath;
            }
        }

        return $baseExcluded;
    }

    /**
     * @param $routePath
     * @return bool
     */
    public function isExcluded($routePath)
    {
        if (! empty($this->excludedPaths) && in_array($routePath, $this->excludedPaths, true)) {
            return true;
        }

        return $this->searchByRegex($routePath);
    }

    /**
     * @param $routePath
     * @return bool
     */
    private function searchByRegex($routePath)
    {
        $i = 0;
        while (isset($this->excludedRegexPaths[$i])) {
            if (preg_match($this->excludedRegexPaths[$i], $routePath)) {
                return true;
            }

            $i++;
        }

        return false;
    }
}
