<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Config;

use Plumrocket\SocialLoginFree\Model\Information;

class Provider
{
    const XML_PATH_BUTTON_SORT = 'psloginpro/general/sortable';
    const XML_PATH_BUTTON_REMINDER_SORT = 'psloginpro/general/reminder_sortable';
    const XML_PATH_REGISTER_REQUIRE_LEVEL = 'psloginpro/general/validate_ignore';
    const XML_PATH_REMINDER_POPUP_ENABLED = 'psloginpro/general/reminder_popup';
    const XML_PATH_PREVENT_DUPLICATE_ENABLED = 'psloginpro/general/prevent_duplicate';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $networkEnabledInfo;

    /**
     * Provider constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Base Helper has got same method
     *
     * Receive magento config value
     *
     * @param string      $path
     * @param string|int  $store
     * @param string|null $scope
     * @return mixed
     */
    public function getConfig($path, $store = null, $scope = null)
    {
        if ($scope === null) {
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        }

        return $this->scopeConfig->getValue($path, $scope, $store);
    }

    /**
     * @param null $scope
     * @param null $scopeCode
     * @return array
     */
    public function getNetworkStatusInfo($scope = null, $scopeCode = null)
    {
        if (null === $this->networkEnabledInfo) {
            $groups = $this->getConfig(Information::CONFIG_SECTION, $scopeCode, $scope);
            $this->networkEnabledInfo = [];

            if (! $groups) {
                return $this->networkEnabledInfo;
            }

            if (is_array($groups)) {
                unset(
                    $groups['general'],
                    $groups['share'],
                    $groups['developer']
                );

                foreach ($groups as $name => $fields) {
                    $this->networkEnabledInfo[$name] = isset($fields['enable'])
                        ? (int)$fields['enable']
                        : 0;
                }
            }
        }

        return $this->networkEnabledInfo;
    }
}
