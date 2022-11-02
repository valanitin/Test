<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Buttons;

class Factory
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\AccountProviderInterface
     */
    private $networkProvider;

    /**
     * Factory constructor.
     *
     * @param \Plumrocket\SocialLoginFree\Model\AccountProviderInterface $networkProvider
     */
    public function __construct(
        \Plumrocket\SocialLoginFree\Model\AccountProviderInterface $networkProvider
    ) {
        $this->networkProvider = $networkProvider;
    }

    /**
     * @param string[] $types
     * @param bool     $onlyEnabled
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create(array $types, $onlyEnabled = true)
    {
        $buttons = [];

        foreach ($types as $type) {
            $network = $this->networkProvider->createByType($type);

            if ($onlyEnabled && ! $network->enabled()) {
                continue;
            }

            $buttons[$network->getProvider()] = $network->getButton();
        }

        return $buttons;
    }
}
