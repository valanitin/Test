<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\Config\Model;

use LuxuryUnlimited\Config\Api\ConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\App\ResourceConnection;

class Config implements ConfigInterface
{
    const TABLE = 'core_config_data';

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
    public function getConfig()
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

    /**
     * Update Config Data
     *
     * @return string
     */
    public function updateConfig()
    {
        try {
            $this->logger->info('-- Update Config Api Call --');
            $result = []; 
            $params = $this->request->getBodyParams();
            if(isset($params['path']) && isset($params['value']) &&
                isset($params['scope']) && isset($params['scope_id'])){
                $path = $params['path'];
                $value = $params['value'];
                $scope = $params['scope'];
                $scopeId = $params['scope_id'];
                $this->writerInterface->save($path,$value,$scope,$scopeId);
                
                $result[] =[
                    'status' => "Success",
                    'message' => "Configuration Saved"
                ];

                return $result;
            }
            else{
                $result[] =[
                    'status' => "Error",
                    'message' => "Params are missing or incorrect to update the configuration"
                ];

                return $result;
            }
        } catch (\Exception $e) {
            $this->logger->info("Update Config Api call---" . $e);
            $result[] = ['status' => 'error','message' => $e->getMessage()];
            return $result;
        }
    }
}
