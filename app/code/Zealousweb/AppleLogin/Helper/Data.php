<?php

namespace Zealousweb\AppleLogin\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Config\Model\Config\Source\Nooptreq;

/**
 * Class Social
 *
 * @package Zealousweb\AppleLogin\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ID = '20184';
    const CURL_AUTH_USERNAME = 'admin';
    const CURL_AUTH_PASSWORD = 'This@admin123';
    const MODULE_NAME = 'Zealousweb_AppleLogin';
    const API_END = 'https://www.zealousweb.com/wp-json/activator/v1/mage-extensions/';
    const BASE_URL = 'web/unsecure/base_url';

    const DEFAUT_FIRSTNAME = 'Apple';
    const DEFAUT_LASTNAME = 'User';
    const DEFAUT_GENDER = '1';
    
    const ENABLE_MODULE = 'appleconfig/apple/enable';
    const APPLE_CLIENTID = 'appleconfig/apple/clientid';
    const APPLE_KEYID = 'appleconfig/apple/keyid';
    const APPLE_ISSUERID = 'appleconfig/apple/issuerid';
    const APPLE_KEYFILE = 'appleconfig/apple/auth_key';
    const APPLE_TOKEN_URL = 'https://appleid.apple.com/auth/token';
    const APPLE_AUTH_URL = 'https://appleid.apple.com/auth/authorize';
    const APPLE_REDIRECTION_TYPE = 'appleconfig/apple/redirection_type';
    const APPLE_IS_SHOW_LOGIN_POPUP = 'appleconfig/apple/is_show_login_popup';
    const APPLE_IS_SHOW_BUTTON_ON_CHECKOUT = 'appleconfig/apple/is_show_button_on_checkout';
    const APPLE_IS_SHOW_BUTTON_ON_CART = 'appleconfig/apple/is_show_button_on_cart';

    const APPLE_DEFAULT_FIRSTNAME = 'appleconfig/default/firstname';
    const APPLE_DEFAULT_LASTNAME = 'appleconfig/default/lastname';
    const APPLE_DEFAULT_GENDER = 'appleconfig/default/gender';
    const APPLE_DEFAULT_PREFIX = 'appleconfig/default/prefix';
    const APPLE_DEFAULT_SUFIX = 'appleconfig/default/sufix';
    const APPLE_DEFAULT_DOB = 'appleconfig/default/dob';
    const APPLE_DEFAULT_TAXVAT = 'appleconfig/default/taxvat';
    const APPLE_DEFAULT_CUSTOMATTRIBUTES = 'appleconfig/default/custom_attributes';

    const APPLE_LAYOUT_DISPLAY_TYPE = 'appleconfig/layout/display_type';
    const APPLE_LAYOUT_BUTTON_IMAGE = 'appleconfig/layout/button_image';
    const APPLE_LAYOUT_ICON = 'appleconfig/layout/apple_icon';
    const APPLE_LAYOUT_BUTTON_LABEL = 'appleconfig/layout/button_label';
    const APPLE_LAYOUT_BUTTON_LAYOUT = 'appleconfig/layout/button_layout';
    const APPLE_LAYOUT_BUTTON_BACKGROUND_COLOR = 'appleconfig/layout/background_color';
    const APPLE_LAYOUT_TEXT_COLOR = 'appleconfig/layout/text_color';
    const APPLE_LAYOUT_BORDER_COLOR = 'appleconfig/layout/border_color';
    const APPLE_LAYOUT_ICON_BACKGROUND_COLOR = 'appleconfig/layout/icon_background_color';
    const APPLE_LAYOUT_BUTTON_HOVER_COLOR = 'appleconfig/layout/button_hover_color';
    const APPLE_LAYOUT_BORDER_HOVER_COLOR = 'appleconfig/layout/border_hover_color';
    const APPLE_LAYOUT_TEXT_HOVER_COLOR = 'appleconfig/layout/text_hover_color';

    const XML_SINGLE_STORE = 'general/single_store_mode/enabled';

    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\AccountManagement
     */
    protected $accountManagement;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    protected $cookieMetadataManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $imageFactory;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var \Magento\Theme\Block\Html\Header\Logo
     */
    private $logo;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\AccountManagement $accountManagement
     * @param \Magento\Framework\Filesystem $filesystem,
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Theme\Block\Html\Header\Logo $logo
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,        
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\AccountManagement $accountManagement,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Theme\Block\Html\Header\Logo $logo
    ) {
        parent::__construct($context);        
        $this->scopeConfig = $scopeConfig;
        $this->customerFactory = $customerFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerSession = $customerSession;
        $this->accountManagement = $accountManagement;
        $this->filesystem = $filesystem;
        $this->urlBuilder = $urlBuilder;
        $this->curl = $curl;
        $this->storeManager = $storeManager;
        $this->configWriter = $configWriter;
        $this->imageFactory = $imageFactory;
        $this->directory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->assetRepo = $assetRepo;
        $this->logo = $logo;
    }
    
    /**
     * Return all websites
     *
     * @return mixed
     */
    public function getAllWebsites()
    {
        return $this->storeManager->getWebsites();
    }

    /**
     * Api Call for license key
     *
     * @param  mixed $params
     * @return mixed
     */
    private function _apiCall($params)
    {
        $url = $this->getApiUrl($params);

        /*$this->curl->setCredentials(self::CURL_AUTH_USERNAME, self::CURL_AUTH_PASSWORD);*/

        $this->curl->get($url);
        $response = $this->curl->getBody();
        $response = json_decode($response, true);
        return $response;
    }

    /**
     * Get API URL
     *
     * @param  mixed $params
     * @return mixed
     */
    private function getApiUrl($params)
    {
        $suffix = [];
        foreach ($params as $key => $value) {
            $suffix[] = $key . '=' . $value;
        }
        return self::API_END . '?' . implode('&', $suffix);
    }

    /**
     * Get store config data
     *
     * @return string
     * @throws LocalizedException
     */
    public function getConfigValue($path, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $id = null)
    {
        if($this->isSingleStoreMode()){
            return $this->scopeConfig->getValue($path,'default',0);
        }
        return $this->scopeConfig->getValue($path, $scope, $id);
    }

    /**
     * Check store is in single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        $stores = $this->storeManager->getStores();
        if(count($stores) > 1){
            return false;
        }

        return $this->scopeConfig->getValue(self::XML_SINGLE_STORE);
    }

    /**
     * Write config to check license key in further
     *
     * @param  string $path
     * @param  bool $value
     * @param  string $scope
     * @param  int $id
     * @return bool
     */
    public function writeConfig($path, $value, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $id = null)
    {
        if($this->isSingleStoreMode()){
            $this->configWriter->save($path, $value, 'default', 0);     
        }else{
            $this->configWriter->save($path, $value, $scope, $id); 
        }
    }

    /**
     * Check module enable or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfigValue(self::ENABLE_MODULE);
    }

    /**
     * Get apple client id
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->getConfigValue(self::APPLE_CLIENTID);
    }

    /**
     * Get apple key id
     *
     * @return string
     */
    public function getKeyId()
    {
        return $this->getConfigValue(self::APPLE_KEYID);
    }

    /**
     * Get apple key id
     *
     * @return string
     */
    public function getIssuerId()
    {
        return $this->getConfigValue(self::APPLE_ISSUERID);
    }

    /**
     * Get auth key file
     *
     * @return string
     */
    public function getAuthKeyFile()
    {
        return $this->getConfigValue(self::APPLE_KEYFILE);
    }

    /**
     * Get default first name of customer
     *
     * @return string
     */
    public function getDefaultFirstName()
    {
        if(!$this->getConfigValue(self::APPLE_DEFAULT_FIRSTNAME)){
            return self::DEFAUT_FIRSTNAME;
        }
        return $this->getConfigValue(self::APPLE_DEFAULT_FIRSTNAME);
    }

    /**
     * Get default last name of customer
     *
     * @return string
     */
    public function getDefaultLastname()
    {
        if(!$this->getConfigValue(self::APPLE_DEFAULT_LASTNAME)){
            return self::DEFAUT_LASTNAME;
        }
        return $this->getConfigValue(self::APPLE_DEFAULT_LASTNAME);
    }

    /**
     * Get default gender of customer
     *
     * @return string
     */
    public function getDefaultGender()
    {
        if(!$this->getConfigValue(self::APPLE_DEFAULT_GENDER)){
            return self::DEFAUT_GENDER;
        }
        return $this->getConfigValue(self::APPLE_DEFAULT_GENDER);
    }

    /**
     * Get default prefix of customer
     *
     * @return string
     */
    public function getDefaultPrefix()
    {
        return $this->getConfigValue(self::APPLE_DEFAULT_PREFIX);
    }

    /**
     * Get default sufix of customer
     *
     * @return string
     */
    public function getDefaultSufix()
    {
        return $this->getConfigValue(self::APPLE_DEFAULT_SUFIX);
    }

    /**
     * Get default Date of birth of customer
     *
     * @return string
     */
    public function getDefaultDob()
    {
        return $this->getConfigValue(self::APPLE_DEFAULT_DOB);
    }

    /**
     * Get default Tax/Vat of customer
     *
     * @return string
     */
    public function getDefaultTaxvat()
    {
        return $this->getConfigValue(self::APPLE_DEFAULT_TAXVAT);
    }

    /**
     * Check is gender required or not
     *
     * @return string
     */
    public function isGenderRequired()
    {
        return ($this->getConfigValue('customer/address/gender_show') == Nooptreq::VALUE_REQUIRED);
    }

    /**
     * Check is prefix required or not
     *
     * @return string
     */
    public function isPrefixRequired()
    {
        return ($this->getConfigValue('customer/address/prefix_show') == Nooptreq::VALUE_REQUIRED);
    }

    /**
     * Check is sufix required or not
     *
     * @return string
     */
    public function isSufixRequired()
    {
        return ($this->getConfigValue('customer/address/suffix_show') == Nooptreq::VALUE_REQUIRED);
    }

    /**
     * Check is sufix required or not
     *
     * @return string
     */
    public function isDobRequired()
    {
        return ($this->getConfigValue('customer/address/dob_show') == Nooptreq::VALUE_REQUIRED);
    }

    /**
     * Check is sufix required or not
     *
     * @return string
     */
    public function isTaxvatRequired()
    {
        return ($this->getConfigValue('customer/address/taxvat_show') == Nooptreq::VALUE_REQUIRED);
    }

    /**
     * Get default custom attributes of customer
     *
     * @return string
     */
    public function getDefaultCustomAttributes()
    {   
        $customerAttributes = [];
        if($this->getConfigValue(self::APPLE_DEFAULT_CUSTOMATTRIBUTES)){
            $attributes = $this->getConfigValue(self::APPLE_DEFAULT_CUSTOMATTRIBUTES);
            $attributes = explode(',', $attributes);
            foreach ($attributes as $attribute) {
                $attribute = explode(':', $attribute);
                $code = isset($attribute[0]) ? $attribute[0] : "";
                $value = isset($attribute[1]) ? $attribute[1] : "";
                if($code != '' && $value != ''){
                    $customerAttributes[$code] = $value;
                }
            }
        }
        return $customerAttributes;
    }

    /**
     * Get auth key file directory
     *
     * @return string
     */
    public function getAuthKeyFileDir()
    {
        $mediaDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $keyFile = $this->getAuthKeyFile();
        if( !empty($keyFile) ) {
            return $mediaDir.'apple/'.$keyFile;    
        }
        return '';
    }

    /**
     * Get token generation url
     *
     * @return string
     */
    public function getTokenUrl()
    {
        return self::APPLE_TOKEN_URL;
    }

    /**
     * Get aurthoroization url
     *
     * @return string
     */
    public function getAuthorizeUrl()
    {
        return self::APPLE_AUTH_URL;
    }

    /**
     * Get redirect url after successfull login
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->urlBuilder->getBaseUrl().'applelogin/apple/callback/';
    }

    /**
     * Build http query
     *
     * @return string
     */
    public function buildHttpQuery($params)
    {
        return str_replace('+', '%20', http_build_query($params));
    }

    /**
     * Get URL to rediret on apple store
     *
     * @return string
     */
    public function getAuthorizationUrl()
    {$this->generateJWT();
        $state = bin2hex(random_bytes(5));
        $params = [
            'response_type' => 'code',
            'response_mode' => 'form_post',
            'client_id' => $this->getClientId(),
            'redirect_uri' => $this->getRedirectUri(),
            'state' => $state,
            'usePopup' => true,
            'scope' => 'name email'
        ];
        $query = $this->buildHttpQuery($params);
        return $this->getAuthorizeUrl().'?'.$query;
    }

    /**
     * Default curl call
     *
     * @param string | $url 
     * @param array | $params 
     * @return mixed
     */
    public function curlCall($url, $params = false) 
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if($params){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildHttpQuery($params));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'User-Agent: curl',
        ]);
        $response = curl_exec($ch);
        return json_decode($response);
    }

    /**
     * Prepare interface
     * 
     * @param  mixed $interface
     * @param  array $attributes
     * @return mixed
     */
    public function perpareInterface($interface, $attributes){

        foreach ($attributes as $code => $value) {
            if($code == 'custom_attributes'){                
                foreach ($value as $_code => $_value) {
                    $interface->setCustomAttribute($_code, $_value);            
                }
                continue;
            }
            $interface->setData($code, $value);
        }

        return $interface;
    }

    /**
     * Create new customer 
     *
     * @param array | $data 
     * @return mixed
     */    
    public function getCustomerInterfaceAttributes($data){

        $customerAttributes = [];
        $customerAttributes['email'] = $data['email'];
        $customerAttributes['firstname'] = $data['firstName'];
        $customerAttributes['lastname'] = $data['lastName'];

        if($this->isGenderRequired()){
            $customerAttributes['gender'] = $this->getDefaultGender();
        }
        if($this->isPrefixRequired()){
            $customerAttributes['prefix'] = $this->getDefaultPrefix();
        }
        if($this->isSufixRequired()){
            $customerAttributes['sufix'] = $this->getDefaultSufix();
        }
        if($this->isDobRequired()){
            $customerAttributes['dob'] = $this->getDefaultDob();
        }
        if($this->isTaxvatRequired()){
            $customerAttributes['taxvat'] = $this->getDefaultTaxvat();
        }

        $customerAttributes['custom_attributes'] = ['social_account' => 'Apple'];
        
        $addCustomAttributes = $this->addCustomAttributes($customerAttributes);

        return $addCustomAttributes;
    }

    /**
     * Add required custom attributes
     *
     * @param array | $data 
     * @return mixed
     */
    public function addCustomAttributes($customerAttributes){

        $customAttributes = $this->getDefaultCustomAttributes();
        if(!empty($customAttributes)){
            foreach ($customAttributes as $attributeCode => $value) {
                $customerAttributes['custom_attributes'][$attributeCode] = $value;
            }
        }
        return $customerAttributes;
    }

    /**
     * Create new customer 
     *
     * @param array | $data 
     * @return mixed
     */
    public function createCustomer($data)
    {
        try{
            $interfaceAttributes = $this->getCustomerInterfaceAttributes($data);
            $customerInterface = $this->perpareInterface($this->customerDataFactory->create(), $interfaceAttributes);
            $customer = $this->accountManagement->createAccount($customerInterface);
            return $this->customerFactory->create()->load($customer->getId());
        }
        catch(Exception $e){
            throw new \Exception(__("There was a problem while creating customer"));
        }
    }

    /**
     * @param $customer
     *
     * @throws InputException
     * @throws FailureToSendException
     */
    public function refreshCustomerSession($customer)
    {
        if ($customer && $customer->getId()) {
            $this->customerSession->setCustomerAsLoggedIn($customer);
            $this->customerSession->regenerateId();

            if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     * @deprecated
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }

        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     * @deprecated
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }

        return $this->cookieMetadataFactory;
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function retrievePositiveInteger(string $data)
    {
        while ('00' === mb_substr($data, 0, 2, '8bit') && mb_substr($data, 2, 2, '8bit') > '7f') {
            $data = mb_substr($data, 2, null, '8bit');
        }
        return $data;
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function encode($data) {

        $encoded = strtr(base64_encode($data), '+/', '-_');
        return rtrim($encoded, '=');
    }

    /**
     * @param string $der
     * @param int    $partLength
     *
     * @return string
     */
    public function fromDER(string $der, int $partLength)
    {
        $hex = unpack('H*', $der)[1];
        
        if ('30' !== mb_substr($hex, 0, 2, '8bit')) { // SEQUENCE
            throw new \RuntimeException();
        }
        
        if ('81' === mb_substr($hex, 2, 2, '8bit')) { // LENGTH > 128
            $hex = mb_substr($hex, 6, null, '8bit');
        } else {
            $hex = mb_substr($hex, 4, null, '8bit');
        }
        
        if ('02' !== mb_substr($hex, 0, 2, '8bit')) { // INTEGER
            throw new \RuntimeException();
        }
        
        $Rl = hexdec(mb_substr($hex, 2, 2, '8bit'));
        $R = $this->retrievePositiveInteger(mb_substr($hex, 4, $Rl * 2, '8bit'));
        $R = str_pad($R, $partLength, '0', STR_PAD_LEFT);
        $hex = mb_substr($hex, 4 + $Rl * 2, null, '8bit');

        if ('02' !== mb_substr($hex, 0, 2, '8bit')) { // INTEGER
            throw new \RuntimeException();
        }

        $Sl = hexdec(mb_substr($hex, 2, 2, '8bit'));
        $S = $this->retrievePositiveInteger(mb_substr($hex, 4, $Sl * 2, '8bit'));
        $S = str_pad($S, $partLength, '0', STR_PAD_LEFT);
        
        return pack('H*', $R.$S);
    }

    public function generateJWT() {
        
        $authFile = $this->getAuthKeyFileDir();
        if(empty($authFile) || !file_exists($authFile)){
            return false;
        }
        
        $header = [
            'alg' => 'ES256',
            "type" => "JWT",
            'kid' => $this->getKeyId()
        ];
        $body = [
            'iss' => $this->getIssuerId(),
            'iat' => time(),
            'exp' => time() + 86400*180,
            'aud' => 'https://appleid.apple.com',
            'sub' => $this->getClientId(),
            'scope' => 'email name'
        ];

        $privKey = openssl_pkey_get_private(file_get_contents($authFile));
        if (!$privKey) return false;

        $payload = $this->encode(json_encode($header)).'.'.$this->encode(json_encode($body));
        $signature = '';
        $success = openssl_sign($payload, $signature, $privKey, OPENSSL_ALGO_SHA256);
        if (!$success) return false;

        $raw_signature = $this->fromDER($signature, 64);

        return $payload.'.'.$this->encode($raw_signature);
    }

    /**
     * Get redirection url.
     * @return string
     */
    public function getRedirectionUrl()
    {
        $redirectionType = $this->getConfigValue(self::APPLE_REDIRECTION_TYPE);

        if( !empty($redirectionType) ) {
            if( $redirectionType == \Zealousweb\AppleLogin\Model\Config\Source\RedirectionType::REDIRECT_CURRENTPGE ) {
                return $this->urlBuilder->getCurrentUrl();
            } elseif( $redirectionType == \Zealousweb\AppleLogin\Model\Config\Source\RedirectionType::REDIRECT_MYACCOUNT ) {
                return $this->urlBuilder->getUrl('customer/account');;
            }
        }

        return $this->urlBuilder->getUrl();
    }

    /**
     * Is show login popup
     *
     * @return bool
     */
    public function isShowLoginPopup()
    {
        return $this->getConfigValue(self::APPLE_IS_SHOW_LOGIN_POPUP);
    }

    /**
     * Check module enable or not
     *
     * @return bool
     */
    public function isShowButtonOnCheckout()
    {
        return $this->getConfigValue(self::APPLE_IS_SHOW_BUTTON_ON_CHECKOUT);
    }

    /**
     * Check module enable or not
     *
     * @return bool
     */
    public function isShowButtonOnCart()
    {
        return $this->getConfigValue(self::APPLE_IS_SHOW_BUTTON_ON_CART);
    }

    /**
     * Get display type
     *
     * @return string
     */
    public function getDisplayType()
    {
        $displayType = $this->getConfigValue(self::APPLE_LAYOUT_DISPLAY_TYPE);

        if( empty($displayType) ) {
            $displayType = 1;
        }
        return $displayType;
    }

    /**
     * Get apple icon
     *
     * @return string
     */
    public function getAppleIcon()
    {
        $appleIcon = $this->getConfigValue(self::APPLE_LAYOUT_ICON);

        if( empty($appleIcon) ) {
            $appleIcon = '';
        }
        return $appleIcon;
    }

    /**
     * Get apple button image
     *
     * @return string
     */
    public function getButtonImage()
    {
        $buttonImage = $this->getConfigValue(self::APPLE_LAYOUT_BUTTON_IMAGE);

        if( empty($buttonImage) ) {
            $buttonImage = '';
        }
        return $buttonImage;
    }

    /**
     * Get button label.
     * @return string
     */
    public function getButtonLabel()
    {
        $buttonLabel = $this->getConfigValue(self::APPLE_LAYOUT_BUTTON_LABEL);

        if( empty($buttonLabel) ) {
            $buttonLabel = __('Sign in with Apple');
        }

        return $buttonLabel;
    }

    /**
     * Get button layout.
     * @return string
     */
    public function getButtonLayout()
    {
        $buttonLayout = $this->getConfigValue(self::APPLE_LAYOUT_BUTTON_LAYOUT);

        if( empty($buttonLayout) ) {
            $buttonLayout = \Zealousweb\AppleLogin\Model\Config\Source\ButtonLayout::BUTTON_LAYOUT_LEFT_SIDE;
        }

        return $buttonLayout;
    }

    /**
     * Get button background color.
     * @return string
     */
    public function getButtonBackgroundColor()
    {
        $buttonBackgroundColor = $this->getConfigValue(self::APPLE_LAYOUT_BUTTON_BACKGROUND_COLOR);

        if( empty($buttonBackgroundColor) ) {
            $buttonBackgroundColor = '#000000';
        }

        return $buttonBackgroundColor;
    }

    /**
     * Get button text color.
     * @return string
     */
    public function getButtonTextColor()
    {
        $buttonTextColor = $this->getConfigValue(self::APPLE_LAYOUT_TEXT_COLOR);

        if( empty($buttonTextColor) ) {
            $buttonTextColor = '#ffffff';
        }

        return $buttonTextColor;
    }

    /**
     * Get button Border color.
     * @return string
     */
    public function getButtonBorderColor()
    {
        $buttonBorderColor = $this->getConfigValue(self::APPLE_LAYOUT_BORDER_COLOR);

        if( empty($buttonBorderColor) ) {
            $buttonBorderColor = '#000000';
        }

        return $buttonBorderColor;
    }

    /**
     * Get icon background color.
     * @return string
     */
    public function getIconBackgroundColor()
    {
        $iconBackgroundColor = $this->getConfigValue(self::APPLE_LAYOUT_ICON_BACKGROUND_COLOR);

        if( empty($iconBackgroundColor) ) {
            $iconBackgroundColor = '#ffffff';
        }

        return $iconBackgroundColor;
    }

    /**
     * Get button hover color.
     * @return string
     */
    public function getButtonHoverColor()
    {
        $buttonHoverColor = $this->getConfigValue(self::APPLE_LAYOUT_BUTTON_HOVER_COLOR);

        if( empty($buttonHoverColor) ) {
            $buttonHoverColor = '#ffffff';
        }

        return $buttonHoverColor;
    }

    /**
     * Get border hover color.
     * @return string
     */
    public function getBorderHoverColor()
    {
        $buttonBorderHoverColor = $this->getConfigValue(self::APPLE_LAYOUT_BORDER_HOVER_COLOR);

        if( empty($buttonBorderHoverColor) ) {
            $buttonBorderHoverColor = '#000000';
        }

        return $buttonBorderHoverColor;
    }

    /**
     * Get border hover color.
     * @return string
     */
    public function getTextHoverColor()
    {
        $buttonTextHoverColor = $this->getConfigValue(self::APPLE_LAYOUT_TEXT_HOVER_COLOR);

        if( empty($buttonTextHoverColor) ) {
            $buttonTextHoverColor = '#000000';
        }

        return $buttonTextHoverColor;
    }

    /**
     * To get media url
     * 
     * @return string
     */
    public function getMediaUrl()
    {
        $store = $this->storeManager->getStore();
        return $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get resize image
     * @param $imageName
     * @param $width
     * @param $height
     *
     * @return mixed
     */
    public function getResizeImage($imageName, $width = 200, $height = 200)
    {
        $name = substr($imageName, strrpos($imageName, '/') + 1);

        /* Real path of image from directory */
        $realPath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($imageName);
        if (!$this->directory->isFile($realPath) || !$this->directory->isExist($realPath)) {
            return '';
        }

        /* Target directory path where our resized image will be save */
        $targetDir = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('resized/'.$width.'x'.$height);
        $pathTargetDir = $this->directory->getRelativePath($targetDir);

        /* If Directory not available, create it */
        if (!$this->directory->isExist($pathTargetDir)) {
            $this->directory->create($pathTargetDir);
        }

        if (!$this->directory->isExist($pathTargetDir)) {
            return '';
        }

        $image = $this->imageFactory->create();
        $image->open($realPath);
        $image->constrainOnly(true);
        $image->keepAspectRatio(true);
        $image->keepFrame(true);
        $image->keepTransparency(true);
        $image->backgroundColor(array(255,255,255));
        $image->resize($width,$height);
        $dest = $targetDir . '/' . pathinfo($realPath, PATHINFO_BASENAME);
        $image->save($dest);

        if ($this->directory->isFile($this->directory->getRelativePath($dest))) {
            return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'resized/'.$width.'x'.$height.'/'.$name;
        }

        return '';
    }

    /**
     * Get detault image.
     * @return mixed
     */
    public function getDefaultImage()
    {
        $displayType = $this->getDisplayType();
        $defualtImage = '';
        
        if( !empty($displayType) && $displayType == \Zealousweb\AppleLogin\Model\Config\Source\DisplayType::DISPLAY_TYPE_BUTTON ) {
            $defualtImage = $this->assetRepo->getUrl("Zealousweb_AppleLogin::images/apple.png");
        } else {
            $defualtImage = $this->assetRepo->getUrl("Zealousweb_AppleLogin::images/apple-icon.png");
        }

        return $defualtImage;
    }

    /**
     * Get logo.
     * @return mixed
     */
    public function getLogoUrl()
    {
        return $this->logo->getLogoSrc();
    }

    /**
     * Get logo.
     * @return mixed
     */
    public function getLogoAlt()
    {
        return $this->logo->getLogoAlt();
    }

    /**
     * Check customer is logged in or not.
     * @return boolean
     */
    public function isCustomerLoggedIn()
    {
        if($this->customerSession->isLoggedIn()) {
           return true;
        }
        return false;
    }
}
