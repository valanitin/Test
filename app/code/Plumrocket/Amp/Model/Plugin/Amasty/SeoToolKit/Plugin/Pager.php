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
 * @package     Plumrocket Amp
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Amasty\SeoToolKit\Plugin;

use Amasty\SeoToolKit\Plugin\Pager as AmastyPager;
use Plumrocket\Amp\Helper\Data;

class Pager
{
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * ConfigPlugin constructor.
     * @param Data $dataHelper
     */
    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param object $subject
     * @param object $proceed
     * @param object $subject
     * @param $result
     * @return bool
     */
    public function aroundAfterGetPageUrl(
        \Amasty\SeoToolKit\Plugin\Pager $subject,
        \Closure $proceed,
        $subjectNativePager,
        $result
    ) {
        if (! $this->dataHelper->isAmpRequest()) {
            return $proceed($subjectNativePager, $result);
        }

        return $result;
    }
}
