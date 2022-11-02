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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Magento\Theme\Block\Html;

class BreadcrumbsPlugin
{
     /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     * @return  void
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Theme\Block\Html\Breadcrumbs $subject
     * @param string $crumbName
     * @param array $crumbInfo
     * @return array
     */
    public function beforeAddCrumb($subject, $crumbName, $crumbInfo)
    {
        /**
         * Add amp parameter for breadcrumb link
         * (Only for amp request)
         */
        if ($this->dataHelper->isAmpRequest()
            && isset($crumbInfo['link'])
            && $crumbInfo['link'] !== ''
        ) {
            $crumbInfo['link'] = $this->dataHelper->getAmpUrl($crumbInfo['link']);
        }

        return [$crumbName, $crumbInfo];
    }
}
