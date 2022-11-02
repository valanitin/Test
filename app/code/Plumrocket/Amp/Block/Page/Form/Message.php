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

namespace Plumrocket\Amp\Block\Page\Form;

/**
 * @method setUniqueFormKey($uniqueFormKey)
 * @method getUniqueFormKey()
 */
class Message extends \Magento\Framework\View\Element\Template
{
    const SHOW_SUCCESS = 3;
    const SHOW_ERROR = 1;
    const SHOW_BOTH = 2;

    const SUCCESS_ANIMATION_ID_PREFIX = 'successMessage-';
    const FAIL_ANIMATION_ID_PREFIX = 'errorMessage-';

    const SUCCESS_MESSAGE_CLASS_TEMPLATE = 'form-%s-success-message';
    const FAIL_MESSAGE_CLASS_TEMPLATE = 'form-%s-error-message';

    /**
     * Form events for render result
     * https://www.ampproject.org/docs/reference/components/amp-form#success/error-response-rendering
     */
    const SUBMIT_SUCCESS_EVENT_NAME = 'submit-success';
    const SUBMIT_ERROR_EVENT_NAME = 'submit-error';
    const SUBMIT_START_EVENT_NAME = 'submit';

    /**
     * @var int
     */
    private $showMessageType = self::SHOW_BOTH;

    /**
     * @var int
     */
    private $canShowWaitingMessage = true;

    /**
     * @var array
     */
    private $actions = [
        self::SUBMIT_START_EVENT_NAME   => [],
        self::SUBMIT_SUCCESS_EVENT_NAME => [],
        self::SUBMIT_ERROR_EVENT_NAME   => [],
    ];

    /**
     * @return int
     */
    private function getShowMessage()
    {
        return $this->showMessageType;
    }

    /**
     * @param int $showMessageType
     * @return $this
     */
    public function setShowMessageType($showMessageType)
    {
        $this->showMessageType = (int)$showMessageType;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableWaitingMessage()
    {
        $this->canShowWaitingMessage = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableWaitingMessage()
    {
        $this->canShowWaitingMessage = false;

        return $this;
    }

    /**
     * Set default template
     *
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _beforeToHtml() // @codingStandardsIgnoreLine
    {
        if (! $this->getTemplate()) {
            $this->setTemplate('Plumrocket_Amp::page/form/message/template.phtml');
        }

        return parent::_beforeToHtml();
    }

    /**
     * @return bool
     */
    public function canShowSuccessMessage()
    {
        return $this->getShowMessage() >= self::SHOW_BOTH;
    }

    /**
     * @return bool
     */
    public function canShowFailMessage()
    {
        return $this->getShowMessage() <= self::SHOW_BOTH;
    }

    /**
     * @return bool
     */
    public function canShowWaitingMessage()
    {
        return $this->canShowWaitingMessage;
    }

    /**
     * @param $uniqueFormKey
     * @return string
     */
    public function getSuccessMessageClass($uniqueFormKey = null)
    {
        return sprintf(
            self::SUCCESS_MESSAGE_CLASS_TEMPLATE,
            $uniqueFormKey ?: $this->getUniqueFormKey()
        );
    }

    /**
     * @param $uniqueFormKey
     * @return string
     */
    public function getFailMessageClass($uniqueFormKey = null)
    {
        return sprintf(
            self::FAIL_MESSAGE_CLASS_TEMPLATE,
            $uniqueFormKey ?: $this->getUniqueFormKey()
        );
    }

    /**
     * @param      $action
     * @param null $key
     * @return $this
     */
    public function addSubmitAction($action, $key = null)
    {
        if ($key === null) {
            $this->actions[self::SUBMIT_START_EVENT_NAME][] = $action;
        } else {
            $this->actions[self::SUBMIT_START_EVENT_NAME][$key] = $action;
        }

        return $this;
    }

    /**
     * @param      $action
     * @param null $key
     * @return $this
     */
    public function addSubmitSuccessAction($action, $key = null)
    {
        if ($key === null) {
            $this->actions[self::SUBMIT_SUCCESS_EVENT_NAME][] = $action;
        } else {
            $this->actions[self::SUBMIT_SUCCESS_EVENT_NAME][$key] = $action;
        }

        return $this;
    }

    /**
     * @param      $action
     * @param null $key
     * @return $this
     */
    public function addSubmitErrorAction($action, $key = null)
    {
        if ($key === null) {
            $this->actions[self::SUBMIT_ERROR_EVENT_NAME][] = $action;
        } else {
            $this->actions[self::SUBMIT_ERROR_EVENT_NAME][$key] = $action;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFormMessageEvents()
    {
        $this->_beforeGetFormMessageEvents();

        return $this->renderActions();
    }

    /**
     * Set default actions
     *
     * @return $this
     */
    protected function _beforeGetFormMessageEvents() // @codingStandardsIgnoreLine
    {
        $this->addSubmitErrorAction(self::FAIL_ANIMATION_ID_PREFIX . $this->getUniqueFormKey() . '.restart');

        return $this;
    }

    /**
     * @return string
     */
    private function renderActions()
    {
        $ampJs = '';

        foreach ($this->actions as $event => $actions) {
            if (empty($actions)) {
                continue;
            }

            $ampJs .= $event . ':' . implode(',', $actions) . ';';
        }

        return $ampJs;
    }
}
