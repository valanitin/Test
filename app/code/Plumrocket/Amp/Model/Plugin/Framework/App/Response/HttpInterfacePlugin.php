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
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Framework\App\Response;

use Magento\Framework\App\Response\HttpInterface;

/**
 * Plugin for processing send HTTP response
 */
class HttpInterfacePlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->_request = $request;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * Change X-Frame-Options if only options request
     * @param  \Magento\Framework\App\Response\HttpInterface $subject
     * @param  string
     * @return string
     */
    public function beforeSendResponse(HttpInterface $subject)
    {
        if (!$this->_dataHelper->moduleEnabled()){
            return null;
        }

        if (($this->_request->getParam('only-options') == 1)
            || ($this->_request->getFullActionName() == 'pramp_cart_add')
        ) {
            $subject->clearHeader($subject::HEADER_X_FRAME_OPT);
            //$subject->setHeader('Content-Security-Policy', 'frame-src self www.google.com', true);
        }

        return null;
    }
}
