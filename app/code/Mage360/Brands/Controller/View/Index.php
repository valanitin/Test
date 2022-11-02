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

namespace Mage360\Brands\Controller\View;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Mage360\Brands\Model\Brands;
use Mage360\Brands\Model\ResourceModel\Brands\CollectionFactory as BrandsCollectionFactory;
use Mage360\Brands\Model\Source\Attributevalue;

class Index extends Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    public $layerResolver;

    /**
     * @var BrandsCollectionFactory
     */
    public $brandsCollectionFactory;

    /**
     * @var Attributevalue
     */
    public $attributevalue;


    private $storeManager;

    /**
     * @var string
     */
    const SITENAME = 'Solo Luxury ';

    /**
     * @var string
     */
    const TITLE_LINE = ' - New Season Styles - ';

    /**
     * @var string
     */
    const DESC_LINE = ' Shop the entire collection of ';

    /**
     * @var string
     */
    const DESC_LINE_TWO = ' at Special Prices & Free Worldwide Delivery at ';

    /**
     *
     * @param Context              $context
     * @param PageFactory          $resultPageFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param Resolver             $layerResolver
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ScopeConfigInterface $scopeConfig,
        Resolver $layerResolver,
        BrandsCollectionFactory $brandsCollectionFactory,
        Attributevalue $attributevalue,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {

        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;

        $this->scopeConfig = $scopeConfig;

        $this->layerResolver = $layerResolver;

        $this->brandsCollectionFactory = $brandsCollectionFactory;

        $this->attributevalue = $attributevalue;

        $this->storeManager = $storeManager;
    }

    public function execute()
    {

        $result = $this->resultPageFactory->create();

        $url = $this->_url->getCurrentUrl();

        $urlKey = "brand";


        /**
         * get current country name.
         */
        $currentCountryName = $this->storeManager->getWebsite()->getName();

        /**
         * get url key from the url.
         */
        preg_match('/'.$urlKey.'\/(.*)/', $url, $matches);

        $brand_key = explode(".", $matches[1]);

        /**
         * Get brand by url key
         */
        $brand = $this->getBrandDetails($brand_key[0]);

        if (!empty($brand->getBrandId())) {
            $attr = $this->attributevalue->getAttribute();

            $attributeCode = $attr->getAttributeCode();

            /**
             * Prepare Layer and pass collection.
             */

            $collection = $this->layerResolver->get()->getProductCollection();

            $collection->addAttributeToFilter($attributeCode, $brand->getAttributeId());

            $list = $result->getLayout()->getBlock('custom.products.list');

			if(count($collection) > 0){ 
				$list->setProductCollection($collection);
			}
            /**
             * pass $brands to the block so it's accessible in pthml.
             */
            $result->getLayout()->getBlock('Brands.Brand')->setBrand($brand);

            /**
             * Set Title and Meta description and keywords.
             */

            $brandUpperCase = ucwords($brand->getName());
            $result->getConfig()->getTitle()->set(
                $brandUpperCase. self::TITLE_LINE .self::SITENAME . $currentCountryName
            );

            // $result->getConfig()->setDescription(
            //     $brand->getSeoDesc()
            // );

            $result->getConfig()->setDescription(
                $brandUpperCase . self::DESC_LINE . $brandUpperCase . self::DESC_LINE_TWO . self::SITENAME
            );

            $result->getConfig()->setKeywords(
                $brand->getSeoKeyword()
            );

            if ($this->scopeConfig->isSetFlag(Brands::CONFIG_BREADCRUMB, ScopeInterface::SCOPE_STORE)) {
                /**
                 * @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbsBlock
                 */
                $breadcrumbsBlock = $result->getLayout()->getBlock('breadcrumbs');
                if ($breadcrumbsBlock) {
                    $breadcrumbsBlock->addCrumb(
                        'home',
                        [
                            'label'    => __('Home'),
                            'link'     => $this->_url->getUrl('')
                        ]
                    );
                    $breadcrumbsBlock->addCrumb(
                        'brands_home',
                        [
                            'link'     => $this->_url->getUrl(
                                $this->scopeConfig->getValue(Brands::CONFIG_MAIN_URL_KEY, ScopeInterface::SCOPE_STORE)
                            ),
                            'label'    => $this->scopeConfig->getValue(
                                Brands::CONFIG_MENU_TITLE,
                                ScopeInterface::SCOPE_STORE
                            ),
                        ]
                    );
                    $breadcrumbsBlock->addCrumb(
                        'brand',
                        [
                            'label'    => $brand->getName(),
                        ]
                    );
                }
            }
        }

        return $result;
    }

    /**
     *  return brand data
     *
     * @return array
     */
    private function getBrandDetails($urlKey)
    {
        $collection = $this->getBrand($urlKey);
        foreach ($collection as $brand) {
            return $brand;
        }
    }

    private function getBrand($urlKey)
    {

        $collection = $this->brandsCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_active', Brands::STATUS_ENABLED)
            ->addFieldToFilter('url_key', $urlKey)
            ->setOrder('name', 'ASC');
        return $collection;
    }
}
