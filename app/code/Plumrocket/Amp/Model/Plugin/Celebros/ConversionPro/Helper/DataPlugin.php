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

namespace Plumrocket\Amp\Model\Plugin\Celebros\ConversionPro\Helper;

use Plumrocket\Amp\Helper\Data;

class DataPlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * DataPlugin constructor.
     * @param Data $dataHelper
     */
    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Celebros\ConversionPro\Helper\Data $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsEnabledOnFrontend(
        \Celebros\ConversionPro\Helper\Data $subject,
        $result
    ) {
        if ($this->dataHelper->isAmpRequest()) {
            return false;
        }

        return $result;
    }
}
