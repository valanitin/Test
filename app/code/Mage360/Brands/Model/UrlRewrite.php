<?php
declare(strict_types=1);
/**
 * Mage360_Brands extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Mage360
 * @package   Mage360_Brands
 * @copyright 2018 Mage360
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Qaiser Bashir
 */

namespace Mage360\Brands\Model;

use Magento\UrlRewrite\Model\UrlRewrite as BaseUrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteService;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\Framework\App\Config\Value;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;

class UrlRewrite extends Value
{

    /**
     * @var string
     */
    const URL_CONFIG_PATH = 'mage360_brands/brands_url/brand_url_key';

    /**
     * @var BaseUrlRewrite
     */
    protected $urlRewrite;

    /**
     * @var UrlRewriteService 
     */
    protected $urlRewriteService;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Url finder
     *
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param BaseUrlRewrite $urlRewrite
     * @param UrlRewriteService $urlRewriteService
     * @param StoreManagerInterface $storeManager
     * @param UrlFinderInterface $urlFinder
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        BaseUrlRewrite $urlRewrite,
        UrlRewriteService $urlRewriteService,
        StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->urlRewrite = $urlRewrite;
        $this->urlRewriteService = $urlRewriteService;
        $this->storeManager = $storeManager;
        $this->urlFinder = $urlFinder;
        $this->scopeConfig = $config;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return UrlRewrite
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterSave()
    {

        $storeId = $this->storeManager->getStore()->getId();

        if ($this->hasDataChanges()) { //different from default

            $getCustomUrlRewrite = isset($this->_data["groups"]) ? $this->_data["groups"]["brands_url"]["fields"]["brand_url_key"]["value"] : '';
            foreach ($this->_data as $key => $value) {
                if ($key == "field" && $value == "brand_url_key") {
                    $filterData = [
                        UrlRewriteService::TARGET_PATH => "brands",
                        UrlRewriteService::STORE_ID => $storeId
                    ];

                    $rewriteFinder = $this->urlFinder->findOneByData($filterData);

                    // if it was already set, just update it to the new one
                    if ($rewriteFinder) {
                        if ($getCustomUrlRewrite != "brands") {
                            $this->urlRewrite->load($rewriteFinder->getUrlRewriteId())
                                ->setRequestPath($getCustomUrlRewrite)
                                ->save();
                        } else {
                            $this->urlRewrite->load($rewriteFinder->getUrlRewriteId())->delete();
                        }
                    } else {
                        if ($getCustomUrlRewrite != "brands") {
                            $this->urlRewrite->setStoreId($storeId)
                                ->setIdPath(rand(1, 100000))
                                ->setRequestPath($getCustomUrlRewrite)
                                ->setTargetPath("brands")
                                ->setIsSystem(0)
                                ->save();
                        }
                    }
                }
            }
        }

        return parent::afterSave();
    }

    /**
     * @return UrlRewrite|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
public function beforeSave()
{
    $storeId = $this->storeManager->getStore()->getId();

        foreach ($this->_data as $key => $value) {
            if ($key == "field" && $value == "brand_url_key") {
                $filterData = [
                    UrlRewriteService::TARGET_PATH => "brands",
                    UrlRewriteService::STORE_ID => $storeId
                ];

                $rewriteFinder = $this->urlFinder->findOneByData($filterData);

                // if it was already set, just update it to the new one
                if ($rewriteFinder) {
                        $this->urlRewrite->load($rewriteFinder->getUrlRewriteId())
                            ->save();
                } else {
                        $this->urlRewrite->setStoreId($storeId)
                            ->setIdPath(rand(1, 100000))
                            ->setTargetPath("brands")
                            ->setIsSystem(0)
                            ->save();
                }
            }
        return parent::beforeSave();
    }
}

    /**
     * get url from configuration
     *
     * @return string
     */
    public function getCustomUrlRewrite()
    {
        return $this->scopeConfig->getValue(self::URL_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
}
