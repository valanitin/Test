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

namespace Plumrocket\Amp\Block\Page\Form\Product\AddTo;

class Message extends \Plumrocket\Amp\Block\Page\Form\Message
{
    /**
     * @var \Plumrocket\Amp\ViewModel\GlobalState
     */
    private $globalState;

    /**
     * @var \Plumrocket\Amp\Model\State
     */
    private $stateModel;

    private $path = ['form', 'addTo'];

    /**
     * Message constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\Amp\ViewModel\GlobalState            $globalState
     * @param \Plumrocket\Amp\Model\State                      $stateModel
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Amp\ViewModel\GlobalState $globalState,
        \Plumrocket\Amp\Model\State $stateModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->globalState = $globalState;
        $this->stateModel = $stateModel;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function addAmpJsPathKey($key)
    {
        $this->path[] = $key;

        return $this;
    }

    /**
     * @return array
     */
    public function getProductIdStatePath()
    {
        return $this->path;
    }

    /**
     * @return \Plumrocket\Amp\Block\Page\Form\Message
     */
    protected function _beforeGetFormMessageEvents()
    {
        $this->globalState->addStateProperty(
            $this->stateModel->createArrayByPath($this->getProductIdStatePath(), 0, false)
        );

        $this->addSubmitSuccessAction(self::SUCCESS_ANIMATION_ID_PREFIX . $this->getUniqueFormKey() . '.restart');

        $this->addSubmitSuccessAction(
            'AMP.setState(' . $this->stateModel->createAmpJsSetter($this->getProductIdStatePath(), 0) . ')',
            'hideLoader'
        )
        ->addSubmitErrorAction(
            'AMP.setState(' . $this->stateModel->createAmpJsSetter($this->getProductIdStatePath(), 0) . ')',
            'hideLoader'
        );

        $this->disableWaitingMessage();

        return parent::_beforeGetFormMessageEvents();
    }
}
