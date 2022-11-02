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

namespace Plumrocket\Amp\Model;

use Plumrocket\Amp\ViewModel\GlobalState;

class State
{
    /**
     * @param array  $path
     * @param string $state
     * @return string
     */
    public function createAmpJsGetter(array $path, $state = GlobalState::STATE_NAME) : string
    {
        if ($state) {
            array_unshift($path, $state);
        }

        return implode('.', $path);
    }

    /**
     * @param array  $path
     * @param mixed  $value
     * @param string $state
     * @return string
     */
    public function createAmpJsSetter(array $path, $value, $state = GlobalState::STATE_NAME) : string
    {
        if ($state) {
            array_unshift($path, $state);
        }

        return $this->arrayToJsObject($this->createChild($path, $value));
    }

    /**
     * @param        $path
     * @param        $value
     * @param string $state
     * @return string
     */
    public function createAmpJsCondition($path, $value, $state = GlobalState::STATE_NAME) : string
    {
        if ($state) {
            array_unshift($path, $state);
        }

        return implode('.', $path) . ' == ' . $this->serialize($value) . '';
    }

    /**
     * @param array $path
     * @param       $value
     * @param string $state
     * @return array
     */
    public function createArrayByPath(array $path, $value, $state = GlobalState::STATE_NAME) : array
    {
        if ($state) {
            array_unshift($path, $state);
        }

        return $this->createChild($path, $value);
    }

    /**
     * @param array $path
     * @param       $value
     * @return array
     */
    private function createChild(array $path, $value) : array
    {
        $item = [];

        $key = array_shift($path);

        if (empty($path)) {
            $item[$key] = $value;
        } else {
            $item[$key] = $this->createChild($path, $value);
        }

        return $item;
    }

    /**
     * Convert array to js object
     *
     * @param array $config
     * @return string
     */
    public function arrayToJsObject(array $config) : string
    {
        $count = count($config);
        $i = 0;
        $str = '{';
        foreach ($config as $key => $value) {
            $str .= $key . ': ' . (is_array($value) ? $this->arrayToJsObject($value) : $this->serialize($value));
            if ($count > 1 && ++$i !== $count) {
                $str .= ', ';
            }
        }

        $str .= '}';
        return $str;
    }

    /**
     * @param $value
     * @return string|int
     */
    private function serialize($value)
    {
        if (is_string($value)) {
            return '\'' . trim($value, '\'') . '\'';
        }

        return $value;
    }

    /**
     * @deprecated since 2.8.0 - use createAmpJsSetter() instead
     *
     * @param array  $path
     * @param mixed  $value
     * @param string $state
     * @return string
     */
    public function createAmpJsByPath(array $path, $value, $state = GlobalState::STATE_NAME) : string
    {
        return $this->createAmpJsSetter($path, $value, $state);
    }
}
