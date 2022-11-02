<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Framework\View\Model\Layout;

class MergePlugin
{
    /**
     * @var \Magento\Framework\App\RequestInterface|null
     */
    private $someRequest = null;

    /**
     * MergePlugin constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->someRequest = $request;
    }

    /**
     * Only for amp widgets by ajax request
     *
     * @param \Magento\Framework\View\Model\Layout\Merge $subject
     * @param array                                      $handles
     * @return array
     */
    public function beforeLoad(\Magento\Framework\View\Model\Layout\Merge $subject, $handles = [])
    {
        if ('admin' === $this->someRequest->getModuleName()
            && 'blocks' === $this->someRequest->getActionName()
            && 0 === strpos($this->someRequest->getParam('code'), 'amp_')
            && 'true' === $this->someRequest->getParam('isAjax')
        ) {
            $subject->addHandle('pramp');
        }

        return is_string($handles) ? [$handles] : $handles;
    }
}
