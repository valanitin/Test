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

namespace Plumrocket\Amp\ViewModel\Form\Product;

class MessageState implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Plumrocket\Amp\Model\State
     */
    private $stateModel;

    /**
     * @var \Plumrocket\Amp\ViewModel\GlobalState
     */
    private $globalState;

    /**
     * MessageState constructor.
     *
     * @param \Plumrocket\Amp\ViewModel\GlobalState $globalState
     * @param \Plumrocket\Amp\Model\State           $stateModel
     */
    public function __construct(
        \Plumrocket\Amp\ViewModel\GlobalState $globalState,
        \Plumrocket\Amp\Model\State $stateModel
    ) {
        $this->stateModel = $stateModel;
        $this->globalState = $globalState;
    }

    /**
     * @param $path
     * @param $productId
     * @return string
     */
    public function getEnableJs($path, $productId) : string
    {
        return $this->stateModel->createAmpJsSetter($path, $productId);
    }

    /**
     * @param $path
     * @param $productId
     * @return string
     */
    public function getClassJs($path, $productId) : string
    {
        return $this->globalState->getStateName() . '.' . implode('.', $path) . ' == ' . $productId . '';
    }
}
