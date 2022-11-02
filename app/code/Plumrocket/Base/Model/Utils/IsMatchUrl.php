<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Utils;

/**
 * Check if url is matched by pattern, compare url without "get" params and fragment
 *
 * @since 2.3.1
 */
class IsMatchUrl
{
    /**
     * @var \Plumrocket\Base\Model\Utils\GetRelativePathFromUrl
     */
    private $getRelativePathFromUrl;

    /**
     * @param \Plumrocket\Base\Model\Utils\GetRelativePathFromUrl $getRelativePathFromUrl
     */
    public function __construct(GetRelativePathFromUrl $getRelativePathFromUrl)
    {
        $this->getRelativePathFromUrl = $getRelativePathFromUrl;
    }

    /**
     * @param string $url
     * @param string $pattern
     * @return bool
     */
    public function execute(string $url, string $pattern): bool
    {
        if ('' === $pattern) {
            return false;
        }

        $relativeUrl = $this->getRelativePathFromUrl->execute($url);
        $regex = $this->patternToRegex($pattern);
        return 1 === preg_match('~^' . $regex . '$~', $relativeUrl);
    }

    /**
     * @param string $url
     * @param array  $patterns
     * @return bool
     */
    public function executeList(string $url, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if ($this->execute($url, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $pattern
     * @return string|string[]
     */
    private function patternToRegex(string $pattern)
    {
        $pattern = $this->getRelativePathFromUrl->execute($pattern);
        $regex = str_replace('/', '\/', preg_quote($pattern, '~'));
        return str_replace('\*', '(?:.*)', $regex);
    }
}
