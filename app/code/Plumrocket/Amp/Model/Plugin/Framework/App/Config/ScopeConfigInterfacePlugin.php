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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Framework\App\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Plumrocket\Amp\Helper\Data;

/**
 * ScopeConfigInterface plugin
 */
class ScopeConfigInterfacePlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data\Proxy
     */
    private $dataHelper;

    /**
     * @var array
     */
    private $ampConfigValue = [
        'po_compressor/general/is_enabled' => 0,
        'cleverdeferjs/general/enable' => 0,
        'dev/js/move_script_to_bottom' => 0,
    ];

    /**
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Data $dataHelper,
        array $data = []
    ) {
        $this->ampConfigValue = array_merge($this->ampConfigValue, $data);
        $this->dataHelper = $dataHelper;
    }

    /**
     * After getValue
     *
     * @param  ScopeConfigInterface $scopeConfigInterface
     * @param  mixed                $result
     * @param  string               $path
     * @param  string               $scopeType
     * @param  null|string          $scopeCode
     * @return mixed
     */
    public function afterGetValue(
        ScopeConfigInterface $subject,
        $result,
        $path,
        $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ) {
        $ampConfigValue = isset($this->ampConfigValue[$path]) ? $this->ampConfigValue[$path] : null;

        if (null !== $ampConfigValue && $this->dataHelper->isAmpRequest()) {
            return $ampConfigValue;
        }

        return $result;
    }
}
