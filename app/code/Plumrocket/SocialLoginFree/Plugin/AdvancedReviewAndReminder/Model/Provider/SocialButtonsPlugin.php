<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Plugin\AdvancedReviewAndReminder\Model\Provider;

class SocialButtonsPlugin
{
    /**
     * @var \Plumrocket\SocialLoginFree\Api\Buttons\ProviderInterface
     */
    private $buttonsProvider;

    public function __construct(\Plumrocket\SocialLoginFree\Api\Buttons\ProviderInterface $buttonsProvider)
    {
        $this->buttonsProvider = $buttonsProvider;
    }

    /**
     * @param \Plumrocket\AdvancedReviewAndReminder\Model\Provider\SocialButtons $subject
     * @param                                                                    $result
     * @return array
     */
    public function afterGetList( //@codingStandardsIgnoreLine
        \Plumrocket\AdvancedReviewAndReminder\Model\Provider\SocialButtons $subject,
        $result
    ) {
        return array_merge($result, $this->buttonsProvider->getPreparedButtons(true, false));
    }
}
