<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Our extensions sometimes require Helper/Data, in case when module has not this helper we give this class
 *
 * @deprecated use for backward compatibility with old extensions
 * @since 2.3.7
 */
class DataFallback extends AbstractHelper
{
}
