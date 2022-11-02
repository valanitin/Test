<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Plugin\Catalog\Helper;

use Amasty\SeoSingleUrl\Model\Source\Breadcrumb;
use Amasty\SeoSingleUrl\Model\Source\Type;
use Magento\Catalog\Helper\Data as MagentoData;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class Data
{
    /**
     * @var \Amasty\SeoSingleUrl\Helper\Data
     */
    private $helper;

    /**
     * @var CollectionFactory
     */
    private $categoryFactoryCollection;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var MagentoData
     */
    private $catalogData;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Amasty\SeoSingleUrl\Helper\Data $helper,
        CollectionFactory $categoryFactoryCollection,
        \Magento\Catalog\Helper\Data $catalogData,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->categoryFactoryCollection = $categoryFactoryCollection;
        $this->serializer = $serializer;
        $this->catalogData = $catalogData;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
    }

    /**
     * @param MagentoData $subject
     * @param \Closure $proceed
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetBreadcrumbPath(
        MagentoData $subject,
        \Closure $proceed
    ) {
        $result = $this->getBreadcrumbsData($subject->getProduct());

        if (!$result) {
            $result = $proceed();
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\ViewModel\Product\Breadcrumbs $subject
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetJsonConfigurationHtmlEscaped($subject, $result)
    {
        $type = $this->helper->getModuleConfig('general/breadcrumb');

        if ($type === Breadcrumb::LAST_VISITED && $result) {
            try {
                $result = $this->serializer->unserialize($result);
                $result['breadcrumbs']['useCategoryPathInUrl'] = 0; // magento get breadcrumb from prev url
                $result = $this->serializer->serialize($result);
            } catch (\InvalidArgumentException $exception) {
                null;//do nothing - return result
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\ViewModel\Product\Breadcrumbs $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsCategoryUsedInProductUrl($subject, $result)
    {
        $type = $this->helper->getModuleConfig('general/breadcrumb');

        if ($type === Breadcrumb::LAST_VISITED) {
            return false;
        }

        return $result;
    }

    /**
     * @param null|Product $product
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getBreadcrumbsData($product = null)
    {
        $type = $this->helper->getModuleConfig('general/breadcrumb');

        if ($product === null) {
            $product = $this->catalogData->getProduct();
        }
        $result = [];

        $url = $this->getLastVisitedCategoryUrl();

        if ($url && $product && $type === Breadcrumb::LAST_VISITED) {
            $urlArray = $this->parseUrl($url);

            if ($urlArray) {
                $storeId = $product->getStoreId();
                $breadcrumbsIds = $this->getBreadcrumbsPath(
                    $storeId,
                    end($urlArray),
                    $product->getCategoryIds()
                );

                $breadcrumbs = $this->categoryFactoryCollection->create()
                    ->setStore($storeId)
                    ->addNameToResult()
                    ->addAttributeToSelect('url_key')
                    ->addIdFilter($breadcrumbsIds);

                if (!empty(array_filter($breadcrumbsIds))) {
                    $breadcrumbs->getSelect()->order(new \Zend_Db_Expr('FIELD(entity_id,'
                        . implode(",", $breadcrumbsIds) . ')'));
                }

                foreach ($breadcrumbs as $breadcrumb) {
                    if ($breadcrumb->getUrlKey() && in_array($breadcrumb->getUrlKey(), $urlArray)) {
                        $result['category' . $breadcrumb->getId()] = [
                            'name' => 'category',
                            'label' => $breadcrumb->getName(),
                            'link' => $breadcrumb->getUrl(),
                            'title' => ''
                        ];
                    }
                }
            }

            $result['product'] = [
                'name' => 'product',
                'label' => $product->getName(),
                'title' => ''
            ];
        }

        return $result;
    }

    /**
     * @param string $url
     *
     * @return array|mixed|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function parseUrl(string $url)
    {
        $url = str_replace($this->storeManager->getStore()->getBaseUrl(), '', $url);
        $url = explode('.', $url);
        $url = array_shift($url); //remove suffix
        $url = explode('/', $url);

        return $url;
    }

    /**
     * @return string
     */
    private function getLastVisitedCategoryUrl()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->registry->registry('current_category');

        return $category ? $category->getUrl() : '';
    }

    /**
     * @param int $storeId
     * @param string $urlKey
     * @param array $availableIds
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getBreadcrumbsPath($storeId, $urlKey, $availableIds)
    {
        $productCategory = $this->categoryFactoryCollection->create()
            ->setStore($storeId)
            ->addAttributeToFilter('url_key', $urlKey)
            ->addIdFilter($availableIds)
            ->addOrderField('level')
            ->setPageSize(1)
            ->getFirstItem();

        return explode('/', $productCategory->getPath());
    }
}
