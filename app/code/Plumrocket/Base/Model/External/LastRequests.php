<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\External;

/**
 * Buffer for last external requests.
 *
 * Can be used for debugging store endpoint problems.
 *
 * @since 2.5.7
 */
class LastRequests
{

    /**
     * @var array[]
     */
    private $data = [];

    /**
     * Add request response.
     *
     * @param string $url
     * @param array $params
     * @param array $response
     */
    public function add(string $url, array $params, array $response): void
    {
        if (strpos($url, Urls::PINGBACK_URL.'/extension') !== false) {
            $this->data[] = [
                'request_url' => $url,
                'request_params' => $params,
                'response' => $response
            ];
        }
    }

    /**
     * Get saved data.
     *
     * @return array[]
     */
    public function getList(): array
    {
        return $this->data;
    }

    /**
     * Clear all saved requests data
     */
    public function clear(): void
    {
        $this->data = [];
    }
}
