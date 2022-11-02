<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Model;

class Registry
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        if (isset($this->registry[$key])) {
            return $this->registry[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @param $value
     * @param bool $graceful
     */
    public function set(string $key, $value, $graceful = false)
    {
        if (isset($this->registry[$key])) {
            if ($graceful) {
                return;
            }
            throw new \RuntimeException('Registry key "' . $key . '" already exists');
        }
        $this->registry[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function delete(string $key)
    {
        if (isset($this->registry[$key])) {
            if (is_object($this->registry[$key])
                && method_exists($this->registry[$key], '__destruct')
                && is_callable([$this->registry[$key], '__destruct'])
            ) {
                $this->registry[$key]->__destruct();
            }
            unset($this->registry[$key]);
        }
    }

    public function __destruct()
    {
        $keys = array_keys($this->registry);
        array_walk($keys, [$this, 'delete']);
    }
}
