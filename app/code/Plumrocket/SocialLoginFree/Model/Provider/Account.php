<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Provider;

class Account implements \Plumrocket\SocialLoginFree\Model\AccountProviderInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Account constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $type
     * @return \Plumrocket\SocialLoginFree\Model\Account
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByType($type)
    {
        if ($className = $this->getClassName($type)) {
            return $this->objectManager->get($className); //@codingStandardsIgnoreLine
        }

        throw new \Magento\Framework\Exception\LocalizedException(
            __('Social Network Model not found for type %1', $type)
        );
    }

    /**
     * @param string $type
     * @return \Plumrocket\SocialLoginFree\Model\Account
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createByType($type)
    {
        if ($className = $this->getClassName($type)) {
            return $this->objectManager->create($className); //@codingStandardsIgnoreLine
        }

        throw new \Magento\Framework\Exception\LocalizedException(
            __('Social Network Model not found for type %1', $type)
        );
    }

    /**
     * @param string $type
     * @return string
     */
    private function getClassName($type)
    {
        $className = 'Plumrocket\SocialLoginFree\Model\\'. ucfirst($type);
        if (! $type || ! class_exists($className)) {
            return '';
        }

        return $className;
    }
}
