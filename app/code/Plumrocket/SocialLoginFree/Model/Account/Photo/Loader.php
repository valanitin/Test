<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Account\Photo;

class Loader
{
    /**
     * @param string $url
     * @param int    $recursionLevel
     * @return mixed|string
     */
    public function load(string $url, int $recursionLevel = 1)
    {
        if ($recursionLevel > 5) {
            return '';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if (! $data) {
            return '';
        }

        $dataArray = explode("\r\n\r\n", $data, 2);

        if (count($dataArray) !== 2) {
            return '';
        }

        list($header, $body) = $dataArray;
        if ($httpCode === 301 || $httpCode === 302) {
            $matches = [];
            preg_match('/Location:\s?(.*?)\n/im', $header, $matches);

            if (isset($matches[1])) {
                return $this->load(trim($matches[1]), ++$recursionLevel);
            }
        } else {
            return $body;
        }

        return '';
    }
}
