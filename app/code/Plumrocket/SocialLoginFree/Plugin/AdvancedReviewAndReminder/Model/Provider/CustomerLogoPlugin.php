<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Plugin\AdvancedReviewAndReminder\Model\Provider;

use Plumrocket\SocialLoginFree\Model\Account\Photo;

class CustomerLogoPlugin
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Photo
     */
    private $photo;

    /**
     * CustomerLogoPlugin constructor.
     *
     * @param \Plumrocket\SocialLoginFree\Model\Account\Photo $photo
     */
    public function __construct(
        Photo $photo
    ) {
        $this->photo = $photo;
    }

    /**
     * @param \Plumrocket\AdvancedReviewAndReminder\Model\Provider\CustomerLogo $subject
     * @param string                                                            $result
     * @return string
     */
    public function afterGenerateLogoUrl(
        \Plumrocket\AdvancedReviewAndReminder\Model\Provider\CustomerLogo $subject,
        $result
    ) {
        if ($customerId = $subject->getProcessCustomerId()) {
            $customerPhoto = $this->photo->getPhotoUrl($customerId);

            if ($customerPhoto) {
                return $customerPhoto;
            }
        }

        return $result;
    }
}
