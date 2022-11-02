<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization;

use Magento\Framework\Exception\NoSuchEntityException;
use Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface;
use Plumrocket\Base\Api\ExtensionAuthorizationRepositoryInterface;
use Plumrocket\Base\Model\ResourceModel\ExtensionAuthorization;

class Repository implements ExtensionAuthorizationRepositoryInterface
{
    /**
     * @var ExtensionAuthorizationInterface[]
     */
    private $cache = [];

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Signature
     */
    private $signature;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Factory
     */
    private $extensionAuthorizationFactory;

    /**
     * @var \Plumrocket\Base\Model\ResourceModel\ExtensionAuthorization
     */
    private $resourceModel;

    /**
     * @param \Plumrocket\Base\Model\Extension\Authorization\Signature    $signature
     * @param \Plumrocket\Base\Model\Extension\Authorization\Factory      $extensionAuthorizationFactory
     * @param \Plumrocket\Base\Model\ResourceModel\ExtensionAuthorization $resourceModel
     */
    public function __construct(
        Signature $signature,
        Factory $extensionAuthorizationFactory,
        ExtensionAuthorization $resourceModel
    ) {
        $this->signature = $signature;
        $this->extensionAuthorizationFactory = $extensionAuthorizationFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @inheritDoc
     */
    public function get(string $moduleName): ExtensionAuthorizationInterface
    {
        if (! isset($this->cache[$moduleName])) {
            $this->resourceModel->deleteOld(); // remove old records before loading
            $signature = $this->signature->create($moduleName);
            $authorization = $this->extensionAuthorizationFactory->create($moduleName);
            $this->resourceModel->load($authorization, $signature, ExtensionAuthorizationInterface::SIGNATURE);
            if (! $authorization->getId()) {
                throw new NoSuchEntityException(
                    __("The extension authorization that was requested doesn't exist. Verify the module and try again.")
                );
            }

            $this->cache[$moduleName] = $authorization;
        }
        return $this->cache[$moduleName];
    }

    /**
     * @param \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface|Data\ExtensionAuthorization $extension
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function save(ExtensionAuthorizationInterface $extension): ExtensionAuthorizationInterface
    {
        unset($this->cache[$extension->getModuleName()]);
        $this->resourceModel->save($extension);
        return $this->get($extension->getModuleName());
    }
}
