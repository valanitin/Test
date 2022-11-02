<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization;

use Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface;

/**
 * @since 2.5.0
 */
class Message
{
    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Key
     */
    private $authorizationKey;

    /**
     * @param \Plumrocket\Base\Model\Extension\Authorization\Key $authorizationKey
     */
    public function __construct(Key $authorizationKey)
    {
        $this->authorizationKey = $authorizationKey;
    }

    /**
     * Receive product description
     *
     * @param \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface $authorization
     * @return string
     */
    public function get(ExtensionAuthorizationInterface $authorization): string
    {
        if ($authorization->isAuthorized()) {
            return 'Congratulations! Your serial key is now activated. ' .
                'Thank you for choosing Plumrocket Inc as your Magento extension provider!';
        }

        if (! $this->authorizationKey->get($authorization->getModuleName())) {
            return 'Serial key is missing. Please login to your account at ' .
                '<a target="_blank" href="https://plumrocket.com/downloadable/customer/products">' .
                'https://plumrocket.com' .
                '</a> to copy your serial key for this product. ' .
                'Read this <a target="_blank" href="https://plumrocket.com/docs/general/magento-license-installation">' .
                'wiki article</a> for more info.';
        }

        if (! $authorization->isAuthorized()) {
            if ($authorization->getStatus() === 503) {
                return 'Your serial key is not valid for Magento Enterprise Edition. ' .
                    'Please purchase Magento Enterprise Edition license for this product at ' .
                    '<a href="https://plumrocket.com" target="_blank">https://plumrocket.com</a>';
            }

            return 'Serial key is not valid for this domain. Please go to ' .
                '<a href="https://plumrocket.com" target="_blank">https://plumrocket.com</a> ' .
                'to purchase new license for live site.  ' .
                'Testing or development subdomains can be added to your license free of charge. Read this ' .
                '<a href="https://plumrocket.com/docs/general/updating-license-domains"  target="_blank">wiki ' .
                'article</a> for more info.';
        }

        return '';
    }
}
