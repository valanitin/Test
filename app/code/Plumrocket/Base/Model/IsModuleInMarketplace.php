<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Model;

use Plumrocket\Base\Model\Extension\Customer\GetKey;
use Plumrocket\Base\Model\Extension\Customer\IsMarketplaceKey;

/**
 * Class IsMarketplace
 *
 * @since 2.1.6
 */
class IsModuleInMarketplace
{
    /**
     * @var \Plumrocket\Base\Model\Extension\Customer\GetKey
     */
    private $getCustomerKey;

    /**
     * @var \Plumrocket\Base\Model\Extension\Customer\IsMarketplaceKey
     */
    private $isMarketplaceKey;

    /**
     * @param \Plumrocket\Base\Model\Extension\Customer\GetKey           $getCustomerKey
     * @param \Plumrocket\Base\Model\Extension\Customer\IsMarketplaceKey $isMarketplaceKey
     */
    public function __construct(
        GetKey $getCustomerKey,
        IsMarketplaceKey $isMarketplaceKey
    ) {
        $this->getCustomerKey = $getCustomerKey;
        $this->isMarketplaceKey = $isMarketplaceKey;
    }

    /**
     * @param string $moduleName
     * @return bool
     */
    public function execute(string $moduleName): bool
    {
        $moduleName = trim($moduleName, '\\');
        if (false !== strpos($moduleName, '_')) {
            $moduleName = explode('_', $moduleName)[1];
        } elseif (false !== strpos($moduleName, '\\')) {
            $moduleName = explode('\\', $moduleName)[1];
        }

        return $this->isMarketplaceKey->execute($this->getCustomerKey->execute($moduleName));
    }
}
