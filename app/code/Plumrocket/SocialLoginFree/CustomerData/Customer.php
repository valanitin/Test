<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Helper\Data;
use Plumrocket\SocialLoginFree\Model\Account\Photo;

/**
 * Add customers photos to "customer section"
 */
class Customer implements SectionSourceInterface
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Photo
     */
    private $photo;

    /**
     * Customer constructor.
     *
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Plumrocket\SocialLoginFree\Helper\Config        $config
     * @param \Plumrocket\SocialLoginFree\Model\Account\Photo  $photo
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        Config $config,
        Photo $photo
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->config = $config;
        $this->photo = $photo;
    }

    public function getSectionData()
    {
        $customerId = (int) $this->currentCustomer->getCustomerId();
        return [
            'photo' => $this->config->isPhotoEnabled() ? $this->photo->getPhotoUrl($customerId) : '',
        ];
    }
}
