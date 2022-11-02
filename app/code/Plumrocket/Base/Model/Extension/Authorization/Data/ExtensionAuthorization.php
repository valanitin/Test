<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization\Data;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface;
use Plumrocket\Base\Helper\Config;

/**
 * @since 2.5.0
 */
class ExtensionAuthorization extends AbstractModel implements ExtensionAuthorizationInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Plumrocket\Base\Helper\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry      $registry
     * @param \Plumrocket\Base\Helper\Config   $config
     * @param string                           $name
     * @param array                            $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Config $config,
        string $name,
        array $data = []
    ) {
        parent::__construct($context, $registry, null, null, $data);
        $this->name = $name;
        $this->config = $config;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Plumrocket\Base\Model\ResourceModel\ExtensionAuthorization::class);
    }

    /**
     * @inheritDoc
     */
    public function getModuleName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function isAuthorized(): bool
    {
        return $this->getStatus() && ($this->getStatus() % 100 === 0);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): int
    {
        return (int) $this->_getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(int $status): ExtensionAuthorizationInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function setSignature(string $signature): ExtensionAuthorizationInterface
    {
        return $this->setData(self::SIGNATURE, $signature);
    }

    /**
     * @inheritDoc
     */
    public function getDate(): string
    {
        return (string) $this->_getData(self::DATE);
    }

    /**
     * @inheritDoc
     */
    public function setDate(string $date): ExtensionAuthorizationInterface
    {
        return $this->setData(self::DATE, $date);
    }

    /**
     * @inheritDoc
     */
    public function isCached(): bool
    {
        if ($this->config->isDebugMode()) {
            return false;
        }

        return $this->getDate() > date('Y-m-d H:i:s')
            && $this->getDate() < date('Y-m-d H:i:s', time() + 30 * 86400);
    }
}
