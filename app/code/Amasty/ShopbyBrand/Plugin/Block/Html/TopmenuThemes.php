<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Plugin\Block\Html;

use Magento\Framework\Data\Tree\Node;
use Magento\Store\Model\ScopeInterface;
use Amasty\ShopbyBrand\Model\Source\TopmenuLink as TopmenuSource;

class TopmenuThemes extends Topmenu
{
    /**
     * @param $subject
     * @param $html
     * @return string
     */
    public function afterRenderCategoriesMenuHtml(
        $subject,
        $html
    ) {
        $position = $topMenuEnabled = $this->scopeConfig->getValue(
            'amshopby_brand/general/topmenu_enabled',
            ScopeInterface::SCOPE_STORE
        );

        if ($position) {
            $htmlBrand = $this->generateHtml($this->_getNodeAsArray());
            if ($position == TopmenuSource::DISPLAY_FIRST) {
                $html = $htmlBrand . $html;
            } else {
                $html .= $htmlBrand;
            }
        }

        return $html;
    }

    /**
     * @param $subject
     * @param $html
     * @return string
     */
    public function afterGetMegamenuHtml(
        $subject,
        $html
    ) {
        return $this->afterRenderCategoriesMenuHtml($subject, $html);
    }

    /**
     * @param $data
     * @return string
     */
    private function generateHtml($data)
    {
        return '<li class="nav-item nav-item--brand level0 level-top">
                    <a class="level-top" href="' . $data['url'] . '"><span>' . $data['name'] . '</span></a>
                </li>';
    }
}
