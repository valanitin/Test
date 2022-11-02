<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Modal;

use Magento\Framework\ObjectManagerInterface;
use Plumrocket\SocialLoginFree\Helper\Config\Network as NetworkConfig;

/**
 * @since 3.2.0
 */
class UrlResolverFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var NetworkConfig
     */
    private $networkConfig;

    /**
     * @param \Magento\Framework\ObjectManagerInterface         $objectManager
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network $networkConfig
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        NetworkConfig $networkConfig
    ) {
        $this->objectManager = $objectManager;
        $this->networkConfig = $networkConfig;
    }

    /**
     * Create url resolver.
     *
     * @param string $networkCode
     * @return \Plumrocket\SocialLoginFree\Model\Network\Modal\UrlResolverInterface
     */
    public function create(string $networkCode): UrlResolverInterface
    {
        return $this->objectManager->create(
            $this->networkConfig->getModalUrlResolver($networkCode),
            [
                'code' => $networkCode,
                'urlPath' => $this->networkConfig->getModalUrlPath($networkCode),
                'urlParams' => $this->networkConfig->getModalUrlParams($networkCode),
            ]
        );
    }
}
