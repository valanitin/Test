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

namespace Plumrocket\Amp\ViewModel;

/**
 * Class GlobalState
 *
 * Use this class for fill default values of global state
 *
 * @package Plumrocket\Amp\ViewModel
 */
class GlobalState implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const STATE_NAME = 'prampGlobal';

    /**
     * @var array
     */
    private $arrayState = [];

    /**
     * @var bool
     */
    private $isStateInitialized = false;

    /**
     * @return string
     */
    public function getStateName()
    {
        return self::STATE_NAME;
    }

    /**
     * @return false|string
     */
    public function getStateJson()
    {
        $this->isStateInitialized = true;

        return json_encode($this->arrayState);
    }

    /**
     * @param array $state
     * @return $this
     */
    public function addStateProperty(array $state)
    {
        $this->arrayState = array_merge($this->arrayState, $state);

        return $this;
    }
}
