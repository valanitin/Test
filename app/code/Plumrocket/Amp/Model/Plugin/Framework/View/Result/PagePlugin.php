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

namespace Plumrocket\Amp\Model\Plugin\Framework\View\Result;

class PagePlugin
{
    /**
     * @var null|\Plumrocket\Amp\Helper\Data
     */
    private $dataHelper = null;

    /**
     * PagePlugin constructor.
     *
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(\Plumrocket\Amp\Helper\Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Add amp handle as first
     * Amp widgets don't work on category/product page without this fix
     *
     * @param \Magento\Framework\View\Result\Page $subject
     */
    public function beforeAddDefaultHandle(\Magento\Framework\View\Result\Page $subject)
    {
        if ($this->dataHelper->isAmpRequest()) {
            $subject->addHandle('pramp');
        }
    }
}
