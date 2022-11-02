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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Amp\Model\Plugin\Magepow\SpeedOptimizer\Helper;

/**
 * @since 2.9.16
 */
class Data
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(\Plumrocket\Amp\Helper\Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Disable Magepow_SpeedOptimizer for AMP mode
     *
     * @param $subject
     * @param $result
     * @return false
     */
    public function afterGetConfigModule($subject, $result)
    {
        if ($this->dataHelper->isAmpRequest()) {
            return false;
        }

        return $result;
    }
}
