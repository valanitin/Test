<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\ResourceModel\Account;

use Magento\Framework\App\ResourceConnection;
use Plumrocket\SocialLoginFree\Model\ResourceModel\Account as ResourceAlias;

class GetLinkedCustomerId
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;
    /**
     * GetLinkedCutomerId constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param string $type
     * @param string $networkAccountId
     * @return int
     */
    public function execute(string $type, string $networkAccountId) : int
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select();

        $select->from(
            ['main_table' => $this->resourceConnection->getTableName(ResourceAlias::MAIN_TABLE)],
            ['customer_id']
        )
               ->where('user_id = :user_id')
               ->where('type = :type');

        $bind = [
            ':user_id' => $networkAccountId,
            ':type' => $type,
        ];

        return (int) $connection->fetchOne($select, $bind);
    }
}
