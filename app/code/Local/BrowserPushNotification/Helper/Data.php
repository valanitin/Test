<?php

/**
 * Firebase data helper
 */

namespace Local\BrowserPushNotification\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const XML_PATH_API_KEY = 'browserpush/notification/apiKey';
    const XML_PATH_AUTO_DOMAIN = 'browserpush/notification/authDomain';
    const XML_PATH_PROJECT_ID = 'browserpush/notification/projectId';
    const XML_PATH_STORAGE_BUCKET = 'browserpush/notification/storageBucket';
    const XML_PATH_MESSAGING_SENDER_ID = 'browserpush/notification/messagingSenderId';
    const XML_PATH_APP_ID = 'browserpush/notification/appId';
    const XML_PATH_MEASUREMENT_ID = 'browserpush/notification/measurementId';
    const XML_PATH_PUBLIC_VAPID_KEY = 'browserpush/notification/publicVapidKey';
    const XML_PATH_TOKENWEBSITE = 'browserpush/notification/tokenWebsite';

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Email constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    
    /**
     * @return string
     */
    public function getConfigurationApiKey()
    {
        return $this->getConfigValue(self::XML_PATH_API_KEY, null);
    }

    /**
     * @return string
     */
    public function getConfigurationAutoDomain()
    {
        return $this->getConfigValue(self::XML_PATH_AUTO_DOMAIN, null);
    }

    /**
     * @return string
     */
    public function getConfigurationProjectId()
    {
        return $this->getConfigValue(self::XML_PATH_PROJECT_ID, null);
    }

    /**
     * @return string
     */
    public function getConfigurationStorageBucket()
    {
        return $this->getConfigValue(self::XML_PATH_STORAGE_BUCKET, null);
    }

    /**
     * @return string
     */
    public function getConfigurationMessagingSenderId()
    {
        return $this->getConfigValue(self::XML_PATH_MESSAGING_SENDER_ID, null);
    }

    /**
     * @return string
     */
    public function getConfigurationApiId()
    {
        return $this->getConfigValue(self::XML_PATH_APP_ID, null);
    }

    /**
     * @return string
     */
    public function getConfigurationMeasurementId()
    {
        return $this->getConfigValue(self::XML_PATH_MEASUREMENT_ID, null);
    }

    /**
     * @return string
     */
    public function getConfigurationTokenWebsite()
    {
        return $this->getConfigValue(self::XML_PATH_TOKENWEBSITE, null);
    }

    /**
     * @return string
     */
    public function getConfigurationPublicVapidKey()
    {
        return $this->getConfigValue(self::XML_PATH_PUBLIC_VAPID_KEY, null);
    }
    
    /**
     * @param string $path
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($path, $storeId = null)
    {
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
