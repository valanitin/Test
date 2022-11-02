<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\External;

/**
 * Contains all external URLs that the extension calls.
 *
 * @since 2.5.0
 */
class Urls
{
    /**
     * Provides xml with extensions changelogs.
     */
    public const CHANGELOGS_URL = 'plumrocket.com/media/info/changelogs_m2.xml';

    /**
     * Provides xml with the latest version of each extension.
     */
    public const VERSIONS_URL = 'plumrocket.com/media/info/versions.xml';

    /**
     * Service for collections the extensions usage statistic.
     */
    public const STATISTIC_URL = 'api.plumrocket.net/v1/statistic';

    /**
     * Base url for retrieving our notifications.
     */
    public const NOTIFICATIONS_URL = 'plumrocket.com/notificationmanager/feed/index';

    /**
     * Service for collections the extensions pingbacks.
     */
    public const PINGBACK_URL = 'plumrocket.com/ilg/pingback';

    /**
     * Service for collections the marketplace extensions pingbacks.
     */
    public const MARKETPLACE_PINGBACK_URL = 'plumrocket.com/ilg/pingback/marketplace';
}
