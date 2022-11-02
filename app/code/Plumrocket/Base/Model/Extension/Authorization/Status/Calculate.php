<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization\Status;

use Plumrocket\Base\Model\Extension\Authorization\Key;

/**
 * @since 2.5.0
 */
class Calculate
{
    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Key
     */
    private $key;

    /**
     * @param \Plumrocket\Base\Model\Extension\Authorization\Key $key
     */
    public function __construct(Key $key)
    {
        $this->key = $key;
    }

    public function execute(string $moduleName): int
    {
        $key = $this->key->get($moduleName);
        return (strlen($key) === 32 && $key[9] === $moduleName[2] && (strlen($moduleName) < 4
                || $key[20] === $moduleName[3])) ? 500 : 201;
    }
}
