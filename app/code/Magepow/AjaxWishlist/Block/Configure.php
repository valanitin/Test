<?php

namespace Magepow\AjaxWishlist\Block;

use Magepow\AjaxWishlist\Helper\Data as AjaxWishlistHelper;
use Magento\Customer\Model\Url;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * AjaxWishlist Configure block
 *
 * @package Magepow\AjaxWishlist\Block
 */
class Configure extends \Magento\Framework\View\Element\Template
{
    /**
     * @var AjaxWishlistHelper
     */
    protected $ajaxWishlistHelper;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * Configure constructor.
     *
     * @param AjaxWishlistHelper $ajaxWishlistHelper
     * @param JsonHelper         $jsonHelper
     * @param Template\Context   $context
     * @param array              $data
     */
    public function __construct(
        AjaxWishlistHelper $ajaxWishlistHelper,
        JsonHelper $jsonHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data
    )
    {
        $this->ajaxWishlistHelper = $ajaxWishlistHelper;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get JSON configuration option
     *
     * @return string
     */
    public function getWidgetConfigurationOptions()
    {
        return $this->jsonHelper->jsonEncode(
            $this->getConfigurationOptions()
        );
    }

    /**
     * Get configuration option
     *
     * @return array
     */
    public function getConfigurationOptions()
    {
        return [
            'isShowSpinner' => (bool) $this->ajaxWishlistHelper->getConfigModule('general/show_spinner'),
            'isShowSuccessMessage' => $this->ajaxWishlistHelper->getConfigModule('general/show_success_message'),
            'successMessageText' => $this->ajaxWishlistHelper->getConfigModule('general/message'),
            'customerLoginUrl' => $this->_urlBuilder->getUrl(Url::ROUTE_ACCOUNT_LOGIN),
            'popupTtl' => $this->ajaxWishlistHelper->getConfigModule('general/popupttl')
        ];
    }
}