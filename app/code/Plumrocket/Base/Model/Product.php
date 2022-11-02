<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model;

use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Api\GetModuleVersionInterface;
use Plumrocket\Base\Api\ProductStatusManagerInterface;

class Product extends AbstractModel
{
    /**
     * @var string
     */
    protected $_name = null;

    /**
     * @var string
     */
    protected $_session = null;

    /**
     * @var \Magento\Framework\App\Helper\AbstractHelper
     */
    private $_helper;

    /**
     * @var integer
     */
    protected $_dbCacheTime = 3;

    /**
     * @var string
     */
    protected $_sUrl;

    /**
     * @var boolean
     */
    protected $_test = false;

    /**
     * @var string
     */
    protected $_customer = null;

    /**
     * @var string
     */
    protected $_edit = null;

    /**
     * Product version
     */
    const V = 1;

    /**
     * Vendor
     */
    const PR = 'Plumrocket_';

    /**
     * @var \Plumrocket\Base\Helper\Base
     */
    private $baseHelper;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $backendAuthSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Encryption\Encryptor
     */
    private $encryptor;

    /**
     * @var \Plumrocket\Base\Api\ProductStatusManagerInterface
     */
    private $productStatusManager;

    /**
     * @var \Plumrocket\Base\Api\GetModuleVersionInterface
     */
    private $getModuleVersion;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * Product constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Plumrocket\Base\Helper\Base                                 $baseHelper
     * @param \Magento\Framework\App\ProductMetadataInterface              $productMetadata
     * @param \Magento\Store\Model\StoreManager                            $storeManager
     * @param \Magento\Framework\Module\ModuleListInterface                $moduleList
     * @param \Magento\Framework\Module\Manager                            $moduleManager
     * @param \Magento\Backend\Model\Auth\Session                          $backendAuthSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param \Magento\Framework\Encryption\EncryptorInterface             $encryptor
     * @param \Plumrocket\Base\Api\ProductStatusManagerInterface           $productStatusManager
     * @param \Plumrocket\Base\Api\GetModuleVersionInterface               $getModuleVersion
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface        $getExtensionInformation
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Plumrocket\Base\Helper\Base $baseHelper,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        ProductStatusManagerInterface $productStatusManager,
        GetModuleVersionInterface $getModuleVersion,
        GetExtensionInformationInterface $getExtensionInformation,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->baseHelper           = $baseHelper;
        $this->productMetadata      = $productMetadata;
        $this->storeManager         = $storeManager;
        $this->moduleList           = $moduleList;
        $this->moduleManager        = $moduleManager;
        $this->backendAuthSession   = $backendAuthSession;
        $this->scopeConfig          = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->productStatusManager = $productStatusManager;
        $this->getModuleVersion = $getModuleVersion;
        $this->getExtensionInformation = $getExtensionInformation;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Product::class);
        $this->_sUrl = implode(
            '', array_map(
                'ch' . 'r', [
                '104', '116', '116', '112', '115', '58', '47', '47', '115', '116', '111', '114', '101', '46', '112', '108', '117', '109', '114', '111', '99', '107', '101', '116', '46', '99', '111', '109', '47', '105', '108', '103', '47', '112', '105', '110', '103', '98', '97', '99', '107', '47']
            )
        );
    }

    /**
     * Load product from database
     *
     * @param  string | int  $id
     * @param  string | null $field
     * @return self
     */
    public function load($id, $field = null)
    {
        if ($field === null && !is_numeric($id)) {
            $this->_name = $id;
            return parent::load($this->getSignature(), 'signature');
        }
        return parent::load($id, $field);
    }

    /**
     * Receive magento admin value
     *
     * @param string $path
     * @param string|int|null $store
     * @param null|string $scope
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
     * Set name
     *
     * @param self
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Receive name in format SocialLoginFree
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Receive edition
     *
     * @return string
     */
    public function getEdit()
    {
        if ($this->_edit === null) {
            $this->_edit = $this->productMetadata->getEdition();
        }

        return $this->_edit;
    }

    /**
     * Receive true if cache is avalilable
     *
     * @return bool
     */
    public function isCached()
    {
        if ($this->_test) {
            return false;
        }
        return $this->getDate() > date('Y-m-d H:i:s') && $this->getDate() < date('Y-m-d H:i:s', time() + 30 * 86400);
    }

    /**
     * Check if product is in stock
     *
     * @return boolean
     */
    public function isInStock()
    {
        return "1";
    }

    /**
     * Receive product description
     *
     * @return string
     */
    public function getDescription()
    {
        
        return null;
    }

    /**
     * Receive current customer
     *
     * @return string
     */
    public function currentCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = 1;
        }
        return 'customer';
    }

    /**
     * Check if product is enabled
     * @deprecated since 2.3.7
     * @see \Plumrocket\Base\Api\ProductStatusManagerInterface::isEnabled
     *
     * @return bool
     */
    public function enabled(): bool
    {
        return $this->productStatusManager->isEnabled($this->getName());
    }

    /**
     * Receive product signature
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->encryptor->hash(
            $this->_name . $this->getSession(),
            Encryptor::HASH_VERSION_MD5
        );
    }

    /**
     * Receive session key
     *
     * @return string
     */
    public function getSessionKey()
    {
        $k = 'session_key';
        if (!$this->hasData($k)) {
            $info = $this->getExtensionInformation->execute($this->getName());
            if ($configSection = $info->getConfigSection()) {
                $this->setData($k, $configSection . '/general/' . strrev('laires'));
            } else {
                $mtd = 'get'.'Con'.'figS'.'ectionId';
                $helper = $this->getHelper();
                if ($helper && method_exists($helper, $mtd)) {
                    $this->setData($k, $helper->$mtd() . '/general/' . strrev('laires'));
                } else {
                    $this->setData($k, 'custom/general/' . strrev('laires'));
                }
            }
        }
        return $this->getData($k);
    }

    /**
     * Receive session
     *
     * @return string
     */
    public function getSession()
    {
        if (!$this->hasData('session')) {
            $this->setSession(
                $this->getConfig($this->getSessionKey(), 0)
            );
        }

        return preg_replace("/\s+/", '', $this->getData('session'));
    }

    /**
     * Load session
     *
     * @return string
     */
    public function loadSession()
    {
        $session = '';
        try {
            $data    = [
                'ed' . 'ition' => $this->getEdit(),
                'bas' . 'e_urls' => $this->getBaseU(),
                'name' => $this->getName(),
                'name_version'  => $this->getVersion(),
                'customer' => $this->getCustomer(),
                'title' => $this->getTitle(),
                'platform' => 'm2',
            ];
            $xml     = $this->_getContent($this->_sUrl . 'session/', $data);
            $session = $xml['data'] ?? null;
        } catch (\Exception $e) {
            if ($this->_test) {
                throw new \Exception($e->getMessage(), 1);
            }
        }
        $this->setSession($session);
        $this->saveStatus($this->getSimpleStatus());
        return $session;
    }

    /**
     * Receive helper
     *
     * @return \Magento\Framework\App\Helper\AbstractHelper|\Plumrocket\Base\Helper\Main|false
     */
    public function getHelper()
    {
        if ($this->_helper === null) {
            try {
                $this->_helper = $this->baseHelper->getModuleHelper($this->getName());
            } catch (NoSuchEntityException $e) {
                $this->_helper = false;
            }
        }
        return $this->_helper;
    }

    /**
     * Receive customer
     *
     * @return null|string
     */
    public function getCustomer()
    {
        $helper = $this->getHelper();
        if ($helper && method_exists($helper, 'getCustomerKey')) {
            return $helper->getCustomerKey();
        }
        return null;
    }

    /**
     * Receive base u
     *
     * @return array
     */
    public function getBaseU()
    {
        $k       = strrev('lru_esab' . '/' . 'eruces/bew');
        $_us     = [];
        $u       = $this->getConfig($k, 0);
        $_us[$u] = $u;
        foreach ($this->storeManager->getStores() as $store) {
            if ($store->getIsActive()) {
                $u = $this->getConfig($k, $store->getId());
                $_us[$u] = $u;
            }
        }
        return array_values($_us);
    }

    /**
     * Check product status
     *
     * @return self
     */
    public function checkStatus()
    {
        $session = $this->getSession();
        try {
            $data = [
                'edit' . 'ion' => self::getEdit(),
                'session' => $session,
                'ba' . 'se_u' . 'rls' => $this->getBaseU(),
                'name' => $this->getName(),
                'name_version' => $this->getVersion(),
                'customer' => $this->getCustomer(),
                'title' => $this->getTitle(),
                'platform' => 'm2',
                'magento_version' => $this->baseHelper->getMagento2Version()
            ];
            $xml  = $this->_getContent($this->_sUrl . 'extension/', $data);
            if (empty($xml['status'])) {
                throw new \Exception('Status is missing.', 1);
            }
            $status = $xml['status'];
        } catch (\Exception $e) {
            if ($this->_test) {
                throw new \Exception($e->getMessage(), 1);
            }
            $status = $this->getSimpleStatus();
        }
        return $this->saveStatus($status);
    }

    /**
     * Receive content
     *
     * @param  string $u
     * @param  array  $data
     * @return array
     */
    protected function _getContent($u, $data = [])
    {
        $res['cache_time'] = "3600000";
		$res['status'] = "success";

        $res = json_decode($res, true);
        if (!empty($res['cache_time']) && ($ct = (int) $res['cache_time']) && $ct > 0) {
            $this->_dbCacheTime = $ct;
        }
        return $res;
    }

    /**
     * Set db check time
     *
     * @param int
     */
    public function setDbCacheTime($ct)
    {
        $this->_dbCacheTime = $ct;
        return $this;
    }

    /**
     * Receive simple product status
     *
     * @return int
     */
    public function getSimpleStatus()
    {
        $session = $this->getSession();
        return (strlen($session) == 32 && $session[9] == $this->_name[2] && (strlen($this->_name) < 4
                || $session[20] == $this->_name[3])) ? 500 : 201;
    }

    /**
     * Receive product title
     *
     * @return [type] [description]
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * Save product status
     *
     * @param  int $status
     * @return self
     */
    public function saveStatus($status)
    {
        $signature = $this->getSignature();
        $this->getResource()->deleteOld();
        if (!$this->getId()) {
            $product = clone $this;
            $product = $product->load($signature, 'signature');
            $this->setId($product->getId());
        }
        return $this->setSignature($signature)
            ->setStatus($status)
            ->setDate(date('Y-m-d H:i:s', time() + $this->_dbCacheTime * 86400))
            ->save();
    }

    /**
     * Receive version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->getModuleVersion->execute($this->getName());
    }

    /**
     * Disable
     * @deprecated since 2.3.7
     * @see \Plumrocket\Base\Api\ProductStatusManagerInterface::disable
     *
     * @return self
     */
    public function disable()
    {
        $this->productStatusManager->disable($this->getName());
        return $this;
    }

    /**
     * Receive modules
     *
     * @return array
     */
    public function getAllModules()
    {
        $result  = [];
        foreach($this->moduleList->getAll() as $key => $module) {
            if (strpos($key, self::PR) !== false && $this->moduleManager->isEnabled($key) && !$this->getConfig('advan'.'ced/modu'.'les_dis'.'able_out'.'put'.'/'.$key, 0) ) {
                $result[$key] = $module;
            }
        }

        return $result;
    }

    /**
     * Make product reindex
     *
     * @return void
     */
    public function reindex()
    {
        $time = time();
        $session = $this->backendAuthSession;

        $ck = self::PR . 'base_reindex';

        if (!$session->isLoggedIn()
            || (86400 + $session->getPBProductReindex() > $time)
            || (86400 + $this->_cacheManager->load($ck) > $time)
        ) {
            if (!$this->_test) {
                return $this;
            }
        }

        $data = [
            'ed' . 'ition' => self::getEdit(),
            'products' => [],
            'ba' . 'se_ur' . 'ls' => $this->getBaseU(),
            'platform' => 'm2',
            'magento_version' => $this->baseHelper->getMagento2Version()
        ];
        $products = [];
        foreach ($this->getAllModules() as $key => $module) {
            $name    = str_replace(self::PR, '', $key);
            $product = clone $this;
            $product = $product->load($name);
            if (! $this->productStatusManager->isEnabled($product->getName()) || $product->isCached()) {
                continue;
            }
            $products[$name]         = $product;
            $v                       = $product->getVersion();
            $c                       = $product->getCustomer();
            $s                       = $product->getSession();
            $data['products'][$name] = [
                $name,
                $v,
                $c ? $c : 0,
                $s ? $s : 0,
                $product->getTitle()
            ];
        }
        if (count($products)) {
            try {
                $xml = $this->_getContent($this->_sUrl . 'extensions/', $data);
                if (!isset($xml['statuses'])) {
                    throw new \Exception('Statuses are missing.', 1);
                }
                $statuses = $xml['statuses'];
            } catch (\Exception $e) {
                if ($this->_test) {
                    throw new \Exception($e->getMessage(), 1);
                }
                $statuses = [];
                foreach ($products as $name => $product) {
                    $statuses[$name] = $product->getSimpleStatus();
                }
            }
            foreach ($products as $name => $product) {
                $status = $statuses[$name] ?? 301;
                $product->setDbCacheTime($this->_dbCacheTime)->saveStatus($status);
                if (!$product->isInStock()) {
                    $this->productStatusManager->disable($product->getName());
                }
            }
        }
        $this->_cacheManager->save($time, $ck);
        $session->setPBProductReindex($time);
    }
}
