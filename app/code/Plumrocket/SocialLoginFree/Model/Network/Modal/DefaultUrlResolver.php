<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Modal;

use Magento\Framework\Url\Helper\Data as UrlHelper;
use Plumrocket\SocialLoginFree\Helper\Config\Network;
use Plumrocket\SocialLoginFree\Model\Network\Exception\NetworkIsNotConfiguredException;
use Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver;

/**
 * @since 3.2.0
 */
class DefaultUrlResolver implements UrlResolverInterface
{

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver
     */
    private $modalCallbackUrlResolver;

    /**
     * @var string
     */
    private $urlPath;

    /**
     * @var array
     */
    private $urlParams;

    /**
     * @var string
     */
    private $code;

    /**
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                  $networkConfig
     * @param \Magento\Framework\Url\Helper\Data                                 $urlHelper
     * @param \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver $modalCallbackUrlResolver
     * @param string                                                             $code
     * @param string                                                             $urlPath
     * @param array                                                              $urlParams
     */
    public function __construct(
        Network $networkConfig,
        UrlHelper $urlHelper,
        ModalCallbackUrlResolver $modalCallbackUrlResolver,
        string $code,
        string $urlPath = '',
        array $urlParams = []
    ) {
        $this->networkConfig = $networkConfig;
        $this->urlHelper = $urlHelper;
        $this->modalCallbackUrlResolver = $modalCallbackUrlResolver;
        $this->urlPath = $urlPath;
        $this->urlParams = $urlParams;
        $this->code = $code;
    }

    /**
     * Get modal window popup.
     *
     * @return string
     * @throws \Plumrocket\SocialLoginFree\Model\Network\Exception\NetworkIsNotConfiguredException
     */
    public function getUrl(): string
    {
        $applicationId = $this->networkConfig->getApplicationId($this->code);
        $secret = $this->networkConfig->getApplicationSecretKey($this->code);

        if (! $applicationId || ! $secret) {
            throw new NetworkIsNotConfiguredException(
                __(
                    'Application Id or Secret Key is empty. ' .
                    "Please check Social Login configuration for {$this->code}."
                )
            );
        }

        $urlParams = [];
        foreach ($this->urlParams as $key => $value) {
            if (strpos($value, '{{') === 0) {
                $value = $this->resolveVariable($value);
            }
            $urlParams[$key] = $value;
        }

        return $this->urlHelper->addRequestParam($this->urlPath, $urlParams);
    }

    /**
     * Replace variable with real value.
     *
     * @param string $value
     * @return string
     */
    private function resolveVariable(string $value): string
    {
        switch ($value) {
            case '{{APP_ID}}':
                return $this->networkConfig->getApplicationId($this->code);
            case '{{REDIRECT_URL}}':
                return $this->modalCallbackUrlResolver->getUrl($this->code);
            default:
                return '';
        }
    }
}
