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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Framework\App;

use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\App\FrontControllerInterface;

/**
 * Plugin for disabling newrelicoutput
 */
class FrontControllerInterfacePlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     * @param \Magento\PageCache\Model\Config $config
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
    }

    /**
     * Perform response postprocessing
     *
     * @param FrontControllerInterface $subject
     * @param ResponseInterface|ResultInterface $result
     * @return ResponseHttp|ResultInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(FrontControllerInterface $subject, $result)
    {
        if ($this->_dataHelper->isAmpRequest()) {
            $disableNewRelic = true;
        } else {
            $disableNewRelic = false;
            if (method_exists($result, 'getContent')) {
                $content = $result->getContent();
                if (strpos($content, 'amp=""') !== false) {
                    /* if page has amp tag in html */
                    $disableNewRelic = true;
                }
            }
        }

        if ($disableNewRelic) {
            /**
             * Disable newrelic output
             */
            if (function_exists('newrelic_disable_autorum')) {
                newrelic_disable_autorum();
            }
        }

        return $result;
    }

}
