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

namespace Plumrocket\Amp\Model\Plugin\Amasty\Shopby\Helper;

use Plumrocket\Amp\Helper\Data;

class ConfigPlugin
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
    public function afterIsEnabled($subject, $result)
    {
        if ($this->dataHelper->isAmpRequest()) {
            return false;
        }

        return $result;
    }

    /**
     * Since version 2.11.3 Amasty_Shopby
     *
     * @param object $subject
     * @param $result
     * @return bool
     */
    public function afterIsCategoryFilterEnabled($subject, $result)
    {
        if ($this->dataHelper->isAmpRequest()) {
            return false;
        }

        return $result;
    }
}
