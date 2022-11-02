<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GeoIPAutoSwitchStore
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GeoIPAutoSwitchStore\Controller\Index;

use Magento\Framework\App\Action\Context;

class SwitchStore extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\Data
     */
    public $moduleHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    public $countryFactory;

    /**
     * @var Context
     */
    public $context;

    /**
     * @var \Magento\Framework\Url\Encoder
     */
    public $urlEncoder;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $session;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData
     */
    private $geoIpHelper;

    /**
     * SwitchStore constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Bss\GeoIPAutoSwitchStore\Helper\Data $moduleHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Framework\Url\Encoder $urlEncoder
     * @param \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData $geoIpHelper
     * @param \Magento\Framework\Session\Generic $session
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Bss\GeoIPAutoSwitchStore\Helper\Data $moduleHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Url\Encoder $urlEncoder,
        \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData $geoIpHelper,
        \Magento\Framework\Session\Generic $session
    ) {
        $this->storeManager = $storeManager;
        $this->moduleHelper = $moduleHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->context = $context;
        $this->countryFactory = $countryFactory;
        $this->urlEncoder = $urlEncoder;
        $this->session = $session;
        $this->geoIpHelper = $geoIpHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $currentUrl = $this->getRequest()->getPost('current_url');
            $currentPath = $this->getRequest()->getPost('current_path');

            $status = false;
            $countryCode = $this->session->getCountryCode();
            if (!$countryCode) {
                $countryCode = $this->moduleHelper->getCountryCodeFromIp();
            }
            $openPopup = $this->session->getOpenPopup();
            $dataCountry = [];
            $sysMessage = $countryCode;
            if ($countryCode && !$openPopup) {
                try {
                    $countryName = $this->getCountryName($countryCode);
                    $storeCountry = $this->getStoreByCountryCode($countryCode);

                    if ($storeCountry && $storeCountry->getId() !== $this->storeManager->getStore()->getId()) {
                        $returnData = $this->setData($currentPath, $currentUrl, $storeCountry, $countryName);
                        $status = $returnData['status'];
                        $dataCountry = $returnData['data'];
                    }

                } catch (\Exception $e) {
                    $sysMessage = $e->getMessage();
                }

            } else {
                $dataCountry = [];
            }

            $dataSelectors = [];
            $allWebsite = $this->storeManager->getWebsites();
            $allGroups = $this->storeManager->getGroups();
            $allStores = $this->storeManager->getStores();
            $countryName = $this->getCountryName($countryCode);
            $storeInCountry = $this->getStoreByCountryCode($countryCode);
            foreach ($allWebsite as $website) {
                $dataSelectors[$website->getCode()] = [];
                $dataSelectors[$website->getCode()]['info'] = ['name' => $website->getName()];
                $dataSelectors[$website->getCode()]['groups'] = [];
                foreach ($allGroups as $group) {
                    if ($group->getWebsiteId() == $website->getId()) {
                        $dataSelectors[$website->getCode()]['groups'][$group->getCode()]['info'] = ['name' => $group->getName()];
                        $dataSelectors[$website->getCode()]['groups'][$group->getCode()]['stores'] = [];
                        foreach ($allStores as $store) {
                            if ($store->getStoreGroupId() == $group->getId()) {
                                $selected = 0;
                                if ($store->getId() == $this->doGetSelectedStoreCountry($storeInCountry)) {
                                    $selected = 1;
                                }
                                $dataSelectors[$website->getCode()]['groups'][$group->getCode()]['stores'][] = [
                                    'store' => $store->getCode(),
                                    'name' => $store->getName(),
                                    '_active' => $store->getIsActive(),
                                    'selected' => $selected,
                                    'data' => $store->getIsActive() ? $this->setData($currentPath, $currentUrl, $store, $countryName) : []
                                ];
                            }
                        }
                    }
                }
            }

            $dataResult = [
                'status' => $status,
                'message' => $sysMessage,
                'data' => $dataCountry
            ];
            $dataResult = $this->doMergeDataAfterRender(
                $dataResult,
                $dataSelectors,
                $countryName,
                $storeInCountry,
                $currentUrl
            );

            $result->setData($dataResult);
        }
        return $result;
    }

    /**
     * Get selected store view
     *
     * @param bool|\Magento\Store\Api\Data\StoreInterface $storeCountry
     * @return int|bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function doGetSelectedStoreCountry($storeCountry)
    {
        if ($storeCountry) {
            if ($storeCountry->getId()) {
                return $storeCountry->getId();
            }
            return $this->storeManager->getStore()->getId();
        }
        return $storeCountry;
    }

    /**
     * Merge data
     *
     * @param array $dataResult
     * @param array $dataSelectors
     * @param string $countryName
     * @param bool|\Magento\Store\Api\Data\StoreInterface $storeCountry
     * @param string $currentUrl
     * @return array
     */
    protected function doMergeDataAfterRender($dataResult, $dataSelectors, $countryName, $storeCountry, $currentUrl)
    {
        if ($storeCountry) {
            return array_merge_recursive($dataResult, [
                'selectors' => $dataSelectors,
                'dataMessage' => $this->moduleHelper->getPopupMessage($storeCountry->getId()),
                'dataCountry' => $countryName,
                'dataButton' => $this->moduleHelper->getPopupButton($storeCountry->getId()),
                'dataStoreName' => $storeCountry->getName(),
                'dataPost' => $this->moduleHelper->getTargetStorePostData($storeCountry, $currentUrl)
            ]);
        }
        return $dataResult;
    }

    /**
     * @param string $currentPath
     * @param string $currentUrl
     * @param \Magento\Store\Model\Store $storeCountry
     * @param string $countryName
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function setData($currentPath, $currentUrl, $storeCountry, $countryName)
    {
        $returnResult['status'] = false;
        $returnResult['data'] = [];

        //Get Redirects Scope to Redirects
        $redirectScope = $this->moduleHelper->getRedirectsScope($this->storeManager->getStore()->getId());

        $currentStoreView = ',' . $storeCountry->getId() . ',';
        if ($redirectScope == 'website') {
            $storeViewIdScope = ',' . $this->getStoreIdFromWebsite();
            if (strpos($storeViewIdScope, $currentStoreView) === false) {
                return $returnResult;
            }
        }

        if ($redirectScope == 'store') {
            $storeViewIdScope = ',' . $this->getStoreIdFromGroup();
            if (strpos($storeViewIdScope, $currentStoreView) === false) {
                return $returnResult;
            }
        }

        $currentPath = $this->geoIpHelper->getCurrentPath(
            $currentPath,
            $currentUrl,
            $this->storeManager->getStore()->getId(),
            $storeCountry->getId()
        );

        $currentUrl = $storeCountry->getBaseUrl() . $currentPath;

        $baseUrl = $this->moduleHelper->getBaseUrl();
        $dataPost = $this->moduleHelper->getTargetStorePostData($storeCountry, $currentUrl);
        $message = $this->moduleHelper->getPopupMessage($storeCountry->getId());
        $button = $this->moduleHelper->getPopupButton($storeCountry->getId());
        $returnResult['data'] = [
            'base_url' => $baseUrl,
            'data_post' => $dataPost,
            'message' => $message,
            'country_name' => $countryName,
            'button' => $button,
            'store_name' => $storeCountry->getName()
        ];
        $returnResult['status'] = true;
        $this->session->setOpenPopup(true);
        return $returnResult;
    }

    /**
     * @param string $countryCode
     * @return string
     */
    protected function getCountryName($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    /**
     * @param string $countryCode
     * @return bool|\Magento\Store\Api\Data\StoreInterface
     */
    protected function getStoreByCountryCode($countryCode)
    {
        //Get All Store view
        $stores = $this->storeManager->getStores(false);

        foreach ($stores as $store) {
            $countryStore = $this->moduleHelper->getCountries($store->getId());

            if (strpos($countryStore, $countryCode) !== false && $store->isActive()) {
                return $store;
            }
        }
        return false;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStoreIdFromGroup()
    {
        $groupId = $this->storeManager->getStore()->getGroupId();
        $storesView = $this->geoIpHelper
            ->getGroupFactory()
            ->create()
            ->getCollection()
            ->addFieldToFilter('group_id', $groupId);
        $storeViewId = '';

        foreach ($storesView as $storeView) {
            foreach ($storeView->getStores() as $myStore) {
                $storeViewId = $storeViewId . $myStore->getId() . ',';
            }
        }
        return $storeViewId;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStoreIdFromWebsite()
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $storesView = $this->geoIpHelper
            ->getGroupFactory()
            ->create()
            ->getCollection()
            ->addFieldToFilter('website_id', $websiteId);
        $storeViewId = '';

        foreach ($storesView as $storeView) {
            foreach ($storeView->getStores() as $myStore) {
                $storeViewId = $storeViewId . $myStore->getId() . ',';
            }
        }
        return $storeViewId;
    }
}
