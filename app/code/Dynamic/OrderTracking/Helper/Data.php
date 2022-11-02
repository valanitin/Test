<?php

/**
 * OrderTracking data helper
 */
namespace Dynamic\OrderTracking\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Timezone
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->timezone = $timezone;
        parent::__construct($context);
    }
 
    public function getOrderStatusList($order)
    {
        $config = $this->_scopeConfig->getValue('ordertracking_reason/ordertracking_configuration/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $orderStatus = json_decode($config, true);

        $orderStatusArr = [];
        if(!empty($orderStatus)) {
            foreach ($orderStatus as $status) {
                $orderStatusArr[] = [
                    "status_code" => $status["status_code"],
                    "status_title" => $status["status_title"]
                ];
            }
        }

        $historyArr = $this->getOrderStatusHistory($order);
        $result = [];

        if(!empty($orderStatusArr)) {
            $i = 0;
            foreach($orderStatusArr as $key => $orderStatusData){
                if(isset($historyArr[$orderStatusData["status_code"]])) {
                    $result[] = [
                        "status_code" => $orderStatusData["status_code"],
                        "status_title" => $orderStatusData["status_title"],
                        "status_date" => $historyArr[$orderStatusData["status_code"]]
                    ];
                } else if($i == $key) {
                    $dateTimeZone = $this->timezone->date(new \DateTime($order->getCreatedAt()))->format('F j, Y');
                    $result[] = [
                        "status_code" => $orderStatusData["status_code"],
                        "status_title" => $orderStatusData["status_title"],
                        "status_date" => $dateTimeZone
                    ];
                } else {
                    $result[] = [
                        "status_code" => $orderStatusData["status_code"],
                        "status_title" => $orderStatusData["status_title"]
                    ];
                }
            }
        }

        return $result;
    }

    public function getOrderStatusHistory($order)
    {        
        $history = $order->getStatusHistoryCollection();
        $historyArr = [];
        if(!empty($history) && count($history) > 0) {
            foreach ($history as $value) {
                $dateTimeZone = $this->timezone->date(new \DateTime($value->getCreatedAt()))->format('F j, Y');
                $historyArr[] = [
                    "status_code" => $value->getStatus(),
                    "status_date" => $dateTimeZone
                ];
            }
        }
        $temp = array_unique(array_column($historyArr, 'status_code'));
        $uniqueArr = array_intersect_key($historyArr, $temp);

        $finalHistoryArr = [];

        if(!empty($uniqueArr) && count($uniqueArr) > 0) {
            foreach ($uniqueArr as $unique) {
                $finalHistoryArr[$unique["status_code"]] = $unique["status_date"];
            }
        }

        return $finalHistoryArr;
    }
}
