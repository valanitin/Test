<?php
namespace Revered\GuestWishlist\Block\Share;

class Wishlist extends \Magento\Wishlist\Block\AbstractBlock
{
    /**
     * Wishlist constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $httpContext,
            $data
        );
    }

    /**
     * Prepare global layout
     *
     * @return $this
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set($this->getHeader());
        return $this;
    }

    /**
     * Retrieve Page Header
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeader()
    {
        return __("Shared Wish List");
    }
}
