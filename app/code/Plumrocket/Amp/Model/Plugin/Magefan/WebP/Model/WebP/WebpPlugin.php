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
 * @package     Plumrocket magento
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Magefan\WebP\Model\WebP;

use Magefan\WebP\Model\WebP;
use Plumrocket\Amp\Helper\Data;

class WebpPlugin
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
     * @param $result
     * @return bool
     */
    public function afterIsEnabled(WebP $subject, $result)
    {
        if ($this->dataHelper->isAmpRequest()) {
            return false;
        }

        return $result;
    }
}
