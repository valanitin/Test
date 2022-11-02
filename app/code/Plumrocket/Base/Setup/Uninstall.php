<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Setup;

use Plumrocket\Base\Model\ResourceModel\ExtensionAuthorization;

/**
 * @since 2.0.0
 */
class Uninstall extends AbstractUninstall
{

    /**
     * @var array
     */
    protected $_tables = [ExtensionAuthorization::TABLE_NAME];

    /**
     * @var array
     */
    protected $_pathes = ['/app/code/Plumrocket/Base'];
}
