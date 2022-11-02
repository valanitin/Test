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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Framework\Url\Helper;

use Magento\Framework\Url\Helper\Data;

class DataPlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * DataPlugin constructor.
     *
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    public function afterRemoveRequestParam(
        Data $subject,
        string $result,
        string $url,
        string $paramKey,
        bool $caseSensitive = false
    ) {
        if ('p' === $paramKey && $this->dataHelper->isAmpRequest()) {
            return $this->dataHelper->removeRequestParam($url, $paramKey, $caseSensitive);
        }

        return $result;
    }
}
