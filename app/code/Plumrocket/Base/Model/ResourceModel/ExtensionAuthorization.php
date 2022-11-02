<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @since 1.0.0
 */
class ExtensionAuthorization extends AbstractDb
{
    public const TABLE_NAME = 'plumbase_product';

    public const ID_FIELD_NAME = 'id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::ID_FIELD_NAME);
    }

    /**
     * Remove old records
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteOld(): void
    {
        $condition = ['date < ?' => date('Y-m-d H:i:s', time() - 86400 * 30)];
        $this->getConnection()->delete($this->getMainTable(), $condition);
    }
}
