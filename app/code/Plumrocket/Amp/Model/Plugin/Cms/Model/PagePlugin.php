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

namespace Plumrocket\Amp\Model\Plugin\Cms\Model;

class PagePlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * Page constructor.
     *
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(\Plumrocket\Amp\Helper\Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Cms\Model\Page $subject
     * @param                         $result
     * @return string
     */
    public function afterGetContent(\Magento\Cms\Model\Page $subject, $result)
    {
        if ($this->dataHelper->isAmpRequest() && $ampHtml = trim($subject->getData('pramp_html'))) {
            return $ampHtml;
        }

        return $result;
    }
}
