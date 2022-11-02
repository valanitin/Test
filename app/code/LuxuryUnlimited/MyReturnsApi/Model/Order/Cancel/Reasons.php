<?php

namespace LuxuryUnlimited\MyReturnsApi\Model\Order\Cancel;

use LuxuryUnlimited\MyReturnsApi\Api\OrderCancelReasonsInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

class Reasons implements OrderCancelReasonsInterface
{
    public const ORDER_CANCELLATION_REASONS = 'ordercancel_reason/ordercancel_configuration/reason';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Json                 $jsonHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Json $jsonHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->jsonHelper  = $jsonHelper;
    }

    /**
     * Get the Reasons available in the Backend for Cancel the order/order items
     *
     * @return array|array[]
     */
    public function getReasons()
    {
        $data    = [];
        $reasons = $this->scopeConfig->getValue(self::ORDER_CANCELLATION_REASONS, ScopeInterface::SCOPE_STORE);
        if (!empty($reasons)) {
            $reasons = $this->jsonHelper->unserialize($reasons);
            foreach ($reasons as $reason) {
                $data[] = $reason['reason_data'];
            }

            return $data;
        }

        return [
            ['status' => 'No Data', 'message' => __('There are no Reason data in this website.')]
        ];
    }
}
