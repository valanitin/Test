<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Utils;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Plumrocket\Base\Library\Mobile\Detect;

/**
 * @since 2.5.0
 */
class DeviceDetect implements DeviceDetectInterface
{
    /**
     * @var \Plumrocket\Base\Library\Mobile\Detect
     */
    private $mobileDetect;

    /**
     * @var bool[]
     */
    private $cache = [];

    /**
     * @param \Plumrocket\Base\Library\Mobile\Detect $mobileDetect
     */
    public function __construct(Detect $mobileDetect)
    {
        $this->mobileDetect = $mobileDetect;
    }

    /**
     * @inheritDoc
     */
    public function isMobile(RequestInterface $request): bool
    {
        if (! $request instanceof Http) {
            return false;
        }

        $key = 'mobile';
        if (! isset($this->cache[$key])) {
            $this->cache[$key] = $this->mobileDetect->isMobile();
        }

        return $this->cache[$key];
    }

    /**
     * @inheritDoc
     */
    public function isTablet(RequestInterface $request): bool
    {
        if (! $request instanceof Http) {
            return false;
        }

        $key = 'tablet';
        if (! isset($this->cache[$key])) {
            $this->cache[$key] = $this->mobileDetect->isTablet();
        }

        return $this->cache[$key];
    }
}
