<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

// phpcs:ignoreFile

namespace Plumrocket\SocialLoginFree\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Validation\ValidationException;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail;
use Plumrocket\SocialLoginFree\Model\Account\Data\PasswordGenerator;
use Plumrocket\SocialLoginFree\Model\Account\Data\Prepare;
use Plumrocket\SocialLoginFree\Model\Account\Debug\Logger;
use Plumrocket\SocialLoginFree\Model\Account\Photo;
use Plumrocket\SocialLoginFree\Model\Provider\Callback as SocialCallback;
use Plumrocket\SocialLoginFree\Model\ResourceModel\Account as AccountResource;
use Plumrocket\SocialLoginFree\Model\ResourceModel\Account\GetLinkedCustomerId;

/**
 * @method setErrors(array $errors)
 * @method array|null getErrors()
 */
class Account extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @deprecated since 3.0.0
     */
    const PHOTO_FILE_EXT = 'png';
    const LOG_FILE = 'pslogin.log';
    const GENDER_NOT_SPECIFIED = Prepare::GENDER_NOT_SPECIFIED;

    /**
     * @deprecated since 3.0.0 - use \Plumrocket\SocialLoginFree\Model\Account::$provider
     * @var null | string
     */
    protected $_type = null;

    /**
     * Key of network, eg: "facebook"
     *
     * @var string
     */
    protected $provider = '';

    /**
     * @deprecated since 3.0.0 - use \Plumrocket\SocialLoginFree\Model\Account::$fieldsMapping
     * @var array
     */
    protected $_fields = [];

    protected $fieldsMapping = [];

    /**
     * @var string
     */
    protected $_protocol = 'OAuth';

    /**
     * @var null | string
     */
    protected $_redirectUri = null;

    /**
     * @var array
     */
    protected $_userData = [];

    /**
     * @var null | string | int
     */
    protected $_applicationId = null;

    /**
     * @var null | string | int
     */
    protected $_secret = null;

    /**
     * @var string
     */
    protected $_responseType = 'code';

    /**
     * @var array
     */
    protected $_dob = [];

    /**
     * @var null | array | string
     */
    protected $_callInfo = null;

    /**
     * @deprecated
     * @var string[]
     */
    protected $_gender = ['male', 'female'];

    /**
     * @var array
     */
    protected $genderMapping = [
        0 => 'male',
        1 => 'female'
    ];

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Customer\Model\Attribute
     */
    protected $attribute;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    protected $config;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Photo
     */
    protected $photo;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\ResourceModel\Account\GetLinkedCustomerId
     */
    protected $getLinkedCustomerId;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Debug\Logger
     */
    protected $debugLogger;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\Prepare
     */
    protected $prepareData;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\PasswordGenerator
     */
    protected $passwordGenerator;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail
     */
    protected $fakeEmail;

    /**
     * @var SocialCallback
     */
    protected $callback;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * Account constructor.
     *
     * @param \Magento\Framework\Model\Context                                            $context
     * @param \Magento\Framework\Registry                                                 $registry
     * @param \Plumrocket\SocialLoginFree\Helper\Data                                     $dataHelper
     * @param \Magento\Store\Model\StoreManager                                           $storeManager
     * @param \Magento\Customer\Model\Customer                                            $customer
     * @param \Magento\Newsletter\Model\SubscriberFactory                                 $subscriberFactory
     * @param \Magento\Eav\Model\Config                                                   $eavConfig
     * @param \Magento\Customer\Model\Attribute                                           $attribute
     * @param \Magento\Customer\Model\Session                                             $customerSession
     * @param \Magento\Framework\App\RequestInterface                                     $request
     * @param \Plumrocket\SocialLoginFree\Helper\Config                                   $config
     * @param \Plumrocket\SocialLoginFree\Model\Account\Photo                             $photo
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                           $customerRepository
     * @param \Plumrocket\SocialLoginFree\Model\ResourceModel\Account\GetLinkedCustomerId $getLinkedCustomerId
     * @param \Plumrocket\SocialLoginFree\Model\Account\Debug\Logger                      $debugLogger
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\Prepare                      $prepareData
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\PasswordGenerator            $passwordGenerator
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail                    $fakeEmail
     * @param \Plumrocket\SocialLoginFree\Model\Provider\Callback                         $callback
     * @param \Magento\Framework\Serialize\SerializerInterface                            $serializer
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null                $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                          $resourceCollection
     * @param array                                                                       $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Plumrocket\SocialLoginFree\Helper\Data $dataHelper,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Attribute $attribute,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        Config $config,
        Photo $photo,
        CustomerRepositoryInterface $customerRepository,
        GetLinkedCustomerId $getLinkedCustomerId,
        Logger $debugLogger,
        Prepare $prepareData,
        PasswordGenerator $passwordGenerator,
        FakeEmail $fakeEmail,
        SocialCallback $callback,
        SerializerInterface $serializer,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->config = $config;
        $this->callback = $callback;
        $this->_helper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->customer = $customer;
        $this->subscriberFactory = $subscriberFactory;
        $this->eavConfig = $eavConfig;
        $this->attribute = $attribute;
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->photo = $photo;
        $this->customerRepository = $customerRepository;
        $this->getLinkedCustomerId = $getLinkedCustomerId;
        $this->debugLogger = $debugLogger;
        $this->prepareData = $prepareData;
        $this->passwordGenerator = $passwordGenerator;
        $this->fakeEmail = $fakeEmail;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->serializer = $serializer;
    }

    public function _construct()
    {
        $this->_init(AccountResource::class);
        $this->_redirectUri = $this->callback->getUrl($this->getProvider());
        $this->_applicationId = $this->config->getNetworkApplicationId($this->getProvider());
        $this->_secret = $this->config->getNetworkApplicationSecretKey($this->getProvider());
    }

    /**
     * @return bool
     */
    public function enabled(): bool
    {
        return $this->config->isEnabledNetwork($this->getProvider());
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return (string) ($this->provider ?: $this->_type);
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->_protocol;
    }

    /**
     * @return array
     */
    public function getNetworkFieldsMapping(): array
    {
        return $this->fieldsMapping ?: $this->_fields;
    }

    private function getNetworkGenderMapping(): array
    {
        return $this->genderMapping ?: $this->_gender;
    }

    public function loadUserData($response)
    {
        return true;
    }

    /**
     * @param int $customerId
     * @return $this
     */
    public function linkCustomerIdToNetwork(int $customerId): self
    {
        $data = [
            'type' => $this->getProvider(),
            'user_id' => $this->getUserData('user_id'),
            'customer_id' => $customerId
        ];

        $this->addData($data)->save();
        return $this;
    }

    /**
     * @deprecated
     * @param $customerId
     * @return $this
     * @throws \Exception
     */
    public function setCustomerIdByUserId($customerId)
    {
        return $this->linkCustomerIdToNetwork((int) $customerId);
    }

    /**
     * @return int
     */
    public function getCustomerIdByUserId(): int
    {
        $networkAccountId = (string) $this->getUserData('user_id');

        if ($networkAccountId) {
            $customerId = $this->getLinkedCustomerId->execute($this->getProvider(), $networkAccountId);

            if ($customerId) {
                try {
                    return (int) $this->customerRepository->getById($customerId)->getId();
                } catch (NoSuchEntityException $e) {
                    return 0;
                } catch (LocalizedException $e) {
                    return 0;
                }
            }
        }

        return 0;
    }

    /**
     * @return int
     */
    public function getCustomerIdByEmail(): int
    {
        $email = $this->getSocialNetworkEmail();

        if ($email) {
            try {
                return (int) $this->customerRepository->get($email)->getId();
            } catch (NoSuchEntityException $e) {
                return 0;
            } catch (LocalizedException $e) {
                return 0;
            }
        }

        return 0;
    }

    public function registrationCustomer()
    {
        $customerId = 0;
        $errors = [];
        $customer = $this->customer->setId(null);

        try {
            $customer->setData($this->getUserData())
                ->setConfirmation($customer->getRandomConfirmationKey())
                ->setPasswordConfirmation($this->getUserData('password'))
                ->setData('is_active', 1)
                ->getGroupId();

            $errors = $this->_validateErrors($customer);

            // If email is not valid, always error.
            $correctEmail = \Zend_Validate::is($this->getSocialNetworkEmail(), 'EmailAddress');

            if ((empty($errors) || $this->config->isIgnoreValidation()) && $correctEmail) {
                $customerId = $customer->save()->getId();

                if ($this->config->isEnabledSubscription()
                    && ! $this->fakeEmail->detect($this->getSocialNetworkEmail())
                ) {
                    $this->subscriberFactory->create()->subscribeCustomerById($customerId);
                }

                // Set email confirmation;
                $customer->setConfirmation(null)->save();
                /*$customer->setConfirmation(null)
                    ->getResource()->saveAttribute($customer, 'confirmation');*/

            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        $this->setCustomer($customer);
        $this->setErrors($errors);

        return (int) $customerId;
    }

    protected function _validateErrors($customer)
    {
        $errors = [];

        // Date of birth.
        $entityType = $this->eavConfig->getEntityType('customer');
        $attribute = $this->attribute->loadByCode($entityType, 'dob');

        if ($attribute->getIsRequired() && $this->getUserData('dob')
            && !\Zend_Validate::is($this->getUserData('dob'), 'Date')
        ) {
            $errors[] = __('The Date of Birth is not correct.');
        }

        if (true !== ($customerErrors = $customer->validate())) {
            $errors = array_merge($customerErrors, $errors);
        }

        return $errors;
    }

    public function getResponseType()
    {
        return $this->_responseType;
    }

    /**
     * Set data received from social network
     *
     * @param      $key
     * @param null $value
     * @return $this
     */
    public function setUserData($key, $value = null): self
    {
        if (is_array($key)) {
            $this->_userData = array_merge($this->_userData, $key);
        } else {
            $this->_userData[$key] = $value;
        }
        return $this;
    }

    /**
     * Get data received from social network
     *
     * @param null $key
     * @return array|mixed|null
     */
    public function getUserData($key = null)
    {
        if ($key !== null) {
            return $this->_userData[$key] ?? null;
        }
        return $this->_userData;
    }

    /**
     * Get email received from social network
     *
     * @return string
     */
    public function getSocialNetworkEmail(): string
    {
        return (string) $this->getUserData('email');
    }

    /**
     * @param $data
     * @return array|false
     */
    protected function _prepareData($data)
    {
        $_data = [];
        foreach ($this->getNetworkFieldsMapping() as $customerField => $userField) {
            $_data[$customerField] = ($userField && isset($data[$userField])) ? $data[$userField] : null;
        }

        $_data = $this->prepareData->email($_data);
        $_data = $this->prepareData->names($_data);
        $_data = $this->prepareData->dateOfBirth($_data, $this->_dob);
        $_data = $this->prepareData->gender($_data, $this->getNetworkGenderMapping());
        $_data = $this->prepareData->taxVat($_data);

        $_data['password'] = $this->passwordGenerator->generatePassword();

        return $_data;
    }

    /**
     * @param int $customerId
     * @return bool
     */
    public function loadAndSaveCustomerPhotoFromNetwork(int $customerId): bool
    {
        try {
            return $this->photo->saveExternal($customerId, (string) $this->getUserData('photo'));
        } catch (ValidationException $e) {
            return false;
        }
    }

    public function sendNewAccountEmail(): bool
    {
        if (! $this->fakeEmail->detect($this->getSocialNetworkEmail())) {
            $storeId = $this->storeManager->getStore()->getId();
            $this->customer->sendNewAccountEmail('registered', '', $storeId);
        }

        return true;
    }

    /**
     * @return array
     */
    public function getButton(): array
    {
        if ($this->getProtocol() === 'OAuth' && (empty($this->_applicationId) || empty($this->_secret))) {
            $uri = null;
        } else {
            $uri = $this->storeManager->getStore()
                                      ->getUrl('pslogin/account/douse', ['type' => $this->getProvider(), 'refresh' => time()]);
        }

        return [
            'href' => $uri,
            'type' => $this->getProvider(),
            'image' => $this->getButtonsIcons(),
            'login_text' => $this->config->getNetworkLoginButtonText($this->getProvider()),
            'register_text' => $this->config->getNetworkRegisterButtonText($this->getProvider()),
            'popup_width' => $this->_popupSize[0],
            'popup_height' => $this->_popupSize[1],
        ];
    }

    /**
     * @return array
     */
    private function getButtonsIcons(): array
    {
        $icons = [];
        $media = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'pslogin/';

        $smallIcon = $this->config->getNetworkSmallIconButton($this->getProvider());
        $icons['icon'] = $smallIcon ? $media . $smallIcon : null;

        $loginIcon = $this->config->getNetworkLoginIconButton($this->getProvider());
        $icons['login'] = $loginIcon ? $media . $loginIcon : null;

        $registerIcon = $this->config->getNetworkRegisterIconButton($this->getProvider());
        $icons['register'] = $registerIcon ? $media . $registerIcon : null;

        return $icons;
    }

    public function getProviderLink()
    {
        if (empty($this->_applicationId) || empty($this->_secret)) {
            $uri = null;
        } elseif (is_array($this->_buttonLinkParams)) {
            $uri = $this->_url .'?'. urldecode(http_build_query($this->_buttonLinkParams));
        } else {
            $uri = $this->_buttonLinkParams;
        }

        return $uri;
    }

    /**
     * @param      $data
     * @param bool $append
     * @param bool $isCallRequest
     */
    public function _setLog($data, $append = false, $isCallRequest = false)
    {
        if ($this->config->isDebugMode()) {
            $log = [];
            $title = strtoupper($this->getProvider());
            $log[] = "\n-------- $title --------";

            try {
                if (!is_array($data)) {
                    $data = $this->serializer->unserialize($data);
                }
            } catch (\InvalidArgumentException $exception) {
                // Lets raw data
            }

            if (is_array($data) || is_object($data)) {
                $log[] = print_r($data, true);
            } else {
                if (strpos(trim($data), ' ') === false) {
                    parse_str($data, $result);
                    $log[] = print_r($result, true);
                } else {
                    $log[] = $data;
                }
            }

            if (!$isCallRequest) {
                $this->_callInfo = $log;
            }

            $log[] = '---------------------------------';

            $psloginLog = $this->customerSession->getPsloginLog();
            if (null === $psloginLog) {
                $psloginLog = [];
            }
            $psloginLog[] = implode(PHP_EOL, $log);
            $this->customerSession->setPsloginLog($psloginLog);
        }
    }

    /**
     * Record error in the log
     *
     * @return $this
     */
    public function recordLog(): self
    {
        $message = implode(PHP_EOL, $this->customerSession->getPsloginLog());
        $this->debugLogger->debug($message);
        return $this;
    }

    protected function _call($url, $params = [], $method = 'GET', $curlResource = null)
    {
        $result = null;
        $paramsStr = is_array($params)? urlencode(http_build_query($params)) : urlencode($params);
        if ($paramsStr) {
            $url .= '?'. urldecode($paramsStr);
        }

        $this->_setLog($url, true, true);

        $curl = is_resource($curlResource) ? $curlResource : curl_init();

        if ($method == 'POST') {
            // POST.
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $paramsStr);
        } else {
            // GET.
            curl_setopt($curl, CURLOPT_URL, $url);
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);

        if ($errno = curl_errno($curl)) {
            $this->_setLog(curl_getinfo($curl), true, true);
            $this->_setLog(curl_error($curl), true, true);
        }

        curl_close($curl);

        return $result;
    }

    /**
     * Retrieve social network errors
     *
     * @return mixed
     */
    public function getDebugErrors()
    {
         // Replace origin application id and secret key to fake
         $protectSecretData = function (&$value) {
             $search = [$this->_applicationId, $this->_secret];
             $replace = ['APPLICATION_ID', 'SECRET_KEY'];
             return $value =  str_replace($search, $replace, $value);
         };

        if (is_array($this->_callInfo)) {
            array_walk_recursive($this->_callInfo, $protectSecretData);
        } elseif (is_string($this->_callInfo)) {
            $this->_callInfo = $protectSecretData($this->_callInfo);
        }

         return $this->_callInfo;
    }

    /**
     * @param $customerId
     * @return bool
     * @deprecated
     * @see loadAndSaveCustomerPhotoFromNetwork
     */
    public function setCustomerPhoto($customerId): bool
    {
        return $this->loadAndSaveCustomerPhotoFromNetwork((int) $customerId);
    }

    /**
     * @deprecated
     * @see \Plumrocket\SocialLoginFree\Model\Account::sendNewAccountEmail
     * @return bool
     */
    public function postToMail()
    {
        return $this->sendNewAccountEmail();
    }
}
