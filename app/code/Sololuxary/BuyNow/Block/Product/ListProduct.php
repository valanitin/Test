<?php


namespace Sololuxary\BuyNow\Block\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ProductList\Item\Block;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Url\Helper\Data as UrlHelper;

class ListProduct extends Block
{
    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * ListProduct constructor.
     * @param Context $context
     * @param UrlHelper $urlHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlHelper $urlHelper,
        array $data = []
    ) {
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
}
