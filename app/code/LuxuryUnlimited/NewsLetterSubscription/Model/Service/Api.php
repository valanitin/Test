<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\NewsLetterSubscription\Model\Service;

use LuxuryUnlimited\NewsLetterSubscription\Model\Config;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\CacheInterface;
use LuxuryUnlimited\NewsLetterSubscription\Logger\Logger;
use Magento\Framework\App\Cache\Type\Config as CacheConfig;

class Api
{
    public const ENC_URLENCODED = 'application/x-www-form-urlencoded';
    
    public const AUTHORIZATION_URL = '/authentication/token';
    
    public const CURL_SUCCESS_STATUS = '200';

    /**
     * @var Json
     */
    private $json;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param Curl $curl
     * @param Json $json
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(
        Curl $curl,
        Json $json,
        Config $config,
        Logger $logger
    ) {
        $this->curl = $curl;
        $this->json = $json;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Api Call
     *
     * @param string $path
     * @param string $params
     * @param string $method
     * @return array
     */
    public function call($path, $params = [], $method = 'GET')
    {
        $url = $this->config->getApiUrl() . $path;
        $this->logger->info("ERP Api url--- " . $url);

        try {
            $this->curl->setHeaders($this->getHeaders());
            if ($method == 'GET') {
                $this->curl->get($url);
            } else {
                $jsonParams = $this->json->serialize($params);
                $this->logger->info("ERP Api request--- " . $jsonParams);
                $startTime = microtime(true);
                $this->curl->post($url, $jsonParams);
                $endTime = (microtime(true) - $startTime);
                $this->logger->info("ERP Api performance--- " . "Elapsed time is: ". $endTime . " seconds");
            }
            $httpStatusCode = $this->curl->getStatus();
            $response = $this->curl->getBody();
            $this->logger->info("ERP Api response--- " . $response);

            if ((string)$httpStatusCode === self::CURL_SUCCESS_STATUS) {
                return $this->json->unserialize($response);
            } else {
                return [];
            }
        } catch (\Exception $e) {
            $this->logger->info("ERP Api call---" . $e);
            return [];
        }
    }

    /**
     * Api Header
     *
     * @return array
     */
    private function getHeaders(): array
    {
        return [
            "Content-Type" => "application/json"
        ];
    }
}
