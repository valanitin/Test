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

namespace Plumrocket\Amp\Model\Result;

class AmpJson extends \Magento\Framework\Controller\Result\Json
{
    const ERROR_KEY = 'error';
    const SUCCESS_KEY = 'success';

    /**
     * @var \Plumrocket\Amp\Helper\Cors
     */
    private $corsHelper;

    /**
     * @var array
     */
    private $messages = [];

    /**
     * AmpJson constructor.
     *
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Plumrocket\Amp\Helper\Cors                  $corsHelper
     */
    public function __construct(
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Plumrocket\Amp\Helper\Cors $corsHelper
    ) {
        parent::__construct($translateInline);
        $this->corsHelper = $corsHelper;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setFormRedirect($url)
    {
        $this->corsHelper->setFormRedirectHeaders($this, $url);

        return $this;
    }

    /**
     * @param string|\Magento\Framework\Phrase $message
     * @return $this
     */
    public function addErrorMessage($message)
    {
        $this->messages[self::ERROR_KEY][] = $message;

        return $this;
    }

    /**
     * @param string|\Magento\Framework\Phrase $message
     * @return $this
     */
    public function addSuccessMessage($message)
    {
        $this->messages[self::SUCCESS_KEY][] = $message;

        return $this;
    }

    /**
     * @param \Magento\Framework\App\Response\HttpInterface $response
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function render(\Magento\Framework\App\Response\HttpInterface $response) //@codingStandardsIgnoreLine
    {
        if (null === $this->json) {
            if (isset($this->messages[self::ERROR_KEY])) {
                $this->setHttpResponseCode(400);
            }

            $this->setData($this->getSerializedMessages());
        }

        return parent::render($response);
    }

    /**
     * @return array
     */
    private function getSerializedMessages() : array
    {
        $serialisedMessages = [];
        foreach ($this->messages as $type => $group) {
            $serialisedMessages[$type] = implode(' ', $group);
        }

        return $serialisedMessages;
    }
}
