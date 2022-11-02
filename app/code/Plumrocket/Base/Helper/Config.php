<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Plumrocket\Base\Api\ConfigUtilsInterface;

/**
 * @since 2.3.0
 */
class Config extends AbstractHelper
{
    public const XML_PATH_NOTIFICATIONS_ENABLED = 'plumbase/notifications/enabled';
    public const XML_PATH_NOTIFICATION_LISTS = 'plumbase/notifications/subscribed_to';
    public const XML_PATH_IS_ENABLED_STATISTIC = 'plumbase/system/subscribed_to';

    /**
     * @var \Plumrocket\Base\Api\ConfigUtilsInterface
     */
    private $configUtils;

    /**
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Plumrocket\Base\Api\ConfigUtilsInterface $configUtils
     */
    public function __construct(
        Context $context,
        ConfigUtilsInterface $configUtils
    ) {
        parent::__construct($context);
        $this->configUtils = $configUtils;
    }

    /**
     * Check if notifications are enabled.
     *
     * @return bool
     */
    public function isEnabledNotifications(): bool
    {
        return (bool) $this->configUtils->getConfig(self::XML_PATH_NOTIFICATIONS_ENABLED);
    }

    /**
     * Get enabled notification lists.
     *
     * @return array
     */
    public function getEnabledNotificationLists(): array
    {
        return $this->configUtils->prepareMultiselectValue(
            (string) $this->configUtils->getConfig(self::XML_PATH_NOTIFICATION_LISTS)
        );
    }

    /**
     * Check if statistic is enabled.
     *
     * @return bool
     */
    public function isEnabledStatistic(): bool
    {
        return (bool) $this->configUtils->getConfig(self::XML_PATH_IS_ENABLED_STATISTIC);
    }

    /**
     * Useful for debugging extension authorization.
     *
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return false;
    }
}
