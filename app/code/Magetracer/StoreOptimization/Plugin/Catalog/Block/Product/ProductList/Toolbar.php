<?php
/**
 * Mage Tracer.
 *
 * @category  Magetracer
 * @package   Magetracer_StoreOptimization
 * @author    Magetracer
 * @copyright Copyright (c) Mage Tracer (https://www.magetracer.com)
 * @license   https://www.magetracer.com/license.html
 */

namespace Magetracer\StoreOptimization\Plugin\Catalog\Block\Product\ProductList;

class Toolbar
{

    public function beforeGetTemplateFile(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $template = null)
    {
        if ($template == 'Magento_Catalog::product/list/toolbar/limiter.phtml') {
            $template = 'Magetracer_StoreOptimization/catalog/product/lazyloader.phtml';
        }
        return [$template];
    }
}
