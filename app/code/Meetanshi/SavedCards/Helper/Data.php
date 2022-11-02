<?php

namespace Meetanshi\SavedCards\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreResolver;

/**
 * Class Data
 * @package Meetanshi\SavedCards\Helper
 */
class Data extends AbstractHelper
{
    const CONFIG_SAVEDCARDS_LOGO = 'payment/savedcards/show_logo';
    const CONFIG_SAVEDCARDS_INSTRUCTION = 'payment/savedcards/instructions';
    const CONFIG_SAVEDCARDS_DEBUG = 'payment/savedcards/debug';
    const CONFIG_SAVEDCARDS_INFO_BACKEND = 'payment/savedcards/cc_info_backend';
    const CONFIG_SAVEDCARDS_WIPE_BUTTON = 'payment/savedcards/cc_wipe_button';
    const CONFIG_SAVEDCARDS_HIDE_CC = 'payment/savedcards/hide_cc_info';
    const CONFIG_SAVEDCARDS_SHOW_ENCRYPTED = 'payment/savedcards/show_encrypted';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;
    /**
     * @var CurlFactory
     */
    private $curlFactory;
    /**
     * @var StoreResolver
     */
    private $storeResolver;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Data constructor.
     * @param Context $context
     * @param EncryptorInterface $encryptor
     * @param CurlFactory $curlFactory
     * @param StoreResolver $storeResolver
     * @param StoreManagerInterface $storeManager
     * @param Repository $repository
     * @param RequestInterface $request
     */
    public function __construct(Context $context, EncryptorInterface $encryptor, CurlFactory $curlFactory, StoreResolver $storeResolver, StoreManagerInterface $storeManager, Repository $repository, RequestInterface $request)
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
        $this->curlFactory = $curlFactory;
        $this->storeResolver = $storeResolver;
        $this->storeManager = $storeManager;
        $this->repository = $repository;
        $this->request = $request;
        $this->encryptor = $encryptor;
    }

    /**
     * @return mixed
     */
    public function showLogo()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SAVEDCARDS_LOGO, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getPaymentLogo()
    {
        $params = ['_secure' => $this->request->isSecure()];
        return $this->repository->getUrlWithParams('Meetanshi_SavedCards::images/savedcards.png', $params);
    }

    /**
     * @return mixed
     */
    public function getInstructions()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SAVEDCARDS_INSTRUCTION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function isLoggerEnabled()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SAVEDCARDS_DEBUG, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function ShowCCInfo()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SAVEDCARDS_INFO_BACKEND, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function ShowWipeLink()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SAVEDCARDS_WIPE_BUTTON, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function hideInfo()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SAVEDCARDS_HIDE_CC, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function showEncryptedInfo()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SAVEDCARDS_SHOW_ENCRYPTED, ScopeInterface::SCOPE_STORE);
    }

    public function getEncrypter()
    {
        return $this->encryptor;
    }
}
