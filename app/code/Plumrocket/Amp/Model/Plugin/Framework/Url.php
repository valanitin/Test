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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Framework;

use Magento\Framework\UrlInterface;

/**
 * Plugin for processing builtin cache
 */
class Url
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Plumrocket\Amp\Model\AmpUrl\Exclude\RoutePath
     */
    private $excludeRoutePath;

    /**
     * Url constructor.
     *
     * @param \Plumrocket\Amp\Helper\Data                    $dataHelper
     * @param \Plumrocket\Amp\Model\AmpUrl\Exclude\RoutePath $excludeRoutePath
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper,
        \Plumrocket\Amp\Model\AmpUrl\Exclude\RoutePath $excludeRoutePath
    ) {
        $this->_dataHelper = $dataHelper;
        $this->excludeRoutePath = $excludeRoutePath;
    }

    /**
     * Add amp parameter for each url
     *
     * @param UrlInterface $subject
     * @param              $url
     * @param null         $routePath
     * @param null         $routeParams
     * @return string
     */
    // @codingStandardsIgnoreLine
    public function afterGetUrl(UrlInterface $subject, $url, $routePath = null, $routeParams = null)
    {
        if ($this->_dataHelper->isAmpRequest() && ! $this->excludeRoutePath->isExcluded($routePath)) {
            return $this->_dataHelper->getAmpUrl($url);
        }

        return $url;
    }
}
