<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network;

use Magento\Customer\Model\Session;

class ApiCallParamsPersistor implements ApiCallParamsPersistorInterface
{
    const PSLOGIN_SESSION_PART_NAME = 'pslogin_api_call_params';

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * ApiCallParamsPersistor constructor.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * @param string $key
     * @param null   $value
     * @return \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface
     */
    public function add(string $key, $value): ApiCallParamsPersistorInterface
    {
        $data = $this->customerSession->getData(self::PSLOGIN_SESSION_PART_NAME);
        $data[$key] = $value;
        $this->customerSession->setData(self::PSLOGIN_SESSION_PART_NAME, $data);
        return $this;
    }

    /**
     * @param array $value
     * @return \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface
     */
    public function set(array $value): ApiCallParamsPersistorInterface
    {
        $this->customerSession->setData(self::PSLOGIN_SESSION_PART_NAME, $value);
        return $this;
    }

    /**
     * @param string|null $key
     * @return mixed|null
     */
    public function get(string $key = null)
    {
        if (null !== $key) {
            $data = $this->customerSession->getData(self::PSLOGIN_SESSION_PART_NAME);
            return $data[$key] ?? null;
        }

        return $this->customerSession->getData(self::PSLOGIN_SESSION_PART_NAME);
    }

    /**
     * @return \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface
     */
    public function clear(): ApiCallParamsPersistorInterface
    {
        $this->customerSession->unsetData(self::PSLOGIN_SESSION_PART_NAME);

        return $this;
    }
}
