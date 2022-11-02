<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Customer;

use Magento\Framework\Config\DataInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Reads customer key from pr_extensions.xml
 *
 * @since 2.5.0
 */
class GetKey
{
    /**
     * @var \Magento\Framework\Config\DataInterface
     */
    private $extensionsConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\Config\DataInterface   $extensionsConfig
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        DataInterface $extensionsConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->extensionsConfig = $extensionsConfig;
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $moduleName e.g. SocialLoginFree
     * @return string
     */
    public function execute(string $moduleName): string
    {
        $customerKey = (string) $this->extensionsConfig->get("$moduleName/customer/key");
        if (! $customerKey) {
            $customerKey = $this->getKeyFromMainHelper($moduleName);
        }

        return $customerKey;
    }

    /**
     * TODO: remove after adding pr_extensions.xml to all modules
     *
     * @param string $moduleName
     * @return \Plumrocket\Base\Helper\Base|false
     */
    private function getKeyFromMainHelper($moduleName): string
    {
        $type = "Plumrocket\\{$moduleName}\Helper\Main";
        try {
            /** @var \Plumrocket\Base\Helper\Base $mainHelper */
            $mainHelper = $this->objectManager->get($type);
            return (string) $mainHelper->getCustomerKey();
        } catch (\ReflectionException $reflectionException) {
            return '';
        }
    }
}
