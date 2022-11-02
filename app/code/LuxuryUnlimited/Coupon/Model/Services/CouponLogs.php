<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\Coupon\Model\Services;

use LuxuryUnlimited\Coupon\Api\CouponLogsInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\App\ResourceConnection;

class CouponLogs implements CouponLogsInterface
{
    const TABLE = 'salesrule_coupon_actions_log';

    /*
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger;
	
	/*
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $Json;

    /*
	 * @var \Magento\Framework\App\Config\Storage\WriterInterface
	 */
	protected $writerInterface;

    /*
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $scopeConfigInterface;

    /*
	 * @var \Magento\Framework\Webapi\Rest\Request
	 */
	protected $request;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * Constructor
     * @param LoggerInterface $logger
     * @param Json $json
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param WriterInterface $writerInterface
     * @param Request $request
     * @param ResourceConnection $resource 
     */
    public function __construct(
        LoggerInterface $logger,
        Json $json,
        ScopeConfigInterface $scopeConfigInterface,
        WriterInterface $writerInterface,
        Request $request,
        ResourceConnection $resource
    ) {
        $this->logger = $logger;
        $this->json = $json;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->writerInterface = $writerInterface;
        $this->request = $request;
        $this->resource = $resource;
    }

    /**
     * Get Config Data
     *
     * @return string
     */
    public function getCouponLogs()
    {
        $connection  = $this->resource->getConnection();
        $select = $connection->select()
            ->from(
                ['c' => $connection->getTableName(self::TABLE)],
                ['*']
            );
        $data[] = $connection->fetchAll($select);
        

        return $data;
    }

}
