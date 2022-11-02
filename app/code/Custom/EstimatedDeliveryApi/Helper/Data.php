<?php
namespace Custom\EstimatedDeliveryApi\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
 
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        DateTime $date
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->date = $date;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getDate()
    {
        return $this->date;
    }
}