<?php

namespace Dynamic\LayeredApi\Model;

use Dynamic\LayeredApi\Api\GetLayered;
use Magento\Catalog\Model\Layer\Category\FilterableAttributeList;
use Magento\Catalog\Model\Layer\FilterListFactory;
use Magento\Catalog\Model\Layer\Resolver as layerResolver;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Category;

class GetLayeredListModel implements GetLayered
{
    /**
     * @var \Magento\Catalog\Model\Layer\Category\FilterableAttributeList
     */
    protected $filterableAttributes;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $layerResolver;

    /**
     * @var \Magento\Catalog\Model\Layer\FilterListFactory
     */
    protected $filterListFactory;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;

    /**
     * Size data constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Category\FilterableAttributeList $filterableAttributes
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param FilterListFactory $filterListFactory
     * @param Category $category
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        FilterableAttributeList $filterableAttributes,
        layerResolver $layerResolver,
        FilterListFactory $filterListFactory,
        Category $category
    ) {
        $this->filterableAttributes = $filterableAttributes;
        $this->layerResolver = $layerResolver;
        $this->filterListFactory = $filterListFactory;
        $this->category = $category;
    }

    /**
     * Returns Layer data
     *
     * @api
     * @return return Layer array collection.
     */
    public function getLayeredList($categoryId)
    {
        $data = [];

        if($categoryId) {
            
            $categoryData = $this->category->load($categoryId);

            if(!$categoryData->getId()) {
                $data = array(
                    ['status' => 'No Data','message' => __('This category does not exist in this website.') ]
                );
                return $data;
            }

            $filterableAttributes = $this->filterableAttributes;
            $layerResolver = $this->layerResolver;
            $filterList = $this->filterListFactory->create(['filterableAttributes' => $filterableAttributes]);
            $layer = $layerResolver->get();
            $layer->setCurrentCategory($categoryId);
            $layer->getProductCollection()
                ->addAttributeToFilter("visibility", ["neq" => Visibility::VISIBILITY_NOT_VISIBLE])
                ->addAttributeToFilter("status", ["eq" => Status::STATUS_ENABLED]);

            $filters = $filterList->getFilters($layer);

            $maxPrice = $layer->getProductCollection()->getMaxPrice();
            $minPrice = $layer->getProductCollection()->getMinPrice();

            if(!empty($filters)) {
                foreach ($filters as $filter) {
                    $values = [];
                    foreach ($filter->getItems() as $item) {
                        $values[] = [
                            "display" => strip_tags($item->getLabel()),
                            "value" => $item->getValue(),
                            "count" => $item->getCount()
                        ];
                    }
                    
                    if (!empty($values)) {
                        if($filter->getRequestVar() == "price") {
                            if ($maxPrice != $minPrice) {
                                $data[] = [
                                    "attr_code" => $filter->getRequestVar(),
                                    "attr_label" => $filter->getName(),
                                    "min_price" => $minPrice,
                                    "max_price" => $maxPrice,
                                    "values" => $values
                                ];
                            }
                        } else {
                            $data[] = [
                                "attr_code" => $filter->getRequestVar(),
                                "attr_label" => $filter->getName(),
                                "values" => $values
                            ];
                        }
                    }
                }

                if(empty($data)) {
                    $data = array(
                        ['status' => 'No Data','message' => __('There are no Layered data in this website.') ]
                    );
                }

            } else {
                $data = array(
                    ['status' => 'No Data','message' => __('There are no Layered data in this website.') ]
                );
            }
        } else {
            $data = array(
                ['status' => 'No Data','message' => __('There are no Layered data in this website.') ]
            );
        }
        return $data;
    }
}
