<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization\Status;

use Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface;
use Plumrocket\Base\Api\ExtensionAuthorizationRepositoryInterface;
use Plumrocket\Base\Model\Extension\Authorization\Signature;

/**
 * @since 2.5.0
 */
class Update
{
    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Signature
     */
    private $signature;

    /**
     * @var \Plumrocket\Base\Api\ExtensionAuthorizationRepositoryInterface
     */
    private $extensionAuthorizationRepository;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Status\Calculate
     */
    private $calculateStatus;

    /**
     * @param \Plumrocket\Base\Model\Extension\Authorization\Signature        $signature
     * @param \Plumrocket\Base\Api\ExtensionAuthorizationRepositoryInterface  $extensionAuthorizationRepository
     * @param \Plumrocket\Base\Model\Extension\Authorization\Status\Calculate $calculateStatus
     */
    public function __construct(
        Signature $signature,
        ExtensionAuthorizationRepositoryInterface $extensionAuthorizationRepository,
        Calculate $calculateStatus
    ) {
        $this->signature = $signature;
        $this->extensionAuthorizationRepository = $extensionAuthorizationRepository;
        $this->calculateStatus = $calculateStatus;
    }

    /**
     * @param \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface $authorization
     * @param int                                                       $cacheLifetime
     * @param int|null                                                  $status
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface
     */
    public function execute(
        ExtensionAuthorizationInterface $authorization,
        int $status = null,
        int $cacheLifetime = 3
    ): ExtensionAuthorizationInterface {
        $authorization->setSignature($this->signature->create($authorization->getModuleName()));
        $authorization->setStatus($status ?? $this->calculateStatus->execute($authorization->getModuleName()));
        $authorization->setDate(date('Y-m-d H:i:s', time() + $cacheLifetime * 86400));
        return $this->extensionAuthorizationRepository->save($authorization);
    }
}
